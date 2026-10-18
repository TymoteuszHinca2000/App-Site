# AppAndSite Base

Bazowy motyw WordPress dla projektów AppAndSite.

## Stack

- Roots Sage 11
- Roots Acorn 5
- Laravel Blade
- Tailwind CSS 4
- Vite 7
- Alpine.js
- ACF Composer
- Livewire
- Swiper
- AOS
- Headroom.js

## Wymagania

- PHP 8.3+
- Node.js 20+
- Composer 2
- WordPress 6.8+

## Instalacja

Umieść motyw w katalogu:

```text
wp-content/themes/appandsite
```

Następnie w katalogu motywu uruchom:

```bash
composer install
npm install
npm run build
```

Do developmentu:

```bash
npm run dev
```

## Konfiguracja

Ustaw wymagane wartości środowiskowe w `.env`.

Nie umieszczaj kluczy API ani danych SMTP bezpośrednio w repozytorium.

## Branding

- nazwa motywu: `AppAndSite Base`
- katalog motywu: `appandsite`
- text domain: `appandsite`
- kategoria bloków ACF: `appandsite`

Motyw bazuje na Roots Sage/Acorn. Informacje licencyjne dla kodu upstream znajdują się w `LICENSE.md` oraz licencjach zależności.
