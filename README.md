# Kanał treści zaprojektowani.com

Gałąź `tresci-wpisy` to kanał, z którego moduł „Publikacja wpisów” w ZP Suite pobiera nowe artykuły i planuje je w WordPressie
(specyfikacja: `plan-wzrostu/auto-publikacja-spec.md` w plikach projektu). Gałąź nie ma kodu wtyczki.

- `manifest.json`: lista wpisów z datami publikacji (8:00 czasu warszawskiego) i linki do nich ze starszych wpisów.
- `wpisy/<slug>.json`: jeden artykuł (treść jako base64 w `html_b64`, dane SEO, FAQ, okładka, post do wizytówki Google).
- `obrazy/`: zdjęcia z realizacji studia użyte w artykułach (okładki 1600×1000).

Pliki generuje `tresci/narzedzia/build.py --kanal paczka-N`, a wysyła `tresci/narzedzia/wyslij_kanal.py paczka-N`.
Nie edytuj ich ręcznie: zmiany wprowadza się w `tresci/zrodla/*.yaml`.
