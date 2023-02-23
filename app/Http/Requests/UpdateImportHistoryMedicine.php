<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImportHistoryMedicine extends FormRequest
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
            'id' => ['required', 'integer'],
            'amount' => ['required', 'integer','min:0'],
            'price' => ['required', 'integer','min:0'],
            'expired_at' => ['required', 'string'],
            'school_id' => ['required', 'integer'],
            'school_branch_id' => ['required', 'integer'],
        ];
    }
}
