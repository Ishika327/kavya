<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreWritingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $slug = Str::slug((string) $this->input('title'));

        $this->merge([
            'slug' => $slug !== '' ? $slug : 'writing-'.Str::lower(Str::random(12)),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('writings', 'slug')],
            'content' => ['required', 'string', 'max:100000'],
            'language' => ['required', 'string', 'in:en,ne'],
            'status' => ['required', 'string', 'in:draft,published'],
            'is_featured' => ['boolean'],
            'is_poem_of_day' => ['boolean'],
            'is_poem_of_week' => ['boolean'],
            'is_anonymous' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
