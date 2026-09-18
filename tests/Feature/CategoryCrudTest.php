<?php

namespace Tests\Feature;

use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Category tests require an isolated SQLite in-memory database.');
        }

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Never let a cached local MySQL configuration make these tests destructive.
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Storage::fake('public');
    }

    private function data(array $overrides = []): array
    {
        return array_replace(['name' => 'Swedish walls', 'slug' => 'swedish-walls', 'description' => 'Room to move.', 'status' => '0', 'sort_order' => 1], $overrides);
    }

    private function image(string $name): File
    {
        // A real PNG fixture avoids depending on GD in the XAMPP test runtime.
        return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
    }

    public function test_create_edit_update_and_delete_use_the_database(): void
    {
        $this->get(route('admin.categories.create'))->assertOk()->assertSee('enctype="multipart/form-data"', false);
        $this->post(route('admin.categories.store'), $this->data())->assertRedirect(route('admin.categories.index'));
        $category = Category::sole();
        $this->assertFalse($category->status);
        $this->get(route('admin.categories.edit', $category))->assertOk()->assertSee('swedish-walls');
        $this->put(route('admin.categories.update', $category), $this->data(['name' => 'Updated walls', 'status' => '1']))->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('Updated walls', $category->fresh()->name);
        $this->assertTrue($category->fresh()->status);
        $this->delete(route('admin.categories.destroy', $category))->assertRedirect();
        $this->assertDatabaseCount('categories', 0);
        $this->get(route('admin.categories.edit', $category))->assertNotFound();
        $this->put(route('admin.categories.update', $category), $this->data())->assertNotFound();
        $this->delete(route('admin.categories.destroy', $category))->assertNotFound();
    }

    public function test_validation_rejects_bad_values_and_duplicate_slugs(): void
    {
        Category::create($this->data());
        $this->from(route('admin.categories.create'))->post(route('admin.categories.store'), $this->data())->assertSessionHasErrors('slug');
        $this->post(route('admin.categories.store'), $this->data(['name' => ' ', 'slug' => 'Bad Slug', 'status' => 'draft', 'sort_order' => -1, 'description' => str_repeat('a', 301)]))->assertSessionHasErrors(['name', 'slug', 'status', 'sort_order', 'description']);
        $other = Category::create($this->data(['slug' => 'other']));
        $this->put(route('admin.categories.update', $other), $this->data())->assertSessionHasErrors('slug');
        $this->assertSame('other', $other->fresh()->slug);
        $this->assertDatabaseCount('categories', 2);
    }

    public function test_images_are_uploaded_preserved_replaced_removed_and_deleted(): void
    {
        $this->post(route('admin.categories.store'), $this->data(['image' => $this->image('cover.png')]))->assertSessionHasNoErrors();
        $category = Category::sole();
        $first = $category->image;
        Storage::disk('public')->assertExists($first);
        $this->put(route('admin.categories.update', $category), $this->data())->assertSessionHasNoErrors();
        $this->assertSame($first, $category->fresh()->image);
        $this->put(route('admin.categories.update', $category), $this->data(['image' => $this->image('new.png')]))->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($first);
        $second = $category->fresh()->image;
        Storage::disk('public')->assertExists($second);
        $this->put(route('admin.categories.update', $category), $this->data(['remove_image' => '1']))->assertSessionHasNoErrors();
        $this->assertNull($category->fresh()->image);
        Storage::disk('public')->assertMissing($second);
        $this->put(route('admin.categories.update', $category), $this->data(['image' => $this->image('last.png')]))->assertSessionHasNoErrors();
        $last = $category->fresh()->image;
        $this->delete(route('admin.categories.destroy', $category))->assertRedirect();
        Storage::disk('public')->assertMissing($last);
    }

    public function test_invalid_uploads_leave_database_and_files_unchanged(): void
    {
        foreach ([UploadedFile::fake()->create('script.php', 2, 'text/plain'), $this->image('large.png')->size(1025)] as $file) {
            $this->post(route('admin.categories.store'), $this->data(['image' => $file]))->assertSessionHasErrors('image');
        }
        $this->assertDatabaseCount('categories', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_model_validation_protects_direct_eloquent_writes(): void
    {
        $this->expectException(ValidationException::class);
        Category::create($this->data(['sort_order' => -1]));
    }

    public function test_failed_model_save_cleans_up_new_upload_and_preserves_old_image(): void
    {
        Storage::disk('public')->put('categories/original.png', 'original');
        $category = Category::create($this->data(['image' => 'categories/original.png']));
        Category::saving(function () {
            throw ValidationException::withMessages(['name' => 'Save failed.']);
        });
        try {
            $this->put(route('admin.categories.update', $category), $this->data(['image' => $this->image('replacement.png')]))->assertSessionHasErrors('name');
            $this->assertSame('categories/original.png', $category->fresh()->image);
            $this->assertSame(['categories/original.png'], Storage::disk('public')->allFiles());
        } finally {
            Category::flushEventListeners();
            Category::clearBootedModels();
        }
    }

    public function test_filtering_pagination_and_seeding_preserve_existing_edits(): void
    {
        $this->seed(CategorySeeder::class);
        Category::where('slug', 'mats')->firstOrFail()->update(['name' => 'My mats']);
        $this->seed(CategorySeeder::class);
        $this->assertDatabaseCount('categories', 3);
        $this->assertDatabaseHas('categories', ['slug' => 'mats', 'name' => 'My mats']);
        $this->get(route('admin.categories.index', ['search' => 'mats', 'status' => 'draft']))->assertOk()->assertSee('My mats')->assertDontSee('/swedish-walls');
        for ($i = 0; $i < 12; $i++) {
            Category::create($this->data(['slug' => 'item-'.$i]));
        }
        $this->get(route('admin.categories.index'))->assertOk()->assertViewHas('categories', fn ($categories) => $categories->count() === 12 && $categories->total() === 15);
    }
}
