<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdatePageRequest extends StorePageRequest
{
    public function rules(): array
    {
        $rules = $this->pageRules();
        $rules['slug'][4] = Rule::unique('pages', 'slug')->ignore($this->route('page'));

        return $rules;
    }
}
