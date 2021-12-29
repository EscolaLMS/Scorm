<?php

namespace EscolaLms\Scorm\Http\Requests;

use App\Models\User;
use EscolaLms\Scorm\Enums\ScormPermissionsEnum;
use Illuminate\Foundation\Http\FormRequest;

class GetScormTrackRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->user();
        return $user->can(ScormPermissionsEnum::SCORM_GET_TRACK, 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
