<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTypeDemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_demande_id' => 'required|integer|exists:type_demandes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'type_demande_id.required' => __('trans.type_required'),
            'type_demande_id.integer' => __('trans.type_invalid'),
            'type_demande_id.exists' => __('trans.type_invalid'),
        ];
    }
}
