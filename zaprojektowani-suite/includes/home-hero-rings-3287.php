<?php
/**
 * 3.2.87: keep desktop rings immediate; attach mobile rings after the load event.
 * Only the reviewed ring pair is made inert temporarily; hero content, poster,
 * scripts, CSS and stage order stay intact. Unknown structure or CSP falls back.
 */
if (!defined('ABSPATH')) { exit; }

function zp_home_hero_rings_3287_html(string $html): string {
    $path = wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if (!in_array($path, ['/', '/en/'], true) || !function_exists('zp_home_first_paint_enabled')
        || !zp_home_first_paint_enabled() || strpos($html, 'id="zp-home-first-paint-3233"') === false
        || strpos($html, 'zp-hero-rings-3287') !== false) {
        return $html;
    }
    try {
        foreach (headers_list() as $header) {
            if (stripos($header, 'Content-Security-Policy:') === 0) { return $html; }
        }
        // This reviewed template establishes absolute, decorative rings.
        // Changed styles must be reviewed before changing their attachment time.
        $root = realpath(ZP_SUITE_PATH);
        $file = ZP_SUITE_PATH . 'templates/home-hero.php';
        $real = realpath($file);
        if (!$root || !$real || is_link($file) || !is_file($real) || !is_readable($real)
            || strpos($real, $root . DIRECTORY_SEPARATOR) !== 0 || !hash_equals(
                '86924cc42b71ac5446929b2875279060ba9a32390f4bce4837fe56dcd5e72bf2',
                (string) hash_file('sha256', $real)
            )) { return $html; }
        $scan = zp_speed_scan($html);
        if (!$scan || $scan['head_end'] < 0) { return $html; }
        // Use the existing quote-aware tag boundary, including a quoted ">".
        if (preg_match_all('~<meta(?=[\s/>])~i', $html, $metas, PREG_OFFSET_CAPTURE)) {
            foreach ($metas[0] as [$token, $at]) {
                if ($at >= $scan['head_end'] || zp_speed_in_skip($scan['skip'], $at)) { continue; }
                $gt = zp_speed_tag_end($html, $at);
                if ($gt < 0) { return $html; }
                $node = substr($html, $at, $gt + 1 - $at);
                $attrs = zp_css_delivery_attributes($node);
                if (strtolower(trim($attrs['http-equiv'] ?? '')) === 'content-security-policy') { return $html; }
                // Duplicate http-equiv values are ambiguous: retain original delivery.
                if (preg_match_all('~\s+([^\s=/>"\']+)(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>"\']+))?~', $node, $am)) {
                    $names = array_map('strtolower', $am[1]);
                    if (count($names) !== count(array_unique($names))) { return $html; }
                }
            }
        }
        $heroes = zp_speed_matching_tags($html, $scan, '#zhHero', $scan['body_open'], strlen($html));
        $stages = zp_speed_matching_tags($html, $scan, '#zhStage', $scan['body_open'], strlen($html));
        if (count($heroes) !== 1 || count($stages) !== 1 || $heroes[0][2] !== 'section' || $stages[0][2] !== 'div') { return $html; }
        [$hero_start, $hero_gt] = $heroes[0];
        $hero_end = zp_speed_nested_end($html, 'section', $hero_gt + 1);
        if ($hero_end < 0 || !preg_match('~</section\s*>$~iD', substr($html, $hero_start, $hero_end - $hero_start), $closing, PREG_OFFSET_CAPTURE)) { return $html; }
        $hero_close = $hero_start + $closing[0][1];

        // The reviewed H1 initializer does not inspect decorative rings.
        // Unknown scripts within this hero require a separate review.
        $scripts = 0;
        foreach ($scan['skip'] as [$a, $b]) {
            if ($a <= $hero_gt || $a >= $hero_close || strncasecmp(substr($html, $a, 7), '<script', 7) !== 0) { continue; }
            $gt = zp_speed_tag_end($html, $a);
            if ($gt < 0 || substr($html, $a, $gt + 1 - $a) !== '<script id="zp-suite-308-hero-letters-js">') { return $html; }
            $tail = substr($html, $gt + 1, $b - $gt - 1);
            if (substr($tail, -9) !== '</script>' || !hash_equals(
                '4184e6092d68410eee761a1dced8ce5d606dffde06c58da027ee63a87c531302',
                hash('sha256', substr($tail, 0, -9))
            )) { return $html; }
            $scripts++;
        }
        if ($scripts !== 1) { return $html; }

        // Walk direct children with existing quote-aware/nested tag helpers.
        // Exact known order rejects a new wrapper, nested stage or unknown block.
        $children = []; $cursor = $hero_gt + 1;
        while ($cursor < $hero_close) {
            if (preg_match('~\G\s+~A', $html, $space, 0, $cursor)) { $cursor += strlen($space[0]); continue; }
            if (substr($html, $cursor, 4) === '<!--') {
                $end = strpos($html, '-->', $cursor + 4);
                if ($end === false || $end + 3 > $hero_close) { return $html; }
                $cursor = $end + 3; continue;
            }
            if (!preg_match('~\G<(div|nav)(?=\s|>)~Ai', $html, $tag, 0, $cursor)) { return $html; }
            $gt = zp_speed_tag_end($html, $cursor);
            if ($gt < 0) { return $html; }
            $attrs = zp_css_delivery_attributes(substr($html, $cursor, $gt + 1 - $cursor));
            $end = zp_speed_nested_end($html, strtolower($tag[1]), $gt + 1);
            if ($end <= $gt || $end > $hero_close) { return $html; }
            $children[] = ['class' => $attrs['class'] ?? '', 'tag' => strtolower($tag[1]), 'start' => $cursor, 'end' => $end];
            $cursor = $end;
        }
        $expected = ['zh__bg','zh__aurora','zh__grid','zh__dust','zh__hair','zh__stage','zh__fade','zh__grain','zh__vig','zh__rail','zh__inner','zh__side','zh__mobileStrip','zh__wm','zh__mobileScroll','zh__scroll','zh__strip'];
        if (array_column($children, 'class') !== $expected) { return $html; }
        foreach ($children as $child) {
            if ($child['tag'] !== ($child['class'] === 'zh__rail' ? 'nav' : 'div')) { return $html; }
        }
        $stage = $children[5];
        if ($stage['start'] !== $stages[0][0]) { return $html; }
        $node = substr($html, $stage['start'], $stage['end'] - $stage['start']);
        if (strlen($node) !== 633 || !hash_equals(
            '8f57b2481ce2c0df6f2a580d68d14bdec9a899e5012b1cf87a0bb5194db7d6ed',
            hash('sha256', $node)
        )) { return $html; }
        $rings = '<div class="zh__ring zh__ring--b"></div>' . "\n"
            . '<div class="zh__ring zh__ring--a"></div>';
        if (substr_count($node, $rings) !== 1) { return $html; }
        $at = $stage['start'] + strpos($node, $rings);
        $template = '<template id="zp-hero-rings-3287">' . $rings . '</template>';
        $script = <<<'JS'
<script id="zp-hero-rings-3287-js">(function(w,d){'use strict';var queued=false;function restore(){var t=d.getElementById('zp-hero-rings-3287');if(t&&t.parentNode&&t.parentNode.id==='zhStage'&&t.content)t.replaceWith(t.content)}function frame(){if(queued)return;queued=true;if(w.requestAnimationFrame)w.requestAnimationFrame(restore);else restore()}var mobile=true;try{if(w.matchMedia)mobile=w.matchMedia('(max-width:1100px)').matches}catch(e){}if(!mobile){restore();return}if(d.readyState==='complete')frame();else w.addEventListener('load',frame,{once:true})})(window,document);</script>
JS;
        $bodyClose = strripos($html, '</body>');
        if ($bodyClose === false || $bodyClose <= $hero_end) { return $html; }
        // Preserve the original pair byte for byte, including its order/spacing.
        // Desktop restores here during parsing. Mobile waits for load and one frame, without font/style getters.
        return substr($html, 0, $at) . $template . $script
            . substr($html, $at + strlen($rings));
    } catch (\Throwable $error) {
        return $html;
    }
}
