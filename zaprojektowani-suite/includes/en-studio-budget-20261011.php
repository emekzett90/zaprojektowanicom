<?php
if (!defined('ABSPATH')) exit;

/** USD range presentation; original Studio range and submitted budget stay PLN. */
final class ZP_EN_Studio_Budget_20261011 {
    public static function filter(string $html,string $lang,string $source): string {
        // Currency59 supplies its own strict public/full-Luna/rate gate. No independent rate or request.
        if ($source!=='/studio-wyceny/'||!in_array($lang,['pl','en'],true)||strpos($html,'id="zp-en-currency-config"')===false||strpos($html,'id="zp-en-studio-budget-script"')!==false) return $html;
        $url=plugins_url('../assets/en-studio-budget-20261011.js',__FILE__);
        return preg_replace('~</body>~i','<script defer id="zp-en-studio-budget-script" src="'.esc_url($url.'?ver=usd_budget_v1').'"></script></body>',$html,1)??$html;
    }
    public static function register(): void { add_filter('zp_en_public_price_html',[self::class,'filter'],20,3); }
}
