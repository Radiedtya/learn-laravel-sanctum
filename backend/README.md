# Project Structure

```
laravel-sanctum/
├── app
│   ├── Http
│   │   └── Controllers
│   │       ├── Api
│   │       │   ├── AktorController.php
│   │       │   ├── AuthController.php
│   │       │   ├── FilmController.php
│   │       │   └── GenreController.php
│   │       └── Controller.php
│   ├── Models
│   │   ├── Aktor.php
│   │   ├── Film.php
│   │   ├── Genre.php
│   │   └── User.php
│   └── Providers
│       └── AppServiceProvider.php
├── bootstrap
│   ├── cache
│   │   ├── packages.php
│   │   └── services.php
│   ├── app.php
│   └── providers.php
├── config
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── services.php
│   └── session.php
├── database
│   ├── factories
│   │   ├── AktorFactory.php
│   │   ├── FilmFactory.php
│   │   ├── GenreFactory.php
│   │   └── UserFactory.php
│   ├── migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_08_05_013830_create_personal_access_tokens_table.php
│   │   ├── 2026_08_06_005302_create_genres_table.php
│   │   ├── 2026_08_06_030705_create_aktors_table.php
│   │   └── 2026_08_06_033756_create_films_table.php
│   ├── seeders
│   │   └── DatabaseSeeder.php
│   └── database.sqlite
├── public
│   ├── favicon.ico
│   ├── index.php
│   └── robots.txt
├── resources
│   ├── css
│   │   └── app.css
│   ├── js
│   │   └── app.js
│   └── views
│       └── welcome.blade.php
├── routes
│   ├── api.php
│   ├── console.php
│   └── web.php
├── storage
│   ├── app
│   │   ├── private
│   │   └── public
│   ├── framework
│   │   ├── cache
│   │   │   └── data
│   │   ├── sessions
│   │   ├── testing
│   │   └── views
│   │       ├── 1c3eabb297821dbc660247eec1727992.php
│   │       ├── bc64b1ea9d12d96655bdba712a9ee3b4.php
│   │       ├── d4edbf39bef3f001e54d5e052914703b.php
│   │       └── fea3559595b01f820a4e6399dafc0134.php
│   └── logs
├── tests
│   ├── Feature
│   │   └── ExampleTest.php
│   ├── Unit
│   │   └── ExampleTest.php
│   └── TestCase.php
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
├── README.md
└── vite.config.js
```
