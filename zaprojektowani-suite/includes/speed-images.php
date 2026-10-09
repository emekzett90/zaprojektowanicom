<?php
/**
 * ZP Suite 2.9.11 — images with a known shape before their file arrives.
 *
 * Chrome lists in DevTools → Issues ("Improvements") every image with loading="lazy" whose box the page does
 * not settle before the file arrives: "Lazy-loaded images should have explicit dimensions". It looks once, at the
 * first layout that includes the image (Chromium: HTMLImageElement::DidFinishLayout, LayoutImage::IsUnsizedImage),
 * and accepts the image when the CSS gives it a width and a height in px or %, or one of them together with a
 * plain aspect-ratio (not "auto …", which the width and height attributes give), and its box is not 0 wide or high.
 * Most images of the site have a known width or height (width:100%, height:44px, …) and take the other one from
 * the file.
 *
 * Each such image gets its file's own shape in its style attribute (--zp-ar:2000/1563), and one rule in <head>,
 * :where(img[style*="--zp-ar"]){aspect-ratio:var(--zp-ar)}, turns it into aspect-ratio. Because it is the
 * file's own shape, the box is exactly what it was once the file has loaded; before that, its space is already
 * kept. The rule has no specificity, so every aspect-ratio the CSS of the plugin sets still wins, and it only
 * replaces the shape from the width/height attributes, which the browser drops anyway once the file arrives.
 * All images get it, not only those sent with loading="lazy": scripts of the plugin make most others lazy in
 * the browser (zp-front-polish-180.js, the desktop pass on the home page).
 *
 * A short script in <head> keeps the box exact: when the file that arrives has another shape (a srcset file
 * rounded differently, or a script swapped the image) it writes that file's shape into --zp-ar, and it takes the
 * shape off where the image has padding or a border inside a border-box (a plain aspect-ratio counts the padding,
 * the shape of the file does not). It looks again once the page has loaded, after the stylesheets loaded late.
 * The file's shape comes from a copy of the same file in new Image() (from the browser's cache, no new request):
 * naturalWidth/naturalHeight of the image itself are rounded after its srcset density, so a logo drawn from a
 * "file 4096w" srcset reads e.g. 105x17 instead of 1051x175, a shape about 3% off.
 *
 * Left alone: images inside <picture>, a srcset whose files differ in shape by more than 0.5% or are not on this
 * server, SVGs and other formats than JPEG, PNG, GIF and WebP, files whose EXIF data turns them in a way browsers
 * may not follow (or cannot be read), and images that already set aspect-ratio in their style attribute.
 *
 * The file sizes are kept in one option (checked against each file's modification time), so a page reads only
 * the headers of new or changed files. Images with neither a known width nor a known height get one here when
 * it is fixed by their section (zp_speed_img_box_css()); logo strips near the top of the page come eager instead
 * (optimizer.php, templates/trust-logos.php).
 */
if (!defined('ABSPATH')) { exit; }

/** [start, end, replacement] changes for zp_speed_process(). */
function zp_speed_lazy_ratio_ops(string $html, array $scan): array {
  if (!preg_match_all('~<img(?=[\s/>])~i', $html, $m, PREG_OFFSET_CAPTURE)) { return []; }
  $ops = [];
  foreach ($m[0] as $hit) {
    $at = $hit[1];
    if (zp_speed_in_skip($scan['skip'], $at)) { continue; }
    $gt = zp_speed_tag_end($html, $at);
    if ($gt < 0) { break; }
    $attrs = zp_speed_tag_attrs(substr($html, $at, $gt + 1 - $at));
    if (isset($attrs['style']) && (stripos($attrs['style']['v'], 'aspect-ratio') !== false || strpos($attrs['style']['v'], '--zp-ar') !== false)) { continue; }
    if (isset($attrs['style']) && $attrs['style']['q'] === '') { continue; }
    if (zp_speed_in_picture($html, $at)) { continue; }
    $ratio = zp_speed_img_ratio($attrs);
    if ($ratio === '') { continue; }
    if (isset($attrs['style'])) {
      $ops[] = [$at + $attrs['style']['at'], $at + $attrs['style']['at'], '--zp-ar:' . $ratio . ';'];
    } else {
      $ops[] = [$at + 4, $at + 4, ' style="--zp-ar:' . $ratio . '"'];
    }
  }
  if ($ops && isset($scan['head_end']) && $scan['head_end'] > 0) {
    $ops[] = [$scan['head_end'], $scan['head_end'], '<style id="zp-speed-img-ar">' . zp_speed_img_box_css($html) . '</style>'
      . '<script id="zp-speed-img-ar-guard">(function(w,d){function r(i){var p=i.style.getPropertyValue("--zp-ar").split("/");return p[0]/p[1]}'
      . 'function c(i){if(!i.style.getPropertyValue("--zp-ar"))return;var s=getComputedStyle(i),e=0,u=i.currentSrc||i.src||"";'
      . '["Top","Right","Bottom","Left"].forEach(function(k){e+=(parseFloat(s["padding"+k])||0)+(parseFloat(s["border"+k+"Width"])||0)});'
      . 'if(s.boxSizing=="border-box"&&e>0){i.style.removeProperty("--zp-ar");return}'
      . 'if(!u||u.indexOf("data:")==0||!i.naturalWidth||i.zpArSrc==u)return;i.zpArSrc=u;'
      . 'var t=new Image;t.onload=function(){var x=t.naturalWidth,y=t.naturalHeight,a=r(i);'
      . 'if(x>0&&y>0&&a>0&&Math.abs(x/y-a)>a*1e-6&&(i.currentSrc||i.src)==u)i.style.setProperty("--zp-ar",x+"/"+y)};t.src=u}'
      . 'function all(){[].forEach.call(d.querySelectorAll("img[style*=\'--zp-ar\']"),c)}'
      . 'd.addEventListener("load",function(e){if(e.target&&e.target.tagName=="IMG")c(e.target)},true);'
      . 'd.addEventListener("DOMContentLoaded",all);w.addEventListener("load",all)})(window,document)</script>'];
  }
  return $ops;
}

/**
 * The plain aspect-ratio for every image with a known shape, and a width for images whose CSS sets none but whose
 * box is fixed by their section; each width is the one the image gets anyway once its file has loaded. Only for
 * the sections on the page; the rules need --zp-ar, so without it the image keeps its old CSS.
 */
function zp_speed_img_box_css(string $html): string {
  $css = ':where(img[style*="--zp-ar"]){aspect-ratio:var(--zp-ar)}';
  // Logo pages, review badges: their "file 4096w" srcset with sizes="100vw" makes them file width × 100vw / 4096
  // wide, at most 40 px (trustindex.webp is 488 px wide: 11.9141vw; facebook.webp 2560 px: 40 px from 64 px up).
  if (strpos($html, 'score-logo') !== false) {
    $css .= '.score-logo img[src*="/trustindex.webp"][style*="--zp-ar"]{width:min(40px,11.9141vw)}'
      . '.score-logo img[src*="/facebook.webp"][style*="--zp-ar"]{width:40px}';
  }
  // Shop pages, Trustindex badge: the file's shape within 148 × 58 px (the file is larger).
  if (strpos($html, 'zpEcomTrust__scoreLogo') !== false) {
    $css .= '.zpEcomTrust__scoreLogo:not(.zpEcomTrust__scoreLogo--fb) img[style*="--zp-ar"]{width:min(148px,calc(58px*(var(--zp-ar))))}';
  }
  // Footer logo from 761 px up: as wide as its link (188 px; the file is 2560 px wide). footer.css sets its width
  // to auto with !important, so this one needs it too.
  if (strpos($html, 'zpMegaFooter__logo') !== false) {
    $css .= '@media (min-width:761px){html body .zpMegaFooter .zpMegaFooter__logo img[style*="--zp-ar"]{width:100%!important}}';
  }
  // Guides up to 1100 px: the team photo is 470 px high or as wide as its column, whichever is smaller (its files
  // are 760 px wide, more than either).
  if (strpos($html, 'zpGuide__team') !== false) {
    $css .= '@media (max-width:1100px){.zpGuide .zpGuide__team[style*="--zp-ar"]{width:calc(470px*(var(--zp-ar)))}}';
  }
  return $css;
}

/**
 * Attributes of one start tag: name => ['v' => decoded value, 'q' => quote or '', 'at' => offset of the value
 * in the tag]. The first of repeated attributes wins, as in the browser.
 */
function zp_speed_tag_attrs(string $tag): array {
  $out = [];
  if (!preg_match_all('~\s([^\s"\'<>/=]+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+)))?~', $tag, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE, 4)) { return $out; }
  foreach ($m as $a) {
    $name = strtolower($a[1][0]);
    if (isset($out[$name])) { continue; }
    $v = ['', '', -1];
    if (isset($a[2]) && $a[2][1] >= 0) { $v = [$a[2][0], '"', $a[2][1]]; }
    elseif (isset($a[3]) && $a[3][1] >= 0) { $v = [$a[3][0], "'", $a[3][1]]; }
    elseif (isset($a[4]) && $a[4][1] >= 0) { $v = [$a[4][0], '', $a[4][1]]; }
    $out[$name] = ['v' => html_entity_decode($v[0], ENT_QUOTES | ENT_HTML5), 'q' => $v[1], 'at' => $v[2]];
  }
  return $out;
}

/** Whether the tag at $at sits inside a <picture> element. */
function zp_speed_in_picture(string $html, int $at): bool {
  $before = substr($html, max(0, $at - 4000), min($at, 4000));
  $open = strripos($before, '<picture');
  if ($open === false) { return false; }
  $close = strripos($before, '</picture');
  return $close === false || $close < $open;
}

/** "W/H" of the image file behind src (every srcset file must have the same shape, give or take its rounding), or ''. */
function zp_speed_img_ratio(array $attrs): string {
  $src = trim($attrs['src']['v'] ?? '');
  if ($src === '' || stripos($src, 'data:') === 0) { return ''; }
  $size = zp_speed_image_size($src);
  if (!$size) { return ''; }
  if (isset($attrs['srcset']) && trim($attrs['srcset']['v']) !== '') {
    foreach (preg_split('~,(?=\s*\S)~', $attrs['srcset']['v']) as $candidate) {
      $url = preg_split('~\s+~', trim($candidate))[0];
      if ($url === '' || $url === $src) { continue; }
      $other = zp_speed_image_size($url);
      if (!$other || abs($other[0] / $other[1] - $size[0] / $size[1]) > 0.005 * $size[0] / $size[1]) { return ''; }
    }
  }
  return $size[0] . '/' . $size[1];
}

/** [width, height] of an image of this site as the browser shows it (EXIF turn applied), or null. */
function zp_speed_image_size(string $url): ?array {
  $pre = apply_filters('zp_speed_image_size', null, $url);
  if (is_array($pre)) { return $pre; }
  $file = zp_speed_local_file(html_entity_decode($url, ENT_QUOTES));
  if ($file === '') { return null; }
  $mtime = @filemtime($file);
  if (!$mtime) { return null; }
  $cache = zp_speed_size_cache();
  $key = 'f' . md5($file);
  if (isset($cache[$key]) && is_array($cache[$key]) && (int) $cache[$key][2] === (int) $mtime) {
    return $cache[$key][0] > 0 ? [(int) $cache[$key][0], (int) $cache[$key][1]] : null;
  }
  $size = [0, 0];
  $info = @getimagesize($file);
  if (is_array($info) && in_array((int) $info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP], true) && $info[0] > 0 && $info[1] > 0) {
    $turn = zp_speed_exif_orientation($file);
    if ($turn === 1 || ($turn >= 2 && $turn <= 4 && (int) $info[2] === IMAGETYPE_JPEG)) { $size = [(int) $info[0], (int) $info[1]]; }
    elseif ($turn >= 5 && (int) $info[2] === IMAGETYPE_JPEG) { $size = [(int) $info[1], (int) $info[0]]; }
  }
  $GLOBALS['zp_speed_sizes'][$key] = [$size[0], $size[1], (int) $mtime];
  $GLOBALS['zp_speed_sizes_dirty'] = true;
  return $size[0] > 0 ? $size : null;
}

function zp_speed_size_cache(): array {
  if (!isset($GLOBALS['zp_speed_sizes']) || !is_array($GLOBALS['zp_speed_sizes'])) {
    $cache = get_option('zp_speed_img_sizes');
    if (!is_array($cache) || (int) ($cache['_t'] ?? 0) < time() - WEEK_IN_SECONDS || count($cache) > 4000) {
      $cache = ['_t' => time()];
    }
    $GLOBALS['zp_speed_sizes'] = $cache;
    $GLOBALS['zp_speed_sizes_dirty'] = false;
  }
  return $GLOBALS['zp_speed_sizes'];
}

add_action('shutdown', function () {
  if (!empty($GLOBALS['zp_speed_sizes_dirty']) && is_array($GLOBALS['zp_speed_sizes'] ?? null)) {
    update_option('zp_speed_img_sizes', $GLOBALS['zp_speed_sizes'], false);
  }
}, 50);

/**
 * EXIF orientation of an image file: 1–8, 1 when the file has no EXIF data, 0 when it has some that cannot be
 * read. Browsers turn JPEG images by it (5–8 swap width and height); other formats with a turn are left alone.
 */
function zp_speed_exif_orientation(string $file): int {
  $fh = @fopen($file, 'rb');
  if (!$fh) { return 0; }
  $head = (string) fread($fh, 65536);
  $exif = '';
  if (strncmp($head, 'RIFF', 4) === 0 && substr($head, 8, 4) === 'WEBP') {
    // WebP: the chunks are walked by their headers; EXIF usually comes after the picture data.
    $pos = 12;
    for ($n = 0; $n < 64; $n++) {
      if (fseek($fh, $pos) !== 0) { break; }
      $hdr = (string) fread($fh, 8);
      if (strlen($hdr) < 8) { break; }
      $size = unpack('V', substr($hdr, 4, 4))[1];
      if (substr($hdr, 0, 4) === 'EXIF') { $exif = (string) fread($fh, min($size, 262144)); break; }
      $pos += 8 + $size + ($size & 1);
    }
    fclose($fh);
    if ($exif === '') { return 1; }
    if (strncmp($exif, "Exif\0\0", 6) === 0) { $exif = substr($exif, 6); }
  } else {
    fclose($fh);
    $at = strpos($head, "Exif\0\0");
    if ($at === false) { return strpos($head, 'eXIf') === false ? 1 : 0; }
    $exif = substr($head, $at + 6);
  }
  $order = substr($exif, 0, 2);
  if ($order !== 'II' && $order !== 'MM') { return 0; }
  $le = $order === 'II';
  $u16 = function ($o) use ($exif, $le) { $s = substr($exif, $o, 2); return strlen($s) === 2 ? unpack($le ? 'v' : 'n', $s)[1] : -1; };
  $u32 = function ($o) use ($exif, $le) { $s = substr($exif, $o, 4); return strlen($s) === 4 ? unpack($le ? 'V' : 'N', $s)[1] : -1; };
  $ifd = $u32(4);
  if ($ifd < 8) { return 0; }
  $count = $u16($ifd);
  if ($count < 0 || $count > 512) { return 0; }
  for ($i = 0; $i < $count; $i++) {
    $entry = $ifd + 2 + 12 * $i;
    $tag = $u16($entry);
    if ($tag < 0) { return 0; }
    if ($tag === 0x0112) {
      $value = $u16($entry + 8);
      return $value >= 1 && $value <= 8 ? $value : 0;
    }
  }
  return 1;
}
