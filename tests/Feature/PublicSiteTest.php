<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\Page;
use App\Models\PageImage;
use App\Models\Setting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_homepage_is_accessible(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('L’eccellenza prende forma.');
    }

    public function test_published_page_is_accessible_but_draft_is_not(): void
    {
        $published = Page::factory()->published()->create(['title' => 'Collezione', 'slug' => 'collezione']);
        $draft = Page::factory()->create(['slug' => 'bozza']);

        $this->get(route('pages.show', ['page' => $published->slug]))->assertOk()->assertSee('Collezione');
        $this->get(route('pages.show', ['page' => $draft->slug]))->assertNotFound();
    }

    public function test_published_submenu_pages_are_rendered_in_a_dropdown(): void
    {
        $parentPage = Page::factory()->published()->create(['title' => 'Collezioni', 'slug' => 'collezioni']);
        Page::factory()->published()->for($parentPage, 'parent')->create([
            'title' => 'Primavera Estate',
            'slug' => 'collezioni/primavera-estate',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Collezioni');
        $response->assertSee('Primavera Estate');
        $response->assertSee('<details', false);
    }

    public function test_homepage_renders_configured_media_and_published_collection(): void
    {
        Setting::query()->create(['key' => 'hero_title', 'value' => 'Titolo personalizzato']);
        Setting::query()->create(['key' => 'hero_video', 'value' => 'settings/video.mp4']);
        Setting::query()->create(['key' => 'hero_fallback', 'value' => 'settings/cover.webp']);
        $collection = Page::factory()->published()->create([
            'title' => 'Collezione speciale',
            'slug' => 'collezioni/speciale',
            'cover_image_path' => 'pages/cover.webp',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Titolo personalizzato')
            ->assertSee('settings/video.mp4')
            ->assertSee('settings/cover.webp')
            ->assertSee('pages/cover.webp')
            ->assertSee(route('pages.show', ['page' => $collection->slug]), false);
    }

    public function test_company_page_cover_is_not_listed_as_a_collection(): void
    {
        Page::factory()->published()->create([
            'slug' => 'azienda',
            'cover_image_path' => 'pages/company-cover.webp',
        ]);
        Page::factory()->published()->create([
            'slug' => 'collezioni/speciale',
            'cover_image_path' => 'pages/collection-cover.webp',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('pages/collection-cover.webp')
            ->assertDontSee('pages/company-cover.webp');
    }

    public function test_homepage_exposes_separate_responsive_video_sources(): void
    {
        config(['filesystems.disks.public.url' => '/storage']);
        Setting::query()->create(['key' => 'hero_video', 'value' => 'settings/desktop.mp4']);
        Setting::query()->create(['key' => 'hero_video_mobile', 'value' => 'settings/mobile.mp4']);

        $this->get(route('home'))->assertOk()
            ->assertSee('data-desktop-src="/storage/settings/desktop.mp4"', false)
            ->assertSee('data-mobile-src="/storage/settings/mobile.mp4"', false)
            ->assertSee('<noscript><video', false)
            ->assertSee('src="/storage/settings/desktop.mp4"', false);
    }

    public function test_homepage_uses_the_only_video_as_fallback_for_both_viewports(): void
    {
        config(['filesystems.disks.public.url' => '/storage']);
        Setting::query()->create(['key' => 'hero_video', 'value' => '']);
        Setting::query()->create(['key' => 'hero_video_mobile', 'value' => 'settings/mobile.mp4']);

        $this->get(route('home'))->assertOk()
            ->assertSee('data-desktop-src="/storage/settings/mobile.mp4"', false)
            ->assertSee('data-mobile-src="/storage/settings/mobile.mp4"', false);
    }

    public function test_page_renders_managed_gallery_and_navigation(): void
    {
        $page = Page::factory()->published()->create(['title' => 'Collezione 01', 'slug' => 'collezioni/01']);
        PageImage::factory()->for($page)->create([
            'path' => 'pages/galleries/scarpa.webp',
            'thumbnail_path' => 'pages/galleries/thumbnails/scarpa.webp',
            'alt_text' => 'Scarpa artigianale',
        ]);
        $otherPage = Page::factory()->published()->create(['title' => 'Collezione 02', 'slug' => 'collezioni/02']);

        $response = $this->get(route('pages.show', ['page' => $page->slug]));

        $response->assertOk()
            ->assertSee('Scarpa artigianale')
            ->assertSee('pages/galleries/scarpa.webp')
            ->assertSee('data-lightbox-image', false)
            ->assertSee(route('pages.show', ['page' => $otherPage->slug]), false);
    }

    public function test_public_media_urls_use_the_public_disk_when_default_disk_is_local(): void
    {
        config(['filesystems.default' => 'local', 'filesystems.disks.public.url' => 'https://media.example.test/storage']);
        Setting::query()->create(['key' => 'logo', 'value' => 'settings/logo.webp']);
        Setting::query()->create(['key' => 'hero_video', 'value' => 'settings/hero.mp4']);
        Setting::query()->create(['key' => 'hero_fallback', 'value' => 'settings/hero.webp']);
        $page = Page::factory()->published()->create([
            'slug' => 'collezione',
            'cover_image_path' => 'pages/cover.webp',
        ]);
        PageImage::factory()->for($page)->create(['path' => 'pages/gallery.webp']);

        $home = $this->get(route('home'));
        $detail = $this->get(route('pages.show', ['page' => $page->slug]));

        $home->assertOk()
            ->assertSee('https://media.example.test/storage/settings/hero.mp4')
            ->assertSee('https://media.example.test/storage/settings/hero.webp')
            ->assertSee('https://media.example.test/storage/pages/cover.webp');
        $this->assertSame(2, substr_count($home->getContent(), 'https://media.example.test/storage/settings/logo.webp'));
        $detail->assertOk()
            ->assertSee('https://media.example.test/storage/pages/cover.webp')
            ->assertSee('https://media.example.test/storage/pages/gallery.webp');
    }

    public function test_contact_page_displays_validation_errors(): void
    {
        $response = $this->from(route('contact.create'))->post(route('contact.store'), []);

        $response->assertRedirect(route('contact.create'));
        $this->followRedirects($response)->assertOk()->assertSee('Controlla i dati inseriti:');
    }

    public function test_contact_form_stores_message_and_sends_notification(): void
    {
        Mail::fake();
        Setting::query()->create(['key' => 'email', 'value' => 'contatti@example.test']);

        $response = $this->post(route('contact.store'), [
            'name' => 'Mario Rossi',
            'email' => 'mario@example.test',
            'subject' => 'Informazioni',
            'message' => 'Vorrei ricevere maggiori informazioni.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['email' => 'mario@example.test', 'subject' => 'Informazioni']);
        Mail::assertSent(ContactMessageReceived::class);
    }
}
