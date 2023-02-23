<?php

namespace App\Modules\Api\Requests\Auth;

use App\Modules\Api\Requests\Request;

class ChangePasswordRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'nullable|string|max:60|min:6',
            'new_password' => 'nullable|string|max:60|min:6|confirmed',
        ];
    }
}
