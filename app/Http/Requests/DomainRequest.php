<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DomainRequest extends FormRequest
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

        // If we're updating a domain, add the ID to the unique rule
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $rules['name'] .= '|unique:domains,name,' . $this->route('domain')->id;
        } else {
            $rules['name'] .= '|unique:domains';
        }

        return $rules;
    }
}
