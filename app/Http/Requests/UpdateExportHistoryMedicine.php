<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExportHistoryMedicine extends FormRequest
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
            'amount' => ['required', 'integer','max:0'],
            'use_guide' => ['required', 'string'],
            'school_id' => ['required', 'integer'],
            'school_branch_id' => ['required', 'integer'],
        ];
    }
}
