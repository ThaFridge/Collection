# GameVault - Voortgang

## Huidige Status: Alle fases (1-14) afgerond — Volledig werkende applicatie op test server

## Server Info

- **URL**: <http://10.0.0.166>
- **OS**: Debian 12.13
- **Stack**: PHP 8.3, Nginx, Node.js 20, Composer 2.9, SQLite
- **Project pad**: `/var/www/gamevault`
- **Laravel versie**: 12.49.0
- **SSH**: `sshpass -p 'gamevault' ssh root@10.0.0.166`
- **Database**: `/var/www/gamevault/database/database.sqlite`

## Wat is af

### Planning

- [x] Plan opgesteld met volledige database schema, architectuur en fases (plan.md)
- [x] Database schema: games, lego_sets, api_providers, game_images, lego_images, tags, game_tag, lego_set_tag, settings
- [x] API provider plugin architectuur ontworpen
- [x] Migratie strategie voor data behoud vastgelegd
- [x] Multi-database support (SQLite default, MySQL, PostgreSQL, SQL Server)
- [x] Composite unique constraint: slug + platform + format (games), set_number (LEGO)
- [x] LEGO collectie schema met bouwstatus tracking en handleiding links

### Mockups (mockups/ map)

- [x] 01 - Homepage / Game Collectie grid met covers, badges, stats bar
- [x] 02 - Wishlist met "Naar Collectie" actie
- [x] 03 - Game toevoegen met API zoek, barcode scan, tags, auto-fill
- [x] 04 - Game detail pagina met galerij, "ook op ander platform" links
- [x] 05 - Statistieken dashboard met grafieken
- [x] 06 - Admin panel met API providers, DB info, import/export
- [x] Dark/Light mode toggle (theme.css + theme.js, gedeeld over alle pagina's)
- [x] 07 - LEGO collectie grid met bouwstatus badges, steentjes, thema's, handleiding links
- [x] 08 - LEGO set detail met bouwstatus stappen-tracker, links (handleiding, BrickLink), galerij

### Fase 1 - Project Setup

- [x] Laravel 12 geïnstalleerd op Debian 12 server (10.0.0.166)
- [x] PHP 8.3 + extensies (mbstring, xml, curl, zip, sqlite3, gd, bcmath, intl)
- [x] Nginx geconfigureerd voor Laravel
- [x] SQLite database aangemaakt
- [x] Storage link aangemaakt

### Fase 2 - Database migraties

- [x] games tabel (volledig schema met composite unique)
- [x] lego_sets tabel (met set_number unique, bouwstatus)
- [x] api_providers tabel
- [x] game_images + lego_images tabellen
- [x] tags + game_tag + lego_set_tag tabellen
- [x] settings tabel
- [x] Seeders (api providers: igdb, rawg, rebrickable, brickset)

### Fase 3 - Models

- [x] Game model (scopes: collection, wishlist; casts; auto-slug; relaties: images, tags)
- [x] LegoSet model (scopes: collection, wishlist, built, notBuilt; auto-slug; auto-instructions-url)
- [x] ApiProvider model (array cast credentials_json)
- [x] Tag model (auto-slug, relaties naar games en lego_sets)
- [x] GameImage, LegoImage, Setting models

### Fase 4 - Game CRUD & Views

- [x] GameController (index/create/store/show/edit/update/destroy)
- [x] GameRequest form request met validatie
- [x] Blade layout met dark/light mode toggle (CSS variabelen)
- [x] Game collectie grid met covers, badges, stats bar
- [x] Create/edit formulieren met alle velden
- [x] Game detail pagina met metadata, "ook op ander platform" links
- [x] Filters: platform, completion_status, zoeken op naam
- [x] Uitgebreide platform lijst (PS1-PS5, Xbox-Xbox Series X, Nintendo, Sega, PC, handhelds)

### Fase 5 - Wishlist (games)

- [x] Status toggle (PATCH /games/{game}/toggle-status)
- [x] Wishlist view met "Naar Collectie" knop per game

### Fase 6 - API Provider Systeem

- [x] ApiProviderInterface (search, fetchDetails, fetchCoverUrl, isConfigured)
- [x] GameSearchResult DTO
- [x] RawgProvider implementatie
- [x] IgdbProvider implementatie (Twitch OAuth, gecached token, covers, genres, developers)
- [x] GameSearchService orchestrator (doorloopt actieve providers op prioriteit)
- [x] GameSearchController met /api/games/search endpoint
- [x] "Test verbinding" knop per provider in admin panel (RAWG, IGDB, Rebrickable, BrickSet)

### Fase 7 - Frontend Search (games)

- [x] JS debounced zoeken (300ms) in create/edit formulier
- [x] Zoekresultaten als klikbare kaarten met cover preview
- [x] Auto-fill formulier bij selectie (naam, genre, developer, publisher, beschrijving, release, cover)

### Fase 8 - Admin Panel

- [x] AdminController (index, updateProvider, testProvider)
- [x] Admin view: providers beheren met aan/uit toggle, API keys, prioriteit
- [x] Test verbinding functie per provider met resultaat feedback
- [x] Database info sectie (driver, database pad)
- [x] Routes: /admin, /admin/providers/{provider}, /admin/providers/{provider}/test

### Fase 9 - LEGO Collectie

- [x] LegoSetController (index/create/store/show/edit/update/destroy)
- [x] LEGO grid view met afbeeldingen, thema badges, bouwstatus
- [x] Create/edit formulieren
- [x] Bouwstatus tracking met visuele stappen-tracker (niet gebouwd → bezig → gebouwd → tentoongesteld)
- [x] Automatische link naar bouwhandleiding op LEGO.com per set nummer
- [x] LEGO wishlist met "Naar Collectie" conversie
- [x] Filters: thema, bouwstatus, zoeken op naam/set-nummer
- [x] Rebrickable API provider implementatie
- [x] LegoProviderInterface + LegoSearchResult DTO
- [x] LegoSearchService orchestrator + LegoSearchController
- [x] LEGO zoekfunctie in create/edit formulier (debounced, auto-fill)

### Fase 10 - Statistieken Dashboard

- [x] DashboardController met statistieken
- [x] Games: totaal, waarde, per platform, per completion status, per genre
- [x] LEGO: totaal sets, waarde, per thema, bouwstatus
- [x] CSS bar charts (geen externe JS libraries)

### Fase 11 - Import/Export

- [x] CSV export (games apart, LEGO apart)
- [x] JSON export (alles samen)
- [x] JSON import met duplicaat detectie (game slug+platform+format / LEGO set_number)
- [x] Export/import pagina met knoppen en upload formulier

### Fase 12 - Tags

- [x] Tags CRUD (aanmaken, verwijderen)
- [x] Tags index pagina met overzicht
- [x] Toggle tags per game en LEGO set (via AJAX)

### Fase 13 - Barcode Scanner

- [x] Camera barcode scanner (html5-qrcode library)
- [x] Ondersteunt EAN-13, EAN-8, UPC-A, UPC-E, Code 128
- [x] Automatisch invullen barcode veld bij scan
- [x] Geïntegreerd in game create/edit formulier

### Fase 14 - Polish & UX

- [x] Homepage met navigatie naar Games en LEGO secties
- [x] Collectie statistieken op homepage (aantallen, waarde)
- [x] Navbar uitgebreid met Dashboard, Tags, Export links
- [x] Responsive design

## Bugfixes

- [x] ApiProvider credentials_json cast gewijzigd van `encrypted` naar `array` (fix voor openssl_encrypt error)
- [x] SQLite database permissions gefixed (www-data schrijfrechten)
- [x] AdminController: credentials worden nu correct gemerged met bestaande waarden

## Belangrijke Beslissingen

- **Framework**: Laravel 12 (PHP 8.3)
- **Database**: SQLite default, multi-driver support
- **Collectie types**: Games + LEGO (uitbreidbaar naar meer categorieën)
- **API's**: Plugbaar systeem, beheerd via admin panel (RAWG, IGDB, Rebrickable, BrickSet)
- **Auth**: Single user nu, multi-user later
- **Format (games)**: Fysiek, digitaal, of beide
- **Bouwstatus (LEGO)**: niet gebouwd, bezig, gebouwd, tentoongesteld
- **LEGO handleiding**: Automatische link naar LEGO.com per set nummer
- **Duplicaten**: Zelfde game mag op meerdere platforms EN in meerdere formats
- **Migraties**: Altijd additief, nooit destructief
