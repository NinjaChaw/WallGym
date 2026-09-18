<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Admin authentication is not implemented in this local workspace yet.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect($this->only(['name', 'slug', 'description']))
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)->all());
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return Category::validationRules($category instanceof Category ? $category : null) + [
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }
}
