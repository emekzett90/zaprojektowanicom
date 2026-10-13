<?php
/**
 * 3.0.0: Article schema for posts made by the content batch (9.10) and the article factory (10.10).
 *
 * Both modules are admin tools of about 220 KB together. On page views they only add BlogPosting/FAQPage
 * to their own posts, so the front end loads a module only for a post that carries its marker meta.
 */
if (!defined('ABSPATH')) { exit; }

add_filter('rank_math/json_ld', static function ($data) {
    if (!is_array($data) || is_admin() || !is_singular('post') || class_exists('ZP_Content_Batch_20261009', false)) { return $data; }
    if (get_post_meta(get_queried_object_id(), '_zp_cb_20261009_batch', true) === '') { return $data; }
    require_once __DIR__ . '/content-batch-20261009.php';
    remove_filter('rank_math/json_ld', ['ZP_Content_Batch_20261009', 'schema'], 120); // the module's own copy; this filter already runs it
    return ZP_Content_Batch_20261009::schema($data);
}, 120);

add_filter('rank_math/json_ld', static function ($data) {
    if (!is_array($data) || is_admin() || !is_singular('post') || class_exists('ZP_Content_Preparation_20261010', false)) { return $data; }
    if (get_post_meta(get_queried_object_id(), '_zp_prepared_20261010_job', true) === '') { return $data; }
    require_once __DIR__ . '/admin-content-preparation-20261010.php';
    remove_filter('rank_math/json_ld', [ZP_Content_Preparation_20261010::class, 'schema'], 125);
    return ZP_Content_Preparation_20261010::schema($data);
}, 125);
