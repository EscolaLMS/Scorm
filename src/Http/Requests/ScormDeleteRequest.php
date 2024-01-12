<?php

namespace EscolaLms\Scorm\Http\Requests;

use EscolaLms\Core\Models\User;
use EscolaLms\Scorm\Enums\ScormPermissionsEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ScormDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('delete', $this->route('scormModel'));
    }

    public function rules(): array
    {
        return [];
    }
}
