<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateHomeAnimationRequest extends FormRequest
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
            'animation_enabled' => ['required', 'boolean'],
            'animation_alt' => ['required', 'string', 'max:160'],
            'animation_file' => ['nullable', 'file', 'mimes:png', 'max:12288'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $animationFile = $this->file('animation_file');

                if (! $animationFile || ! $animationFile->isValid()) {
                    return;
                }

                $dimensions = @getimagesize($animationFile->getRealPath());

                if (! $dimensions || $dimensions[0] % 4 !== 0 || $dimensions[1] % 3 !== 0) {
                    $validator->errors()->add(
                        'animation_file',
                        'The animation must be a 4-column by 3-row PNG sprite atlas.',
                    );
                }
            },
        ];
    }
}
