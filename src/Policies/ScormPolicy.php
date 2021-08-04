<?php

namespace EscolaLms\Scorm\Policies;

use EscolaLms\Auth\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Peopleaps\Scorm\Model\ScormModel;

class ScormPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('create Scorm');
    }

    /**
     * @param User $user
     * @param ScormModel $scorm
     * @return bool
     */
    public function delete(User $user, ScormModel $scorm)
    {
        return $user->can('delete Scorm');
    }

    /**
     * @param User $user
     * @param ScormModel $scorm
     * @return bool
     */
    public function update(User $user, ScormModel $scorm)
    {
        return $user->can('update Scorm');
    }
}
