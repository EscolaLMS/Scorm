<?php

namespace EscolaLms\Scorm\Policies;

use EscolaLms\Auth\Models\User;
use EscolaLms\Scorm\Enums\ScormPermissionsEnum;
use Illuminate\Auth\Access\HandlesAuthorization;
use Peopleaps\Scorm\Model\ScormModel;

class ScormPolicy
{
    use HandlesAuthorization;

    public function list(User $user): bool
    {
        return $user->canAny([
            ScormPermissionsEnum::SCORM_LIST,
            ScormPermissionsEnum::SCORM_LIST_OWN,
        ]);
    }

    public function read(User $user, ScormModel $scorm): bool
    {
        return $user->can(ScormPermissionsEnum::SCORM_READ)
            || ($user->can(ScormPermissionsEnum::SCORM_READ_OWN) && $scorm->user_id === $user->getKey());
    }

    public function create(User $user): bool
    {
        return $user->can(ScormPermissionsEnum::SCORM_CREATE);
    }

    public function delete(User $user, ScormModel $scorm): bool
    {
        return $user->can(ScormPermissionsEnum::SCORM_DELETE)
            || ($user->can(ScormPermissionsEnum::SCORM_DELETE_OWN) && $scorm->user_id === $user->getKey());
    }

    public function update(User $user, ScormModel $scorm): bool
    {
        return $user->can(ScormPermissionsEnum::SCORM_UPDATE);
    }
}
