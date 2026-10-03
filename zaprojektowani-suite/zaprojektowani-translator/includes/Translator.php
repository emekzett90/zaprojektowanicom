<?php
namespace ZPTE;

if (!defined('ABSPATH')) { exit; }

/** Polish → English translation of page fragments (keys of the language module) and English URL slugs. */
final class Translator {
    const MAX_ITEMS = 30;
    const MAX_CHARS = 7000;
    const MAX_KEY = 8000; // longer fragments are left for a manual translation

    /** Split [key => kind] into request-sized batches (document order kept). @return array<int,array<string,string>> */
    public static function batches(array $items): array {
        $out = [];
        $cur = [];
        $chars = 0;
        foreach ($items as $k => $kind) {
            $len = strlen((string) $k);
            if ($cur && (count($cur) >= self::MAX_ITEMS || $chars + $len > self::MAX_CHARS)) {
                $out[] = $cur;
                $cur = [];
                $chars = 0;
            }
            $cur[(string) $k] = (string) $kind;
            $chars += $len;
        }
        if ($cur) { $out[] = $cur; }
        return $out;
    }

    public static function system_prompt(): string {
        $p = <<<'TXT'
You translate the Polish website of Zaprojektowani (zaprojektowani.com), a premium design studio from Katowice, Poland: websites, WooCommerce online stores, logo and visual identity, branding, Google Ads and Meta Ads campaigns. Clients are companies from all over Poland.

Translate every item from Polish into natural, fluent American English, as a native copywriter of a premium agency would write it. Keep the meaning, tone and level of detail; do not shorten, summarize or add anything.

Rules:
- Return every item with exactly the same "id".
- Markers like <1>, </1> and <2/> stand for links, bold text and icons. Keep every marker exactly as written and the same number of times, around the matching English words. Never add, drop or renumber markers and never add HTML.
- Keep the entities &amp; and &lt; as they are.
- Keep brand, product and people's names: Zaprojektowani, WordPress, WooCommerce, Elementor, Shopify, PrestaShop, Google Ads, Meta Ads, Facebook, Instagram, client and project names, first names.
- Prices: "3 999 zł" → "PLN 3,999", "od 999 zł" → "from PLN 999", "zł/mies." → "PLN/month". English number format: 1,500 and 4.9.
- Polish city names stay as they are (Katowice, Gliwice, Kraków); Śląsk → Silesia, Polska → Poland.
- Type "title" is an HTML page title, "meta" a meta description (keep a similar length), "attr" an image description or a short interface label, "schema" structured data for Google, "text" page text.
- Established terms on this site: Strony internetowe = Websites; Sklepy internetowe = Online stores; Logo i branding = Logo and branding; Identyfikacja wizualna = Visual identity; Kampanie reklamowe = Ad campaigns; Realizacje = Our work; Wiedza = Insights; Bezpłatna wycena = Free quote; Umów konsultację = Book a consultation; Spis treści = Table of contents; Najczęstsze pytania = Frequently asked questions.
- An item that is already English, a code or a proper name is returned unchanged.
- Output only the JSON answer, no comments.
TXT;
        $extra = trim((string) Settings::get('instructions'));
        if ($extra !== '') { $p .= "\n\nAdditional instructions from the site owner:\n" . $extra; }
        return $p;
    }

    private static function schema(): array {
        return [
            'type' => 'object',
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => ['id' => ['type' => 'string'], 'en' => ['type' => 'string']],
                        'required' => ['id', 'en'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            'required' => ['items'],
            'additionalProperties' => false,
        ];
    }

    /**
     * Translate one batch [key => kind]. Returns ['ok' => [key => en], 'failed' => [key...], 'error' => string,
     * 'fatal' => bool, 'retry' => bool]. A batch cut off by the token limit is split in two and retried.
     */
    public static function translate(array $batch, string $context): array {
        $out = ['ok' => [], 'failed' => [], 'error' => '', 'fatal' => false, 'retry' => false];
        $ids = [];
        $items = [];
        $n = 0;
        foreach ($batch as $k => $kind) {
            $id = (string) (++$n);
            $ids[$id] = (string) $k;
            $items[] = ['id' => $id, 'type' => $kind, 'text' => (string) $k];
        }
        $user = wp_json_encode(['page' => $context, 'items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $res = OpenAI::json([
            ['role' => 'system', 'content' => self::system_prompt()],
            ['role' => 'user', 'content' => (string) $user],
        ], 'translations', self::schema(), 4000 + 3 * (int) (array_sum(array_map('strlen', array_keys($batch))) / 2));
        $chars = array_sum(array_map(static function ($k) { return function_exists('mb_strlen') ? mb_strlen((string) $k) : strlen((string) $k); }, array_keys($batch)));
        if ($res['in'] || $res['out'] || $res['ok']) { Log::count($chars, (int) $res['in'], (int) $res['out']); }

        if (!$res['ok']) {
            if ($res['truncated'] && count($batch) > 1) {
                $half = (int) ceil(count($batch) / 2);
                $a = self::translate(array_slice($batch, 0, $half, true), $context);
                if ($a['fatal'] || $a['retry']) { return $a; }
                $b = self::translate(array_slice($batch, $half, null, true), $context);
                $b['ok'] = $a['ok'] + $b['ok'];
                $b['failed'] = array_merge($a['failed'], $b['failed']);
                return $b;
            }
            $out['error'] = (string) $res['error'];
            $out['fatal'] = (bool) $res['fatal'];
            $out['retry'] = (bool) $res['retry'];
            if (!$out['fatal'] && !$out['retry']) { $out['failed'] = array_values($ids); }
            return $out;
        }

        $got = [];
        foreach ((array) ($res['data']['items'] ?? []) as $it) {
            if (!is_array($it) || !isset($it['id'], $it['en'], $ids[(string) $it['id']])) { continue; }
            $got[(string) $it['id']] = (string) $it['en'];
        }
        foreach ($ids as $id => $pl) {
            $en = isset($got[$id]) ? self::clean($got[$id]) : '';
            if ($en !== '' && self::valid($pl, $en)) { $out['ok'][$pl] = $en; }
            else { $out['failed'][] = $pl; }
        }
        return $out;
    }

    public static function clean(string $en): string {
        $en = \ZPL\Html::norm($en);
        // Models sometimes wrap a lone answer in quotes.
        if (preg_match('~^"(.*)"$~s', $en, $m) && strpos($m[1], '"') === false) { $en = $m[1]; }
        return $en;
    }

    /** Markers intact, sensible length, no Polish left behind. */
    public static function valid(string $pl, string $en): bool {
        if (preg_match('~</?\d+/?>~', $pl) || preg_match('~</?\d+/?>~', $en)) {
            if (!\ZPL\Html::markers_match($pl, $en)) { return false; }
        }
        $lp = strlen($pl);
        $le = strlen($en);
        if ($le > 4 * $lp + 80) { return false; }
        if ($lp > 60 && $le < $lp * 0.25) { return false; }
        return !self::still_polish($pl, $en);
    }

    /** The English text still reads Polish (an untranslated sentence). */
    public static function still_polish(string $pl, string $en): bool {
        if ($en === $pl) {
            // Unchanged is fine for names and short English phrases, not for a Polish sentence.
            $words = (int) preg_match_all('~\p{L}{2,}~u', preg_replace('~</?\d+/?>~', ' ', $pl) ?? $pl);
            return self::polish_score($pl) >= 1 || $words >= 6;
        }
        return self::polish_score($en) >= 3;
    }

    private static function polish_score(string $s): int {
        $plain = mb_strtolower(preg_replace('~</?\d+/?>~', ' ', $s) ?? $s);
        // Words that only occur in Polish (no "to", "do", "i": they are English too).
        $n = (int) preg_match_all('~(?<!\p{L})(?:się|jest|nie|dla|oraz|lub|że|jak|czy|przez|są|jako|który|która|które|ale|już|tylko|może|można|będzie|także|jeśli|gdy|żeby|bardzo|więcej|swoją|twoja|twoje|naszą|nasze)(?!\p{L})~u', $plain);
        // Polish letters: names (Stanisław, Śląsk) carry one or two, a Polish sentence many.
        $n += (int) floor(preg_match_all('~[ąćęłńśźż]~u', $plain) / 3);
        return $n;
    }

    // ------------------------------------------------------------------ slugs

    /**
     * English URL slug for a page title, plus English slugs for parent path segments with no English route yet.
     * Returns ['slug' => string, 'segments' => [pl => en]] (empty slug on failure).
     */
    public static function slug(string $title_pl, string $title_en, string $slug_pl, array $segments): array {
        $schema = [
            'type' => 'object',
            'properties' => [
                'slug' => ['type' => 'string'],
                'segments' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => ['pl' => ['type' => 'string'], 'en' => ['type' => 'string']],
                        'required' => ['pl', 'en'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            'required' => ['slug', 'segments'],
            'additionalProperties' => false,
        ];
        $prompt = 'You create English URL slugs for the English version of zaprojektowani.com (premium web design and branding studio). '
            . 'Slug rules: lowercase ASCII words joined with hyphens, 3 to 7 words, no stop words unless needed for meaning, keep the main search keyword of the title, no dates, no "zaprojektowani". '
            . 'Also translate every Polish path segment listed in "segments" into a short English slug the same way (one entry per segment, "pl" copied exactly).';
        $res = OpenAI::json([
            ['role' => 'system', 'content' => $prompt],
            ['role' => 'user', 'content' => (string) wp_json_encode(['title_pl' => $title_pl, 'title_en' => $title_en, 'slug_pl' => $slug_pl, 'segments' => array_values($segments)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
        ], 'slug', $schema, 1500);
        if ($res['in'] || $res['out']) { Log::count(0, (int) $res['in'], (int) $res['out']); }
        $out = ['slug' => '', 'segments' => []];
        if (!$res['ok']) { return $out; }
        $out['slug'] = self::sanitize_slug((string) ($res['data']['slug'] ?? ''));
        foreach ((array) ($res['data']['segments'] ?? []) as $s) {
            if (!is_array($s) || !isset($s['pl'], $s['en']) || !in_array((string) $s['pl'], $segments, true)) { continue; }
            $en = self::sanitize_slug((string) $s['en']);
            if ($en !== '') { $out['segments'][(string) $s['pl']] = $en; }
        }
        return $out;
    }

    public static function sanitize_slug(string $s): string {
        $s = sanitize_title(remove_accents($s));
        $s = preg_replace('~[^a-z0-9-]+~', '-', strtolower($s)) ?? '';
        $s = trim(preg_replace('~-{2,}~', '-', $s) ?? '', '-');
        if (strlen($s) > 80) { $s = rtrim(substr($s, 0, 80), '-'); $s = preg_replace('~-[^-]*$~', '', $s) ?: $s; }
        return $s;
    }

    /** Slug from an English title without asking the model (fallback). */
    public static function slug_from_title(string $title_en): string {
        $t = preg_replace('~\s*[|–—-]\s*Zaprojektowani.*$~iu', '', $title_en) ?? $title_en;
        $words = preg_split('~\s+~', strtolower(remove_accents($t))) ?: [];
        $stop = ['a', 'an', 'the', 'and', 'or', 'of', 'for', 'to', 'in', 'on', 'with', 'your', 'our', 'is', 'are', 'it', 'that', 'this'];
        $keep = array_values(array_filter($words, static function ($w) use ($stop) { return $w !== '' && !in_array(trim($w, '?!.,:;'), $stop, true); }));
        return self::sanitize_slug(implode('-', array_slice($keep, 0, 7)));
    }
}
