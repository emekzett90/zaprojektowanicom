<?php
/** Future factory uploads only. No hooks, provider calls, state changes or existing-media edits. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Content_Preparation_Images_Speed_20261010 {
    public static function encode(string $bytes): array {
        $size=@getimagesizefromstring($bytes);
        if(strlen($bytes)<100||strlen($bytes)>10000000||!is_array($size)||($size['mime']??null)!=='image/webp'||$size[0]!==1536||$size[1]!==1024)throw new RuntimeException('image_invalid');
        if(!class_exists('ZP_Managed_WebP_20261010'))throw new RuntimeException('image_storage');
        if(!function_exists('wp_tempnam'))require_once ABSPATH.'wp-admin/includes/file.php';
        try{$r=ZP_Managed_WebP_20261010::encode_bytes($bytes,1536,1024);}catch(Throwable $e){throw new RuntimeException('image_storage');}
        if(!is_array($r)||!is_string($r['bytes']??null)||strlen($r['bytes'])<32||strlen($r['bytes'])>512000||($r['byte_count']??null)!==strlen($r['bytes'])||($r['width']??null)!==1536||($r['height']??null)!==1024||!in_array($r['quality']??null,[80,72,64],true)||($r['source_sha256']??null)!==hash('sha256',$bytes)||($r['sha256']??null)!==hash('sha256',$r['bytes'])||!is_string($r['encoder']??null))throw new RuntimeException('image_storage');
        $output=@getimagesizefromstring($r['bytes']);if(!is_array($output)||($output['mime']??null)!=='image/webp'||$output[0]!==1536||$output[1]!==1024)throw new RuntimeException('image_storage');return $r;
    }
}
