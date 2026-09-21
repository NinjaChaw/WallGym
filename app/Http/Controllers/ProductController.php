<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $visible = Product::query()->where('status', 'active')
            ->whereHas('category', fn ($query) => $query->where('status', true));
        $product = (clone $visible)->with(['category', 'images'])->where('slug', $slug)->firstOrFail();
        $gallery = $product->images->filter(fn ($image) => Storage::disk('public')->exists($image->image_path))->values();
        $relatedProducts = (clone $visible)->with(['category', 'images'])->whereKeyNot($product->id)
            ->orderByRaw('case when category_id = ? then 0 else 1 end', [$product->category_id])
            ->latest('id')->limit(2)->get();
        $relatedCovers = $relatedProducts->mapWithKeys(fn ($related) => [
            $related->id => $related->images->first(fn ($image) => Storage::disk('public')->exists($image->image_path)),
        ]);

        return view('product', compact('product', 'gallery', 'relatedProducts', 'relatedCovers'));
    }
}
