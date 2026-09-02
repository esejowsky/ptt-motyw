# Motyw PT — arkusz zewnętrzny dla UNIT3D 9.2

Jeden plik CSS. Nie wymaga wgrywania czegokolwiek na serwer, więc przeżywa aktualizacje silnika.

## Jak włączyć

1. Ustawienia konta → **External CSS stylesheet** → wklej adres:

```
https://esejowsky.github.io/ptt-motyw/v1/motyw-ptt.css
```

2. W polu **Style** zostaw **Galactic** — pod tę bazę motyw jest dopięty.
3. Pola **Standalone CSS** nie wypełniaj. Gdy coś tam wpiszesz, silnik pomija arkusz zewnętrzny
   i motyw w ogóle się nie załaduje.

## Co obejmuje

Nawigację, stopkę i identyfikację (orzeł, pas flagi), listę torrentów, stronę torrenta, forum,
statystyki, profil, upload, prośby, sklep BON i strony pomocy.

## Czego nie obejmuje

- **Ekrany logowania i rejestracji** — mają własny `<head>` i nie ładują arkusza użytkownika.
- **Metadane w drugiej linii wiersza** (kodek, gatunki, rok, grupa wydawnicza, flagi języków,
  znaczniki REMUX/DV/HDR/ATMOS) — tych danych nie ma w markupie silnika, a CSS ich nie utworzy.
- **Zestaw pozycji w menu** — to lista linków generowana po stronie serwera, nie styl.

## Uwagi

- Motyw wypełnia komplet zmiennych, których używa silnik, więc wygląda tak samo niezależnie
  od wybranej bazy — sprawdzone również na jasnym Material Design.
- Fonty są systemowe: polityka bezpieczeństwa serwisu nie dopuszcza wczytywania własnych krojów.
- Adres jest wersjonowany (`/v1/`). Nowe wydania trafiają pod kolejny numer, żeby aktualizacja
  była natychmiastowa mimo pamięci podręcznej hostingu.
