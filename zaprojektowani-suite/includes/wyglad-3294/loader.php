<?php
/**
 * 3.2.94 — poprawki po 3.2.93 (Mat 29.10.2026 20:19 UTC): mniejsza sekcja „W centrum uwagi” i ikonki kategorii w Wiedzy,
 * odstęp od nagłówka w Wiedzy na telefonie, niższe hero na /tworzenie-stron-internetowych/, wyrównane logotypy w paskach.
 * Każdy moduł tego wydania to osobny plik w tym folderze; ładowane alfabetycznie.
 */
if (!defined('ABSPATH')) { exit; }
foreach (glob(__DIR__ . '/*.php') ?: [] as $zp_w94_file) {
  if (basename($zp_w94_file) !== 'loader.php') { require_once $zp_w94_file; }
}
unset($zp_w94_file);
