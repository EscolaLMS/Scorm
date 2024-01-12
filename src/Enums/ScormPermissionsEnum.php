<?php

namespace EscolaLms\Scorm\Enums;

use EscolaLms\Core\Enums\BasicEnum;

class ScormPermissionsEnum extends BasicEnum
{
    const SCORM_LIST = 'scorm_list';
    const SCORM_READ = 'scorm_read';
    const SCORM_CREATE = 'scorm_create';
    const SCORM_DELETE = 'scorm_delete';
    const SCORM_UPDATE = 'scorm_update';
    const SCORM_GET_TRACK = 'scorm_track-read';
    const SCORM_SET_TRACK = 'scorm_track-update';
    const SCORM_LIST_OWN = 'scorm_list-own';
    const SCORM_READ_OWN = 'scorm_read-own';
    const SCORM_DELETE_OWN = 'scorm_delete-own';

    public static function adminPermissions(): array
    {
        return [
            ScormPermissionsEnum::SCORM_LIST,
            ScormPermissionsEnum::SCORM_READ,
            ScormPermissionsEnum::SCORM_CREATE,
            ScormPermissionsEnum::SCORM_UPDATE,
            ScormPermissionsEnum::SCORM_DELETE,
            ScormPermissionsEnum::SCORM_SET_TRACK,
            ScormPermissionsEnum::SCORM_GET_TRACK,
        ];
    }

    public static function tutorPermissions(): array
    {
        return [
            ScormPermissionsEnum::SCORM_LIST_OWN,
            ScormPermissionsEnum::SCORM_READ_OWN,
            ScormPermissionsEnum::SCORM_CREATE,
            ScormPermissionsEnum::SCORM_UPDATE,
            ScormPermissionsEnum::SCORM_DELETE_OWN,
            ScormPermissionsEnum::SCORM_SET_TRACK,
            ScormPermissionsEnum::SCORM_GET_TRACK,
        ];
    }
}
