<?php
/** Local-only encoding for explicitly owned article-photo pipelines. No hooks, uploads or native metadata writes. */
if (!defined('ABSPATH')) { exit; }
final class ZP_Managed_WebP_20261010 {
    public const MAX_BYTES=512000;
    public const QUALITIES=[80,72,64];
    public const VERSION='managed-webp-20261010-v2';
    private static function dimensions(int $width,int $height): void {
        if($width<1||$height<1||$width>1536||$height>1024)throw new RuntimeException('webp_dimensions');
    }
    private static function image(string $bytes): array {
        if(strlen($bytes)<32||strlen($bytes)>12000000)throw new RuntimeException('webp_input');$size=@getimagesizefromstring($bytes);
        if(!is_array($size)||($size['mime']??'')!=='image/webp'||$size[0]<1||$size[1]<1||$size[0]>1536||$size[1]>1024)throw new RuntimeException('webp_input');return$size;
    }
    /** Call only after the owner's source/file/signature/unapplied-post checks. Existing source files are never modified. */
    public static function encode_file(string $file,string $expectedSha,int $width,int $height): array {
        self::dimensions($width,$height);$base=realpath(wp_get_upload_dir()['basedir']??'');$real=realpath($file);
        if(!$base||!$real||is_link($file)||!is_file($real)||!is_readable($real)||!str_starts_with($real,$base.DIRECTORY_SEPARATOR)||!preg_match('/^[a-f0-9]{64}$/D',$expectedSha)||filesize($real)>12000000)throw new RuntimeException('webp_source');
        $bytes=file_get_contents($real);if(!is_string($bytes)||!hash_equals($expectedSha,hash('sha256',$bytes)))throw new RuntimeException('webp_source');$result=self::encode_bytes($bytes,$width,$height);clearstatcache(true,$real);
        if(!is_file($real)||!hash_equals($expectedSha,hash_file('sha256',$real)))throw new RuntimeException('webp_source_changed');return$result;
    }
    /** Provider bytes have already passed the caller's response validation; this method still validates MIME/bounds itself. */
    public static function encode_bytes(string $bytes,int $width,int $height): array {
        self::dimensions($width,$height);$size=self::image($bytes);if($width>$size[0]||$height>$size[1])throw new RuntimeException('webp_dimensions');$sourceSha=hash('sha256',$bytes);
        $marker=wp_tempnam('zp-managed-webp-20261010');if(!is_string($marker)||is_link($marker)||!is_file($marker))throw new RuntimeException('webp_temp');$dir=$marker.'-work';$created=false;
        try{
            if(file_exists($dir)||!mkdir($dir,0700))throw new RuntimeException('webp_temp');$created=true;$input=$dir.'/input.webp';if(file_put_contents($input,$bytes,LOCK_EX)!==strlen($bytes))throw new RuntimeException('webp_temp');chmod($input,0600);
            // Both native WordPress editors preserve lossless WebP even when a lower quality is explicitly requested.
            // Reload a private lossless PNG copy so the native encoder can apply the requested lossy WebP quality.
            $decoded=wp_get_image_editor($input);if(is_wp_error($decoded)||!is_object($decoded)||!method_exists($decoded,'save'))throw new RuntimeException('webp_editor');
            $pixels=$dir.'/source.png';$bridge=$decoded->save($pixels,'image/png');$pixelSize=is_file($pixels)?@getimagesize($pixels):false;
            if(is_wp_error($bridge)||!is_array($bridge)||($bridge['path']??null)!==$pixels||is_link($pixels)||!is_file($pixels)||!is_readable($pixels)||filesize($pixels)>12000000||!is_array($pixelSize)||($pixelSize['mime']??null)!=='image/png'||$pixelSize[0]!==$size[0]||$pixelSize[1]!==$size[1])throw new RuntimeException('webp_output');chmod($pixels,0600);unset($decoded);
            foreach(self::QUALITIES as$quality){
                $editor=wp_get_image_editor($pixels);if(is_wp_error($editor)||!is_object($editor)||!method_exists($editor,'set_quality'))throw new RuntimeException('webp_editor');
                if($width!==$size[0]||$height!==$size[1]){$resize=$editor->resize($width,$height,true);if(is_wp_error($resize)||$resize===false)throw new RuntimeException('webp_resize');}
                // Native resize can reset quality; set it only after the existing center crop has completed.
                $set=$editor->set_quality($quality);if(is_wp_error($set)||$set===false)throw new RuntimeException('webp_quality');
                $target=$dir.'/q'.$quality.'.webp';$saved=$editor->save($target,'image/webp');$path=is_array($saved)?($saved['path']??null):null;
                if(is_wp_error($saved)||!is_string($path)||$path!==$target||is_link($target)||!is_file($target)||!is_readable($target)||filesize($target)>12000000)throw new RuntimeException('webp_output');chmod($target,0600);$output=file_get_contents($target);if(!is_string($output))throw new RuntimeException('webp_output');$actual=self::image($output);
                if($actual[0]!==$width||$actual[1]!==$height)throw new RuntimeException('webp_output');
                if(strlen($output)<=self::MAX_BYTES)return ['bytes'=>$output,'sha256'=>hash('sha256',$output),'source_sha256'=>$sourceSha,'width'=>$width,'height'=>$height,'quality'=>$quality,'byte_count'=>strlen($output),'encoder'=>self::VERSION];
            }
            throw new RuntimeException('webp_weight_limit');
        }finally{
            if($created){foreach(['input.webp','source.png','q80.webp','q72.webp','q64.webp']as$name){$path=$dir.'/'.$name;if(is_file($path)&&!is_link($path))unlink($path);}if(is_dir($dir)&&!is_link($dir))rmdir($dir);}
            if(is_file($marker)&&!is_link($marker))unlink($marker);
        }
    }
}
