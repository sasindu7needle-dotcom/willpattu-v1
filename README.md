# Wilpattu Park

Laravel 13 / PHP 8.3 website using MySQL. The 16 requested pages are implemented; PDF section 7 (AI Safari Planner) is excluded. Blade templates, plain CSS and small vanilla JavaScript interactions do not require a frontend build.

## Local setup

1. Install dependencies with `composer install`.
2. Copy `.env.example` to `.env`, configure MySQL, and create `wilpattu_park` using utf8mb4.
3. Run `php artisan key:generate` and `php artisan migrate`.
4. Run `php artisan serve --port=8765` and open http://127.0.0.1:8765.

For Laragon, set the virtual host document root to this project's `public` directory. Never expose the project root.

## Content and integrations

- `config/content.php`: page copy and journal articles.
- `config/park.php`: integrations and contact settings.
- `PARK_BOOKING_URL`: approved booking engine URL; empty values route visitors to reservation enquiries.
- `PARK_EMAIL` / `PARK_PHONE`: published Makara Resorts contact information, configurable for the property.
- `PARK_WHATSAPP`: verified international WhatsApp number (digits); leave empty until supplied.
- Reservation enquiries are stored in MySQL's `enquiries` table with category, status and a unique reference. Email delivery and confirmed booking/payment processing are not configured.
- Gallery has category filters and a keyboard-accessible image dialog; FAQ uses native disclosure controls.
- Destination photography is locally stored from the supplied Makara guide. Accommodation and dining imagery, room specifications, current pricing/offer validity, verified guest reviews and the exact property location require approved client materials. The map currently identifies the national park.
- The seasons and travel estimates are adapted from the provided guide; confirm current conditions with reservations.

## Checks

`php artisan test --compact` runs page rendering, enquiry persistence, validation and rate-limit tests using an isolated SQLite in-memory database. Runtime storage uses MySQL. Browser smoke checks in `tests/Browser/park_smoke.py` require Python Playwright and Chromium and expect the local server on port 8765.

Design references: supplied UI image (green/gold safari layout) and second image (serif heading style, approximated using Cormorant Garamond). Content source: https://www.makararesorts.lk/kalpitiya-excursions-2/wilpattu-national-park.html. Contacts: https://www.makararesorts.lk/contact-us.html.
