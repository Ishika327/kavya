<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_category(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Nature',
                'description' => 'Writing inspired by the natural world.',
                'image' => UploadedFile::fake()->create('nature.jpg', 100, 'image/jpeg'),
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::query()->where('name', 'Nature')->firstOrFail();
        $this->assertSame('nature', $category->slug);
        $this->assertNotNull($category->image_path);
        Storage::disk('public')->assertExists($category->image_path);

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'The Outdoors',
                'description' => 'Updated description.',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'The Outdoors',
            'slug' => 'the-outdoors',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_readers_cannot_manage_categories(): void
    {
        $reader = User::factory()->create();

        $this->actingAs($reader)
            ->get(route('admin.categories.index'))
            ->assertForbidden();

        $this->actingAs($reader)
            ->post(route('admin.categories.store'), ['name' => 'Nature'])
            ->assertForbidden();
    }

    public function test_admin_can_replace_or_remove_a_category_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create([
            'image_path' => 'categories/original.jpg',
        ]);
        Storage::disk('public')->put('categories/original.jpg', 'original');

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => $category->name,
                'description' => $category->description,
                'image' => UploadedFile::fake()->create('replacement.png', 100, 'image/png'),
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category->refresh();
        $this->assertNotSame('categories/original.jpg', $category->image_path);
        Storage::disk('public')->assertMissing('categories/original.jpg');
        Storage::disk('public')->assertExists($category->image_path);

        $currentImage = $category->image_path;

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => $category->name,
                'description' => $category->description,
                'remove_image' => '1',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category->refresh();
        $this->assertNull($category->image_path);
        Storage::disk('public')->assertMissing($currentImage);
    }

    public function test_category_with_writings_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        Writing::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error', 'Categories with writings cannot be deleted.');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_category_list_shows_all_categories_without_pagination(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Category::factory()->count(14)->create();

        $this->actingAs($admin)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertDontSee('Previous')
            ->assertDontSee('Next')
            ->assertDontSee('Showing 1 to');
    }
}
