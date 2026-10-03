<?php
/**
 * Tłumacz EN (OpenAI) — moduł Zaprojektowani Suite.
 *
 * Raz dziennie (WP-Cron) szuka nowych polskich treści bez wersji angielskiej, tłumaczy je przez API OpenAI
 * i publikuje pod angielskim adresem /en/… w module językowym (zaprojektowani-languages): własny adres EN,
 * hreflang, kanoniczny adres i mapa witryny EN. Uzupełnia też brakujące fragmenty na istniejących stronach EN.
 *
 * Tłumaczenia nie trafiają do plików ani do opcji zpl_overrides: siedzą w tabelach {prefix}zpte_*, a moduł
 * językowy pyta o nie filtrem zpl_dictionary na samym końcu (słownik z wtyczki i ręczne poprawki wygrywają).
 * Klucz API wkleja administrator w ZP Suite → Tłumacz EN; zapisany jest zaszyfrowany i nigdy nie jest wyświetlany.
 */
if (!defined('ABSPATH')) { exit; }
if (defined('ZPTE_VERSION')) { return; }

define('ZPTE_VERSION', '1.0.0');
define('ZPTE_DIR', __DIR__ . '/');

foreach (['Settings', 'Store', 'Log', 'OpenAI', 'Translator', 'Bridge', 'Source', 'Sync', 'Worker', 'Admin'] as $zpte_class) {
    require_once ZPTE_DIR . 'includes/' . $zpte_class . '.php';
}
unset($zpte_class);

add_action('plugins_loaded', ['ZPTE\\Worker', 'boot'], 20);
add_action('plugins_loaded', ['ZPTE\\Bridge', 'boot'], 20);
if (is_admin()) { \ZPTE\Admin::boot(); }

// The Suite has no deactivation routine of its own: stop the daily check with it (data and settings stay).
if (defined('ZP_SUITE_PATH')) {
    register_deactivation_hook(ZP_SUITE_PATH . 'zaprojektowani-suite.php', ['ZPTE\\Worker', 'unschedule']);
}
