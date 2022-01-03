<?php

namespace EscolaLms\Scorm\Strategies;

use EscolaLms\Scorm\Strategies\Contract\ScormFieldStrategyContract;
use Peopleaps\Scorm\Entity\Scorm;
use Peopleaps\Scorm\Entity\ScoTracking;

class Scorm2004FieldStrategy implements ScormFieldStrategyContract
{
    const FIELDS = [
        'cmi.progress_measure' => 'progression',
        'cmi.score.raw' => 'score_raw',
        'cmi.score.min' => 'score_min',
        'cmi.score.max' => 'score_max',
        'cmi.score.scaled' => 'score_scaled',
        'cmi.success_status' => 'lesson_status',
        'cmi.completion_status' => 'completion_status',
        'cmi.session_time' => 'session_time',
        'cmi.total_time' => 'total_time_string',
        'cmi.entry' => 'entry',
        'cmi.suspend_data' => 'suspend_data',
        'cmi.credit' => 'credit',
        'cmi.exit' => 'exit_mode',
        'cmi.location' => 'lesson_location',
        'cmi.mode' => 'lesson_mode',
    ];

    public function getField(string $key): ?string
    {
        if (!array_key_exists($key, self::FIELDS)) {
            return null;
        }

        return self::FIELDS[$key];
    }

    public function getCmiData(?ScoTracking $track = null): array
    {
        return [
            'learner_id' => $track->getUserId(),
            'progress_measure' => strval($track->getProgression() / 100),
            'score.raw' => strval($track->getScoreRaw()),
            'score.min' => strval($track->getScoreMin()),
            'score.max' => strval($track->getScoreMax()),
            'score.scaled' => strval($track->getScoreScaled()),
            'success_status' => $track->getLessonStatus(),
            'completion_status' => $track->getCompletionStatus(),
            'session_time' => $track->getSessionTime(),
            'total_time' => $track->getTotalTime(Scorm::SCORM_2004),
            'entry' => $track->getEntry(),
            'suspend_data' => $track->getSuspendData(),
            'credit' => $track->getCredit(),
            'exit' => $track->getExitMode(),
            'location' => $track->getLessonLocation(),
            'mode' => $track->getLessonMode(),
        ];
    }
}
