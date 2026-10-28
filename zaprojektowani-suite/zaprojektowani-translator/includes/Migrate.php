<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/**
 * One-time changes of saved data that come with a new version. Each runs once, in an admin or cron request, after the
 * copy of the earlier translators is finished (its data would otherwise come in afterwards unchanged).
 * Option zpte2_migrations: [name => time it ran].
 */
final class Migrate {
    const OPTION = 'zpte2_migrations';
    /** 3.1.1: where a step that runs over several requests stopped (step => last page id). */
    const CURSOR = 'zpte2_migrations_at';

    /** Steps in the order they run: name => method. A step that returns false carries on in the next admin or cron request. */
    const STEPS = ['311-knowledge' => 'knowledge', '311-lengths' => 'lengths'];

    public static function run(): void {
        if (!Store::ready() || !Import::done()) { return; }
        $done = get_option(self::OPTION, []);
        $done = is_array($done) ? $done : [];
        foreach (self::STEPS as $name => $method) {
            if (isset($done[$name])) { continue; }
            // Noted first: a step that fails is not tried again on every request (and two requests do not run it at once).
            $done[$name] = time();
            update_option(self::OPTION, $done, false);
            if (self::$method() === false) {
                unset($done[$name]);
                update_option(self::OPTION, $done, false);
                return;
            }
        }
    }

    /**
     * 3.1.1: "Wiedza" is Knowledge, not Insights (Mat, 14.10.2026), and the section's English address is /en/knowledge/
     * (/en/insights/ answers with a 301, shipped legacy routes). Machine translations that name the section Insights are
     * translated again; corrections made by hand stay. "PageSpeed Insights" and other products keep their names.
     * The old translations stay until the new ones arrive (Sync::AGAIN): English pages never show Polish meanwhile, also
     * while the worker is paused.
     */
    private static function knowledge(): void {
        global $wpdb;
        $t = Store::table('strings');
        $rows = $wpdb->get_results("SELECT pl, en, manual FROM {$t} WHERE en LIKE '%Insights%' AND pl LIKE '%iedz%'");
        $owner = \ZPL\Dict::overrides('en');
        $hashes = [];
        foreach ((array) $rows as $r) {
            // Corrections made by hand, and texts the site terms or the owner's overrides translate (they outrank the copy).
            if ((int) $r->manual || isset(Serve::SITE_TERMS[(string) $r->pl]) || isset($owner[(string) $r->pl])) { continue; }
            $en = preg_replace('~\b(?:PageSpeed|Facebook|Instagram|Meta|Google|Search Console|Audience|LinkedIn|TikTok|Pinterest|YouTube|Analytics|Page|Business|Customer|Consumer|Market|Data)\s+Insights\b~i', '', (string) $r->en) ?? '';
            if (!preg_match('~\bInsights\b~', $en)) { continue; }
            $hashes[Store::hash((string) $r->pl)] = time();
        }
        if ($hashes) { update_option(Sync::AGAIN, Sync::again() + $hashes, false); }
        $gone = count($hashes);

        // English addresses: the language module's own (owner's) routes and this module's pages.
        $moved = 0;
        $custom = get_option('zpl_routes', []);
        if (is_array($custom)) {
            $changed = false;
            foreach ($custom as $pl => $en) {
                $new = is_string($en) ? self::knowledge_path($en) : '';
                if ($new === '') { continue; }
                $custom[$pl] = $new;
                Routes::moved((string) $en, $new);
                $changed = true;
                $moved++;
            }
            if ($changed) { update_option('zpl_routes', $custom); }
        }
        foreach ($wpdb->get_results('SELECT id, en_path FROM ' . Store::table('pages') . " WHERE en_path LIKE '/en/insights/%'") ?: [] as $row) {
            $new = self::knowledge_path((string) $row->en_path);
            if ($new !== '' && Routes::assign((int) $row->id, $new)) { $moved++; }
        }
        // Older redirects that end on an /en/insights/… address go straight to the new one (no second hop).
        $legacy = Routes::legacy();
        $relink = false;
        foreach ($legacy as $old => $to) {
            $new = is_string($to) ? self::knowledge_path($to) : '';
            if ($new !== '') { $legacy[$old] = $new; $relink = true; }
        }
        if ($relink) { update_option(Routes::LEGACY, $legacy, false); }

        // The owner's overrides in Języki PL/EN for the section name follow it too (they outrank everything).
        $ov = get_option('zpl_overrides', []);
        if (is_array($ov)) {
            $changed = false;
            foreach (Serve::SITE_TERMS as $pl => $en) {
                if (isset($ov[$pl]) && is_string($ov[$pl]) && strpos($ov[$pl], 'Insights') !== false && strpos($en, 'Knowledge') !== false) { $ov[$pl] = $en; $changed = true; }
            }
            if ($changed) { update_option('zpl_overrides', $ov); update_option('zpl_overrides_version', (string) time()); }
        }

        Routes::rebuild();
        Routes::flush();
        Sync::compile_shared();
        $queued = 0;
        if ($hashes) {
            // Read again without the "nothing changed" shortcut: their Polish text did not change.
            foreach (Store::pages_using(array_keys($hashes)) as $pid) { Store::page_update((int) $pid, ['source_hash' => '']); Store::queue((int) $pid, 30); $queued++; }
        }
        Index::rebuild();
        Log::add('Wiedza = Knowledge: ' . Admin::n($gone, 'tekst', 'teksty', 'tekstów') . ' z „Insights” do ponownego przetłumaczenia (' . Admin::n($queued, 'strona', 'strony', 'stron') . ' w kolejce), '
            . Admin::n($moved, 'adres', 'adresy', 'adresów') . ' /en/insights/ → /en/knowledge/ z przekierowaniem 301.');
    }

    /**
     * 3.1.1 (SearchFit): English titles over 60 characters and descriptions over 155 get short versions for Google, made
     * from machine translations when a page is compiled (Sync::short_version, \ZPL\Page::variant), never by OpenAI. Every
     * English page is compiled once more, for up to 2 seconds in each admin or cron request.
     */
    private static function lengths(): bool {
        global $wpdb;
        $t = Store::table('pages');
        $at = get_option(self::CURSOR, []);
        $at = is_array($at) ? $at : [];
        $until = microtime(true) + 2;
        $ids = array_map('intval', (array) $wpdb->get_col($wpdb->prepare("SELECT id FROM {$t} WHERE status IN ('done','partial') AND id > %d ORDER BY id ASC", (int) ($at['311-lengths'] ?? 0))));
        foreach ($ids as $id) {
            Sync::compile($id);
            if (microtime(true) > $until && $id !== end($ids)) {
                $at['311-lengths'] = $id;
                update_option(self::CURSOR, $at, false);
                return false;
            }
        }
        unset($at['311-lengths']);
        if ($at) { update_option(self::CURSOR, $at, false); } else { delete_option(self::CURSOR); }
        $m = Store::table('maps');
        $titles = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$m} WHERE page_id > 0 AND map LIKE %s", '%' . $wpdb->esc_like('"' . \ZPL\Page::VARIANTS['title']) . '%'));
        $metas = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$m} WHERE page_id > 0 AND map LIKE %s", '%' . $wpdb->esc_like('"' . \ZPL\Page::VARIANTS['meta']) . '%'));
        Log::add('Tytuły i opisy dla Google (do ' . Translator::MAX_TITLE . ' i ' . Translator::MAX_META . ' znaków), bez OpenAI: krótszy tytuł ma '
            . Admin::n($titles, 'strona', 'strony', 'stron') . ', krótszy opis ' . $metas . '. Tłumaczenia z Języków PL/EN, poprawki ręczne i teksty wtyczki zostają bez zmian.');
        return true;
    }

    /** "/en/insights/…" => "/en/knowledge/…", '' for any other address. */
    private static function knowledge_path(string $en): string {
        return strpos($en, '/en/insights/') === 0 ? '/en/knowledge/' . substr($en, strlen('/en/insights/')) : '';
    }
}
