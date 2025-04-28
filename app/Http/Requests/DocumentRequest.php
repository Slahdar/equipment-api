<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'version' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id'
        ];

        // File is required only when creating a new document
        if ($this->method() === 'POST') {
            $rules['file'] = 'required|file|mimes:pdf';
        } else {
            $rules['file'] = 'nullable|file|mimes:pdf';
        }

        return $rules;
    }
}