<?php
if (!defined('ABSPATH')) { exit; }

/**
 * SearchFit 14.10.2026, point 9: the service, industry and Katowice pages all shared Rank Math's site-wide image
 * (social_media.png, a 660 KB PNG). Each of them now shares the picture its own hero shows: the same file, whole,
 * on the hero's dark background, as a 1200×630 JPEG under 300 KB. It is drawn once, on the first view that needs
 * it, and kept in uploads/zp-og/; a hero file of another size gets a new image. Where it cannot be drawn (file
 * missing, no GD with WebP, uploads not writable) Rank Math keeps its site-wide image, as before.
 *
 * An /en/ page is rendered from its Polish source, so it shares the same file; the language module translates
 * og:image:alt (Html::META) with the dictionary entry of the hero's own alt.
 */

/** Page path => [hero file, alt]: the picture each page shows first, with the alt it has there. */
function zp_og_hero_sources(): array {
  $laptop = [ZP_SUITE_PATH . 'assets/strony-internetowe/hero-laptop.webp', 'Projekt strony internetowej — laptop z witryną kancelarii'];
  $team = [ZP_SUITE_PATH . 'assets/logo-branding/team-branding.webp', 'Zespół Zaprojektowani tworzący branding'];
  // Files behind the hero addresses in service-heroes.php and templates/shop-katowice/sections/hero.html.
  $uploads = wp_upload_dir(null, false)['basedir'] . '/';
  $sources = [
    '/strony-internetowe-katowice/' => $laptop,
    '/agencja-reklamowa-katowice/' => $laptop,
    '/strona-wizytowka/' => $laptop,
    '/tworzenie-landing-page/' => $laptop,
    '/opieka-wordpress/' => $laptop,
    '/strony-wordpress/' => $laptop,
    '/logo-branding-katowice/' => $team,
    '/projektowanie-logo/' => $team,
    '/identyfikacja-wizualna/' => $team,
    '/tworzenie-stron-internetowych/' => [$uploads . '2026/10/Polerstone-na-skalnej-podstawie.webp', 'Polerstone — projekt strony internetowej na laptopie i skalnej podstawie'],
    '/tworzenie-sklepow-internetowych/' => [$uploads . '2026/10/Swiat-Grili-na-smartfonie-i-laptopie.webp', 'Świat Grilli — sklep internetowy na smartfonie i laptopie'],
    '/sklepy-internetowe-katowice/' => [$uploads . '2026/05/laptop_sklepy_internetowe_katowice-scaled.webp', 'Mockup sklepu internetowego WooCommerce — Zaprojektowani'],
  ];
  foreach (zp_seo_industries() as $key => $industry) {
    $sources[$industry['path']] = [ZP_SUITE_PATH . 'assets/img/branze/' . $key . '-hero.webp', $industry['hero_alt']];
  }
  return $sources;
}

/** Rank Math image data (url, width, height, type, alt) for a page's share image, or null. */
function zp_og_image(string $path): ?array {
  static $images = [];
  if (array_key_exists($path, $images)) { return $images[$path]; }
  $images[$path] = null;
  $source = zp_og_hero_sources()[$path] ?? null;
  $uploads = wp_upload_dir(null, false);
  if ($source === null || !is_file($source[0]) || !empty($uploads['error'])) { return null; }
  // Named after the hero file and its size, so a new hero gets a new image (and shares never show a stale one).
  $name = pathinfo($source[0], PATHINFO_FILENAME) . '-og-' . substr(md5(basename($source[0]) . '|' . filesize($source[0]) . '|1'), 0, 8) . '.jpg';
  $file = $uploads['basedir'] . '/zp-og/' . $name;
  if (!is_file($file)) {
    $failed = 'zp_og_failed_' . md5($name);
    if (get_transient($failed)) { return null; }
    if (!zp_og_make($source[0], $file)) {
      // Not retried on every view; Rank Math's site-wide image stays meanwhile.
      set_transient($failed, 1, 12 * HOUR_IN_SECONDS);
      return null;
    }
  }
  return $images[$path] = ['url' => $uploads['baseurl'] . '/zp-og/' . $name, 'width' => 1200, 'height' => 630, 'type' => 'image/jpeg', 'alt' => $source[1]];
}

/** Draws the share image: the hero picture whole, on the hero's background, saved as a JPEG under 300 KB. */
function zp_og_make(string $source, string $file): bool {
  $info = @getimagesize($source);
  $open = [IMAGETYPE_WEBP => 'imagecreatefromwebp', IMAGETYPE_PNG => 'imagecreatefrompng', IMAGETYPE_JPEG => 'imagecreatefromjpeg'][$info[2] ?? 0] ?? '';
  if ($open === '' || !function_exists($open) || !function_exists('imagejpeg') || !wp_mkdir_p(dirname($file))) { return false; }
  wp_raise_memory_limit('image');
  $picture = @$open($source);
  if (!$picture) { return false; }
  $canvas = imagecreatetruecolor(1200, 630);
  // The hero background: linear-gradient(135deg, #030407, #07111f 48%, #05070b) with its blue glow
  // rgba(28, 71, 122, .42), here centred behind the picture.
  $diagonal = [];
  for ($i = 0; $i <= 1828; $i++) {
    $t = $i / 1828;
    [$from, $to, $k] = $t < .48 ? [[3, 4, 7], [7, 17, 31], $t / .48] : [[7, 17, 31], [5, 7, 11], ($t - .48) / .52];
    $diagonal[$i] = [$from[0] + ($to[0] - $from[0]) * $k, $from[1] + ($to[1] - $from[1]) * $k, $from[2] + ($to[2] - $from[2]) * $k];
  }
  for ($y = 0; $y < 630; $y++) {
    for ($x = 0; $x < 1200; $x++) {
      [$r, $g, $b] = $diagonal[$x + $y];
      $glow = .42 * max(0, 1 - hypot($x - 600, $y - 280) / 560);
      imagesetpixel($canvas, $x, $y, ((int) ($r + (28 - $r) * $glow) << 16) | ((int) ($g + (71 - $g) * $glow) << 8) | (int) ($b + (122 - $b) * $glow));
    }
  }
  // The whole picture, as large as fits, centred and standing on the bottom edge like in the hero.
  $w = imagesx($picture);
  $h = imagesy($picture);
  $scale = min(1200 / $w, 630 / $h, 1);
  $dw = (int) round($w * $scale);
  $dh = (int) round($h * $scale);
  imagecopyresampled($canvas, $picture, intdiv(1200 - $dw, 2), 630 - $dh, 0, 0, $dw, $dh, $w, $h);
  unset($picture);
  // The best quality that stays under 300 KB (these pictures come to about 100–200 KB).
  $jpeg = '';
  foreach ([84, 76, 68, 60] as $quality) {
    ob_start();
    $ok = imagejpeg($canvas, null, $quality);
    $jpeg = (string) ob_get_clean();
    if ($ok && $jpeg !== '' && strlen($jpeg) <= 300 * 1024) { break; }
    $jpeg = '';
  }
  if ($jpeg === '') { return false; }
  // Written under a temporary name first, so a view at the same moment never reads half a file.
  $tmp = $file . '.' . getmypid() . '.tmp';
  if (file_put_contents($tmp, $jpeg) !== strlen($jpeg) || !rename($tmp, $file)) {
    @unlink($tmp);
    return false;
  }
  return true;
}

/** The share image of the page being shown, or null where Rank Math keeps its own. */
function zp_og_page_image(): ?array {
  if (is_admin() || !zp_seo_plan_active()) { return null; }
  $id = (int) get_queried_object_id();
  // An image chosen for the page in Rank Math, or its featured image, stays the page's share image.
  if ($id && (get_post_meta($id, 'rank_math_facebook_image', true) || get_post_meta($id, 'rank_math_twitter_image', true) || has_post_thumbnail($id))) {
    return null;
  }
  // The Polish path, also on /en/ (as the hero itself, see service-heroes.php).
  return zp_og_image(zp_seo_service_hero_path());
}

/** Rank Math's image list (RankMath\OpenGraph\Image): the hero image is added where the page has none of its own. */
function zp_og_add_image($images): void {
  $og = is_object($images) && method_exists($images, 'add_image') ? zp_og_page_image() : null;
  if ($og !== null) { $images->add_image($og); }
}

// Rank Math builds the list once for Facebook (og:image and its width, height, alt, type) and once for X (twitter:image).
add_action('rank_math/opengraph/facebook/add_additional_images', 'zp_og_add_image');
add_action('rank_math/opengraph/twitter/add_additional_images', 'zp_og_add_image');
// Rank Math adds its site-wide image only to a page without images; this keeps it out whatever the order of its steps.
add_filter('rank_math/opengraph/pre_set_default_image', static function ($skip) {
  return $skip ?: zp_og_page_image() !== null;
});
