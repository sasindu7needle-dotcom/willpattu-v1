<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'inventory' => ['required', 'integer', 'min:0', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'local_rate' => ['required', 'numeric', 'min:1', 'max:1000000', 'decimal:0,2'],
            'international_rate' => ['required', 'numeric', 'min:1', 'max:1000000', 'decimal:0,2'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
