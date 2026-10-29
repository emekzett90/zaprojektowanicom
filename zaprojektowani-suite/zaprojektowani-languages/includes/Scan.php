<?php
namespace ZPL;

if (!defined('ABSPATH') && !defined('ZPL_CLI')) { exit; }

/**
 * Scan frames of the „Zaprojektowani Skaner EN” plugin: the administrator's browser opens every English page in a
 * frame (?zpl_scan=1) and looks for Polish text. The scanner itself (REST, UI, automatic fixes) lives in that plugin.
 */
final class Scan {
    private static $on = false;

    /**
     * Scan frame (?zpl_scan=1 opened by an administrator): the page is rendered exactly as a visitor sees it
     * (no admin bar or admin-only widgets), sends no data (forms, statistics) and never switches language on its own.
     */
    public static function boot_frame(): void {
        if (!isset($_GET['zpl_scan']) || !function_exists('current_user_can') || !current_user_can('manage_options')) { return; }
        self::$on = true;
        add_filter('show_admin_bar', '__return_false', PHP_INT_MAX);
        // After login handling and the main query, before any template output: render as a visitor.
        add_action('wp', static function () { wp_set_current_user(0); }, PHP_INT_MIN);
        add_action('send_headers', static function () { nocache_headers(); header('X-Robots-Tag: noindex'); });
    }

    public static function active(): bool { return self::$on; }

    /** Runs first in <head> of a scan frame: records alert()/confirm(), blocks sending data (forms, stats beacons). */
    public static function guard_script(): string {
        return '<script id="zpl-scan-guard">(function(){var W=window,S=W.__zplScan={alerts:[],blocked:[],block:true};'
            . 'var ok=function(u){u=String(u||"");return /\/zpl\/v1\/|rest_route=%2Fzpl%2Fv1|rest_route=\/zpl\/v1/.test(u);};'
            . '["alert","confirm","prompt"].forEach(function(n){W[n]=function(m){S.alerts.push(String(m===undefined?"":m));return n==="confirm"?false:(n==="prompt"?null:undefined);};});'
            . 'var f=W.fetch;if(f){W.fetch=function(i,o){var m=String((o&&o.method)||(i&&i.method)||"GET").toUpperCase(),u=i&&i.url?i.url:i;if(S.block&&m!=="GET"&&m!=="HEAD"&&!ok(u)){S.blocked.push(m+" "+u);return Promise.reject(new TypeError("zpl scan"));}return f.apply(this,arguments);};}'
            . 'var X=W.XMLHttpRequest&&W.XMLHttpRequest.prototype;if(X){var xo=X.open,xs=X.send;X.open=function(m,u){this.__zm=String(m).toUpperCase();this.__zu=u;return xo.apply(this,arguments);};'
            . 'X.send=function(){if(S.block&&this.__zm!=="GET"&&this.__zm!=="HEAD"&&!ok(this.__zu)){S.blocked.push(this.__zm+" "+this.__zu);try{this.abort();}catch(e){}return;}return xs.apply(this,arguments);};}'
            . 'if(W.navigator&&navigator.sendBeacon){var sb=navigator.sendBeacon.bind(navigator);navigator.sendBeacon=function(u,d){if(S.block&&!ok(u)){S.blocked.push("BEACON "+u);return true;}return sb(u,d);};}'
            . 'W.open=function(){return null;};'
            . 'document.addEventListener("submit",function(e){if(S.block){e.preventDefault();e.stopImmediatePropagation();S.blocked.push("SUBMIT");}},true);'
            . 'var F=W.HTMLFormElement&&HTMLFormElement.prototype;if(F){F.submit=function(){S.blocked.push("SUBMIT");};if(F.requestSubmit){F.requestSubmit=function(){S.blocked.push("SUBMIT");};}}'
            . '})();</script>';
    }
}
