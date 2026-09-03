# Motyw PT — arkusz zewnętrzny dla UNIT3D 9.2

Jeden plik CSS. Nie wymaga wgrywania czegokolwiek na serwer, więc przeżywa aktualizacje silnika.

## Jak włączyć

1. Ustawienia konta → **External CSS stylesheet** → wklej adres:

```
https://esejowsky.github.io/ptt-motyw/v1/motyw-ptt.css
```

2. W polu **Style** zostaw **Galactic** (motyw wypełnia komplet zmiennych silnika, więc zadziała
   też na innych bazach, ale Galactic jest bazą, na której był strojony).
3. Pola **Standalone CSS** nie wypełniaj. Gdy coś tam wpiszesz, silnik pomija arkusz zewnętrzny
   i motyw w ogóle się nie załaduje.

## Co obejmuje

Nagłówek w trzech pasach (statystyki konta, marka z wyszukiwarką, menu), nawigację wtórną,
stopkę, listę torrentów we wszystkich czterech widokach, stronę torrenta, upload, stronę główną
z czatem, forum, wiki i artykuły, profil i wszystkie podstrony konta, prośby, statystyki, sklep
BON, helpdesk, strony statyczne, wiadomości, powiadomienia, playlisty, ankiety, zakładki,
MediaHub, napisy i strony błędów.

## Czego nie obejmuje

- **Ekrany logowania i rejestracji** — mają własny `<head>` i nie ładują arkusza użytkownika.
- **Zestaw pozycji w menu** — to lista linków generowana po stronie serwera, nie styl.
- Danych, których nie ma w markupie. CSS nie tworzy treści.

## Jak jest zbudowany

Źródła leżą w `resources/sass/ptt-ext/`. Jedna warstwa, pliki numerowane wg stron:
`00` tokeny (paleta i komplet 329 zmiennych silnika), `01` reset, `02` prymitywy (formularze,
tabele, paginacja, znaczniki, edytor BBCode), `03` chrom, `10–12` torrenty, `20–21` forum
i wiki, `30–33` konto, `40–45` pozostałe strony. Każdy selektor żyje w dokładnie jednym pliku.

Kompilacja:

```
sass resources/sass/ptt-ext/index.scss public/ptt/motyw-ptt.css --no-source-map --style=compressed --no-charset
```

## Uwagi

- Fonty są systemowe: polityka bezpieczeństwa serwisu nie dopuszcza wczytywania własnych krojów.
- Adres jest wersjonowany (`/v1/`). Aktualizacje trafiają pod ten sam adres, hosting cache'uje
  plik około dziesięciu minut.
- Reguły pod własne dodatki PolishTorrent (druga linia wiersza, strona wydania, paski promocyjne)
  są oznaczone w źródłach jako „produkcja PT”.
