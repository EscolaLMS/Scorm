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
}
