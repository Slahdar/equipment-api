<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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

        // If we're updating a brand, add the ID to the unique rule
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $rules['name'] .= '|unique:brands,name,' . $this->route('brand')->id;
        } else {
            $rules['name'] .= '|unique:brands';
        }

        return $rules;
    }
}
