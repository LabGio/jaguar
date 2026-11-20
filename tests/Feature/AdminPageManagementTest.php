<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPageManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_upload_and_replace_hero_videos_independently(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'hero_video' => UploadedFile::fake()->create('desktop.mp4', 100, 'video/mp4'),
            'hero_video_mobile' => UploadedFile::fake()->create('mobile.webm', 100, 'video/webm'),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $desktop = Setting::query()->where('key', 'hero_video')->value('value');
        $mobile = Setting::query()->where('key', 'hero_video_mobile')->value('value');
        Storage::disk('public')->assertExists([$desktop, $mobile]);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'hero_video_mobile' => UploadedFile::fake()->create('new-mobile.mp4', 100, 'video/mp4'),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $newMobile = Setting::query()->where('key', 'hero_video_mobile')->value('value');
        $this->assertNotSame($mobile, $newMobile);
        $this->assertSame($desktop, Setting::query()->where('key', 'hero_video')->value('value'));
        Storage::disk('public')->assertMissing($mobile);
        Storage::disk('public')->assertExists([$desktop, $newMobile]);
    }

    public function test_mobile_hero_video_rejects_invalid_files(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'hero_video_mobile' => UploadedFile::fake()->create('invalid.txt', 1, 'text/plain'),
        ])->assertSessionHasErrors('hero_video_mobile');
    }

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
