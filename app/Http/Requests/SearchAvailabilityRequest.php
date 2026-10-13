<?php

namespace App\Http\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SearchAvailabilityRequest extends FormRequest
{
    protected function getRedirectUrl(): string
    {
        return $this->routeIs('booking.search') ? route('booking.create') : parent::getRedirectUrl();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property' => ['required', Rule::in(['makara'])],
            'arrival' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.now('Asia/Colombo')->toDateString()],
            'departure' => ['required', 'date_format:Y-m-d', 'after:arrival'],
            'nationality' => ['required', Rule::in(['Sri Lankan', 'International'])],
            'adults' => ['required', 'integer', 'min:1', 'max:50'],
            'children' => ['required', 'integer', 'min:0', 'max:20'],
            'promo_code' => ['nullable', 'string', 'max:50', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value) {
                    $fail('Online promo codes are not available yet. Remove the code to continue, or contact reservations.');
                }
            }],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if (! $validator->errors()->has('arrival') && ! $validator->errors()->has('departure')) {
                $nights = CarbonImmutable::parse($this->input('arrival'))->diffInDays(CarbonImmutable::parse($this->input('departure')));
                if ($nights > 30) {
                    $validator->errors()->add('departure', 'For stays longer than 30 nights, please contact reservations.');
                }
            }
        }];
    }
}
