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

    /** Steps in the order they run: name => method. */
    const STEPS = ['311-knowledge' => 'knowledge', '311-lengths' => 'lengths'];

    public static function run(): void {
        if (!Store::ready() || !Import::done()) { return; }
        $done = get_option(self::OPTION, []);
        $done = is_array($done) ? $done : [];
        foreach (self::STEPS as $name => $method) {
            if (isset($done[$name])) { continue; }
            // Noted first: a step that fails is not tried again on every request.
            $done[$name] = time();
            update_option(self::OPTION, $done, false);
            self::$method();
        }
    }

    /**
     * 3.1.1: "Wiedza" is Knowledge, not Insights (Mat, 14.10.2026), and the section's English address is /en/knowledge/
     * (/en/insights/ answers with a 301, shipped legacy routes). Machine translations that name the section Insights are
     * translated again; corrections made by hand stay. "PageSpeed Insights" and other products keep their names.
     */
    private static function knowledge(): void {
        global $wpdb;
        $t = Store::table('strings');
        $rows = $wpdb->get_results("SELECT id, pl, en, manual FROM {$t} WHERE en LIKE '%Insights%' AND pl LIKE '%iedz%'");
        $hashes = [];
        $gone = 0;
        foreach ((array) $rows as $r) {
            if ((int) $r->manual) { continue; }
            $en = preg_replace('~\b(?:PageSpeed|Facebook|Instagram|Meta|Google|Search Console|Audience|LinkedIn|TikTok|Pinterest|YouTube|Analytics|Page|Business|Customer|Consumer|Market|Data)\s+Insights\b~i', '', (string) $r->en) ?? '';
            if (!preg_match('~\bInsights\b~', $en)) { continue; }
            $wpdb->delete($t, ['id' => (int) $r->id]);
            $hashes[Store::hash((string) $r->pl)] = true;
            $gone++;
        }

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
            foreach (Store::pages_using(array_keys($hashes)) as $pid) { Store::queue((int) $pid, 30); $queued++; }
        }
        Index::rebuild();
        Log::add('Wiedza = Knowledge: ' . Admin::n($gone, 'tłumaczenie', 'tłumaczenia', 'tłumaczeń') . ' z „Insights” do ponownego przetłumaczenia (' . Admin::n($queued, 'strona', 'strony', 'stron') . ' w kolejce), '
            . Admin::n($moved, 'adres', 'adresy', 'adresów') . ' /en/insights/ → /en/knowledge/ z przekierowaniem 301.');
    }

    /**
     * 3.1.1 (SearchFit): English titles over 60 characters and descriptions over 155 get short versions for Google
     * (Sync::plan, \ZPL\Page::variant). Each English page is read once more without the "nothing changed" shortcut,
     * after the pages waiting for fixes; only the titles and descriptions that are too long go to OpenAI.
     */
    private static function lengths(): void {
        global $wpdb;
        $t = Store::table('pages');
        $ids = array_map('intval', (array) $wpdb->get_col("SELECT id FROM {$t} WHERE status IN ('done','partial')"));
        if (!$ids) { return; }
        $wpdb->query("UPDATE {$t} SET source_hash = '' WHERE status IN ('done','partial')");
        foreach ($ids as $id) { Store::queue($id, 60); }
        Log::add('Tytuły i opisy dla Google (do ' . Translator::MAX_TITLE . ' i ' . Translator::MAX_META . ' znaków): sprawdzam ponownie '
            . Admin::n(count($ids), 'stronę', 'strony', 'stron') . '; do OpenAI trafią tylko za długie tytuły i opisy.');
    }

    /** "/en/insights/…" => "/en/knowledge/…", '' for any other address. */
    private static function knowledge_path(string $en): string {
        return strpos($en, '/en/insights/') === 0 ? '/en/knowledge/' . substr($en, strlen('/en/insights/')) : '';
    }
}
