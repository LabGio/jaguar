<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\Page;
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

    public function test_contact_form_stores_message_and_sends_notification(): void
    {
        Mail::fake();
        Setting::factory()->create(['key' => 'email', 'value' => 'contatti@example.test']);

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
