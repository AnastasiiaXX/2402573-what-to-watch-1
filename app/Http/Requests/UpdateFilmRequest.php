<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFilmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isModerator();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'poster_image' => ['string', 'max:255'],
            'preview_image' => ['string', 'max:255'],
            'background_image' => ['string', 'max:255'],
            'background_color' => ['string', 'max:9'],
            'video_link' => ['string', 'max:255'],
            'preview_video_link' => ['string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'director' => ['string', 'max:255'],
            'starring' => ['array'],
            'genre' => ['array'],
            'released' => ['integer'],
            'run_time' => ['integer'],
            'imdb_id' => ['string', Rule::unique('films')->ignore($this->route('film')->id), 'regex:/^tt\d+$/', 'required'],
            'status' => ['required','string', Rule::in(['ready', 'pending', 'on moderation'])]
        ];
    }
}
