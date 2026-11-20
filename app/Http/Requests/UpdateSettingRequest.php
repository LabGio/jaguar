<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'company_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'vat_number' => ['nullable', 'string', 'max:50'],
            'map_embed_url' => ['nullable', 'url', 'max:2048'],
            'facebook_url' => ['nullable', 'url', 'max:2048'],
            'instagram_url' => ['nullable', 'url', 'max:2048'],
            'linkedin_url' => ['nullable', 'url', 'max:2048'],
            'footer_text' => ['nullable', 'string', 'max:1000'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'homepage_intro_title' => ['nullable', 'string', 'max:255'],
            'homepage_intro_text' => ['nullable', 'string', 'max:3000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:4096'],
            'hero_fallback' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:8192'],
            'hero_video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm', 'extensions:mp4,webm', 'max:51200'],
            'hero_video_mobile' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm', 'extensions:mp4,webm', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'hero_video.uploaded' => 'Il caricamento del video desktop non è riuscito. Verifica le dimensioni del file e i limiti di caricamento del server.',
            'hero_video.mimetypes' => 'Il video desktop deve essere un file MP4 o WebM valido.',
            'hero_video.extensions' => 'Il video desktop deve avere estensione .mp4 o .webm.',
            'hero_video.max' => 'Il video desktop non può superare 50 MB.',
            'hero_video_mobile.uploaded' => 'Il caricamento del video smartphone non è riuscito. Verifica le dimensioni del file e i limiti di caricamento del server.',
            'hero_video_mobile.mimetypes' => 'Il video smartphone deve essere un file MP4 o WebM valido.',
            'hero_video_mobile.extensions' => 'Il video smartphone deve avere estensione .mp4 o .webm.',
            'hero_video_mobile.max' => 'Il video smartphone non può superare 50 MB.',
        ];
    }
}
