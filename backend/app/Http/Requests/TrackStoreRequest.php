<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'array', 'min:1'],
            'title.*' => 'required|string|min:1|max:255',
            'artist' => 'required|string|min:1|max:255',
            'release_date' => 'required|date|after_or_equal:today',
            'audio_url' => 'required|array|min:1',
            'audio_url.*' => 'required|file|mimetypes:audio/mpeg,audio/wav,audio/mp3,audio/x-wav|max:51200',
            'cover_url' => 'required|image|mimetypes:image/jpeg,image/png,image/jpg,image/webp|max:5120',
        ];
    }
}
