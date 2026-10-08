<?php
/**
 * ZP Suite 2.9.3 — faster pages with the same look.
 *
 * Two things made phones recompute the styles of the whole page over and over while it loads
 * (100–300 ms each time), so the page stayed busy for seconds after it appeared:
 * - over many versions the plugin started printing about 70 <style> blocks inside <body>
 *   (front fixes, sections, footer); each one the browser meets while it reads the page restyles
 *   everything above it. Those after the page's first heading are gathered in two places, in the
 *   same order (see zp_speed_process);
 * - about a hundred rules read "body:has(X) ...", which make the browser restyle the whole page
 *   whenever anything is added to it. They become an equivalent check of an attribute of <body>
 *   (see zp_speed_rewrite_has).
 * The CSS, its order, the strength of every rule and the rest of the HTML are unchanged, so the
 * page looks and animates the same; it only gets there sooner.
 *
 * It also remembers which media file each image URL belongs to (see below), so pages need
 * about 45 fewer database queries.
 *
 * Not in this file (2.9.3): the focus rules "[class^="zp"] *:focus" (stability-design-170.php,
 * the hard-final-2206 block in zaprojektowani-suite.php, footer.css) and the header rules
 * "[class*="HeaderLine"] *" made Chrome restyle the whole page whenever any element's class
 * changed: opening or closing the mobile menu (zpNewNav-lock on <html> and <body>) or scrolling
 * past the top (zp-nav-scrolled on <body>), about 0.3 s each on a phone. The focus rules now end
 * in "*:where(<the focusable elements>):focus", and each list also has "body *:not(<the same
 * list>):focus" for anything else that can take focus (scroll boxes: focused from a script, by a
 * click on an empty part, or with Tab when they hold no links or buttons), with the same rule
 * strength and no class condition. The header rule names the legacy line classes
 * (".zpHeaderLine2147 *", ...). Inside zp containers the rules match the same elements as before,
 * with the same rule strength, but the browser restyles only those elements. Outside them the
 * :not() part adds only focused scroll boxes; of 46 pages checked, one has such a box (the
 * /realizacje/ filter bar on phones), and Chrome lets neither Tab nor a tap focus it.
 * Keep new rules in that form: an attribute selector on class followed by "*" or a bare
 * pseudo-class brings the cost back.
 * Keep the "*" before ":where" and ":not": the home page's inline CSS minifier
 * (seobility-fixes-753.php) drops the space before any ":", which would turn "X :where(...)" into
 * "X:where(...)".
 *
 * Off switch: ZP Suite → Przyspieszenie. One page view without it, to compare: add ?zp_speed=0.
 */
if (!defined('ABSPATH')) { exit; }

define('ZP_SPEED_VERSION', '2.9.9');

function zp_speed_enabled(): bool {
  return get_option('zp_speed_off') !== '1';
}

/*
 * The buffer starts while the plugin file loads, before any other buffer of the suite
 * (the language module starts its own on plugins_loaded), so it receives the final HTML
 * after every other change has been made.
 */
if (
  zp_speed_enabled()
  && !is_admin()
  && !(defined('WP_CLI') && WP_CLI)
  && !wp_doing_ajax()
  && !wp_doing_cron()
  && !(defined('XMLRPC_REQUEST') && XMLRPC_REQUEST)
  && in_array(isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string) $_SERVER['REQUEST_METHOD']) : 'GET', ['GET', 'HEAD'], true)
) {
  ob_start('zp_speed_buffer');
}

function zp_speed_buffer($chunk, $phase = 0) {
  static $buffer = '';
  if (!is_string($chunk)) { return $chunk; }
  if ($phase & PHP_OUTPUT_HANDLER_CLEAN) { $buffer = ''; return ''; }
  $buffer .= $chunk;
  if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) { return ''; }
  $html = $buffer;
  $buffer = '';
  try {
    if (function_exists('zp_suite_quality_html')) { $html = zp_suite_quality_html($html); }
    $out = zp_speed_should_process($html) ? zp_speed_process($html) : null;
  } catch (\Throwable $e) {
    $out = null;
  }
  // ?zp_timing=1 (includes/speed-server.php): sent from here, the outermost buffer, so it covers every HTML filter.
  if (function_exists('zp_speed_timing_header') && !headers_sent()) {
    $timing = zp_speed_timing_header();
    if ($timing !== '') { header('Server-Timing: ' . $timing); }
  }
  return is_string($out) ? $out : $html;
}

function zp_speed_should_process(string $html): bool {
  if (strlen($html) < 500 || stripos($html, '</head') === false || stripos($html, '<body') === false) { return false; }
  if (isset($_GET['zp_speed']) && (string) $_GET['zp_speed'] === '0') { return false; }
  if (defined('REST_REQUEST') && REST_REQUEST) { return false; }
  if (function_exists('is_feed') && is_feed()) { return false; }
  if (function_exists('is_customize_preview') && is_customize_preview()) { return false; }
  if (isset($_GET['elementor-preview']) || isset($_GET['fl_builder']) || isset($_GET['et_fb'])) { return false; }
  if (isset($GLOBALS['pagenow']) && in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-signup.php', 'wp-activate.php'], true)) { return false; }
  $code = (int) http_response_code();
  if ($code >= 300 && $code < 400) { return false; }
  foreach (headers_list() as $header) {
    if (stripos($header, 'content-type:') === 0 && stripos($header, 'text/html') === false) { return false; }
    if (stripos($header, 'content-encoding:') === 0) { return false; }
  }
  return true;
}

/** End of an HTML start tag that begins at $at (quotes may hide '>'), or -1. */
function zp_speed_tag_end(string $html, int $at): int {
  $len = strlen($html);
  $quote = '';
  for ($i = $at + 1; $i < $len; $i++) {
    $c = $html[$i];
    if ($quote !== '') {
      if ($c === $quote) { $quote = ''; }
      continue;
    }
    if ($c === '"' || $c === "'") {
      // Only a quote that opens an attribute value (after '=') starts a quoted section.
      $j = $i - 1;
      while ($j > $at && ($html[$j] === ' ' || $html[$j] === "\t" || $html[$j] === "\n" || $html[$j] === "\r")) { $j--; }
      if ($html[$j] === '=') { $quote = $c; }
      continue;
    }
    if ($c === '>') { return $i; }
  }
  return -1;
}

/** Position just after the end tag that closes a raw-text element opened before $from, or -1. */
function zp_speed_raw_end(string $html, string $name, int $from): int {
  $len = strlen($html);
  $needle = '</' . $name;
  $pos = $from;
  while (($pos = stripos($html, $needle, $pos)) !== false) {
    $next = $pos + strlen($needle);
    $c = $next < $len ? $html[$next] : '>';
    if ($c === '>' || $c === '/' || ctype_space($c)) {
      $gt = strpos($html, '>', $next);
      return $gt === false ? -1 : $gt + 1;
    }
    $pos = $next;
  }
  return -1;
}

/** Position just after the end tag that closes a (possibly nested) element opened before $from, or -1. */
function zp_speed_nested_end(string $html, string $name, int $from): int {
  $depth = 1;
  $pos = $from;
  $re = '~<(/?)' . preg_quote($name, '~') . '(?=[\s/>])~i';
  while ($depth > 0 && preg_match($re, $html, $m, PREG_OFFSET_CAPTURE, $pos)) {
    $at = $m[0][1];
    $gt = zp_speed_tag_end($html, $at);
    if ($gt < 0) { return -1; }
    if ($m[1][0] === '/') {
      $depth--;
    } elseif ($html[$gt - 1] !== '/') {
      $depth++;
    }
    $pos = $gt + 1;
  }
  return $depth === 0 ? $pos : -1;
}

/**
 * One pass over the page. Finds every <style> element, the stylesheet links printed in <body>
 * and the stretches that are text rather than markup (comments, scripts, styles, text areas,
 * templates, noscript, iframes). Styles inside <svg>, <math>, <template>, <noscript>, scripts,
 * comments and text areas are not page styles and are left alone. Returns null when the page is
 * not a normal HTML document.
 */
function zp_speed_scan(string $html): ?array {
  $len = strlen($html);
  $head_end = -1;
  $body_tag = -1;  // the <body ...> start tag
  $body_open = -1; // just after it
  $styles = [];    // [start, end, in body]
  $links = [];     // [start, end] of stylesheet links in <body>
  $sheet_links = []; // stylesheet links in both head and body, for scoped asset checks
  $skip = [];      // [start, end] of text that is not markup, in order
  $pos = 0;
  $re = '~<!--|<(script|style|textarea|title|xmp|noscript|noembed|noframes|iframe|template|svg|math|link|body)(?=[\s/>])|</head\s*>~i';
  while ($pos < $len && preg_match($re, $html, $m, PREG_OFFSET_CAPTURE, $pos)) {
    $at = $m[0][1];
    $token = strtolower($m[0][0]);
    if ($token === '<!--') {
      $close = strpos($html, '-->', $at + 4);
      if ($close === false) { return null; }
      $skip[] = [$at, $close + 3];
      $pos = $close + 3;
      continue;
    }
    if (strpos($token, '</head') === 0) {
      if ($head_end < 0) { $head_end = $at; }
      $pos = $at + strlen($m[0][0]);
      continue;
    }
    $name = strtolower($m[1][0]);
    $gt = zp_speed_tag_end($html, $at);
    if ($gt < 0) { return null; }
    if ($name === 'body') {
      if ($head_end >= 0 && $body_open < 0) { $body_tag = $at; $body_open = $gt + 1; }
      $pos = $gt + 1;
      continue;
    }
    if ($name === 'link') {
      $tag = substr($html, $at, $gt + 1 - $at);
      if (preg_match('~\srel\s*=\s*(["\']?)[^"\'>]*\bstylesheet\b~i', $tag)) {
        $sheet_links[] = [$at, $gt + 1];
        if ($body_open >= 0) { $links[] = [$at, $gt + 1]; }
      }
      $pos = $gt + 1;
      continue;
    }
    if ($html[$gt - 1] === '/' && in_array($name, ['svg', 'math'], true)) { // self-closing foreign element
      $pos = $gt + 1;
      continue;
    }
    if (in_array($name, ['template', 'svg', 'math'], true)) {
      $end = zp_speed_nested_end($html, $name, $gt + 1);
    } else {
      $end = zp_speed_raw_end($html, $name, $gt + 1);
    }
    if ($end < 0) { return null; }
    if ($name === 'style') {
      $styles[] = [$at, $end, $body_open >= 0];
    }
    if ($name !== 'svg' && $name !== 'math') { // elements inside <svg> are part of the page
      $skip[] = [$at, $end];
    }
    $pos = $end;
  }
  if ($body_open < 0) { return null; }
  return ['body_tag' => $body_tag, 'body_open' => $body_open, 'styles' => $styles, 'links' => $links, 'sheet_links' => $sheet_links, 'skip' => $skip];
}

/** Whether $pos falls in one of the sorted, separate [start, end) stretches. */
function zp_speed_in_skip(array $skip, int $pos): bool {
  $lo = 0;
  $hi = count($skip) - 1;
  while ($lo <= $hi) {
    $mid = ($lo + $hi) >> 1;
    if ($pos < $skip[$mid][0]) {
      $hi = $mid - 1;
    } elseif ($pos >= $skip[$mid][1]) {
      $lo = $mid + 1;
    } else {
      return true;
    }
  }
  return false;
}

/**
 * These three legacy patches were printed on every page (head and footer), although
 * every selector requires one of the components below. Check the final rendered DOM,
 * rather than URL or post_content: it also covers Elementor, reusable shortcodes and EN.
 * Keep all original copies and their cascade positions if any target is present.
 * Only registered, static component styles are eligible; arbitrary CSS is untouched.
 */
function zp_speed_unused_styles(string $html, array $scan): array {
  $rules = [
    'zp-suite-front-fixes-303-faq-refinement' => ['front-fixes-303', ['.zpFaqPage']],
    'zp-suite-front-fixes-304-contact-card' => ['front-fixes-304', ['.zpSSSignature', '#zpShowcaseServices', '.zpShowcaseServices']],
    'zp-suite-front-fixes-305-brand-case-art' => ['front-fixes-305', ['#zp-logo-branding-katowice']],
  ];
  $present = [];
  $unused = [];
  foreach ($scan['sheet_links'] as $link) {
    $tag = substr($html, $link[0], $link[1] - $link[0]);
    if (!preg_match('~\sid\s*=\s*(["\'])([^"\']+)\1~i', $tag, $id) || !isset($rules[$id[2]])) { continue; }
    $rule = $rules[$id[2]];
    // Match the expected plugin-owned file too, so a reused id cannot remove other CSS.
    if (!preg_match('~\shref\s*=\s*(["\'])[^"\']*/assets/css/' . preg_quote($rule[0], '~') . '(?:\.min)?\.css(?:\?[^"\']*)?\1~i', $tag)) { continue; }
    if (!isset($present[$id[2]])) {
      $present[$id[2]] = false;
      foreach ($rule[1] as $selector) {
        if (zp_speed_matching_tags($html, $scan, $selector, $scan['body_tag'], strlen($html))) {
          $present[$id[2]] = true;
          break;
        }
      }
    }
    if (!$present[$id[2]]) { $unused[$link[0]] = $link; }
  }
  return $unused;
}

/**
 * Builds the page. Styles printed in <body> before the page's first heading (<h1>) stay where they
 * are: they style the top of the page, which should appear as early as before, and restyling the
 * little above them is cheap. The other styles before the first stylesheet link in <body> are
 * gathered at the place of the first of them, and that link with every style and link after it at
 * the link's place, all in their order. A stylesheet link holds back the painting of everything
 * after it until the file arrives, so the links stay where the first one was. Nothing moves later
 * than it was, every style stays after everything in <head> (also after styles that scripts add
 * to <head> later, e.g. a cookie banner) and the order of the styles among themselves is the same,
 * so the CSS cascade is exactly the same; only the content no longer has style blocks between it.
 * The body:has() rules are rewritten as described below.
 */
function zp_speed_process(string $html): ?string {
  $scan = zp_speed_scan($html);
  if ($scan === null) { return null; }
  $body_open = $scan['body_open'];
  $has = zp_speed_has_plan($html, $scan);
  $unused_styles = zp_speed_unused_styles($html, $scan);

  $moves = []; // [start, end, 'style' | 'link'] in <body>, in order
  foreach ($scan['styles'] as $style) {
    if ($style[2]) { $moves[] = [$style[0], $style[1], 'style']; }
  }
  foreach ($scan['links'] as $link) {
    if (!isset($unused_styles[$link[0]])) { $moves[] = [$link[0], $link[1], 'link']; }
  }
  usort($moves, function ($a, $b) { return $a[0] <=> $b[0]; });
  $first_link = count($moves);
  foreach ($moves as $i => $move) {
    if ($move[2] === 'link') { $first_link = $i; break; }
  }
  // The styles before the first heading stay (see above); the gathering starts after it.
  $h1 = -1;
  $pos = $body_open;
  while (preg_match('~<h1(?=[\s>])~i', $html, $hm, PREG_OFFSET_CAPTURE, $pos)) {
    if (!zp_speed_in_skip($scan['skip'], $hm[0][1])) { $h1 = $hm[0][1]; break; }
    $pos = $hm[0][1] + 3;
  }
  $keep = 0;
  while ($h1 >= 0 && $keep < $first_link && $moves[$keep][0] < $h1) { $keep++; }
  $moves = array_slice($moves, $keep);
  $first_link -= $keep;
  if ($first_link === 0 && count($moves) < 2) { $moves = []; } // nothing worth moving

  $text = function (array $el) use ($html, $has) {
    $part = substr($html, $el[0], $el[1] - $el[0]);
    return $el[2] === 'style' ? zp_speed_rewrite_has($part, $has) : $part;
  };
  $ops = []; // [start, end, replacement]
  foreach ($unused_styles as $link) { $ops[] = [$link[0], $link[1], '']; }
  $moved = [];
  foreach ($moves as $move) { $moved[$move[0]] = true; }
  foreach ($scan['styles'] as $style) {
    if (!isset($moved[$style[0]])) { // a style that stays where it is
      $new = zp_speed_rewrite_has(substr($html, $style[0], $style[1] - $style[0]), $has);
      if ($new !== substr($html, $style[0], $style[1] - $style[0])) { $ops[] = [$style[0], $style[1], $new]; }
    }
  }
  if ($has['present']) {
    $gt = $body_open - 1;
    if ($html[$gt - 1] === '/') { $gt--; }
    $ops[] = [$gt, $gt, ' data-zp-has="' . implode(' ', $has['present']) . '"'];
  }
  $early = [];
  $late = [];
  foreach ($moves as $i => $move) {
    if ($i < $first_link) { $early[] = $text($move); } else { $late[] = $text($move); }
  }
  $ops[] = [$body_open, $body_open, '<!-- zp-speed ' . ZP_SPEED_VERSION . ': ' . $keep . '+' . count($early) . '+' . count($late) . ' -->'];
  foreach ($moves as $i => $move) {
    $put = '';
    if ($i === 0 && $early) { $put = implode('', $early); }
    if ($i === $first_link) { $put = implode('', $late); }
    $ops[] = [$move[0], $move[1], $put];
  }
  foreach (zp_speed_unused_font_preloads($html, $scan) as $op) { $ops[] = $op; }
  usort($ops, function ($a, $b) { return $a[0] <=> $b[0] ?: $a[1] <=> $b[1]; });
  $out = '';
  $last = 0;
  foreach ($ops as $op) {
    if ($op[0] < $last) { return null; } // overlapping changes: never expected, keep the page as it is
    $out .= substr($html, $last, $op[0] - $last) . $op[2];
    $last = $op[1];
  }
  return $out . substr($html, $last);
}

/*
 * body:has() rules. About a hundred rules of the plugin's CSS read "body:has(X) ...", where X is
 * an element that marks a kind of page (the About page, the shop sections, the Katowice hero).
 * With such a rule the browser checks the whole page again and restyles every element whenever
 * anything is added to the page: every bit of text and image while the page loads and every icon
 * a script draws. On a phone that is the largest single cost after loading (100–200 ms each time).
 * Whether X is on the page is known before the page is sent and scripts never add or remove these
 * elements, so <body> gets data-zp-has with a short code for each X that is on the page, and the
 * rule becomes body:where([data-zp-has~="code"]) with a :not() that keeps the rule exactly as
 * strong as before, so the same rules win. Browsers too old for :has() skipped these rules and
 * still do: a selector they do not know (zph-0:has(zph-0), which never matches) is added to the
 * rule. Only rules whose X is plain #id / .class selectors are rewritten; anything else is kept.
 */
define('ZP_SPEED_HAS_RE', '~(?<![\w.#\[:\\\\-])(body(?:[.#][\w-]+|\[[^\]"\'()]*\])*):has\(([^()]*)\)~i');

function zp_speed_has_arg(string $arg): ?string {
  $arg = trim(preg_replace('~\s+~', ' ', $arg));
  return preg_match('~^[.#][\w-]+(?:[.#][\w-]+)*(?: [.#][\w-]+(?:[.#][\w-]+)*)*$~', $arg) ? $arg : null;
}

function zp_speed_has_plan(string $html, array $scan): array {
  $plan = ['codes' => [], 'present' => []];
  if (stripos(substr($html, $scan['body_tag'], $scan['body_open'] - $scan['body_tag']), 'data-zp-has') !== false) { return $plan; }
  foreach ($scan['styles'] as $style) {
    $css = substr($html, $style[0], $style[1] - $style[0]);
    if (stripos($css, ':has(') === false || !preg_match_all(ZP_SPEED_HAS_RE, $css, $m)) { continue; }
    foreach ($m[2] as $arg) {
      $arg = zp_speed_has_arg($arg);
      if ($arg !== null && !isset($plan['codes'][$arg])) {
        $plan['codes'][$arg] = 'h' . substr(md5($arg), 0, 8);
        if (zp_speed_selector_present($html, $scan, explode(' ', $arg), 0, $scan['body_open'], strlen($html))) {
          $plan['present'][] = $plan['codes'][$arg];
        }
      }
    }
  }
  return $plan;
}

/** Whether an element matching the descendant chain $parts (from $i) starts between $from and $to. */
function zp_speed_selector_present(string $html, array $scan, array $parts, int $i, int $from, int $to): bool {
  foreach (zp_speed_matching_tags($html, $scan, $parts[$i], $from, $to) as $tag) {
    if ($i === count($parts) - 1) { return true; }
    if (in_array($tag[2], ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'source', 'track', 'wbr'], true)) { continue; }
    $end = zp_speed_nested_end($html, $tag[2], $tag[1] + 1);
    if ($end > 0 && zp_speed_selector_present($html, $scan, $parts, $i + 1, $tag[1] + 1, min($end, $to))) { return true; }
  }
  return false;
}

/** Start tags between $from and $to that match a compound of #id / .class selectors: [start, end, name]. */
function zp_speed_matching_tags(string $html, array $scan, string $compound, int $from, int $to): array {
  preg_match_all('~([.#])([\w-]+)~', $compound, $m, PREG_SET_ORDER);
  $id = null;
  $classes = [];
  foreach ($m as $sel) {
    if ($sel[1] === '#') { $id = $id === null || $id === $sel[2] ? $sel[2] : "\0"; } else { $classes[] = $sel[2]; }
  }
  $needle = $id !== null ? $id : $classes[0];
  $len = strlen($html);
  $found = [];
  $seen = [];
  $pos = $from;
  while (($pos = strpos($html, $needle, $pos)) !== false && $pos < $to) {
    $at = $pos;
    $pos += strlen($needle);
    if ($at === 0 || zp_speed_in_skip($scan['skip'], $at)) { continue; }
    $lt = strrpos($html, '<', $at - $len - 1);
    if ($lt === false || $lt < $from || isset($seen[$lt]) || !preg_match('~\G<([a-zA-Z][a-zA-Z0-9-]*)~', $html, $tm, 0, $lt)) { continue; }
    $gt = zp_speed_tag_end($html, $lt);
    if ($gt < $at) { continue; }
    $seen[$lt] = true;
    $attrs = [];
    preg_match_all('~[\s/]([^\s=/>"\']+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?~', substr($html, $lt + strlen($tm[0]), $gt - $lt - strlen($tm[0])), $am, PREG_SET_ORDER);
    foreach ($am as $a) {
      $key = strtolower($a[1]);
      if (!isset($attrs[$key])) { $attrs[$key] = ($a[2] ?? '') . ($a[3] ?? '') . ($a[4] ?? ''); }
    }
    if ($id !== null && ($attrs['id'] ?? null) !== $id) { continue; }
    $list = preg_split('~[ \t\n\f\r]+~', $attrs['class'] ?? '', -1, PREG_SPLIT_NO_EMPTY);
    if (array_diff($classes, $list)) { continue; }
    $found[] = [$lt, $gt, strtolower($tm[1])];
  }
  return $found;
}

/** Rewrites the body:has() rules of one <style> element (see above). */
function zp_speed_rewrite_has(string $css, array $plan): string {
  if (!$plan['codes'] || stripos($css, ':has(') === false || !preg_match_all(ZP_SPEED_HAS_RE, $css, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) { return $css; }
  $len = strlen($css);
  $ops = [];
  $marked = [];
  foreach ($m as $hit) {
    $at = $hit[0][1];
    $end = $at + strlen($hit[0][0]);
    $arg = zp_speed_has_arg($hit[2][0]);
    if ($arg === null || !isset($plan['codes'][$arg]) || $at === 0) { continue; }
    // Only a selector of a style rule: followed by its "{", not inside a comment, a declaration,
    // an at-rule such as @supports, or brackets such as :is().
    $brace = strpos($css, '{', $end);
    $semi = strpos($css, ';', $end);
    $close = strpos($css, '}', $end);
    if ($brace === false || ($semi !== false && $semi < $brace) || ($close !== false && $close < $brace)) { continue; }
    $start = 0;
    foreach (['{', '}', ';'] as $c) {
      $p = strrpos($css, $c, $at - $len - 1);
      if ($p !== false && $p + 1 > $start) { $start = $p + 1; }
    }
    $open = strrpos($css, '/*', $at - $len - 1);
    $shut = strrpos($css, '*/', $at - $len - 1);
    if ($open !== false && ($shut === false || $shut < $open)) { continue; }
    $prelude = substr($css, $start, $at - $start);
    if ((ltrim(preg_replace('~/\*.*?\*/~s', '', $prelude))[0] ?? '') === '@' || substr_count($prelude, '(') !== substr_count($prelude, ')')) { continue; }
    $filler = str_repeat('#zph-0', substr_count($arg, '#')) . str_repeat('.zph-0', substr_count($arg, '.'));
    $ops[] = [$at, $end, $hit[1][0] . ':where([data-zp-has~="' . $plan['codes'][$arg] . '"]):not(' . $filler . ')'];
    if (!isset($marked[$brace])) {
      $marked[$brace] = true;
      $ops[] = [$brace, $brace, ',zph-0:has(zph-0)'];
    }
  }
  if (!$ops) { return $css; }
  usort($ops, function ($a, $b) { return $a[0] <=> $b[0] ?: $a[1] <=> $b[1]; });
  $out = '';
  $last = 0;
  foreach ($ops as $op) {
    if ($op[0] < $last) { return $css; }
    $out .= substr($css, $last, $op[0] - $last) . $op[2];
    $last = $op[1];
  }
  return $out . substr($css, $last);
}

/*
 * 2.9.9 — font preloads that a page never uses. Every page preloads the regular (400) Jakarta file
 * (optimizer.php), but the legal pages and the FAQ (PL and EN) do not declare that face at all
 * (their text at 400 is drawn with the 500 file), so Chrome warned there that a preloaded font was
 * not used. The preload is left out only when nothing on the page names the file: no other mention
 * in the page and none in any stylesheet the page names (read from disk). When a stylesheet cannot
 * be read, is on another host or imports another one, the preload stays. Pages that use the face
 * keep it, so no text changes.
 */
function zp_speed_unused_font_preloads(string $html, array $scan): array {
  $ops = [];
  foreach (['plus-jakarta-sans-v12-latin_latin-ext-regular.woff2'] as $file) {
    if (strpos($html, $file) === false) { continue; }
    $tags = [];
    $in_tags = 0;
    if (!preg_match_all('~<link\b[^>]*>~i', $html, $m, PREG_OFFSET_CAPTURE)) { continue; }
    foreach ($m[0] as $tag) {
      if (strpos($tag[0], $file) === false) { continue; }
      if (!preg_match('~\srel\s*=\s*(["\']?)preload\1[\s/>]~i', $tag[0]) || zp_speed_in_skip($scan['skip'], $tag[1])) { continue 2; }
      $tags[] = $tag;
      $in_tags += substr_count($tag[0], $file);
    }
    if (!$tags || substr_count($html, $file) > $in_tags || zp_speed_css_names($html, $file)) { continue; }
    foreach ($tags as $tag) {
      $end = $tag[1] + strlen($tag[0]);
      if (substr($html, $end, 1) === "\n") { $end++; }
      $ops[] = [$tag[1], $end, ''];
    }
  }
  return $ops;
}

/** Whether a stylesheet the page names mentions $needle (true also when one cannot be checked). */
function zp_speed_css_names(string $html, string $needle): bool {
  static $seen = [];
  if (!preg_match_all('~[^"\'\s<>()=,]+\.css(?:\?[^"\'\s<>()]*)?(?=["\'\s<>()]|$)~i', $html, $m)) { return false; }
  foreach (array_unique($m[0]) as $url) {
    $plain = html_entity_decode(str_replace('\\/', '/', $url), ENT_QUOTES);
    if (!preg_match('~^(?:https?:)?//|^/~i', $plain)) {
      // A relative name (e.g. in a CSS comment) counts only as the address of a link.
      if (preg_match('~\b(?:href|src)\s*=\s*["\']?' . preg_quote($url, '~') . '~i', $html)) { return true; }
      continue;
    }
    $file = zp_speed_local_file($plain);
    if ($file === '') { return true; }
    if (!isset($seen[$file])) {
      $css = @file_get_contents($file);
      $seen[$file] = $css === false || stripos($css, '@import') !== false ? '' : $css;
    }
    if ($seen[$file] === '' || strpos($seen[$file], $needle) !== false) { return true; }
  }
  return false;
}

/** The file on this server behind a URL of this site, or '' (another host, not a file). */
function zp_speed_local_file(string $url): string {
  $home = (string) home_url('/');
  if (preg_match('~^(?:https?:)?//([^/?#]+)(/[^?#]*)?~i', $url, $u)) {
    if (!preg_match('~^(?:https?:)?//([^/?#]+)~i', $home, $h) || strcasecmp($u[1], $h[1]) !== 0) { return ''; }
    $path = isset($u[2]) ? $u[2] : '/';
  } elseif (preg_match('~^/(?!/)[^?#]*~', $url, $u)) {
    $path = $u[0];
  } else {
    return '';
  }
  $path = rawurldecode($path);
  if (strpos($path, '..') !== false || strpos($path, "\0") !== false) { return ''; }
  foreach ([[content_url('/'), WP_CONTENT_DIR . '/'], [site_url('/'), ABSPATH]] as $map) {
    $base = (string) wp_parse_url($map[0], PHP_URL_PATH);
    if ($base !== '' && strpos($path, $base) === 0) {
      $file = $map[1] . substr($path, strlen($base));
      return is_file($file) ? $file : '';
    }
  }
  return '';
}

/*
 * Image lookups. For every image on a page the optimizer asks WordPress which media file the URL
 * belongs to (to add width, height and srcset): one database query per image, about 45 on the home
 * page. The answers are kept in one option and cleared whenever a media file is added, edited or
 * deleted, and once a week, so the page HTML stays exactly the same.
 */
function zp_speed_attachment_cache(): array {
  if (!isset($GLOBALS['zp_speed_att']) || !is_array($GLOBALS['zp_speed_att'])) {
    $cache = get_option('zp_speed_att_ids');
    if (!is_array($cache) || (int) ($cache['_t'] ?? 0) < time() - WEEK_IN_SECONDS || count($cache) > 3000) {
      $cache = ['_t' => time()];
    }
    $GLOBALS['zp_speed_att'] = $cache;
    $GLOBALS['zp_speed_att_dirty'] = false;
  }
  return $GLOBALS['zp_speed_att'];
}

add_filter('pre_attachment_url_to_postid', function ($post_id, $url) {
  if ($post_id !== null || is_admin() || !zp_speed_enabled()) { return $post_id; }
  $cache = zp_speed_attachment_cache();
  $key = 'u' . md5((string) $url);
  return array_key_exists($key, $cache) ? (int) $cache[$key] : null;
}, 10, 2);

add_filter('attachment_url_to_postid', function ($post_id, $url) {
  if (is_admin() || !zp_speed_enabled()) { return $post_id; }
  $cache = zp_speed_attachment_cache();
  $key = 'u' . md5((string) $url);
  if (!array_key_exists($key, $cache) || $cache[$key] !== (int) $post_id) {
    $GLOBALS['zp_speed_att'][$key] = (int) $post_id;
    $GLOBALS['zp_speed_att_dirty'] = true;
  }
  return $post_id;
}, 9999, 2);

add_action('shutdown', function () {
  if (!empty($GLOBALS['zp_speed_att_dirty']) && is_array($GLOBALS['zp_speed_att'] ?? null)) {
    update_option('zp_speed_att_ids', $GLOBALS['zp_speed_att'], false);
  }
}, 50);

function zp_speed_attachment_cache_clear() {
  delete_option('zp_speed_att_ids');
  $GLOBALS['zp_speed_att'] = null;
  $GLOBALS['zp_speed_att_dirty'] = false;
}
add_action('add_attachment', 'zp_speed_attachment_cache_clear');
add_action('edit_attachment', 'zp_speed_attachment_cache_clear');
add_action('delete_attachment', 'zp_speed_attachment_cache_clear');

/* ZP Suite → Przyspieszenie: the off switch. After the menu clean-up (admin-menu-consolidation-186.php, 9999), which removes
 * other items; not called "Szybkość", because front-fixes-183.php removes every item with that word or "Statystyki". */
add_action('admin_menu', function () {
  add_submenu_page('zp-suite', 'Przyspieszenie strony', 'Przyspieszenie', 'manage_options', 'zp-suite-speed', 'zp_speed_render_admin_page');
}, 10000);

add_action('admin_post_zp_speed_save', function () {
  if (!current_user_can('manage_options')) { wp_die('Brak uprawnień.'); }
  check_admin_referer('zp_speed_save');
  update_option('zp_speed_off', empty($_POST['zp_speed_on']) ? '1' : '0', false);
  wp_safe_redirect(add_query_arg(['page' => 'zp-suite-speed', 'saved' => '1'], admin_url('admin.php')));
  exit;
});

function zp_speed_render_admin_page() {
  if (!current_user_can('manage_options')) { return; }
  $on = zp_speed_enabled();
  ?>
  <div class="wrap">
    <h1>Przyspieszenie strony</h1>
    <?php if (isset($_GET['saved'])) : ?><div class="notice notice-success"><p>Zapisano.</p></div><?php endif; ?>
    <p>Style, które wtyczka wypisuje w środku treści strony (poniżej głównego nagłówka), są zebrane w dwóch miejscach w tej samej kolejności, a reguły „body:has()” sprawdzają gotowy znacznik na &lt;body&gt;. Dzięki temu telefon nie przelicza wyglądu całej strony kilkadziesiąt razy w trakcie wczytywania. Wygląd i animacje się nie zmieniają.</p>
    <p>Żeby porównać jedną stronę bez przyspieszenia, dopisz do jej adresu <code>?zp_speed=0</code>.</p>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <input type="hidden" name="action" value="zp_speed_save">
      <?php wp_nonce_field('zp_speed_save'); ?>
      <label><input type="checkbox" name="zp_speed_on" value="1" <?php checked($on); ?>> Przyspieszenie włączone</label>
      <?php submit_button('Zapisz'); ?>
    </form>
  </div>
  <?php
}
