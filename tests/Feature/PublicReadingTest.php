<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Like;
use App\Models\ReadingHistory;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicReadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_and_explore_show_published_writings_only(): void
    {
        $category = Category::factory()->create(['name' => 'Nature']);
        $published = Writing::factory()->create([
            'category_id' => $category->id,
            'title' => 'Published Sunrise',
            'status' => 'published',
            'published_at' => now(),
            'content' => 'A quiet morning.',
        ]);
        Writing::factory()->create([
            'category_id' => $category->id,
            'title' => 'Private Draft',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Published Sunrise')
            ->assertDontSee('Private Draft');

        $this->get(route('explore', ['q' => 'Sunrise']))
            ->assertOk()
            ->assertSee('Published Sunrise')
            ->assertDontSee('Private Draft');

        $this->get(route('categories.show', $category))
            ->assertOk()
            ->assertSee('Published Sunrise');

        $this->get(route('writings.show', $published))
            ->assertOk()
            ->assertSee('A quiet morning.')
            ->assertSee('Reading mode')
            ->assertSee('Share as image')
            ->assertSee('kavya-theme')
            ->assertSee('integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"', false);
    }

    public function test_writing_back_link_rejects_external_referrers(): void
    {
        $writing = Writing::factory()->create([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->withHeader('referer', 'https://attacker.example/phishing')
            ->get(route('writings.show', $writing))
            ->assertOk()
            ->assertSee('href="'.route('explore').'"', false)
            ->assertDontSee('attacker.example');
    }

    public function test_draft_writings_are_not_publicly_accessible(): void
    {
        $draft = Writing::factory()->create([
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get(route('writings.show', $draft))
            ->assertNotFound();
    }

    public function test_authenticated_reader_can_like_favorite_comment_and_build_history(): void
    {
        $reader = User::factory()->create();
        $writing = Writing::factory()->create([
            'status' => 'published',
            'published_at' => now(),
            'content' => 'A poem worth returning to.',
        ]);

        $this->actingAs($reader)
            ->get(route('writings.show', $writing))
            ->assertOk()
            ->assertSee('Like');

        $this->assertDatabaseHas('reading_histories', [
            'user_id' => $reader->id,
            'writing_id' => $writing->id,
        ]);

        $this->post(route('writings.like', $writing))->assertRedirect();
        $this->post(route('writings.like', $writing))->assertRedirect();
        $this->post(route('writings.favorite', $writing))->assertRedirect();
        $this->post(route('writings.comments.store', $writing), [
            'content' => 'This moved me.',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseCount('likes', 0);
        $this->assertDatabaseCount('favorites', 1);
        $this->assertDatabaseHas('comments', [
            'user_id' => $reader->id,
            'writing_id' => $writing->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('reading_histories', 1);
        $this->assertSame(1, ReadingHistory::query()->count());
    }

    public function test_favorites_and_history_are_private_to_authenticated_reader(): void
    {
        $reader = User::factory()->create();

        $this->get(route('favorites'))->assertRedirect(route('login'));
        $this->get(route('history'))->assertRedirect(route('login'));

        $writing = Writing::factory()->create([
            'status' => 'published',
            'published_at' => now(),
        ]);
        Favorite::query()->create(['user_id' => $reader->id, 'writing_id' => $writing->id]);
        Like::query()->create(['user_id' => $reader->id, 'writing_id' => $writing->id]);
        Comment::query()->create([
            'user_id' => $reader->id,
            'writing_id' => $writing->id,
            'content' => 'Approved thought',
            'status' => 'approved',
        ]);

        $this->actingAs($reader)
            ->get(route('favorites'))
            ->assertOk()
            ->assertSee($writing->title);

        $this->actingAs($reader)
            ->get(route('history'))
            ->assertOk();
    }

    public function test_explore_filters_by_category_language_and_popularity(): void
    {
        $nature = Category::factory()->create(['name' => 'Nature', 'slug' => 'nature']);
        $life = Category::factory()->create(['name' => 'Life', 'slug' => 'life']);
        Writing::factory()->create([
            'category_id' => $nature->id,
            'title' => 'Nepali Nature',
            'language' => 'ne',
            'status' => 'published',
            'published_at' => now(),
            'view_count' => 5,
        ]);
        Writing::factory()->create([
            'category_id' => $nature->id,
            'title' => 'English Nature',
            'language' => 'en',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'view_count' => 50,
        ]);
        Writing::factory(11)->create([
            'category_id' => $nature->id,
            'language' => 'en',
            'status' => 'published',
            'published_at' => now()->subDays(2),
            'view_count' => 0,
        ]);
        Writing::factory()->create([
            'category_id' => $life->id,
            'title' => 'English Life',
            'language' => 'en',
            'status' => 'published',
            'published_at' => now(),
            'view_count' => 100,
        ]);

        $this->get(route('explore', [
            'category' => 'nature',
            'language' => 'ne',
        ]))
            ->assertOk()
            ->assertSee('Nepali Nature')
            ->assertDontSee('English Nature')
            ->assertDontSee('English Life');

        $response = $this->get(route('explore', [
            'category' => 'nature',
            'sort' => 'popular',
        ]));

        $response
            ->assertOk()
            ->assertSeeInOrder(['English Nature', 'Nepali Nature'])
            ->assertSee('category=nature');
    }

    public function test_explore_rejects_unknown_filter_values(): void
    {
        $this->get(route('explore', ['language' => 'fr']))
            ->assertSessionHasErrors('language');

        $this->get(route('explore', ['sort' => 'random']))
            ->assertSessionHasErrors('sort');
    }

    public function test_homepage_popular_section_ranks_by_views_before_publish_date(): void
    {
        $category = Category::factory()->create();
        Writing::factory()->create([
            'category_id' => $category->id,
            'title' => 'Older Popular',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'view_count' => 100,
        ]);
        Writing::factory()->create([
            'category_id' => $category->id,
            'title' => 'Newer Quiet',
            'status' => 'published',
            'published_at' => now(),
            'view_count' => 1,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertViewHas('popularWritings', function ($popularWritings): bool {
                return $popularWritings->first()->title === 'Older Popular';
            });
    }
}
