<?php
if (!defined('ABSPATH')) { exit; }

/**
 * New articles from the content thread (2.5.0: content batch 2, 2.6.0: content batch 3), published by the migration.
 *
 * Each article copies the Elementor layout of an existing article in the same category
 * (data/articles.php, 'source'), so it gets the same template, article styles and CTA
 * watermark as Mat's posts. Only the HTML widget that holds the article changes: its <style>
 * blocks stay, the article and its JSON-LD are replaced with data/articles/<slug>.html.
 * Photos come from assets/img/wpisy/ (boards and mockups from real projects) and are
 * imported into the media library once. Without a source article the post is created as
 * a draft, so nothing half-styled goes live. The created posts are marked with
 * _zp_seo_plan_created; restoring the plan moves them to drafts.
 */

/** Attachment ID of a photo from assets/img/wpisy/, imported into the media library once. */
function zp_seo_articles_media(string $name, string $alt = ''): int {
  $map = (array) get_option('zp_seo_plan_media', []);
  if (!empty($map[$name]) && get_post_type((int) $map[$name]) === 'attachment') { return (int) $map[$name]; }
  $file = ZP_SUITE_PATH . 'assets/img/wpisy/' . basename($name) . '.webp';
  if (!is_file($file)) { return 0; }
  $upload = wp_upload_bits(basename($name) . '.webp', null, (string) file_get_contents($file));
  if (!empty($upload['error'])) { return 0; }
  $id = wp_insert_attachment([
    'post_mime_type' => 'image/webp',
    'post_title' => $alt !== '' ? $alt : basename($name),
    'post_status' => 'inherit',
  ], $upload['file'], 0, true);
  if (is_wp_error($id) || !$id) { return 0; }
  if (!function_exists('wp_generate_attachment_metadata')) { require_once ABSPATH . 'wp-admin/includes/image.php'; }
  wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));
  if ($alt !== '') { update_post_meta($id, '_wp_attachment_image_alt', $alt); }
  $map[$name] = (int) $id;
  update_option('zp_seo_plan_media', $map, false);
  return (int) $id;
}

/**
 * 2.8.1: covers cropped to their collage ("-kadr"; Mat 7.10: on /wiedza/ the new articles' images
 * did not fill the card and left a light band under the picture). Articles the plan already
 * published get the cropped cover as their featured image, but only while they still show the
 * plan's old cover (or none), so a featured image Mat picked himself stays.
 */
function zp_seo_articles_refresh_featured(): array {
  $log = [];
  $map = (array) get_option('zp_seo_plan_media', []);
  foreach (zp_seo_plan_data('articles') as $path => $a) {
    $new = (string) ($a['featured'] ?? '');
    if (substr($new, -5) !== '-kadr') { continue; }
    $id = zp_seo_plan_find_post($path);
    if (!$id || !get_post_meta($id, '_zp_seo_plan_created', true)) { continue; }
    $current = (int) get_post_thumbnail_id($id);
    $old = (int) ($map[substr($new, 0, -5)] ?? 0);
    if ($current && $current !== $old) { continue; }
    $thumb = zp_seo_articles_media($new, (string) $a['title']);
    if ($thumb && $thumb !== $current) {
      set_post_thumbnail($id, $thumb);
      $log[] = 'Artykuł ' . $path . ': przycięta okładka (wypełnia miniaturę na liście wpisów).';
    }
  }
  return $log;
}

/** Article HTML with {{IMG:name}} replaced by media library URLs (alt taken from the figure). */
function zp_seo_articles_html(string $slug): string {
  $file = __DIR__ . '/data/articles/' . basename($slug) . '.html';
  if (!is_file($file)) { return ''; }
  $html = (string) file_get_contents($file);
  return (string) preg_replace_callback('~src="\{\{IMG:([a-z0-9-]+)\}\}"([^>]*?alt="([^"]*)")?~', static function ($m) {
    $id = zp_seo_articles_media($m[1], html_entity_decode((string) ($m[3] ?? ''), ENT_QUOTES, 'UTF-8'));
    $url = $id ? (string) wp_get_attachment_url($id) : ZP_SUITE_URL . 'assets/img/wpisy/' . $m[1] . '.webp';
    return 'src="' . esc_url($url) . '"' . ($m[2] ?? '');
  }, $html);
}

/**
 * The source post's Elementor data with the article widget's HTML replaced, or null when
 * the source has no Elementor HTML widget with an article in it.
 */
function zp_seo_articles_elementor(int $source_id, string $article, array &$notes): ?string {
  $raw = get_post_meta($source_id, '_elementor_data', true);
  $data = is_string($raw) ? json_decode($raw, true) : (is_array($raw) ? $raw : null);
  if (!is_array($data)) { return null; }
  $done = false;
  $walk = static function (array $elements) use (&$walk, &$done, &$notes, $article): array {
    foreach ($elements as $i => $el) {
      if (!is_array($el)) { continue; }
      if (($el['elType'] ?? '') === 'widget') {
        $html = (string) ($el['settings']['html'] ?? '');
        if (!$done && ($el['widgetType'] ?? '') === 'html' && strpos($html, 'zpArticleNew') !== false && stripos($html, '<article') !== false) {
          preg_match_all('~<style\b[^>]*>.*?</style>~is', $html, $styles);
          preg_match_all('~<script\b(?![^>]*application/ld\+json)[^>]*>.*?</script>~is', $html, $scripts);
          $elements[$i]['settings']['html'] = implode("\n", $styles[0]) . "\n" . $article . ($scripts[0] ? "\n" . implode("\n", $scripts[0]) : '');
          $done = true;
        } elseif (!in_array((string) ($el['widgetType'] ?? ''), ['spacer', 'divider', 'shortcode'], true)) {
          $notes[] = (string) ($el['widgetType'] ?? 'widget');
        }
      }
      if (!empty($el['elements']) && is_array($el['elements'])) { $elements[$i]['elements'] = $walk($el['elements']); }
    }
    return $elements;
  };
  $data = $walk($data);
  return $done ? (string) wp_json_encode($data) : null;
}

function zp_seo_articles_create(): array {
  $log = [];
  $copy = ['_elementor_edit_mode', '_elementor_template_type', '_elementor_version', '_elementor_pro_version', '_elementor_page_settings', '_wp_page_template'];
  $made = 0;
  foreach (zp_seo_plan_data('articles') as $path => $a) {
    if (!empty($a['swap'])) { continue; }
    $slug = (string) $a['slug'];
    if (zp_seo_plan_find_post($path)) { $log[] = 'Artykuł ' . $path . ' już jest na stronie — bez zmian.'; continue; }
    $mine = get_posts(['name' => $slug, 'post_type' => 'post', 'post_status' => ['draft', 'pending', 'private', 'future'], 'numberposts' => 1, 'suppress_filters' => true, 'meta_key' => '_zp_seo_plan_created']);
    if ($mine) {
      if (get_post_meta($mine[0]->ID, '_zp_seo_plan_layout', true) === 'elementor') {
        wp_update_post(['ID' => $mine[0]->ID, 'post_status' => 'publish']);
        $log[] = 'Opublikowano ponownie artykuł ' . $path . ' (ID ' . $mine[0]->ID . ').';
      } else {
        $log[] = 'Artykuł ' . $path . ' czeka w szkicach (ID ' . $mine[0]->ID . ') — bez zmian.';
      }
      continue;
    }
    $taken = get_posts(['name' => $slug, 'post_type' => ['post', 'page'], 'post_status' => ['publish', 'draft', 'pending', 'private', 'future'], 'numberposts' => 1, 'fields' => 'ids', 'suppress_filters' => true]);
    if ($taken) { $log[] = 'UWAGA: adres ' . $slug . ' jest zajęty przez inny wpis (ID ' . $taken[0] . ') — pominięto artykuł ' . $path . '.'; continue; }
    $term = get_term_by('slug', (string) $a['category'], 'category');
    if (!$term) { $log[] = 'UWAGA: brak kategorii ' . $a['category'] . ' — pominięto artykuł ' . $path . '.'; continue; }
    $article = zp_seo_articles_html($slug);
    if ($article === '') { $log[] = 'BŁĄD: brak treści artykułu ' . $slug . '.'; continue; }

    $source = zp_seo_plan_find_post((string) $a['source']);
    $notes = [];
    $elementor = $source ? zp_seo_articles_elementor($source, $article, $notes) : null;
    $status = $elementor !== null ? 'publish' : 'draft';
    // The migration may run on an ordinary visit, where the HTML filter for users without
    // unfiltered_html would strip the article's markup and JSON-LD from post_content.
    $kses = has_filter('content_save_pre', 'wp_filter_post_kses');
    if ($kses) { kses_remove_filters(); }
    $id = wp_insert_post(wp_slash([
      'post_type' => 'post', 'post_status' => $status, 'post_title' => (string) $a['title'], 'post_name' => $slug,
      // Excerpt for blog lists and cards: the plan's meta description (an automatic excerpt
      // would start with the article's hero labels).
      'post_content' => $article, 'post_excerpt' => (string) ((zp_seo_plan_entry($path) ?? [])['description'] ?? ''), 'post_category' => [(int) $term->term_id],
      'post_author' => $source ? (int) get_post_field('post_author', $source) : get_current_user_id(),
      'comment_status' => $source ? (string) get_post_field('comment_status', $source) : 'closed', 'ping_status' => 'closed',
    ]), true);
    if ($kses) { kses_init_filters(); }
    if (is_wp_error($id) || !$id) {
      $log[] = 'BŁĄD: nie udało się utworzyć artykułu ' . $path . (is_wp_error($id) ? ' — ' . $id->get_error_message() : '');
      continue;
    }
    update_post_meta($id, '_zp_seo_plan_created', ZP_SEO_PLAN_VERSION);
    update_post_meta($id, 'rank_math_primary_category', (int) $term->term_id);
    if ($elementor !== null) {
      foreach ($copy as $key) {
        $value = get_post_meta($source, $key, true);
        if ($value !== '' && $value !== null) { update_post_meta($id, $key, wp_slash($value)); }
      }
      update_post_meta($id, '_elementor_data', wp_slash($elementor));
      update_post_meta($id, '_zp_seo_plan_layout', 'elementor');
    }
    if (!empty($a['featured'])) {
      $thumb = zp_seo_articles_media((string) $a['featured'], (string) $a['title']);
      if ($thumb) { set_post_thumbnail($id, $thumb); }
    }
    $made++;
    $real = zp_seo_plan_path((string) get_permalink($id));
    $log[] = ($status === 'publish' ? 'Opublikowano artykuł ' : 'UWAGA: artykuł zapisany jako szkic (brak wpisu-wzoru z Elementorem ' . $a['source'] . ') ')
      . $path . ' (ID ' . $id . ')' . ($real !== $path && $status === 'publish' ? ' — UWAGA: adres to ' . $real : '')
      . ($notes ? ' — skopiowane z wzoru także: ' . implode(', ', array_unique($notes)) : '')
      . (empty($a['featured']) ? ' — bez obrazka wyróżniającego' : '');
  }
  if ($made && class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance->files_manager)) {
    try { \Elementor\Plugin::$instance->files_manager->clear_cache(); } catch (\Throwable $e) { /* not fatal */ }
  }
  return $log;
}

/** Post meta that holds a post's layout, kept by zp_seo_articles_swap() before it writes the new article. */
function zp_seo_articles_layout_keys(): array {
  return ['_elementor_data', '_elementor_edit_mode', '_elementor_template_type', '_elementor_version', '_elementor_pro_version', '_elementor_page_settings', '_wp_page_template'];
}

/**
 * Whether $hay holds $needle, also when a space in it is a non-breaking space or a line break
 * (in the post's HTML or in Elementor's JSON).
 */
function zp_seo_articles_has_text(string $hay, string $needle): bool {
  if ($needle === '' || strpos($hay, $needle) !== false) { return $needle !== ''; }
  $flat = str_replace(['&nbsp;', '&#160;', '\\u00a0', "\xc2\xa0", '\\n', '\\r', '\\t'], ' ', $hay);
  return strpos((string) preg_replace('~\s+~', ' ', $flat), $needle) !== false;
}

/** Drops Elementor's cached render of a post after its layout changed outside the editor. */
function zp_seo_articles_elementor_flush(array $ids): void {
  foreach ($ids as $id) {
    foreach (['_elementor_css', '_elementor_element_cache', '_elementor_page_assets'] as $key) { delete_post_meta((int) $id, $key); }
  }
  if ($ids && class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance->files_manager)) {
    try { \Elementor\Plugin::$instance->files_manager->clear_cache(); } catch (\Throwable $e) { /* not fatal */ }
  }
}

/**
 * 2.9.6: existing posts that showed another article's text get their own article (data/articles.php
 * entries with 'swap'). It happens once, and only while the post still contains the fragment of the
 * other text, so a post Mat fixed by hand stays as it is. The article goes into the post's own
 * Elementor layout; when that layout has no article widget, the layout of 'source' is used, as for
 * new articles (also when the post is no longer edited with Elementor). The previous content,
 * excerpt, featured image and layout are kept in _zp_seo_plan_swap (restoring the plan puts them
 * back). Title, SEO title and description come from the plan in the steps that follow.
 */
function zp_seo_articles_swap(): array {
  $log = [];
  $made = [];
  foreach (zp_seo_plan_data('articles') as $path => $a) {
    $needle = (string) ($a['swap'] ?? '');
    if ($needle === '') { continue; }
    $id = zp_seo_plan_find_post($path);
    if (!$id) { $log[] = 'UWAGA: nie znaleziono wpisu ' . $path . ' — bez nowej treści.'; continue; }
    $raw = get_post_meta($id, '_elementor_data', true);
    $own = is_string($raw) ? $raw : (is_array($raw) ? (string) wp_json_encode($raw) : '');
    $content = (string) get_post_field('post_content', $id);
    if (!zp_seo_articles_has_text($content, $needle) && !zp_seo_articles_has_text($own, $needle)) {
      if (!get_post_meta($id, '_zp_seo_plan_swap', true)) { $log[] = 'Wpis ' . $path . ' ma już własny tekst — bez podmiany.'; }
      continue;
    }
    $article = zp_seo_articles_html((string) $a['slug']);
    if ($article === '') { $log[] = 'BŁĄD: brak treści artykułu ' . $a['slug'] . '.'; continue; }

    $notes = [];
    $layout = $id;
    $builder = get_post_meta($id, '_elementor_edit_mode', true) === 'builder';
    $elementor = $own !== '' && $builder ? zp_seo_articles_elementor($id, $article, $notes) : null;
    if ($elementor === null || zp_seo_articles_has_text($elementor, $needle)) {
      // The other article is not in the post's article widget: take the layout of the source post.
      $layout = zp_seo_plan_find_post((string) $a['source']);
      $notes = [];
      $elementor = $layout ? zp_seo_articles_elementor($layout, $article, $notes) : null;
    }
    if ($elementor === null) { $log[] = 'UWAGA: wpis ' . $path . ' bez podmiany treści (brak wpisu-wzoru z Elementorem ' . $a['source'] . ').'; continue; }

    $post = get_post($id);
    // The backup is written first and kept when it exists: a swap cut off halfway (the needle is
    // still in the layout) must not replace the original with the half-written state.
    $backup = get_post_meta($id, '_zp_seo_plan_swap', true);
    if (!is_array($backup)) {
      $backup = ['version' => ZP_SEO_PLAN_VERSION, 'post_content' => $post->post_content, 'post_excerpt' => $post->post_excerpt, 'thumbnail' => (int) get_post_thumbnail_id($id), 'meta' => []];
      foreach (zp_seo_articles_layout_keys() as $key) {
        if (metadata_exists('post', $id, $key)) { $backup['meta'][$key] = get_post_meta($id, $key, true); }
      }
      update_post_meta($id, '_zp_seo_plan_swap', wp_slash($backup));
    }
    $saved = zp_seo_plan_update_post([
      'ID' => $id, 'post_content' => $article,
      'post_excerpt' => (string) ((zp_seo_plan_entry($path) ?? [])['description'] ?? $post->post_excerpt),
    ], true);
    if (is_wp_error($saved) || !$saved) {
      $log[] = 'BŁĄD: nie udało się zapisać nowej treści wpisu ' . $path . (is_wp_error($saved) ? ' — ' . $saved->get_error_message() : '');
      continue;
    }
    $backup['written'] = md5($article);
    update_post_meta($id, '_zp_seo_plan_swap', wp_slash($backup));
    if ($layout !== $id) {
      foreach (zp_seo_articles_layout_keys() as $key) {
        if ($key === '_elementor_data') { continue; }
        $value = get_post_meta($layout, $key, true);
        if ($value !== '' && $value !== null) { update_post_meta($id, $key, wp_slash($value)); }
      }
    }
    update_post_meta($id, '_elementor_data', wp_slash($elementor));
    $thumb = !empty($a['featured']) ? zp_seo_articles_media((string) $a['featured'], (string) $a['title']) : 0;
    if ($thumb) { set_post_thumbnail($id, $thumb); }
    $made[] = $id;
    $log[] = 'Wpis ' . $path . ' (ID ' . $id . ') ma nowy tekst zamiast cudzego artykułu' . ($thumb ? ' i nową okładkę' : '')
      . ($layout !== $id ? ' (układ z ' . $a['source'] . ')' : '') . '; poprzednia treść jest w kopii planu.';
  }
  zp_seo_articles_elementor_flush($made);
  return $log;
}

/** Undoes zp_seo_articles_swap() for posts whose content is still the article it wrote. */
function zp_seo_articles_swap_restore(): array {
  $done = [];
  foreach (get_posts(['post_type' => 'post', 'post_status' => 'any', 'numberposts' => 20, 'fields' => 'ids', 'meta_key' => '_zp_seo_plan_swap', 'suppress_filters' => true]) as $id) {
    $b = get_post_meta($id, '_zp_seo_plan_swap', true);
    if (!is_array($b) || md5((string) get_post_field('post_content', $id)) !== ($b['written'] ?? '')) { continue; }
    zp_seo_plan_update_post(['ID' => $id, 'post_content' => (string) $b['post_content'], 'post_excerpt' => (string) $b['post_excerpt']]);
    foreach (zp_seo_articles_layout_keys() as $key) {
      if (array_key_exists($key, (array) $b['meta'])) { update_post_meta($id, $key, wp_slash($b['meta'][$key])); } else { delete_post_meta($id, $key); }
    }
    if (!empty($b['thumbnail'])) { set_post_thumbnail($id, (int) $b['thumbnail']); } else { delete_post_thumbnail($id); }
    delete_post_meta($id, '_zp_seo_plan_swap');
    $done[] = $id;
  }
  zp_seo_articles_elementor_flush($done);
  return $done ? ['Przywrócono poprzednią treść wpisów: ' . count($done) . '.'] : [];
}
