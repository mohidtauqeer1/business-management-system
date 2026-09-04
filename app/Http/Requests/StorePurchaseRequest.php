<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'supplier_id' => [
            'required',
            'exists:suppliers,id',
        ],

        'purchase_date' => [
            'required',
            'date',
        ],

        'invoice_number' => [
            'required',
            'string',
            'max:100',
        ],

        'paid_amount' => [
            'required',
            'numeric',
            'min:0',
        ],


        'items' => [
            'required',
            'array',
            'min:1',
        ],

        'items.*.product_id' => [
            'required',
            'exists:products,id',
        ],

        'items.*.quantity' => [
            'required',
            'numeric',
            'gt:0',
        ],

        'items.*.unit_price' => [
            'required',
            'numeric',
            'min:0',
        ],
    ];
}
}
