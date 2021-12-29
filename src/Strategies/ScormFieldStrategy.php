<?php

namespace EscolaLms\Scorm\Strategies;

use Carbon\Carbon;
use EscolaLms\Scorm\Strategies\Contract\ScormFieldStrategyContract;
use Peopleaps\Scorm\Entity\ScoTracking;
use Peopleaps\Scorm\Model\ScormScoTrackingModel;

class ScormFieldStrategy
{
    private ScormFieldStrategyContract $scormFieldStrategy;

    public function __construct(
        ScormFieldStrategyContract $scormFieldStrategy
    )
    {
        $this->scormFieldStrategy = $scormFieldStrategy;
    }

    public function getField(string $key): ?string
    {
        return $this->scormFieldStrategy->getField($key);
    }

    public function getCmiData(?ScormScoTrackingModel $track = null): array
    {
        if (!$track) {
            return [];
        }

        $entity = $this->entity($track);
        return $this->scormFieldStrategy->getCmiData($entity);
    }

    private function entity(ScormScoTrackingModel $model): ScoTracking
    {
        $scoTracking = new ScoTracking();
        $scoTracking->setUuid($model->uuid);
        $scoTracking->setProgression($model->progression);
        $scoTracking->setScoreRaw($model->score_raw);
        $scoTracking->setScoreMin($model->score_min);
        $scoTracking->setScoreMax($model->score_max);
        $scoTracking->setScoreScaled($model->score_scaled);
        $scoTracking->setLessonStatus($model->lesson_status);
        $scoTracking->setCompletionStatus($model->completion_status);
        $scoTracking->setSessionTime($model->session_time);
        $scoTracking->setTotalTimeInt($model->total_time_int);
        $scoTracking->setTotalTimeString($model->total_time_string);
        $scoTracking->setEntry($model->entry);
        $scoTracking->setSuspendData($model->suspend_data);
        $scoTracking->setCredit($model->credit);
        $scoTracking->setExitMode($model->exit_mode);
        $scoTracking->setLessonLocation($model->lesson_location);
        $scoTracking->setLessonMode($model->lesson_mode);
        $scoTracking->setIsLocked($model->is_locked);
        $scoTracking->setDetails($model->details);
        $scoTracking->setLatestDate(Carbon::parse($model->latest_date));

        return $scoTracking;
    }
}
