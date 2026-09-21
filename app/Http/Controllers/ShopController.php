<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:recent,name-asc,name-desc'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $query = Product::with(['category', 'images'])->where('status', 'active')
            ->whereHas('category', fn ($query) => $query->where('status', true));
        if ($category = $filters['category'] ?? null) {
            $query->whereHas('category', fn ($query) => $query->where('slug', $category));
        }
        if ($search = trim($filters['search'] ?? '')) {
            $query->where(fn ($query) => $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhereHas('category', fn ($query) => $query->where('name', 'like', '%'.$search.'%')));
        }
        $sort = $filters['sort'] ?? 'recent';
        if ($sort === 'recent') {
            $query->orderByDesc('id');
        } else {
            $query->orderBy('name', $sort === 'name-desc' ? 'desc' : 'asc')->orderBy('id');
        }
        $products = $query->paginate(12)->withQueryString();
        // Resolve missing files once per page, using the existing image URL accessor.
        $covers = $products->getCollection()->mapWithKeys(function ($product) {
            $image = $product->images->first(fn ($image) => Storage::disk('public')->exists($image->image_path));

            return [$product->id => $image];
        });
        $categories = Category::where('status', true)
            ->whereHas('products', fn ($query) => $query->where('status', 'active'))
            ->orderBy('sort_order')->orderBy('name')->get();

        return view('shop', compact('products', 'categories', 'covers'));
    }
}
