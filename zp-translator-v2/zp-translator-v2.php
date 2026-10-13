<?php
/**
 * Plugin Name: ZP Translator v2
 * Description: Niezależny tłumacz treści EN, kontrolowana kolejka i zgodny adapter ZP Suite.
 * Version: 2.0.1
 * Requires PHP: 8.0
 */
if (!defined('ABSPATH')) exit;
define('ZPT2_VERSION', '2.0.1');
foreach (['store','source','api','engine','admin','shared'] as $file) require_once __DIR__.'/includes/'.$file.'.php';
add_action('plugins_loaded', ['ZPT2_Engine','boot'], 90);
register_activation_hook(__FILE__, ['ZPT2_Engine','activate']);
register_deactivation_hook(__FILE__, ['ZPT2_Engine','deactivate']);
