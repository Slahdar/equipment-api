<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
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
        return [
            'product_id' => 'required|exists:products,id',
            'location' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'commissioning_date' => 'required|date',
            'additional_fields' => 'nullable|json'
        ];
    }
}