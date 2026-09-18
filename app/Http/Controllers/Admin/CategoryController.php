<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,active,draft'],
            'sort' => ['nullable', 'in:position,name'],
        ]);

        $query = Category::query();

        if ($search = trim($filters['search'] ?? '')) {
            $query->where(fn ($query) => $query->where('name', 'like', '%'.$search.'%')->orWhere('slug', 'like', '%'.$search.'%'));
        }

        if (in_array($filters['status'] ?? 'all', ['active', 'draft'], true)) {
            $query->where('status', $filters['status'] === 'active');
        }

        $categories = $query->orderBy(($filters['sort'] ?? 'position') === 'name' ? 'name' : 'sort_order')->orderBy('id')->paginate(12)->withQueryString();
        $total = Category::count();
        $active = Category::where('status', true)->count();

        return view('admin.categories.index', compact('categories', 'total', 'active'));
    }

    public function create(): View
    {
        return view('admin.categories.create', ['category' => new Category(['sort_order' => min(999, (int) Category::max('sort_order') + 1)])]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        
        $this->persist($request, new Category);

        return to_route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->persist($request, $category);

        return to_route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $image = $category->image;
        $category->delete();
        $this->deleteImage($image);

        return to_route('admin.categories.index')->with('success', 'Category deleted.');
    }

    private function persist(CategoryRequest $request, Category $category): void
    {
        $data = $request->safe()->except(['image', 'remove_image']);
        $oldImage = $category->image;
        $newImage = null;
        try {
            if ($request->hasFile('image')) {
                $newImage = $request->file('image')->store('categories', 'public');
                if (! $newImage) {
                    throw ValidationException::withMessages(['image' => 'The image could not be stored. Please try again.']);
                }
                $data['image'] = $newImage;
            } elseif ($request->boolean('remove_image')) {
                $data['image'] = null;
            }
            $category->fill($data)->save();
        } catch (Throwable $exception) {
            $this->deleteImage($newImage);
            if ($exception instanceof UniqueConstraintViolationException) {
                throw ValidationException::withMessages(['slug' => 'This slug is already in use.']);
            }
            throw $exception;
        }
        if ($oldImage !== $category->image) {
            $this->deleteImage($oldImage);
        }
    }

    private function deleteImage(?string $path): void
    {
        if ($path && preg_match('~\Acategories/[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)\z~', $path)) {
            try {
                if (! Storage::disk('public')->delete($path)) {
                    report(new \RuntimeException('Unable to remove category image: '.$path));
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}
