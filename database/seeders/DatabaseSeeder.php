<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use App\Services\SiteSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Amministratore',
            'email' => 'admin@example.test',
            'role' => 'admin',
        ]);

        Page::factory()->published()->create([
            'title' => 'Azienda',
            'slug' => 'azienda',
            'intro' => 'Presentate qui la storia, i valori e la filosofia aziendale.',
            'menu_order' => 10,
        ]);

        app(SiteSettings::class)->save([
            'company_name' => config('app.name'),
            'hero_title' => 'L’eccellenza prende forma.',
            'hero_subtitle' => 'Configurate testi, immagini e video dall’area amministrativa.',
        ]);

        $this->call(CollectionGallerySeeder::class);
    }
}
