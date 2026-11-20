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
        if (! User::query()->where('email', 'admin@example.test')->exists()) {
            User::factory()->create([
                'name' => 'Amministratore',
                'email' => 'admin@example.test',
                'role' => 'admin',
            ]);
        }

        Page::query()->updateOrCreate(
            ['slug' => 'azienda'],
            [
                'title' => 'Azienda',
                'intro' => 'Dal 1981, calzature di qualità nel distretto del Cuoio.',
                'content' => "Jaguar nasce nel comune di Castelfranco di Sotto, in provincia di Pisa, nel 1981: i fratelli Martone, Rocco e Marciano, decidono di avventurarsi nel mondo calzaturiero dando vita a un piccolo laboratorio artigianale a carattere familiare.\n\nDopo aver affrontato le prime stagioni di difficoltà e di duro lavoro, l’azienda riesce ad affermarsi progressivamente nel panorama internazionale, diventando sinonimo di elevata qualità al giusto prezzo.\n\nL’utilizzo di materie prime di pregio provenienti dallo stesso comprensorio del Cuoio, la manodopera specializzata nell’assemblaggio e nella rifinitura e uno stile originale e in continua evoluzione consentono all’azienda di soddisfare le richieste di una clientela sempre più vasta, differenziata ed esigente.\n\nNel 1994 Jaguar si trasferisce a Santa Maria a Monte e amplia notevolmente la propria produzione industriale. Negli anni Novanta prende avvio una solida collaborazione con marchi internazionali come Tommy Hilfiger, Max Mara, Penny Black, Strenesse e Donald J. Pliner.\n\nSuccessivamente viene lanciato il marchio Sabrina Martone.",
                'meta_title' => 'Azienda | Calzaturificio Jaguar',
                'meta_description' => 'La storia del Calzaturificio Jaguar: calzature di qualità realizzate in Italia dal 1981.',
                'is_published' => true,
                'menu_order' => 10,
            ],
        );

        app(SiteSettings::class)->save([
            'company_name' => 'Calzaturificio Jaguar',
            'hero_title' => 'Una storia di calzature italiane dal 1981.',
            'hero_subtitle' => 'Qualità, ricerca e manifattura nel distretto del Cuoio.',
            'homepage_intro_title' => 'Esperienza e manifattura italiana.',
            'homepage_intro_text' => 'Fondata nel 1981 a Castelfranco di Sotto dai fratelli Rocco e Marciano Martone come laboratorio artigianale a conduzione familiare, Jaguar si è affermata per la qualità, i materiali del comprensorio del Cuoio e la cura dell’assemblaggio e della rifinitura. Dal 1994 ha sede a Santa Maria a Monte e firma le proprie collezioni con il marchio Sabrina Martone.',
        ]);

        $this->call(CollectionGallerySeeder::class);
    }
}
