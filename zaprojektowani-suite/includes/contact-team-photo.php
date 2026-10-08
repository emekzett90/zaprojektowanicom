<?php
/**
 * Long contact form: a team photo above the contact tiles.
 *
 * The right column of the long form ([zp_contact_page] and [zp_home_contact_form], both built from
 * templates/contact-page/form.html) showed a "Nowa wiadomość" preview of the message being typed.
 * It now shows a cut-out photo of the team with nothing behind it, its lower part fading into the
 * background; phones show only the contact tiles, as before. The photos are the team cut-outs from
 * the media library, as resized copies in assets/img/contact-team/ (transparency kept, 1x and 2x).
 *
 * A page never repeats a photo: an output buffer reads every <img>/<source> of the finished page
 * (hero, sections, footer band) and gives the form a pose that is not used anywhere else on it,
 * chosen from the page address, so pages differ from each other and a page keeps its photo.
 */
if (!defined('ABSPATH')) { exit; }

/**
 * The poses. "same" lists the file names under which the same photo appears elsewhere on the site
 * (media library originals and the plugin's own copies).
 */
function zp_suite_contact_team_pool(): array {
  return [
    'telefon-tablet' => [
      'file' => 'zespol-telefon-tablet', 'w' => 386, 'h' => 480,
      'alt'  => 'Zespół Zaprojektowani podczas kontaktu z klientem',
      'same' => ['team_kontakt', 'team-kontakt', 'team-duo', 'rozmowa-z-klientem-zespol'],
    ],
    'tablet-notes' => [
      'file' => 'zespol-tablet-notes', 'w' => 463, 'h' => 480,
      'alt'  => 'Zespół Zaprojektowani omawia projekt na tablecie',
      'same' => ['wiedza_ekipa', 'wiedza-ekipa', 'team-tablet', 'zespol-przy-tablecie'],
    ],
    'laptop' => [
      'file' => 'zespol-laptop', 'w' => 491, 'h' => 480,
      'alt'  => 'Projektanci Zaprojektowani omawiają projekt przy laptopie',
      'same' => ['team_box', 'trusted-team', 'team-laptop'],
    ],
    'olowek' => [
      'file' => 'zespol-olowek', 'w' => 477, 'h' => 480,
      'alt'  => 'Zespół Zaprojektowani z wielkim ołówkiem – projektowanie stron i logo',
      'same' => ['stronaglown_ekipa_', 'team-branding', 'team-pencil', 'zespol-z-olowkiem'],
    ],
    'rece' => [
      'file' => 'zespol-rece', 'w' => 470, 'h' => 480,
      'alt'  => 'Zespół Zaprojektowani – studio stron internetowych i brandingu z Katowic',
      // team_zaprojektowani_finalnie is the footer band photo (footer.team_photo), present on every page.
      'same' => ['team_hero_homepage', 'team-hero', 'team-trio', 'zespol-studia-przy-marce', 'team_zaprojektowani_finalnie'],
    ],
  ];
}

/** Address of the current page (PL path also on /en/, where the language module rewrites REQUEST_URI). */
function zp_suite_contact_team_page_key(): string {
  $path = (string) wp_parse_url(isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
  return trailingslashit('/' . trim($path, '/'));
}

/**
 * A pose for this page, outside $exclude when possible. The "#8" suffix of the hash key spreads the
 * poses evenly over the pages with the form (24 Polish pages in October 2026: 7, 3, 6 and 8 pages per
 * pose; without it 6, 2, 4 and 12, with the pencil photo on most service pages).
 */
function zp_suite_contact_team_pick(array $exclude, string $key): string {
  $all = array_keys(zp_suite_contact_team_pool());
  $free = array_values(array_diff($all, $exclude));
  if (!$free) { $free = $all; }
  return $free[(int) sprintf('%u', crc32($key . '#8')) % count($free)];
}

function zp_suite_contact_team_img(string $slug): string {
  $pool = zp_suite_contact_team_pool();
  if (!isset($pool[$slug])) { return ''; }
  $p = $pool[$slug];
  $base = ZP_SUITE_URL . 'assets/img/contact-team/' . $p['file'];
  return '<img class="zpContactFormLight__teamImg" src="' . esc_url($base . '-1x.webp') . '"'
    . ' srcset="' . esc_url($base . '-1x.webp') . ' 1x, ' . esc_url($base . '-2x.webp') . ' 2x"'
    . ' width="' . (int) $p['w'] . '" height="' . (int) $p['h'] . '"'
    . ' alt="' . esc_attr($p['alt']) . '" loading="lazy" decoding="async">';
}

function zp_suite_contact_team_figure(string $slug): string {
  return '<figure class="zpContactFormLight__team" data-zp-contact-team="' . esc_attr($slug) . '">'
    . zp_suite_contact_team_img($slug) . '</figure>';
}

function zp_suite_contact_team_css(): string {
  return '<style id="zp-suite-contact-team-css">'
    . 'html body #zpContactFormLight .zpContactFormLight__team{display:flex;align-items:flex-end;justify-content:center;width:100%;height:clamp(300px,calc(100vh - 470px),460px);margin:0;padding:0}'
    // No panel behind the photo: the cut-out stands on the page and its lower part fades out (also on the dark Studio Wyceny band).
    . 'html body #zpContactFormLight .zpContactFormLight__teamImg{display:block;width:100%;height:100%;max-width:none;margin:0;object-fit:contain;object-position:center bottom;-webkit-mask-image:linear-gradient(180deg,#000 0%,#000 62%,transparent 100%);mask-image:linear-gradient(180deg,#000 0%,#000 62%,transparent 100%)}'
    . '@media (max-width:1180px){html body #zpContactFormLight .zpContactFormLight__team{height:clamp(280px,62vh,420px)}}'
    // Phones: only the contact tiles, as before (the "Nowa wiadomość" preview was hidden there too); a hidden lazy image is not downloaded.
    . '@media (max-width:760px){html body #zpContactFormLight .zpContactFormLight__team{display:none!important}}'
    . '</style>';
}

/**
 * Called by the form templates: the marker becomes the photo. The pose chosen here is provisional;
 * the page buffer below replaces it when the same photo is used elsewhere on the page.
 */
function zp_suite_contact_team_fill(string $html): string {
  static $css_done = false;
  $at = strpos($html, '<!-- zp-contact-team');
  if ($at === false) { return $html; }
  $end = strpos($html, '-->', $at);
  if ($end === false) { return $html; }
  $figure = zp_suite_contact_team_figure(zp_suite_contact_team_pick([], zp_suite_contact_team_page_key()));
  $html = substr($html, 0, $at) . $figure . substr($html, $end + 3);
  if (!$css_done) {
    $css_done = true;
    // Next to the form's own <style> blocks (after the aside), where includes/speed-293.php already
    // gathers the page's later styles, so that gathering point stays where it was.
    $style = strpos($html, '<style', $at + strlen($figure));
    $html = $style === false ? $html . zp_suite_contact_team_css() : substr($html, 0, $style) . zp_suite_contact_team_css() . substr($html, $style);
  }
  return $html;
}

/** Poses whose photo appears on the page outside the form's own figure. */
function zp_suite_contact_team_used(string $html): array {
  $urls = '';
  if (preg_match_all('~<(?:img|source)\b[^>]*>~i', $html, $tags)) {
    foreach ($tags[0] as $tag) {
      if (preg_match_all('~\s(?:data-(?:lazy-)?)?(?:src|srcset)\s*=\s*(["\'])(.*?)\1~is', $tag, $m)) {
        $urls .= ' ' . implode(' ', $m[2]);
      }
    }
  }
  $used = [];
  foreach (zp_suite_contact_team_pool() as $slug => $p) {
    foreach (array_merge([$p['file']], $p['same']) as $name) {
      if (stripos($urls, $name) !== false) { $used[] = $slug; break; }
    }
  }
  // Service cards whose photo a script switches to team_kontakt (front-fixes-263/264).
  if (!in_array('telefon-tablet', $used, true) && preg_match('~class=["\'][^"\']*\bzpSSCard__mock--contact\b~', $html)) {
    $used[] = 'telefon-tablet';
  }
  return $used;
}

/** Output buffer: the whole page is collected first (as in includes/speed-293.php), then the photo is set. */
function zp_suite_contact_team_buffer($chunk, $phase = 0) {
  static $buffer = '';
  if (!is_string($chunk)) { return $chunk; }
  if ($phase & PHP_OUTPUT_HANDLER_CLEAN) { $buffer = ''; return ''; }
  $buffer .= $chunk;
  if (!($phase & PHP_OUTPUT_HANDLER_FINAL)) { return ''; }
  $html = $buffer;
  $buffer = '';
  try {
    return zp_suite_contact_team_apply($html);
  } catch (\Throwable $e) {
    return $html;
  }
}

function zp_suite_contact_team_apply(string $html): string {
  if (strpos($html, 'data-zp-contact-team=') === false) { return $html; }
  $rx = '~<figure class="zpContactFormLight__team" data-zp-contact-team="[a-z-]+">.*?</figure>~s';
  $rest = preg_replace($rx, '', $html);
  if (!is_string($rest)) { return $html; }
  $taken = zp_suite_contact_team_used($rest);
  $key = zp_suite_contact_team_page_key();
  $new = preg_replace_callback($rx, static function () use (&$taken, $key) {
    $slug = zp_suite_contact_team_pick($taken, $key);
    $taken[] = $slug;
    return zp_suite_contact_team_figure($slug);
  }, $html);
  return is_string($new) ? $new : $html;
}

// Started on "wp", before every template_redirect buffer of the suite (so it runs after them and sees the
// images they add) and before /kampanie-reklamowe/, which is rendered at template_redirect PHP_INT_MIN;
// after the language module's buffer (plugins_loaded), so the alt text is still translated on /en/.
add_action('wp', function () {
  if (is_admin() || is_feed() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) { return; }
  ob_start('zp_suite_contact_team_buffer');
}, PHP_INT_MIN);

add_filter('zpl_dictionary', function ($map, $lang = 'en') {
  $map = is_array($map) ? $map : [];
  $dict = [
    'Zespół Zaprojektowani i dane kontaktowe' => 'The Zaprojektowani team and contact details',
    'Zespół Zaprojektowani podczas kontaktu z klientem' => 'The Zaprojektowani team talking to a client',
    'Zespół Zaprojektowani omawia projekt na tablecie' => 'The Zaprojektowani team reviewing a project on a tablet',
    'Projektanci Zaprojektowani omawiają projekt przy laptopie' => 'Zaprojektowani designers discussing a project on a laptop',
    'Zespół Zaprojektowani z wielkim ołówkiem – projektowanie stron i logo' => 'The Zaprojektowani team with a giant pencil – website and logo design',
    'Zespół Zaprojektowani – studio stron internetowych i brandingu z Katowic' => 'The Zaprojektowani team – a website and branding studio from Katowice, Poland',
  ];
  foreach ($dict as $pl => $en) {
    $from = $lang === 'pl' ? $en : $pl;
    if (!isset($map[$from])) { $map[$from] = $lang === 'pl' ? $pl : $en; }
  }
  return $map;
}, 10, 2);
