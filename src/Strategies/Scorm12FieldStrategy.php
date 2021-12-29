<?php

namespace EscolaLms\Scorm\Strategies;

use Carbon\Carbon;
use EscolaLms\Scorm\Strategies\Contract\ScormFieldStrategyContract;
use Peopleaps\Scorm\Entity\Scorm;
use Peopleaps\Scorm\Entity\ScoTracking;

class Scorm12FieldStrategy implements ScormFieldStrategyContract
{
    private const FIELDS = [
        'cmi.progress_measure' => 'progression',
        'cmi.core.score.raw' => 'score_raw',
        'cmi.core.score.min' => 'score_min',
        'cmi.core.score.max' => 'score_max',
        'cmi.core.lesson_status' => 'lesson_status',
        'cmi.core.session_time' => 'session_time',
        'cmi.core.total_time' => 'total_time_int',
        'cmi.core.entry' => 'entry',
        'cmi.suspend_data' => 'suspend_data',
        'cmi.core.credit' => 'credit',
        'cmi.core.exit' => 'exit_mode',
        'cmi.core.lesson_location' => 'lesson_location',
        'cmi.core.lesson_mode' => 'lesson_mode',
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
            'suspend_data' => $track->getSuspendData(),
            // 'progress_measure' => strval($track->getProgression() / 100),
            'core.student_id' => $track->getUserId(),
            'core.lesson_location' => $track->getLessonLocation(),
            'core.credit' => $track->getCredit(),
            'core.lesson_status' => $track->getLessonStatus(),
            'core.entry' => $track->getEntry(),
            'core.lesson_mode' => $track->getLessonMode(),
            'core.exit' => $track->getExitMode(),
            'core.score.raw' => strval($track->getScoreRaw()),
            'core.score.min' => strval($track->getScoreMin()),
            'core.score.max' => strval($track->getScoreMax()),
            'core.total_time' => $track->getFormattedTotalTimeInt(),
        ];
    }
}
