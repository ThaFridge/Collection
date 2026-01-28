# GameVault - Voortgang

## Huidige Status: Planning & Mockups fase

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

## Wat nog moet (implementatie volgorde)

### Fase 1 - Project Setup
- [ ] Laravel installeren
- [ ] SQLite configureren
- [ ] Database bestand aanmaken

### Fase 2 - Database migraties
- [ ] games tabel
- [ ] lego_sets tabel
- [ ] api_providers tabel
- [ ] game_images + lego_images tabellen
- [ ] tags + game_tag + lego_set_tag tabellen
- [ ] settings tabel
- [ ] Seeders (api providers: igdb, rawg, rebrickable, brickset)

### Fase 3 - Models
- [ ] Game model (scopes, casts, relaties)
- [ ] LegoSet model (scopes: collection, wishlist, built, notBuilt)
- [ ] ApiProvider model (encrypted credentials)
- [ ] Tag, GameImage, LegoImage, Setting models

### Fase 4 - Game CRUD & Views
- [ ] GameController
- [ ] Form requests
- [ ] Blade layout (dark/light mode)
- [ ] Game grid view (collectie)
- [ ] Create/edit formulieren
- [ ] Game detail pagina

### Fase 5 - Wishlist (games)
- [ ] Status toggle
- [ ] Wishlist view

### Fase 6 - API Provider Systeem
- [ ] ApiProviderInterface
- [ ] GameSearchResult DTO
- [ ] RawgProvider
- [ ] IgdbProvider
- [ ] GameSearchService orchestrator
- [ ] GameSearchController

### Fase 7 - Frontend Search (games)
- [ ] JS debounced zoeken
- [ ] Zoekresultaten kaarten
- [ ] Auto-fill + cover download

### Fase 8 - Admin Panel
- [ ] ApiProviderController
- [ ] Admin views
- [ ] Routes

### Fase 9 - LEGO Collectie
- [ ] LegoSetController (CRUD)
- [ ] LEGO grid view met afbeeldingen
- [ ] Create/edit formulieren met set-nummer zoeken
- [ ] Bouwstatus tracking (niet gebouwd → bezig → gebouwd → tentoongesteld)
- [ ] Automatische link naar bouwhandleiding op LEGO.com
- [ ] LEGO wishlist met "Naar Collectie" conversie
- [ ] Rebrickable/BrickSet API provider implementaties
- [ ] LegoSearchResult DTO

### Fase 10 - Statistieken Dashboard
- [ ] Dashboard controller + view
- [ ] Games: totaal, waarde, per platform, per status
- [ ] LEGO: totaal sets, waarde, per thema, bouwstatus
- [ ] Grafieken

### Fase 11 - Import/Export
- [ ] CSV export (games + LEGO apart)
- [ ] JSON export/import
- [ ] Duplicaat detectie (game+platform+format / LEGO set_number)

### Fase 12 - Tags & Extra Media
- [ ] Tags CRUD (gedeeld tussen games en LEGO)
- [ ] Meerdere afbeeldingen per game en LEGO set

### Fase 13 - Barcode Scanner
- [ ] Camera barcode scanner (JS)
- [ ] Barcode lookup via API (games + LEGO)

### Fase 14 - Polish & UX
- [ ] CSS grid layout
- [ ] Filters en sorting
- [ ] Flash messages
- [ ] Responsive design
- [ ] Dark/light mode in Laravel
- [ ] Homepage met navigatie naar Games en LEGO secties

## Belangrijke Beslissingen
- **Framework**: Laravel (PHP)
- **Database**: SQLite default, multi-driver support
- **Collectie types**: Games + LEGO (uitbreidbaar naar meer categorieën)
- **API's**: Plugbaar systeem, beheerd via admin panel (RAWG, IGDB, Rebrickable, BrickSet)
- **Auth**: Single user nu, multi-user later
- **Format (games)**: Fysiek, digitaal, of beide
- **Bouwstatus (LEGO)**: niet gebouwd, bezig, gebouwd, tentoongesteld
- **LEGO handleiding**: Automatische link naar LEGO.com per set nummer
- **Duplicaten**: Zelfde game mag op meerdere platforms EN in meerdere formats
- **Migraties**: Altijd additief, nooit destructief

## Volgende Sessie
Start bij **Fase 1 - Project Setup**: Laravel installeren en configureren.
