<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTypeLicenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_licence_id' => 'required|integer|exists:type_licences,id',
        ];
    }

    public function messages(): array
    {
        return [
            'type_licence_id.required' => __('trans.type_licence_required'),
            'type_licence_id.integer' => __('trans.type_licence_invalid'),
            'type_licence_id.exists' => __('trans.type_licence_invalid'),
        ];
    }
}
