<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactPageRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:160'],
            'kicker' => ['required', 'string', 'max:120'],
            'intro' => ['required', 'string', 'max:1000'],
            'hero_image' => ['required', 'integer', Rule::in(config('park.content_images'))],
            'settings' => ['required', 'array'],
            'settings.eyebrow' => ['required', 'string', 'max:120'],
            'settings.heading' => ['required', 'string', 'max:200'],
            'settings.supporting_text' => ['required', 'string', 'max:1000'],
            'settings.reservations_label' => ['required', 'string', 'max:120'],
            'settings.availability_note' => ['required', 'string', 'max:1000'],
            'settings.submission_note' => ['required', 'string', 'max:500'],
        ];
    }
}
