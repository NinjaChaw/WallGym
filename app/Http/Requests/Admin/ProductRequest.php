<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Matches the existing local admin routes; authentication is a separate module.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect($this->only(['name', 'slug', 'sku', 'description', 'dimensions', 'material']))
            ->map(fn ($value) => is_string($value) ? (trim($value) === '' ? null : trim($value)) : $value)->all());
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $id = $product instanceof Product ? $product->id : null;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:120', 'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', Rule::unique('products')->ignore($id)],
            'sku' => ['nullable', 'string', 'max:60', Rule::unique('products')->ignore($id)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'dimensions' => ['nullable', 'string', 'max:120'],
            'material' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(['draft', 'active'])],
            'price' => ['nullable', 'required_if:status,active', 'required_with:currency', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999'],
            'currency' => ['nullable', 'required_if:status,active', 'required_with:price', Rule::in(['BDT', 'SEK', 'EUR', 'USD'])],
            'stock' => ['nullable', 'required_if:status,active', 'integer', 'min:0', 'max:999999'],
            'images' => ['sometimes', 'array', 'max:10'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'gallery' => ['sometimes', 'array', 'max:10'],
            'gallery.*' => ['array:alt_text,sort_order,remove'],
            'gallery.*.alt_text' => ['nullable', 'string', 'max:255'],
            'gallery.*.sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'gallery.*.remove' => ['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $product = $this->route('product');
            $gallery = $this->input('gallery', []);
            if (! is_array($gallery)) {
                return;
            }
            $ownedIds = $product instanceof Product ? $product->images()->pluck('id')->map(fn ($id) => (string) $id)->all() : [];
            foreach (array_keys($gallery) as $id) {
                if (! in_array((string) $id, $ownedIds, true)) {
                    $validator->errors()->add('gallery', 'An image does not belong to this product. Reload the page and try again.');
                    break;
                }
            }
        }];
    }
}
