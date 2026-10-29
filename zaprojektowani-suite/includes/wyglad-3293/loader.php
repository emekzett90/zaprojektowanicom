<?php
/**
 * 3.2.93 — wygląd podstron (wątek „Popraw wygląd podstron i Wiedzy”, 28.10.2026).
 * Każdy moduł tego wydania to osobny plik w tym folderze; ładowane alfabetycznie.
 */
if (!defined('ABSPATH')) { exit; }
foreach (glob(__DIR__ . '/*.php') ?: [] as $zp_wyglad_file) {
  if (basename($zp_wyglad_file) !== 'loader.php') { require_once $zp_wyglad_file; }
}
unset($zp_wyglad_file);
