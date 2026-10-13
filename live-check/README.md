# Sprawdzenie: czy pliki ZP Suite na żywo są takie jak w ZIP-ie Codexa

Po co: Codex napisał, że jego ZIP to „historyczna baza + 18 plików sprawdzonych na serwerze”, nie świeża kopia serwera.
Zanim wydamy nową wersję na bazie tego ZIP-a, trzeba wiedzieć, czy na serwerze są pliki inne niż w ZIP-ie (inaczej nowa wersja cofnęłaby te zmiany).

Jak (sesja na iMacu, Chrome Mata zalogowany do wp-admin, tylko odczyt):
1. Otwórz dowolną stronę https://zaprojektowani.com/wp-admin/.
2. Uruchom w konsoli skrypt `sprawdz-pliki-live.js` (link raw poniżej). Pobiera listę 812 plików (php, js, css, html) z sumami SHA-256 z ZIP-a, otwiera każdy plik w edytorze wtyczek (GET, bez zapisu) i porównuje sumę.
3. Wynik: `window.zpLiveCheck` (liczby i lista różnic). Treść różniących się plików: `window.zpLiveDiffContent[ścieżka]`.
4. Odeślij listę różnic. Jeśli są różnice, zapisz ich treść jako pliki pod tymi samymi ścieżkami i wypchnij gałąź `live-pliki-2026-10-13` do repozytorium (albo wklej, jeśli plików jest kilka i są małe).

Linki raw:
- skrypt: https://raw.githubusercontent.com/emekzett90/zaprojektowanicom/rollback-codex-2026-10-13/live-check/sprawdz-pliki-live.js
- lista: https://raw.githubusercontent.com/emekzett90/zaprojektowanicom/rollback-codex-2026-10-13/live-check/pliki.json
