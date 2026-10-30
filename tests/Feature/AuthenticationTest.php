<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Poetry Reader',
            'email' => 'reader@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs(User::query()->where('email', 'reader@example.com')->first());
        $this->assertDatabaseHas('users', [
            'email' => 'reader@example.com',
            'role' => 'reader',
        ]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'reader@example.com',
            'password' => 'password',
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_only_admins_can_access_admin_dashboard(): void
    {
        $reader = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($reader)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin dashboard');
    }

    public function test_admin_dashboard_displays_live_statistics_and_recent_activity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reader = User::factory()->create(['name' => 'Recent Reader']);
        $category = Category::factory()->create(['name' => 'Nature']);
        $writing = Writing::factory()->create([
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'title' => 'A Fresh Beginning',
            'status' => 'published',
            'published_at' => now(),
            'view_count' => 12,
        ]);
        Comment::factory()->create([
            'user_id' => $reader->id,
            'writing_id' => $writing->id,
            'content' => 'A beautiful reflection.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('A Fresh Beginning')
            ->assertSee('Nature')
            ->assertSee('Recent Reader')
            ->assertSee('A beautiful reflection.')
            ->assertSee('12');
    }
}
