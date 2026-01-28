# GameVault - Verzamel Website Implementatieplan

## Overzicht
Laravel + SQLite website voor het beheren van meerdere collecties (games, LEGO, en toekomstige categorieën) met wishlist, automatische info/covers via externe APIs (beheerd vanuit admin panel).

## Database Schema

### `games` tabel
| Kolom | Type | Notities |
|---|---|---|
| id | bigint (PK, auto) | |
| name | string(255) | Verplicht |
| platform | string(100) | bijv. "PS5", "Switch" |
| slug | string(255) | URL-friendly, uniek per platform+format (composite unique: slug+platform+format) |
| cover_image_path | string(500), nullable | Lokaal opgeslagen pad |
| cover_image_url | string(500), nullable | Originele remote URL |
| release_date | date, nullable | |
| genre | string(255), nullable | |
| developer | string(255), nullable | |
| publisher | string(255), nullable | |
| description | text, nullable | |
| purchase_price | decimal(8,2), nullable | |
| purchase_date | date, nullable | |
| condition | string(50), nullable | bijv. "New", "Good", "Fair" |
| notes | text, nullable | |
| status | string(20), default 'collection' | 'collection' of 'wishlist' |
| completion_status | string(30), default 'not_played' | 'not_played', 'playing', 'completed', 'platinum' |
| rating | tinyint, nullable | 1-10 persoonlijke score |
| format | string(20), default 'physical' | 'physical', 'digital', of 'both' — een game kan zowel fysiek als digitaal in bezit zijn |
| barcode | string(50), nullable | EAN/UPC barcode voor fysieke games |
| external_api_id | string(255), nullable | ID van de API bron |
| external_api_source | string(50), nullable | bijv. 'igdb', 'rawg' |
| metadata_json | json, nullable | Raw API response cache |
| created_at | timestamp | |
| updated_at | timestamp | |

### `api_providers` tabel
| Kolom | Type | Notities |
|---|---|---|
| id | bigint (PK, auto) | |
| slug | string(50), uniek | bijv. 'igdb', 'rawg' |
| name | string(100) | Weergavenaam |
| is_active | boolean, default false | Aan/uit toggle |
| priority | integer, default 0 | Hoger = eerst geprobeerd |
| credentials_json | text, nullable | Encrypted API keys |
| created_at | timestamp | |
| updated_at | timestamp | |

### `game_images` tabel
| Kolom | Type | Notities |
|---|---|---|
| id | bigint (PK, auto) | |
| game_id | bigint (FK → games) | |
| image_path | string(500) | Lokaal pad |
| type | string(30), default 'screenshot' | 'cover', 'screenshot', 'photo' |
| sort_order | integer, default 0 | |
| created_at | timestamp | |

### `tags` tabel
| Kolom | Type | Notities |
|---|---|---|
| id | bigint (PK, auto) | |
| name | string(100), uniek | |
| slug | string(100), uniek | |

### `game_tag` pivot tabel
| Kolom | Type | Notities |
|---|---|---|
| game_id | bigint (FK → games) | |
| tag_id | bigint (FK → tags) | |

---

## LEGO Collectie

### `lego_sets` tabel
| Kolom | Type | Notities |
|---|---|---|
| id | bigint (PK, auto) | |
| set_number | string(20) | LEGO set nummer bijv. "75192" (uniek) |
| name | string(255) | Verplicht |
| slug | string(255), uniek | URL-friendly |
| theme | string(100), nullable | bijv. "Star Wars", "Technic", "City" |
| subtheme | string(100), nullable | bijv. "Ultimate Collector Series" |
| piece_count | integer, nullable | Aantal steentjes |
| minifigure_count | integer, nullable | Aantal minifiguren |
| image_path | string(500), nullable | Lokaal opgeslagen afbeelding |
| image_url | string(500), nullable | Originele remote URL |
| release_year | integer, nullable | |
| retail_price | decimal(8,2), nullable | Officiële adviesprijs |
| purchase_price | decimal(8,2), nullable | Werkelijke aankoopprijs |
| purchase_date | date, nullable | |
| condition | string(50), nullable | 'new_sealed', 'new_open', 'built', 'used' |
| status | string(20), default 'collection' | 'collection' of 'wishlist' |
| build_status | string(30), default 'not_built' | 'not_built', 'in_progress', 'built', 'displayed' |
| notes | text, nullable | |
| instructions_url | string(500), nullable | Link naar LEGO.com bouwhandleiding |
| bricklink_url | string(500), nullable | Link naar BrickLink pagina |
| external_api_id | string(255), nullable | ID van API bron |
| external_api_source | string(50), nullable | bijv. 'rebrickable', 'brickset' |
| metadata_json | json, nullable | Raw API response cache |
| created_at | timestamp | |
| updated_at | timestamp | |

### `lego_images` tabel
| Kolom | Type | Notities |
|---|---|---|
| id | bigint (PK, auto) | |
| lego_set_id | bigint (FK → lego_sets) | |
| image_path | string(500) | Lokaal pad |
| type | string(30), default 'photo' | 'box', 'built', 'photo', 'instructions' |
| sort_order | integer, default 0 | |
| created_at | timestamp | |

### `lego_set_tag` pivot tabel
| Kolom | Type | Notities |
|---|---|---|
| lego_set_id | bigint (FK → lego_sets) | |
| tag_id | bigint (FK → tags) | Tags tabel wordt gedeeld met games |

### LEGO API Providers
- **Rebrickable API** (gratis) — set info, afbeeldingen, onderdelen, thema's
- **BrickSet API** — reviews, prijzen, beschikbaarheid
- Handleiding link wordt automatisch gegenereerd: `https://www.lego.com/nl-nl/service/buildinginstructions/{set_number}`

### LEGO Specifieke Features
- Bouwstatus tracking: niet gebouwd → bezig → gebouwd → tentoongesteld
- Directe link naar bouwhandleiding op LEGO.com per set
- Set zoeken op set-nummer of naam via API
- Wishlist met "Naar Collectie" conversie (zelfde als games)
- Statistieken: totaal sets, totale waarde, stukjes, per thema

---

### `settings` tabel
| Kolom | Type | Notities |
|---|---|---|
| id | bigint (PK, auto) | |
| key | string(100), uniek | |
| value | text, nullable | |

## Database Configuratie

De applicatie ondersteunt meerdere database drivers, instelbaar via `.env`:

| Driver | `.env` instelling | Gebruik |
|---|---|---|
| SQLite (default) | `DB_CONNECTION=sqlite` | Lokaal testen, geen setup nodig |
| MySQL / MariaDB | `DB_CONNECTION=mysql` | Productie optie |
| PostgreSQL | `DB_CONNECTION=pgsql` | Productie optie |
| SQL Server | `DB_CONNECTION=sqlsrv` | Enterprise/Windows omgevingen |

- **Default = SQLite** — werkt out-of-the-box zonder externe database server
- Wisselen van database = alleen `.env` aanpassen, migraties werken op alle drivers
- In het admin panel komt een database-info sectie die toont welke driver actief is
- Migraties gebruiken alleen database-agnostische Laravel Schema Builder (geen raw SQL), zodat ze op alle drivers werken

## Architectuur

### API Provider Plugin Systeem
- `ApiProviderInterface` - contract dat elke provider implementeert (`search()`, `fetchDetails()`, `fetchCoverUrl()`, `isConfigured()`)
- `GameSearchService` - orchestrator die actieve providers op prioriteit doorloopt
- Nieuwe provider toevoegen = 1 class + 1 regel in de provider map
- Admin panel: providers aan/uit zetten, API keys beheren (opgeslagen in DB, encrypted)

### Game Zoek Flow
```
Gebruiker typt game naam
        │
        ▼
JS debounce (300ms) → AJAX GET /api/games/search?q=naam&platform=PS5
        │
        ▼
GameSearchService doorloopt actieve providers op prioriteit
        │
        ▼
Provider zoekt via externe API → normaliseert naar GameSearchResult DTO
        │
        ▼
Resultaten terug als JSON → frontend toont klikbare kaarten met covers
        │
        ▼
Gebruiker klikt resultaat → formulier auto-filled
        │
        ▼
Bij opslaan: cover image wordt lokaal gedownload naar storage/covers/
```

### Migratie Strategie (data behoud)
- Initiële migraties maken tabellen aan
- Alle toekomstige wijzigingen zijn **additief** (alleen nullable kolommen toevoegen)
- Nooit `drop` of `dropColumn` gebruiken in `up()`
- Deploy draait `php artisan migrate --force` — bestaande data blijft intact
- SQLite bestand in `.gitignore`, niet in repo

## Implementatie Volgorde

### Fase 1 - Project Setup
1. Laravel installeren in repo root
2. SQLite configureren in `.env`
3. Database bestand aanmaken

### Fase 2 - Database
4. Migratie: `games` tabel (volledig schema)
5. Migratie: `lego_sets` tabel (volledig schema)
6. Migratie: `api_providers` tabel
7. Migratie: `game_images`, `lego_images`, `tags`, `game_tag`, `lego_set_tag` tabellen
8. Migratie: `settings` tabel
9. Seeders voor providers (igdb, rawg, rebrickable, brickset)

### Fase 3 - Models
10. `Game` model (scopes: `scopeCollection`, `scopeWishlist`, casts)
11. `LegoSet` model (scopes: `scopeCollection`, `scopeWishlist`, `scopeBuilt`, `scopeNotBuilt`, casts)
12. `ApiProvider` model (encrypted credentials)
13. `Tag`, `GameImage`, `LegoImage`, `Setting` models

### Fase 4 - CRUD & Views
10. `GameController` (index/create/store/show/edit/update/destroy)
11. Form requests voor validatie
12. Blade views: layout, game grid met covers, create/edit formulieren
13. Routes in `web.php`

### Fase 5 - Wishlist
14. Status toggle (`PATCH /games/{game}/toggle-status`)
15. Wishlist view (hergebruikt game grid, gefilterd op status)

### Fase 6 - API Provider Systeem
16. Interface, abstract class, DTO (`GameSearchResult`)
17. `RawgProvider` implementatie
18. `IgdbProvider` implementatie
19. `GameSearchService` orchestrator
20. `GameSearchController` search endpoint

### Fase 7 - Frontend Search
21. JS debounced zoeken in create/edit formulier
22. Zoekresultaten als klikbare kaarten met cover preview
23. Auto-fill formulier bij selectie, cover lokaal downloaden bij opslaan

### Fase 8 - Admin Panel
24. `ApiProviderController` (index/update)
25. Admin view: providers beheren, API keys instellen
26. Route group `/admin/api-providers`

### Fase 9 - LEGO Collectie
- `LegoSetController` (index/create/store/show/edit/update/destroy)
- Blade views: LEGO grid met afbeeldingen, create/edit formulieren
- Bouwstatus tracking (niet gebouwd / bezig / gebouwd / tentoongesteld)
- Automatische link naar bouwhandleiding: `https://www.lego.com/nl-nl/service/buildinginstructions/{set_number}`
- LEGO wishlist met "Naar Collectie" conversie
- Zoeken via Rebrickable/BrickSet API (set nummer of naam)

### Fase 10 - Statistieken Dashboard
- Dashboard pagina: totaal games + LEGO sets, totale waarde per categorie, games per platform, LEGO per thema
- Grafieken/visueel overzicht

### Fase 11 - Import/Export

- CSV export van volledige collectie (games + LEGO apart)
- JSON export/import voor backup
- Duplicaat detectie: game+platform+format / LEGO set_number

### Fase 12 - Tags & Extra Media

- Tags systeem: aanmaken, koppelen aan games en LEGO sets, filteren op tags
- Meerdere afbeeldingen per game en LEGO set

### Fase 13 - Barcode Scanner

- Mobiele camera barcode/EAN scanner (via JS library bijv. QuaggaJS of Html5-QRCode)
- Barcode opzoeken via API → game of LEGO set auto-invullen

### Fase 14 - Polish & UX

- CSS grid layout voor covers/afbeeldingen
- Filter/sort (platform, genre, status, tags, completion, rating, thema)
- Flash messages, error handling
- Responsive design (mobiel-vriendelijk, handig in de winkel)
- Dark/light mode toggle
- Homepage met navigatie naar Games en LEGO secties

## Toekomstige Uitbreiding (multi-user)

- Migratie: `user_id` toevoegen aan `games` en `lego_sets` (nullable, bestaande rows → user 1)
- Laravel Breeze/Fortify voor auth
- Global scope zodat users alleen eigen collecties zien

## Verificatie

1. `php artisan migrate` — alle tabellen aangemaakt zonder errors
2. Game toevoegen via formulier → verschijnt in collectie grid
3. LEGO set toevoegen → verschijnt in LEGO collectie grid
4. Game/LEGO naar wishlist verplaatsen → verschijnt in wishlist
5. Game zoeken via API → resultaten met covers verschijnen
6. LEGO set zoeken op nummer → info + handleiding link automatisch ingevuld
7. Admin panel: provider activeren, keys opslaan → zoeken werkt
8. Tweede migratie toevoegen → `php artisan migrate` behoudt bestaande data
9. Bouwstatus wijzigen op LEGO set → status update correct
