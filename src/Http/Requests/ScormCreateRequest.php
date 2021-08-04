<?php

namespace EscolaLms\Scorm\Http\Requests;

use EscolaLms\Core\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ScormCreateRequest extends FormRequest
{


    /**
     * @return bool
     */
    public function authorize()
    {
        /** @var User $user */
        $user = $this->user();
        return $user->can('create Scorm', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'zip' => 'file|required|mimes:zip',
        ];
    }
}
