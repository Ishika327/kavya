<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WritingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_publish_and_delete_writing(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.writings.store'), [
                'category_id' => $category->id,
                'title' => 'मेरो कविता',
                'content' => "पहिलो पंक्ति\nदोस्रो पंक्ति",
                'language' => 'ne',
                'status' => 'draft',
                'cover_image' => UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg'),
            ])
            ->assertRedirect(route('admin.writings.index'));

        $writing = Writing::query()->where('title', 'मेरो कविता')->firstOrFail();
        $this->assertNotSame('', $writing->slug);
        $this->assertNull($writing->published_at);
        Storage::disk('public')->assertExists($writing->cover_image_path);

        $this->actingAs($admin)
            ->put(route('admin.writings.update', $writing), [
                'category_id' => $category->id,
                'title' => 'Published Kavya',
                'content' => "Line one\nLine two",
                'language' => 'en',
                'status' => 'published',
                'is_featured' => '1',
                'is_anonymous' => '1',
            ])
            ->assertRedirect(route('admin.writings.index'));

        $writing->refresh();
        $this->assertSame('published-kavya', $writing->slug);
        $this->assertNotNull($writing->published_at);
        $this->assertTrue($writing->is_featured);
        $this->assertTrue($writing->is_anonymous);

        $this->actingAs($admin)
            ->delete(route('admin.writings.destroy', $writing))
            ->assertRedirect(route('admin.writings.index'));

        $this->assertDatabaseMissing('writings', ['id' => $writing->id]);
    }

    public function test_readers_cannot_manage_writings(): void
    {
        $reader = User::factory()->create();

        $this->actingAs($reader)
            ->get(route('admin.writings.index'))
            ->assertForbidden();

        $this->actingAs($reader)
            ->post(route('admin.writings.store'), [])
            ->assertForbidden();
    }

    public function test_writing_requires_an_existing_category_and_valid_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.writings.store'), [
                'category_id' => 999,
                'title' => 'Invalid',
                'content' => 'Content',
                'language' => 'en',
                'status' => 'archived',
            ])
            ->assertSessionHasErrors(['category_id', 'status']);
    }

    public function test_poem_of_day_and_week_selections_are_exclusive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        $first = Writing::factory()->create(['category_id' => $category->id]);
        $second = Writing::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)->put(route('admin.writings.update', $first), [
            'category_id' => $category->id,
            'title' => $first->title,
            'content' => $first->content,
            'language' => 'en',
            'status' => 'published',
            'is_poem_of_day' => '1',
            'is_poem_of_week' => '1',
        ])->assertRedirect();

        $this->actingAs($admin)->put(route('admin.writings.update', $second), [
            'category_id' => $category->id,
            'title' => $second->title,
            'content' => $second->content,
            'language' => 'en',
            'status' => 'published',
            'is_poem_of_day' => '1',
            'is_poem_of_week' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('writings', [
            'id' => $first->id,
            'is_poem_of_day' => false,
            'is_poem_of_week' => false,
        ]);
        $this->assertDatabaseHas('writings', [
            'id' => $second->id,
            'is_poem_of_day' => true,
            'is_poem_of_week' => true,
        ]);
    }
}
