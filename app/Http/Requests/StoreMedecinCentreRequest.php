<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedecinCentreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:medecins_centre,email',
            'telephone' => 'required|string|max:20',
            'numero_ordre' => 'required|string|max:50',
            'date_naissance' => 'required|date',
            'nationalite' => 'required|string|max:100',
            'adresse' => 'required|string',
            'statut' => 'nullable|in:actif,inactif',
            'document_justificatif' => 'required|file|mimes:pdf|max:10240',
        ];
    }
}
