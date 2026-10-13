<?php

namespace App\Http\Requests;

class StoreBookingRequest extends SearchAvailabilityRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'rooms' => ['required', 'array', 'min:1', 'max:30'],
            'rooms.*' => ['required', 'integer', 'min:0', 'max:50'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['required', 'string', 'max:30'],
            'requests' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
            'booking_token' => ['required', 'uuid'],
            'website' => ['nullable', 'max:0'],
        ]);
    }
}
