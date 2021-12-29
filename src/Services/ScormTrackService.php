<?php

namespace EscolaLms\Scorm\Services;

use Carbon\Carbon;
use EscolaLms\Scorm\Services\Contracts\ScormTrackServiceContract;
use EscolaLms\Scorm\Strategies\Scorm12FieldStrategy;
use EscolaLms\Scorm\Strategies\Scorm2004FieldStrategy;
use EscolaLms\Scorm\Strategies\ScormFieldStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Peopleaps\Scorm\Entity\Scorm;
use Peopleaps\Scorm\Entity\ScoTracking;
use Peopleaps\Scorm\Model\ScormModel;
use Peopleaps\Scorm\Model\ScormScoModel;
use Peopleaps\Scorm\Model\ScormScoTrackingModel;
use Ramsey\Uuid\Uuid;

class ScormTrackService implements ScormTrackServiceContract
{
    public function getUserResult(int $scoId, int $userId): ?ScormScoTrackingModel
    {
        return ScormScoTrackingModel::where('sco_id', $scoId)
            ->with('sco.scorm')
            ->where('user_id', $userId)->first();
    }

    public function getUserResultSpecifiedValue(string $key, int $scoId, int $userId)
    {
        $scormScoTracking = $this->getUserResult($scoId, $userId);

        if (!$scormScoTracking) {
            return '';
        }

        $strategy = $this->getScormFieldStrategy($scormScoTracking->sco->scorm->version);
        return is_null($scormScoTracking->{$strategy->getField($key)}) ? '' : $scormScoTracking->{$strategy->getField($key)};
    }

    public function createScoTracking($scoUuid, $userId = null): ScoTracking
    {
        $sco = ScormScoModel::where('uuid', $scoUuid)
            ->orderBy('id', 'desc')
            ->firstOrFail();

        $version = $sco->scorm->version;
        $scoTracking = new ScoTracking();
        $scoTracking->setSco($sco->toArray());

        switch ($version) {
            case Scorm::SCORM_12:
                $scoTracking->setLessonStatus('not attempted');
                $scoTracking->setSuspendData('');
                $scoTracking->setEntry('ab-initio');
                $scoTracking->setLessonLocation('');
                $scoTracking->setCredit('no-credit');
                $scoTracking->setTotalTimeInt(0);
                $scoTracking->setSessionTime(0);
                $scoTracking->setLessonMode('normal');
                $scoTracking->setExitMode('');

                if (is_null($sco->prerequisites)) {
                    $scoTracking->setIsLocked(false);
                } else {
                    $scoTracking->setIsLocked(true);
                }
                break;
            case Scorm::SCORM_2004:
                $scoTracking->setTotalTimeString('PT0S');
                $scoTracking->setCompletionStatus('unknown');
                $scoTracking->setLessonStatus('unknown');
                $scoTracking->setIsLocked(false);

                break;
        }

        $scoTracking->setUserId($userId);

        // Create a new tracking model
        $storeTracking = ScormScoTrackingModel::firstOrCreate([
            'user_id' => $userId,
            'sco_id' => $sco->id
        ], [
            'uuid' => Uuid::uuid4(),
            'progression' => $scoTracking->getProgression(),
            'score_raw' => $scoTracking->getScoreRaw(),
            'score_min' => $scoTracking->getScoreMin(),
            'score_max' => $scoTracking->getScoreMax(),
            'score_scaled' => $scoTracking->getScoreScaled(),
            'lesson_status' => $scoTracking->getLessonStatus(),
            'completion_status' => $scoTracking->getCompletionStatus(),
            'session_time' => $scoTracking->getSessionTime(),
            'total_time_int' => $scoTracking->getTotalTimeInt(),
            'total_time_string' => $scoTracking->getTotalTimeString(),
            'entry' => $scoTracking->getEntry(),
            'suspend_data' => $scoTracking->getSuspendData(),
            'credit' => $scoTracking->getCredit(),
            'exit_mode' => $scoTracking->getExitMode(),
            'lesson_location' => $scoTracking->getLessonLocation(),
            'lesson_mode' => $scoTracking->getLessonMode(),
            'is_locked' => $scoTracking->getIsLocked(),
            'details' => $scoTracking->getDetails(),
            'latest_date' => $scoTracking->getLatestDate(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $scoTracking->setUuid($storeTracking->uuid);
        $scoTracking->setProgression($storeTracking->progression);
        $scoTracking->setScoreRaw($storeTracking->score_raw);
        $scoTracking->setScoreMin($storeTracking->score_min);
        $scoTracking->setScoreMax($storeTracking->score_max);
        $scoTracking->setScoreScaled($storeTracking->score_scaled);
        $scoTracking->setLessonStatus($storeTracking->lesson_status);
        $scoTracking->setCompletionStatus($storeTracking->completion_status);
        $scoTracking->setSessionTime($storeTracking->session_time);
        $scoTracking->setTotalTimeInt($storeTracking->total_time_int);
        $scoTracking->setTotalTimeString($storeTracking->total_time_string);
        $scoTracking->setEntry($storeTracking->entry);
        $scoTracking->setSuspendData($storeTracking->suspend_data);
        $scoTracking->setCredit($storeTracking->credit);
        $scoTracking->setExitMode($storeTracking->exit_mode);
        $scoTracking->setLessonLocation($storeTracking->lesson_location);
        $scoTracking->setLessonMode($storeTracking->lesson_mode);
        $scoTracking->setIsLocked($storeTracking->is_locked);
        $scoTracking->setDetails($storeTracking->details);
        $scoTracking->setLatestDate(Carbon::parse($storeTracking->latest_date));

        return $scoTracking;
    }

    public function findScoTrackingId($scoUuid, $scoTrackingUuid)
    {
        return ScormScoTrackingModel::with([
            'sco'
        ])->whereHas('sco', function (Builder $query) use ($scoUuid) {
            $query->where('uuid', $scoUuid);
        })->where('uuid', $scoTrackingUuid)
            ->firstOrFail();
    }

    public function checkUserIsCompletedScorm($scormId, $userId): bool
    {
        $completedSco = [];
        $scos = ScormScoModel::where('scorm_id', $scormId)->get();

        foreach ($scos as $sco) {
            $scoTracking = ScormScoTrackingModel::where('sco_id', $sco->id)->where('user_id', $userId)->first();

            if ($scoTracking && ($scoTracking->lesson_status == 'passed' || $scoTracking->lesson_status == 'completed')) {
                $completedSco[] = true;
            }
        }

        if (count($completedSco) == $scos->count()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateScoTracking($scoUuid, $userId, $data)
    {
        $tracking = $this->createScoTracking($scoUuid, $userId);
        $tracking->setLatestDate(Carbon::now());
        $sco = $tracking->getSco();
        $scorm = ScormModel::where('id', $sco['scorm_id'])->firstOrFail();
        $updateResult = ScormScoTrackingModel::where('user_id', $tracking->getUserId())
            ->where('sco_id', $sco['id'])
            ->firstOrFail();

        $statusPriority = [
            'unknown' => 0,
            'not attempted' => 1,
            'browsed' => 2,
            'incomplete' => 3,
            'completed' => 4,
            'failed' => 5,
            'passed' => 6,
        ];

        switch ($scorm->version) {
            case Scorm::SCORM_12:
                if (isset($data['cmi.suspend_data']) && !empty($data['cmi.suspend_data'])) {
                    $tracking->setSuspendData($data['cmi.suspend_data']);
                }

                $scoreRaw = isset($data['cmi.core.score.raw']) ? intval($data['cmi.core.score.raw']) : $updateResult->score_raw;
                $scoreMin = isset($data['cmi.core.score.min']) ? intval($data['cmi.core.score.min']) : $updateResult->score_min;
                $scoreMax = isset($data['cmi.core.score.max']) ? intval($data['cmi.core.score.max']) : $updateResult->score_max;
                $lessonStatus = isset($data['cmi.core.lesson_status']) ? $data['cmi.core.lesson_status'] : 'unknown';
                $sessionTime = isset($data['cmi.core.session_time']) ? $data['cmi.core.session_time'] : null;
                $sessionTimeInHundredth = $this->convertTimeInHundredth($sessionTime);
                $progression = isset($data['cmi.progress_measure']) ? floatval($data['cmi.progress_measure']) : 0;

                $entry = $data['cmi.core.entry'] ?? $updateResult->entry;
                $exit = $data['cmi.core.exit'] ?? $updateResult->exit;
                $lessonLocation = $data['cmi.core.lesson_location'] ?? $updateResult->lesson_location;
                $totalTime = $data['cmi.core.total_time'] ?? 0;

                $tracking->setDetails($data);
                $tracking->setEntry($entry);
                $tracking->setExitMode($exit);
                $tracking->setLessonLocation($lessonLocation);
                $tracking->setSessionTime($sessionTimeInHundredth);

                // Compute total time
                $totalTimeInHundredth = $this->convertTimeInHundredth($totalTime);
                $totalTimeInHundredth += $sessionTimeInHundredth;

                // Persist total time
                if ($tracking->getTotalTimeInt() > 0) {
                    $totalTimeInHundredth += $tracking->getTotalTimeInt();
                }

                $tracking->setTotalTime($totalTimeInHundredth, Scorm::SCORM_12);

                $bestScore = $tracking->getScoreRaw();
                $bestStatus = $tracking->getLessonStatus();

                // Update best score if the current score is better than the previous best score

                if (empty($bestScore) || (!is_null($scoreRaw) && (int)$scoreRaw > (int)$bestScore)) {
                    $tracking->setScoreRaw($scoreRaw);
                    $tracking->setScoreMin($scoreMin);
                    $tracking->setScoreMax($scoreMax);
                }

                if (empty($bestStatus) || ($lessonStatus !== $bestStatus && $statusPriority[$lessonStatus] > $statusPriority[$bestStatus])) {
                    $tracking->setLessonStatus($lessonStatus);
                    $bestStatus = $lessonStatus;
                }

                if (empty($progression) && ('completed' === $bestStatus || 'passed' === $bestStatus)) {
                    $progression = 100;
                }

                if ($progression > $tracking->getProgression()) {
                    $tracking->setProgression($progression);
                }

                break;

            case Scorm::SCORM_2004:
                $tracking->setDetails($data);

                if (isset($data['cmi.suspend_data']) && !empty($data['cmi.suspend_data'])) {
                    $tracking->setSuspendData($data['cmi.suspend_data']);
                }

                $dataSessionTime = isset($data['cmi.session_time']) ?
                    $this->formatSessionTime($data['cmi.session_time']) :
                    'PT0S';
                $completionStatus = $data['cmi.completion_status'] ?? 'unknown';
                $lessonStatus = $data['cmi.success_status'] ?? 'unknown';
                $scoreRaw = isset($data['cmi.score.raw']) ? intval($data['cmi.score.raw']) : $updateResult->score_raw;
                $scoreMin = isset($data['cmi.score.min']) ? intval($data['cmi.score.min']) : $updateResult->score_min;
                $scoreMax = isset($data['cmi.score.max']) ? intval($data['cmi.score.max']) : $updateResult->score_max;
                $scoreScaled = isset($data['cmi.score.scaled']) ? floatval($data['cmi.score.scaled']) : $updateResult->score_scaled;
                $progression = isset($data['cmi.progress_measure']) ? floatval($data['cmi.progress_measure']) : 0;
                $location = $data['cmi.location'] ?? $updateResult->lesson_location;
                $bestScore = $tracking->getScoreRaw();

                // Computes total time
                $totalTime = new \DateInterval($tracking->getTotalTimeString());

                try {
                    $sessionTime = new \DateInterval($dataSessionTime);
                } catch (\Exception $e) {
                    $sessionTime = new \DateInterval('PT0S');
                }
                $computedTime = new \DateTime();
                $computedTime->setTimestamp(0);
                $computedTime->add($totalTime);
                $computedTime->add($sessionTime);
                $computedTimeInSecond = $computedTime->getTimestamp();
                $totalTimeInterval = $this->retrieveIntervalFromSeconds($computedTimeInSecond);
                $data['cmi.total_time'] = $totalTimeInterval;
                $tracking->setTotalTimeString($totalTimeInterval);

                // Update best score if the current score is better than the previous best score
                if (empty($bestScore) || (!is_null($scoreRaw) && (int)$scoreRaw > (int)$bestScore)) {
                    $tracking->setScoreRaw($scoreRaw);
                    $tracking->setScoreMin($scoreMin);
                    $tracking->setScoreMax($scoreMax);
                    $tracking->setScoreScaled($scoreScaled);
                }

                // Update best success status and completion status
                $bestStatus = $tracking->getLessonStatus();
                if (empty($bestStatus) || ($lessonStatus !== $bestStatus && $statusPriority[$lessonStatus] > $statusPriority[$bestStatus])) {
                    $tracking->setLessonStatus($lessonStatus);
                    $bestStatus = $lessonStatus;
                }

                if (
                    empty($tracking->getCompletionStatus())
                    || ($completionStatus !== $tracking->getCompletionStatus() && $statusPriority[$completionStatus] > $statusPriority[$tracking->getCompletionStatus()])
                ) {
                    // This is no longer needed as completionStatus and successStatus are merged together
                    // I keep it for now for possible retro compatibility
                    $tracking->setCompletionStatus($completionStatus);
                }

                if (empty($progression) && ('completed' === $bestStatus || 'passed' === $bestStatus)) {
                    $progression = 100;
                }

                if ($progression > $tracking->getProgression()) {
                    $tracking->setProgression($progression);
                }

                $tracking->setLessonLocation($location);

                break;
        }

        $updateResult->progression = $tracking->getProgression();
        $updateResult->score_raw = $tracking->getScoreRaw();
        $updateResult->score_min = $tracking->getScoreMin();
        $updateResult->score_max = $tracking->getScoreMax();
        $updateResult->score_scaled = $tracking->getScoreScaled();
        $updateResult->lesson_status = $tracking->getLessonStatus();
        $updateResult->completion_status = $tracking->getCompletionStatus();
        $updateResult->session_time = $tracking->getSessionTime();
        $updateResult->total_time_int = $tracking->getTotalTimeInt();
        $updateResult->total_time_string = $tracking->getTotalTimeString();
        $updateResult->entry = $tracking->getEntry();
        $updateResult->suspend_data = $tracking->getSuspendData();
        $updateResult->exit_mode = $tracking->getExitMode();
        $updateResult->credit = $tracking->getCredit();
        $updateResult->lesson_location = $tracking->getLessonLocation();
        $updateResult->lesson_mode = $tracking->getLessonMode();
        $updateResult->is_locked = $tracking->getIsLocked();
        $updateResult->details = $tracking->getDetails();
        $updateResult->latest_date = $tracking->getLatestDate();

        $updateResult->save();

        return $updateResult;
    }


    private function convertTimeInHundredth($time)
    {
        if ($time != null) {
            $timeInArray = explode(':', $time);
            $timeInArraySec = explode('.', $timeInArray[2]);
            $timeInHundredth = 0;

            if (isset($timeInArraySec[1])) {
                if (1 === strlen($timeInArraySec[1])) {
                    $timeInArraySec[1] .= '0';
                }
                $timeInHundredth = intval($timeInArraySec[1]);
            }
            $timeInHundredth += intval($timeInArraySec[0]) * 100;
            $timeInHundredth += intval($timeInArray[1]) * 6000;
            $timeInHundredth += intval($timeInArray[0]) * 360000;

            return $timeInHundredth;
        } else {
            return 0;
        }
    }

    /**
     * Converts a time in seconds to a DateInterval string.
     *
     * @param int $seconds
     *
     * @return string
     */
    private function retrieveIntervalFromSeconds($seconds): string
    {
        $result = '';
        $remainingTime = (int)$seconds;

        if (empty($remainingTime)) {
            $result .= 'PT0S';
        } else {
            $nbDays = (int)($remainingTime / 86400);
            $remainingTime %= 86400;
            $nbHours = (int)($remainingTime / 3600);
            $remainingTime %= 3600;
            $nbMinutes = (int)($remainingTime / 60);
            $nbSeconds = $remainingTime % 60;
            $result .= 'P' . $nbDays . 'DT' . $nbHours . 'H' . $nbMinutes . 'M' . $nbSeconds . 'S';
        }

        return $result;
    }

    private function formatSessionTime($sessionTime)
    {
        $formattedValue = 'PT0S';
        $generalPattern = '/^P([0-9]+Y)?([0-9]+M)?([0-9]+D)?T([0-9]+H)?([0-9]+M)?([0-9]+S)?$/';
        $decimalPattern = '/^P([0-9]+Y)?([0-9]+M)?([0-9]+D)?T([0-9]+H)?([0-9]+M)?[0-9]+\.[0-9]{1,2}S$/';

        if ('PT' !== $sessionTime) {
            if (preg_match($generalPattern, $sessionTime)) {
                $formattedValue = $sessionTime;
            } elseif (preg_match($decimalPattern, $sessionTime)) {
                $formattedValue = preg_replace(['/\.[0-9]+S$/'], ['S'], $sessionTime);
            }
        }

        return $formattedValue;
    }

    private function getScormFieldStrategy(string $version): ScormFieldStrategy
    {
        $scormVersion = Str::ucfirst(Str::camel($version));
        $strategy = 'EscolaLms\\Scorm\\Strategies\\' . $scormVersion . 'FieldStrategy';

        return new ScormFieldStrategy(new $strategy());
    }
}
