<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Video della homepage

In **Impostazioni sito** si possono caricare un video hero per desktop e tablet
(consigliato 1920 × 1080) e uno per smartphone (consigliato 720 × 1280).
Selezionare ogni video e premere **Carica file**: il video viene salvato subito
e la pagina mostra avanzamento ed eventuali errori. Le altre modifiche vanno
salvate separatamente con **Salva impostazioni**. Senza JavaScript i video
vengono inviati insieme alle impostazioni.
Sono accettati MP4 e WebM fino a 50 MB ciascuno. Il video mobile viene
selezionato fino a 700 px di larghezza; se è disponibile un solo video, viene
usato su tutti i dispositivi. Senza JavaScript viene usato il video desktop,
oppure quello mobile se è l'unico disponibile. L'immagine fallback è usata come
poster durante il caricamento e quando non sono presenti video.
I limiti del server possono essere inferiori: per caricare un video da 50 MB,
configurare PHP con `upload_max_filesize` almeno `50M` e `post_max_size`
maggiore di `50M`, oltre agli eventuali limiti del server web. Senza JavaScript,
per caricare entrambi i video nella stessa richiesta serve `post_max_size`
maggiore di `100M`. L'area amministrativa segnala gli errori di formato e caricamento;
se PHP rifiuta una richiesta troppo grande, suggerisce di caricare un video alla
volta. Un eventuale blocco imposto direttamente dal server web può invece
comparire prima che l'applicazione riesca a mostrare il proprio messaggio.
Se `public/storage` non è un collegamento a `storage/app/public`, Laravel
fornisce comunque i video caricati tramite il loro URL pubblico. Per gli altri
file pubblici resta necessario configurare correttamente il collegamento.

## Contenuti dal vecchio WordPress

`php artisan db:seed --class=DatabaseSeeder` importa la pagina Azienda, le
collezioni Estate ’15, Inverno ’15-’16 e Nuova Collezione e le relative gallerie
immagine. Le immagini originali vengono lette da `jaguar_immagini/` e copiate,
ottimizzate, nel disco pubblico; il logo viene letto da
`jaguar_immagini/jaguar.png`. Rieseguire il seeder riallinea testi e gallerie ai
dati importati, sovrascrivendo eventuali modifiche amministrative a queste pagine.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
