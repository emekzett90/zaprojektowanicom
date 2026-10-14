<?php
/**
 * Tłumacz EN 2 — moduł Zaprojektowani Suite. Jedyny tłumacz strony.
 *
 * Codziennie o 03:00 (czas strony) i od razu po publikacji wpisu czyta polskie strony tak, jak widzą je goście,
 * tłumaczy przez API OpenAI tylko nowe i zmienione teksty (sens, nie słowo w słowo) i podaje je modułowi
 * językowemu (zaprojektowani-languages): angielskie adresy /en/… z przekierowaniami 301, hreflang, adres
 * kanoniczny, mapy witryny EN, menu i stopka po angielsku na każdej stronie EN.
 *
 * Tłumaczenia są w tabelach {prefix}zpen_* (kluczem jest polski tekst, nie wersja strony, więc edycja strony
 * nie kasuje jej angielskiej wersji). Dane poprzednich tłumaczy zostają nietknięte: przy pierwszym uruchomieniu
 * są kopiowane, a wtyczka ZP Translator v2 jest wyłączana (do przywrócenia przez wgranie poprzedniej wersji).
 * Klucz API wkleja administrator w ZP Suite → Tłumacz EN; jest zapisany zaszyfrowany i nigdy nie jest wyświetlany.
 */
if (!defined('ABSPATH')) { exit; }
if (defined('ZPTE_VERSION')) { return; }

define('ZPTE_VERSION', '2.0.0');
define('ZPTE_DIR', __DIR__ . '/');

foreach (['Settings', 'Store', 'Log', 'OpenAI', 'Translator', 'Legacy', 'Routes', 'Index', 'Serve', 'Source', 'Sync', 'Import', 'Retire', 'Worker', 'Health', 'Bridge', 'Admin'] as $zpte_class) {
    require_once ZPTE_DIR . 'includes/' . $zpte_class . '.php';
}
unset($zpte_class);

// The language module reads routes at plugins_loaded (first): serving hooks are added now.
\ZPTE\Serve::hooks();
\ZPTE\Retire::boot();
\ZPTE\Worker::boot();
\ZPTE\Health::boot();
if (is_admin()) { \ZPTE\Admin::boot(); }

// The Suite has no deactivation routine of its own: stop the schedules with it (data and settings stay).
if (defined('ZP_SUITE_PATH')) {
    register_deactivation_hook(ZP_SUITE_PATH . 'zaprojektowani-suite.php', ['ZPTE\\Worker', 'unschedule']);
}
