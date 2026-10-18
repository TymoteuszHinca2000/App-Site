# AppAndSite Base – setup

## Identyfikatory

- nazwa motywu: `AppAndSite Base`
- katalog motywu: `appandsite`
- text domain: `appandsite`
- kategoria bloków ACF: `appandsite`

## Pierwsze uruchomienie

```bash
composer install
npm install
npm run build
```

Do pracy developerskiej:

```bash
npm run dev
```

## `.env`

Ustaw adres lokalny projektu oraz wymagane dane integracji.

Nie commituj produkcyjnych danych SMTP, kluczy API ani innych sekretów.

## Ważne

Katalog motywu powinien nazywać się `appandsite`, ponieważ ta nazwa jest używana w konfiguracji ścieżek assetów.

Motyw korzysta z Roots Sage, Roots Acorn i pozostałych bibliotek open-source. Odpowiednie informacje licencyjne pozostają w plikach licencji i paczkach zależności.
