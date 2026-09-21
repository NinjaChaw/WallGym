<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,active,draft'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort' => ['nullable', 'in:name,recent'],
        ]);
        $query = Product::with(['category', 'images']);
        if ($search = trim($filters['search'] ?? '')) {
            $query->where(fn ($query) => $query->where('name', 'like', '%'.$search.'%')->orWhere('slug', 'like', '%'.$search.'%')->orWhere('sku', 'like', '%'.$search.'%'));
        }
        if (in_array($filters['status'] ?? '', ['draft', 'active'], true)) {
            $query->where('status', $filters['status']);
        }
        if ($filters['category_id'] ?? null) {
            $query->where('category_id', $filters['category_id']);
        }
        $products = (($filters['sort'] ?? 'name') === 'recent' ? $query->latest('id') : $query->orderBy('name')->orderBy('id'))->paginate(12)->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', ['product' => new Product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = $this->persist($request, new Product);

        return to_route('admin.products.show', $product)->with('success', 'Product created.');
    }

    public function show(Product $product): View
    {
        return view('admin.products.show', ['product' => $product->load(['category', 'images'])]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', ['product' => $product->load('images'), 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $this->persist($request, $product);

        return to_route('admin.products.show', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $paths = DB::transaction(function () use ($product) {
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $paths = $locked->images()->pluck('image_path')->all();
            $locked->delete();

            return $paths;
        });
        $this->deleteImages($paths);

        return to_route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function persist(ProductRequest $request, Product $product): Product
    {
        $newPaths = [];
        $oldPaths = [];
        $data = $request->safe()->except(['images', 'gallery']);
        $gallery = $request->validated('gallery', []);
        try {
            $product = DB::transaction(function () use ($request, $product, $data, $gallery, &$newPaths, &$oldPaths) {
                $product = $product->exists ? Product::whereKey($product->id)->lockForUpdate()->firstOrFail() : $product;
                $images = $product->exists ? $product->images()->get() : collect();
                foreach (array_keys($gallery) as $id) {
                    if (! $images->contains('id', $id)) {
                        throw ValidationException::withMessages(['gallery' => 'An image no longer belongs to this product. Reload and try again.']);
                    }
                }
                $retained = $images->reject(fn ($image) => (bool) ($gallery[$image->id]['remove'] ?? false));
                if ($retained->count() + count($request->file('images', [])) > 10) {
                    throw ValidationException::withMessages(['images' => 'Keep a maximum of 10 images per product. Remove an existing image before adding more.']);
                }
                $product->fill($data)->save();
                foreach ($images->diff($retained) as $image) {
                    $oldPaths[] = $image->image_path;
                    $image->delete();
                }
                $retained = $retained->sortBy(fn ($image) => (int) ($gallery[$image->id]['sort_order'] ?? $image->sort_order))->values();
                foreach ($retained as $order => $image) {
                    $image->update([
                        'sort_order' => $order,
                        'alt_text' => array_key_exists($image->id, $gallery) ? ($gallery[$image->id]['alt_text'] ?? null) : $image->alt_text,
                    ]);
                }
                foreach ($request->file('images', []) as $index => $file) {
                    $path = $file->store('products', 'public');
                    if (! $path) {
                        throw ValidationException::withMessages(['images' => 'An image could not be stored. Please try again.']);
                    }
                    $newPaths[] = $path;
                    $product->images()->create(['image_path' => $path, 'alt_text' => $product->name, 'sort_order' => $retained->count() + $index]);
                }

                return $product;
            });
        } catch (Throwable $exception) {
            $this->deleteImages($newPaths);
            if ($exception instanceof UniqueConstraintViolationException) {
                $field = str_contains(strtolower($exception->getMessage()), 'sku') ? 'sku' : 'slug';
                throw ValidationException::withMessages([$field => 'This '.$field.' is already in use.']);
            }
            throw $exception;
        }
        $this->deleteImages($oldPaths);

        return $product;
    }

    private function deleteImages(array $paths): void
    {
        foreach (array_unique($paths) as $path) {
            if (! preg_match('~\Aproducts/[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)\z~', $path)) {
                continue;
            }
            try {
                if (! ProductImage::where('image_path', $path)->exists() && ! Storage::disk('public')->delete($path)) {
                    report(new \RuntimeException('Unable to remove product image: '.$path));
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}
