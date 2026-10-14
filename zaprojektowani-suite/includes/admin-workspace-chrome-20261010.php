<?php
/** Shared Suite chrome for existing native tools. No worker, setting or frontend changes. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Admin_Workspace_Chrome_20261010 {
    private static ?array $context = null;
    private static ?WP_Hook $original = null;
    private static ?WP_Hook $wrapper = null;

    public static function pages(): array {
        return [
            'zp-tlumacz-en' => ['Tłumacz EN', 'translations'],
            'zpl-languages' => ['Języki PL/EN', 'translations'],
            'zpl-skaner' => ['Skaner EN', 'translations'],
            'zp-suite-home-cms' => ['Strona główna CMS', 'settings'],
            'zp-suite-publikacja' => ['Publikacja wpisów', 'calendar'],
            'zp-suite-speed' => ['Optymalizacja zasobów', 'performance'],
            'zp-suite-content-20261009' => ['Pakiet artykułów', 'calendar'],
            'zp-suite-photos-20261009' => ['Zdjęcia artykułów', 'calendar'],
            'zp-suite-html-cache' => ['Cache HTML', 'performance'],
        ];
    }

    private static function permitted(): bool {
        global $page_hook;
        return is_admin() && current_user_can('manage_options')
            && !(function_exists('wp_doing_ajax') && wp_doing_ajax())
            && !isset($_GET['noheader'])
            && (self::$context === null || (isset($_GET['page']) && is_string($_GET['page'])
                && wp_unslash($_GET['page']) === self::$context['slug'] && $page_hook === self::$context['hook']))
            && function_exists('zp_admin20_open') && function_exists('zp_admin20_close');
    }

    public static function screen($screen): void {
        global $page_hook, $plugin_page, $_registered_pages;
        if (self::$context !== null || !self::permitted()) { return; }
        $slug = isset($_GET['page']) && is_string($_GET['page']) ? wp_unslash($_GET['page']) : '';
        $pages = self::pages();
        // Match the exact native request and actual registered hook, including hidden admin_page_ aliases.
        if (!isset($pages[$slug]) || !is_string($page_hook) || $page_hook === ''
            || !is_object($screen) || ($screen->id ?? '') !== $page_hook
            || !isset($_registered_pages[$page_hook])
            || !preg_match('/(?:^|_)page_' . preg_quote($slug, '/') . '$/D', $page_hook)
            || (isset($plugin_page) && $plugin_page !== $slug)) { return; }
        self::$context = ['slug' => $slug, 'hook' => $page_hook, 'title' => $pages[$slug][0], 'active' => $pages[$slug][1]];
        // Capture after native load/enqueue callbacks. Never wrap across admin-footer HTML.
        add_action('admin_head', [self::class, 'capture'], PHP_INT_MAX);
    }

    public static function capture(): void {
        global $wp_filter;
        if (!self::permitted() || self::$context === null || self::$wrapper !== null) { return; }
        $hook = self::$context['hook'];
        $original = $wp_filter[$hook] ?? null;
        if (!$original instanceof WP_Hook || !$original->callbacks) { return; }
        // Retain WordPress's own dispatcher: priority, insertion order, accepted_args and callback changes.
        self::$original = $original;
        self::$wrapper = new WP_Hook();
        $wp_filter[$hook] = self::$wrapper;
        add_action($hook, [self::class, 'render'], PHP_INT_MIN, PHP_INT_MAX);
        echo '<style id="zp-admin-workspace-chrome-20261010">' . self::css() . '</style>';
    }

    public static function render(...$args): void {
        global $wp_filter;
        if (!self::permitted() || self::$context === null || self::$original === null || self::$wrapper === null) { return; }
        $hook = self::$context['hook'];
        // Preserve a native callback added after admin_head (e.g. by an admin notice) inside the same shell.
        foreach (self::$wrapper->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $entry) {
                if ($entry['function'] === [self::class, 'render']) { continue; }
                self::$original->add_filter($hook, $entry['function'], $priority, $entry['accepted_args']);
                self::$wrapper->remove_filter($hook, $entry['function'], $priority);
            }
        }
        zp_admin20_open(self::$context['title'], self::$context['active']);
        echo '<div class="zpAdmin20__legacy" data-zp-native-page="' . esc_attr(self::$context['slug']) . '">';
        $wp_filter[$hook] = self::$original;
        try {
            // The original native renderer still owns its data, forms, nonces, assets and polling.
            self::$original->do_action($args);
        } finally {
            $wp_filter[$hook] = self::$wrapper;
            echo '</div>';
            zp_admin20_close();
        }
    }

    public static function css(): string {
        return <<<'CSS'
.zpAdmin20 .zpAdmin20__legacy{min-width:0;font-family:inherit;color:var(--ink);font-size:14px;line-height:1.6}
.zpAdmin20 .zpAdmin20__legacy>.wrap{margin:0;max-width:none}
.zpAdmin20 .zpAdmin20__legacy :where(h1,h2,h3,h4,p,label,button,input,select,textarea,th,td,summary,a:not(.dashicons)){font-family:inherit!important}
.zpAdmin20 .zpAdmin20__legacy>.wrap>h1:first-of-type:not(:has(a,button,input,select,textarea)),.zpAdmin20 .zpAdmin20__legacy>.wrap>.zpxHead>h1:first-of-type:not(:has(a,button,input,select,textarea)){display:none}
.zpAdmin20 .zpAdmin20__legacy h2{font-size:18px;line-height:1.4;letter-spacing:-.02em;color:var(--ink)}
.zpAdmin20 .zpAdmin20__legacy h3{font-size:15px;line-height:1.5;color:var(--ink)}
.zpAdmin20 .zpAdmin20__legacy .nav-tab-wrapper{display:flex;flex-wrap:wrap;gap:7px;border:0;padding:0;margin:18px 0}
.zpAdmin20 .zpAdmin20__legacy .nav-tab{float:none;margin:0;border:1px solid var(--line);border-radius:8px;background:#fff;padding:7px 13px;color:var(--muted);font-size:12px;line-height:1.6}
.zpAdmin20 .zpAdmin20__legacy .nav-tab-active{background:var(--ink);border-color:var(--ink);color:#fff}
.zpAdmin20 .zpAdmin20__legacy .widefat{border:1px solid var(--line);border-radius:12px;box-shadow:none;background:#fff;border-spacing:0;max-width:100%}
.zpAdmin20 .zpAdmin20__legacy .widefat :where(th,td){padding:12px 14px;line-height:1.6;font-size:12px;vertical-align:top;overflow-wrap:anywhere}
.zpAdmin20 .zpAdmin20__legacy .widefat thead th{background:#f8fafc;color:var(--muted);font-weight:600}
.zpAdmin20 .zpAdmin20__legacy .widefat td{border-bottom:1px solid #edf1f5}
.zpAdmin20 .zpAdmin20__legacy .widefat tbody tr:last-child td{border-bottom:0}
.zpAdmin20 .zpAdmin20__legacy .widefat.striped>tbody>:nth-child(odd){background:#fafbfd}
.zpAdmin20 .zpAdmin20__legacy .form-table :where(th,td){font-size:13px;padding:14px 10px}
.zpAdmin20 .zpAdmin20__legacy .button{font-family:inherit!important;border-radius:8px;border-color:var(--line);min-height:36px;padding:3px 13px}
.zpAdmin20 .zpAdmin20__legacy .button-primary{background:var(--ink);border-color:var(--ink);color:#fff}
.zpAdmin20 .zpAdmin20__legacy :where(input:not([type=checkbox]):not([type=radio]):not([type=hidden]):not([type=button]):not([type=submit]),select,textarea){border-color:#ccd4e0;border-radius:8px;max-width:100%}
.zpAdmin20 .zpAdmin20__legacy .notice{border-radius:8px;box-shadow:none}
.zpAdmin20 .zpAdmin20__legacy :where(.zpxCard,.zpxItem,.postbox){border:1px solid var(--line);border-radius:16px;background:#fff;box-shadow:none}
.zpAdmin20 .zpAdmin20__legacy :where(.zpxCard,.zpxItem){padding:22px}
.zpAdmin20 .zpAdmin20__legacy .zpxHead{margin-top:16px;border-radius:16px;background:var(--ink);color:#fff}
.zpAdmin20 .zpAdmin20__legacy .zpxHead :where(p,.zpxKicker){color:#c5cfdf}
.zpAdmin20 .zpAdmin20__legacy .zpxHead .zpxStat{color:#fff}
.zpAdmin20 .zpAdmin20__legacy .zpxHead h2{color:#fff}
@media(max-width:782px){.zpAdmin20 .zpAdmin20__legacy .form-table th{padding-bottom:4px}.zpAdmin20 .zpAdmin20__legacy :where(.zpxCard,.zpxItem){padding:16px}.zpAdmin20 .zpAdmin20__legacy .nav-tab{padding:7px 10px}}
CSS;
    }
}

// Frontend, admin-post and AJAX never install page wrappers or load additional scripts.
if (is_admin() && !(function_exists('wp_doing_ajax') && wp_doing_ajax())) {
    add_action('current_screen', [ZP_Admin_Workspace_Chrome_20261010::class, 'screen'], PHP_INT_MAX);
}
