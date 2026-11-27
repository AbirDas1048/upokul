<?php

namespace Tests\Feature;

use App\Models\ContentItem;
use App\Models\PermissionRegistry;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\InteractsWithAdmin;
use Tests\TestCase;

class AdminRbacTest extends TestCase
{
    use InteractsWithAdmin;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installSite();
    }

    // ---------------------------------------------------------------- authentication

    public function test_guests_are_sent_to_the_login_page(): void
    {
        foreach (['/admin', '/admin/contacts', '/admin/users', '/admin/roles', '/admin/settings', '/admin/content/services'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }
    }

    public function test_an_admin_can_sign_in_and_out(): void
    {
        $user = $this->master();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'Secret-pass-123'])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_wrong_password_and_deactivated_accounts_cannot_sign_in(): void
    {
        $user = $this->adminWith(['dashboard.view']);

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $user->forceFill(['is_active' => false])->save();
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'Secret-pass-123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_an_admin_deactivated_mid_session_is_signed_out_on_the_next_request(): void
    {
        $user = $this->adminWith(['dashboard.view']);
        $this->actingAs($user)->get('/admin')->assertOk();

        $user->forceFill(['is_active' => false])->save();

        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_admin_pages_are_never_cached(): void
    {
        $this->actingAs($this->master())->get('/admin')->assertHeader('Cache-Control', 'no-store, private');
    }

    // ---------------------------------------------------------------- the master admin

    /** @return array<string, array{0: string}> */
    public static function adminPages(): array
    {
        return array_map(fn (string $url) => [$url], [
            '/admin', '/admin/contacts', '/admin/sections', '/admin/sections/hero', '/admin/sections/services',
            '/admin/content/services', '/admin/content/services/create', '/admin/content/journeys', '/admin/content/gallery',
            '/admin/content/process_steps', '/admin/content/stats', '/admin/content/reviews', '/admin/content/faqs', '/admin/content/about_points',
            '/admin/appearance', '/admin/settings/general', '/admin/settings/contact', '/admin/settings/branding',
            '/admin/settings/seo', '/admin/settings/mail', '/admin/users', '/admin/users/create', '/admin/roles', '/admin/roles/create',
            '/admin/activity', '/admin/profile',
        ]);
    }

    #[DataProvider('adminPages')]
    public function test_the_master_admin_can_open_every_page_without_any_role(string $url): void
    {
        $master = $this->master();
        $this->assertNull($master->role_id);

        $this->actingAs($master)->get($url)->assertOk();
    }

    public function test_the_master_admin_passes_every_permission_check(): void
    {
        $master = $this->master();

        foreach (PermissionRegistry::all() as $key) {
            $this->assertTrue($master->can($key), $key);
        }
    }

    // ---------------------------------------------------------------- role based permissions

    public function test_a_user_without_a_role_can_sign_in_but_can_do_nothing(): void
    {
        $user = new User(['name' => 'No Role', 'email' => 'norole@example.com', 'password' => 'Secret-pass-123']);
        $user->is_active = true;
        $user->save();

        $this->actingAs($user);
        $this->get('/admin')->assertForbidden();
        $this->get('/admin/content/services')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/profile')->assertOk();
    }

    public function test_view_permission_allows_listing_but_not_adding_editing_or_deleting(): void
    {
        $item = ContentItem::ofType('services')->firstOrFail();
        $this->actingAs($this->adminWith(['services.view']));

        $this->get('/admin/content/services')->assertOk();
        $this->get('/admin/content/services/create')->assertForbidden();
        $this->post('/admin/content/services', ['title' => 'X'])->assertForbidden();
        $this->get("/admin/content/services/{$item->id}/edit")->assertForbidden();
        $this->put("/admin/content/services/{$item->id}", ['title' => 'Hacked'])->assertForbidden();
        $this->delete("/admin/content/services/{$item->id}")->assertForbidden();

        $this->assertNotSame('Hacked', $item->fresh()->title);
        $this->assertModelExists($item);
    }

    public function test_each_action_is_its_own_permission(): void
    {
        $item = ContentItem::ofType('faqs')->firstOrFail();
        $before = ContentItem::ofType('faqs')->count();

        // Add only
        $this->actingAs($this->adminWith(['faqs.view', 'faqs.create'], 'adder@example.com', 'Adder'));
        $this->post('/admin/content/faqs', ['title' => 'New question?', 'answer' => 'New answer.', 'sort_order' => 5, 'is_active' => 1])->assertRedirect();
        $this->assertSame($before + 1, ContentItem::ofType('faqs')->count());
        $this->put("/admin/content/faqs/{$item->id}", ['title' => 'x', 'answer' => 'y'])->assertForbidden();
        $this->delete("/admin/content/faqs/{$item->id}")->assertForbidden();

        // Edit only
        $this->actingAs($this->adminWith(['faqs.update'], 'editor@example.com', 'Editor'));
        $this->put("/admin/content/faqs/{$item->id}", ['title' => 'Edited?', 'answer' => 'Edited.', 'sort_order' => 1, 'is_active' => 1])->assertRedirect();
        $this->assertSame('Edited?', $item->fresh()->title);
        $this->post('/admin/content/faqs', ['title' => 'Nope', 'answer' => 'Nope'])->assertForbidden();
        $this->delete("/admin/content/faqs/{$item->id}")->assertForbidden();

        // Delete only
        $this->actingAs($this->adminWith(['faqs.delete'], 'deleter@example.com', 'Deleter'));
        $this->delete("/admin/content/faqs/{$item->id}")->assertRedirect();
        $this->assertModelMissing($item);
    }

    public function test_permissions_are_per_module(): void
    {
        $this->actingAs($this->adminWith(['services.view', 'services.create', 'services.update', 'services.delete']));

        $this->get('/admin/content/services')->assertOk();
        $this->get('/admin/content/reviews')->assertForbidden();
        $this->get('/admin/content/faqs')->assertForbidden();
        $this->get('/admin/settings/general')->assertForbidden();
        $this->get('/admin/contacts')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/roles')->assertForbidden();
        $this->get('/admin/activity')->assertForbidden();
    }

    public function test_the_sidebar_only_shows_what_the_user_may_open(): void
    {
        $this->actingAs($this->adminWith(['dashboard.view', 'services.view']));

        $this->get('/admin')->assertOk()
            ->assertSee(route('admin.content.index', 'services'), false)
            ->assertDontSee(route('admin.content.index', 'reviews'), false)
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('admin.roles.index'), false);
    }

    public function test_an_unknown_content_type_is_a_404(): void
    {
        $this->actingAs($this->master())->get('/admin/content/nonsense')->assertNotFound();
    }

    public function test_content_cannot_be_reached_through_another_types_url(): void
    {
        $review = ContentItem::ofType('reviews')->firstOrFail();

        $this->actingAs($this->master())->get("/admin/content/services/{$review->id}/edit")->assertNotFound();
    }

    public function test_permissions_follow_role_changes_immediately(): void
    {
        $user = $this->adminWith(['faqs.view']);
        $this->actingAs($user)->get('/admin/content/faqs')->assertOk();

        $user->role->syncPermissions(['reviews.view']);
        $this->app['auth']->forgetGuards();

        $this->actingAs(User::find($user->id))->get('/admin/content/faqs')->assertForbidden();
    }

    // ---------------------------------------------------------------- roles

    public function test_saving_a_role_adds_view_for_add_edit_delete_and_drops_unknown_keys(): void
    {
        $role = Role::create(['name' => 'Tmp']);
        $role->syncPermissions(['services.delete', 'reviews.update', 'made.up', 'services.explode']);

        $this->assertEqualsCanonicalizing(['services.delete', 'services.view', 'reviews.update', 'reviews.view'], $role->permissionKeys());
    }

    public function test_an_admin_can_create_a_role_from_the_permission_matrix(): void
    {
        $this->actingAs($this->master())->post('/admin/roles', [
            'name' => 'Reviews desk',
            'description' => 'Handles testimonials',
            'permissions' => ['reviews.create', 'reviews.delete'],
        ])->assertRedirect(route('admin.roles.index'));

        $role = Role::where('name', 'Reviews desk')->firstOrFail();
        $this->assertEqualsCanonicalizing(['reviews.view', 'reviews.create', 'reviews.delete'], $role->permissionKeys());
    }

    public function test_role_names_are_unique(): void
    {
        Role::create(['name' => 'Dup']);

        $this->actingAs($this->master())->post('/admin/roles', ['name' => 'Dup'])->assertSessionHasErrors('name');
    }

    public function test_a_role_in_use_cannot_be_deleted(): void
    {
        $user = $this->adminWith(['dashboard.view'], 'busy@example.com', 'Busy');

        $this->actingAs($this->master())->delete('/admin/roles/'.$user->role_id)->assertSessionHas('error');
        $this->assertModelExists($user->role);
    }

    public function test_an_unused_role_can_be_deleted(): void
    {
        $role = Role::create(['name' => 'Spare']);

        $this->actingAs($this->master())->delete("/admin/roles/{$role->id}")->assertRedirect(route('admin.roles.index'));
        $this->assertModelMissing($role);
    }

    // ---------------------------------------------------------------- privilege escalation

    public function test_a_role_manager_cannot_grant_permissions_they_do_not_have(): void
    {
        $this->actingAs($this->adminWith(['roles.view', 'roles.create', 'services.view']));

        $this->post('/admin/roles', ['name' => 'Sneaky', 'permissions' => ['services.view', 'users.delete', 'settings.update']])->assertForbidden();
        $this->assertFalse(Role::where('name', 'Sneaky')->exists());

        $this->post('/admin/roles', ['name' => 'Fine', 'permissions' => ['services.view']])->assertRedirect();
        $this->assertTrue(Role::where('name', 'Fine')->exists());
    }

    public function test_a_role_manager_cannot_edit_a_role_that_is_more_powerful_than_themselves(): void
    {
        $strong = Role::create(['name' => 'Strong']);
        $strong->syncPermissions(['settings.update', 'users.delete']);

        $this->actingAs($this->adminWith(['roles.view', 'roles.update', 'roles.delete']));

        $this->get("/admin/roles/{$strong->id}/edit")->assertForbidden();
        $this->put("/admin/roles/{$strong->id}", ['name' => 'Strong', 'permissions' => []])->assertForbidden();
        $this->delete("/admin/roles/{$strong->id}")->assertForbidden();

        $this->assertEqualsCanonicalizing(['settings.update', 'settings.view', 'users.delete', 'users.view'], $strong->fresh()->permissionKeys());
    }

    public function test_a_user_manager_cannot_assign_a_role_with_more_permissions_than_their_own(): void
    {
        $strong = Role::create(['name' => 'Strong']);
        $strong->syncPermissions(['settings.update']);

        $this->actingAs($this->adminWith(['users.view', 'users.create']));

        $this->post('/admin/users', [
            'name' => 'Friend', 'email' => 'friend@example.com', 'password' => 'Another-Pass-9', 'password_confirmation' => 'Another-Pass-9',
            'role_id' => $strong->id, 'is_active' => 1,
        ])->assertForbidden();

        $this->assertFalse(User::where('email', 'friend@example.com')->exists());
    }

    public function test_a_user_manager_cannot_touch_the_master_admin_or_a_stronger_user_or_themselves(): void
    {
        $master = $this->master();
        $strong = $this->adminWith(['settings.update', 'users.view'], 'strong@example.com', 'Strong');
        $actor = $this->adminWith(['users.view', 'users.update', 'users.delete'], 'actor@example.com', 'Actor');
        $this->actingAs($actor);

        foreach ([$master, $strong, $actor] as $target) {
            $this->get("/admin/users/{$target->id}/edit")->assertForbidden();
            $this->put("/admin/users/{$target->id}", ['name' => 'Pwned', 'email' => 'p@example.com', 'role_id' => $actor->role_id])->assertForbidden();
            $this->delete("/admin/users/{$target->id}")->assertForbidden();
        }

        $this->assertNotSame('Pwned', $master->fresh()->name);
        $this->assertModelExists($master);
        $this->assertModelExists($strong);
        $this->assertModelExists($actor);
    }

    public function test_nobody_not_even_a_master_can_delete_the_master_admin_or_themselves(): void
    {
        $master = $this->master();
        $second = $this->master(['email' => 'second-master@example.com']);

        $this->actingAs($master)->delete("/admin/users/{$master->id}")->assertForbidden();
        $this->delete("/admin/users/{$second->id}")->assertForbidden();

        $this->assertModelExists($master);
        $this->assertModelExists($second);
    }

    public function test_user_input_can_never_create_a_master_or_set_forbidden_columns(): void
    {
        $role = Role::create(['name' => 'Plain']);
        $role->syncPermissions(['dashboard.view']);

        $this->actingAs($this->master())->post('/admin/users', [
            'name' => 'Trying', 'email' => 'trying@example.com', 'password' => 'Another-Pass-9', 'password_confirmation' => 'Another-Pass-9',
            'role_id' => $role->id, 'is_active' => 1, 'is_master' => 1,
        ])->assertRedirect(route('admin.users.index'));

        $created = User::where('email', 'trying@example.com')->firstOrFail();
        $this->assertFalse($created->is_master);
        $this->assertSame($role->id, $created->role_id);
    }

    public function test_a_master_can_add_edit_and_delete_staff(): void
    {
        $role = Role::create(['name' => 'Plain']);
        $role->syncPermissions(['dashboard.view']);
        $this->actingAs($this->master());

        $this->post('/admin/users', [
            'name' => 'New Staff', 'email' => 'new@example.com', 'password' => 'Another-Pass-9', 'password_confirmation' => 'Another-Pass-9',
            'role_id' => $role->id, 'is_active' => 1,
        ])->assertRedirect(route('admin.users.index'));
        $staff = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertTrue($staff->is_active);

        $this->put("/admin/users/{$staff->id}", ['name' => 'Renamed', 'email' => 'new@example.com', 'role_id' => $role->id, 'is_active' => 0])->assertRedirect();
        $this->assertSame('Renamed', $staff->fresh()->name);
        $this->assertFalse($staff->fresh()->is_active);

        $this->delete("/admin/users/{$staff->id}")->assertRedirect();
        $this->assertModelMissing($staff);
    }

    public function test_weak_or_duplicate_credentials_are_rejected(): void
    {
        $role = Role::create(['name' => 'Plain']);
        $existing = $this->adminWith(['dashboard.view'], 'taken@example.com', 'Other');
        $this->actingAs($this->master());

        $this->post('/admin/users', ['name' => 'A', 'email' => 'a@example.com', 'password' => 'short', 'password_confirmation' => 'short', 'role_id' => $role->id])->assertSessionHasErrors('password');
        $this->post('/admin/users', ['name' => 'A', 'email' => $existing->email, 'password' => 'Another-Pass-9', 'password_confirmation' => 'Another-Pass-9', 'role_id' => $role->id])->assertSessionHasErrors('email');
        $this->post('/admin/users', ['name' => 'A', 'email' => 'a@example.com', 'password' => 'Another-Pass-9', 'password_confirmation' => 'Another-Pass-9', 'role_id' => 9999])->assertSessionHasErrors('role_id');
    }

    // ---------------------------------------------------------------- profile

    public function test_anyone_can_change_their_own_password_but_only_with_the_current_one(): void
    {
        $user = $this->adminWith([]);
        $this->actingAs($user);

        $this->put('/admin/profile', ['name' => $user->name, 'email' => $user->email, 'current_password' => 'wrong', 'password' => 'Brand-New-Pass-7', 'password_confirmation' => 'Brand-New-Pass-7'])
            ->assertSessionHasErrors('current_password');

        $this->put('/admin/profile', ['name' => 'Better Name', 'email' => $user->email, 'current_password' => 'Secret-pass-123', 'password' => 'Brand-New-Pass-7', 'password_confirmation' => 'Brand-New-Pass-7'])
            ->assertSessionHasNoErrors();

        $this->assertSame('Better Name', $user->fresh()->name);
        $this->assertTrue(password_verify('Brand-New-Pass-7', $user->fresh()->password));
    }

    public function test_the_profile_form_cannot_be_used_to_become_master_or_change_role(): void
    {
        $user = $this->adminWith(['dashboard.view']);
        $roleId = $user->role_id;

        $this->actingAs($user)->put('/admin/profile', ['name' => 'Me', 'email' => $user->email, 'is_master' => 1, 'role_id' => 999, 'is_active' => 0])->assertSessionHasNoErrors();

        $fresh = $user->fresh();
        $this->assertFalse($fresh->is_master);
        $this->assertSame($roleId, $fresh->role_id);
        $this->assertTrue($fresh->is_active);
    }

    // ---------------------------------------------------------------- activity log

    public function test_changes_are_recorded_in_the_activity_log(): void
    {
        $this->actingAs($this->master());
        $this->post('/admin/roles', ['name' => 'Audited', 'permissions' => ['faqs.view']]);

        $this->get('/admin/activity')->assertOk()->assertSee('Created role &quot;Audited&quot;', false);
    }

    public function test_only_users_with_the_clear_permission_can_clear_the_log(): void
    {
        $this->actingAs($this->adminWith(['activity.view']))->delete('/admin/activity')->assertForbidden();
        $this->actingAs($this->adminWith(['activity.view', 'activity.delete'], 'clearer@example.com', 'Clearer'))->delete('/admin/activity')->assertRedirect();
    }
}
