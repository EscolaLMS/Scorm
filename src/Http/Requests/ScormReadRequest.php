<?php

namespace EscolaLms\Scorm\Http\Requests;

use EscolaLms\Core\Models\User;
use EscolaLms\Scorm\Enums\ScormPermissionsEnum;
use Illuminate\Foundation\Http\FormRequest;

class ScormReadRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->user();
        return $user->can(ScormPermissionsEnum::SCORM_READ, 'api');
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
