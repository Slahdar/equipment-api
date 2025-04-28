<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentTypeRequest extends FormRequest
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
            'name' => 'required|string|max:255'
        ];

        // If we're updating a document type, add the ID to the unique rule
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $rules['name'] .= '|unique:document_types,name,' . $this->route('documentType')->id;
        } else {
            $rules['name'] .= '|unique:document_types';
        }

        return $rules;
    }
}