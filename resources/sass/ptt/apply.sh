#!/bin/sh
# =====================================================================
# Motyw PTT — instalacja / odtworzenie po aktualizacji silnika
#
#   sh resources/sass/ptt/apply.sh
#
# Wszystko, co nasze, żyje w katalogach nieznanych upstreamowi:
#     resources/sass/ptt/    — źródła stylów
#     resources/views/ptt/   — nadpisania widoków (Blade szuka tu pierwszy)
#     resources/ptt/lang/    — uzupełnione tłumaczenia
#     public/ptt/            — skompilowany CSS + logo
#
# `php artisan git:update` może nadpisać dwie rzeczy: ścieżkę widoków
# w config/view.php oraz plik tłumaczeń lang/pl/auth.php. Ten skrypt
# przywraca obie.
# =====================================================================
set -e
ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
cd "$ROOT"

echo "→ podpięcie widoków w config/view.php"
if grep -q "views/ptt" config/view.php; then
    echo "  już jest"
else
    php -r '
        $p = "config/view.php";
        $t = file_get_contents($p);
        $old = "\x27paths\x27 => [\n        resource_path(\x27views\x27),";
        $new = "\x27paths\x27 => [\n        // motyw PTT — nadpisania widoków; szukane PRZED plikami silnika\n        resource_path(\x27views/ptt\x27),\n        resource_path(\x27views\x27),";
        file_put_contents($p, str_replace($old, $new, $t));
    '
    grep -q "views/ptt" config/view.php && echo "  dopisano" || { echo "  BŁĄD: dopisz ręcznie resource_path('views/ptt') do 'paths' w config/view.php"; exit 1; }
fi

echo "→ polskie tłumaczenia ekranów wejściowych"
# UNIT3D dostarcza lang/pl/auth.php z zaledwie 2 kluczami (reszta spada
# na angielski fallback) — podmieniamy na komplet.
cp resources/ptt/lang/pl/auth.php lang/pl/auth.php

echo "→ kompilacja stylów"
# UNIT3D buduje bunem, ale sass wystarczy każdy — bierzemy pierwszy dostępny
if command -v bunx >/dev/null 2>&1; then
    bunx sass resources/sass/ptt/_auth.scss public/ptt/auth.css --no-source-map --style=compressed
elif command -v npx >/dev/null 2>&1; then
    npx sass resources/sass/ptt/_auth.scss public/ptt/auth.css --no-source-map --style=compressed
elif [ -x node_modules/.bin/sass ] && command -v node >/dev/null 2>&1; then
    node_modules/.bin/sass resources/sass/ptt/_auth.scss public/ptt/auth.css --no-source-map --style=compressed
else
    echo "  UWAGA: nie znalazłem sass (bunx/npx/node_modules) — pomijam kompilację."
    echo "  Skompiluj ręcznie: bunx sass resources/sass/ptt/_auth.scss public/ptt/auth.css --style=compressed"
fi

echo "→ czyszczenie cache"
if command -v php >/dev/null 2>&1; then
    php artisan view:clear
    php artisan config:clear
else
    echo "  UWAGA: brak php w PATH — uruchom: php artisan view:clear && php artisan config:clear"
fi

echo "✓ motyw PTT gotowy"
