<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageImage;
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

    public function test_admin_can_upload_a_video_with_a_json_response_without_changing_other_settings(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        Setting::query()->create(['key' => 'hero_title', 'value' => 'Titolo esistente']);

        $this->actingAs($admin)->withHeader('Accept', 'application/json')->put(route('admin.settings.update'), [
            'hero_video' => UploadedFile::fake()->create('desktop.mp4', 100, 'video/mp4'),
        ])->assertOk()->assertExactJson(['saved' => true]);

        $path = Setting::query()->where('key', 'hero_video')->value('value');
        Storage::disk('public')->assertExists($path);
        $this->assertDatabaseHas('settings', ['key' => 'hero_title', 'value' => 'Titolo esistente']);
    }

    public function test_video_upload_json_response_reports_validation_errors(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->actingAs($admin)->withHeader('Accept', 'application/json')->put(route('admin.settings.update'), [
            'hero_video_mobile' => UploadedFile::fake()->create('invalid.txt', 1, 'text/plain'),
        ])->assertUnprocessable()
            ->assertJsonPath('errors.hero_video_mobile.0', 'Il video smartphone deve essere un file MP4 o WebM valido.');
    }

    public function test_video_upload_json_request_requires_authentication(): void
    {
        $this->withHeader('Accept', 'application/json')->put(route('admin.settings.update'))
            ->assertUnauthorized();
    }

    public function test_mobile_hero_video_rejects_invalid_files(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'hero_video_mobile' => UploadedFile::fake()->create('invalid.txt', 1, 'text/plain'),
        ])->assertSessionHasErrors('hero_video_mobile');
    }

    public function test_invalid_hero_video_shows_a_useful_error(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->actingAs($admin)->from(route('admin.settings.edit'))->put(route('admin.settings.update'), [
            'hero_video_mobile' => UploadedFile::fake()->create('invalid.txt', 1, 'text/plain'),
        ])->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHasErrors(['hero_video_mobile' => 'Il video smartphone deve essere un file MP4 o WebM valido.']);
    }

    public function test_failed_hero_video_upload_shows_server_limit_guidance(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);
        $file = UploadedFile::fake()->create('desktop.mp4', 1, 'video/mp4');
        $failedUpload = new UploadedFile($file->getPathname(), 'desktop.mp4', 'video/mp4', UPLOAD_ERR_INI_SIZE, true);

        $this->actingAs($admin)->from(route('admin.settings.edit'))->put(route('admin.settings.update'), [
            'hero_video' => $failedUpload,
        ])->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHasErrors(['hero_video' => 'Il caricamento del video desktop non è riuscito. Verifica le dimensioni del file e i limiti di caricamento del server.']);
    }

    public function test_hero_video_over_application_limit_shows_the_maximum_size(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'hero_video' => UploadedFile::fake()->create('desktop.mp4', 51201, 'video/mp4'),
        ])->assertSessionHasErrors(['hero_video' => 'Il video desktop non può superare 50 MB.']);
    }

    public function test_oversized_settings_request_shows_a_recovery_page(): void
    {
        $this->call('POST', route('admin.settings.update'), server: ['CONTENT_LENGTH' => PHP_INT_MAX])
            ->assertStatus(413)
            ->assertSee('Le impostazioni non sono state salvate.')
            ->assertSee('Torna alle impostazioni')
            ->assertSee(route('admin.settings.edit'));
    }

    public function test_oversized_settings_json_request_keeps_json_response(): void
    {
        $this->call('POST', route('admin.settings.update'), server: [
            'CONTENT_LENGTH' => PHP_INT_MAX,
            'HTTP_ACCEPT' => 'application/json',
        ])->assertStatus(413)->assertHeader('Content-Type', 'application/json');
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

    public function test_admin_can_save_page_and_site_translations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Azienda',
            'slug' => 'azienda',
            'menu_order' => 1,
            'is_published' => '1',
            'translations' => [
                'en' => ['title' => 'Company', 'content' => 'English content'],
                'de' => ['title' => 'Unternehmen'],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertSame('Company', Page::query()->where('slug', 'azienda')->firstOrFail()->translations['en']['title']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'hero_title' => 'Titolo italiano',
            'hero_title_en' => 'English title',
        ])->assertSessionHasNoErrors();

        $this->get('/en')->assertOk()->assertSee('English title');
        $this->get('/de')->assertOk()->assertSee('Titolo italiano');
    }

    public function test_language_prefixes_cannot_be_used_as_page_slugs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Pagina',
            'slug' => 'en/azienda',
            'menu_order' => 1,
        ])->assertSessionHasErrors('slug');
    }

    public function test_admin_can_save_translated_image_description(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $page = Page::factory()->published()->create();
        $image = PageImage::factory()->for($page)->create(['alt_text' => 'Scarpa']);

        $this->actingAs($admin)->patch(route('admin.pages.images.update', [$page, $image]), [
            'alt_text' => 'Scarpa',
            'translations' => ['en' => ['alt_text' => 'Shoe'], 'de' => ['alt_text' => 'Schuh']],
        ])->assertSessionHasNoErrors();

        $this->assertSame('Schuh', $image->fresh()->translations['de']['alt_text']);
    }

    public function test_migration_reports_existing_reserved_slugs(): void
    {
        Page::factory()->create(['slug' => 'en/azienda']);
        $migration = require database_path('migrations/2026_11_20_211354_add_translations_to_content_tables.php');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Gli slug en e de sono riservati alle lingue');

        $migration->up();
    }
}
