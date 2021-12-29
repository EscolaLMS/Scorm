<?php

namespace EscolaLms\Scorm\Policies;

use EscolaLms\Auth\Models\User;
use EscolaLms\Scorm\Enums\ScormPermissionsEnum;
use Illuminate\Auth\Access\HandlesAuthorization;
use Peopleaps\Scorm\Model\ScormModel;

class ScormPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(ScormPermissionsEnum::SCORM_CREATE);
    }

    /**
     * @param User $user
     * @param ScormModel $scorm
     * @return bool
     */
    public function delete(User $user, ScormModel $scorm): bool
    {
        return $user->can(ScormPermissionsEnum::SCORM_DELETE);
    }

    /**
     * @param User $user
     * @param ScormModel $scorm
     * @return bool
     */
    public function update(User $user, ScormModel $scorm): bool
    {
        return $user->can(ScormPermissionsEnum::SCORM_UPDATE);
    }
}
