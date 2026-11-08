<?php

namespace App\Http\Requests;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return $this->pageRules();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function pageRules(): array
    {
        $parentPage = $this->route('page');
        $parentIdRules = [
            'nullable',
            'integer',
            Rule::exists('pages', 'id')->where('parent_id', null),
        ];

        if ($parentPage instanceof Page) {
            $parentIdRules[] = Rule::notIn([$parentPage->id]);
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'parent_id' => $parentIdRules,
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:[a-z0-9\/-]*[a-z0-9])?$/', 'unique:pages,slug'],
            'intro' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string', 'max:50000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:8192'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'is_published' => ['nullable', 'boolean'],
            'menu_order' => ['required', 'integer', 'min:0', 'max:999999'],
        ];
    }
}
