<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Category extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'status' => false, 
        'sort_order' => 1
    ];

    public static function validationRules(?self $category = null): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],

            'slug' => [
                'required',
                'string',
                'max:100',
                'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/',
                Rule::unique('categories', 'slug')
                    ->ignore($category?->getKey()),
            ],

            'description' => ['nullable', 'string', 'max:300'],
            'status' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? asset('storage/'.$this->image)
            : null;
    }
}
