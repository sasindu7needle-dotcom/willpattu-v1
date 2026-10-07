<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:30'],
            'category' => ['required', Rule::in(['Reservations', 'Safari Enquiries', 'Groups', 'Travel Agents', 'General Enquiries', 'Private Transfer'])],
            'arrival' => ['nullable', 'date', 'after_or_equal:today'],
            'departure' => ['nullable', 'date', 'after:arrival', 'required_with:arrival'],
            'travellers' => ['required', 'integer', 'between:1,50'],
            'interest' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ];
    }
}
