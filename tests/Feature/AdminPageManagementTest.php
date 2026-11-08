<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminPageManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_from_administration(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_can_create_a_published_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Primavera Estate',
            'slug' => 'collezioni/primavera-estate',
            'intro' => 'Nuova collezione.',
            'content' => 'Contenuto editoriale.',
            'menu_order' => 20,
            'is_published' => '1',
        ]);

        $page = Page::query()->where('slug', 'collezioni/primavera-estate')->firstOrFail();

        $response->assertRedirect(route('admin.pages.edit', $page));
        $this->assertDatabaseHas('pages', ['title' => 'Primavera Estate', 'is_published' => true]);
    }

    public function test_page_slug_must_be_unique(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Page::factory()->create(['slug' => 'azienda']);

        $response = $this->actingAs($admin)->from(route('admin.pages.create'))->post(route('admin.pages.store'), [
            'title' => 'Altra pagina',
            'slug' => 'azienda',
            'menu_order' => 0,
        ]);

        $response->assertRedirect(route('admin.pages.create'));
        $response->assertSessionHasErrors('slug');
    }

    public function test_admin_can_create_a_submenu_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $parentPage = Page::factory()->create(['title' => 'Collezioni', 'slug' => 'collezioni']);

        $response = $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Primavera Estate',
            'parent_id' => $parentPage->id,
            'slug' => 'collezioni/primavera-estate',
            'menu_order' => 10,
            'is_published' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'title' => 'Primavera Estate',
            'parent_id' => $parentPage->id,
        ]);
    }

    public function test_submenu_page_cannot_be_used_as_a_parent(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $parentPage = Page::factory()->create();
        $submenuPage = Page::factory()->for($parentPage, 'parent')->create();

        $response = $this->actingAs($admin)->from(route('admin.pages.create'))->post(route('admin.pages.store'), [
            'title' => 'Pagina annidata',
            'parent_id' => $submenuPage->id,
            'slug' => 'pagina-annidata',
            'menu_order' => 0,
        ]);

        $response->assertRedirect(route('admin.pages.create'));
        $response->assertSessionHasErrors('parent_id');
    }
}
