<?php
/** Fixed-endpoint background Responses transport. No key, header or raw error is persisted. */
if (!defined('ABSPATH')) { exit; }
final class ZP_EN_Rewrite_Transport_20261011 {
    public const MODEL = 'gpt-6.1-sol';
    public const MAX_OUTPUT = 8192;
    public const DEFAULT_MODEL = 'gpt-6-luna';
    /** Immutable standard-price policies: integer milli-microUSD per token. */
    public static function policy(string $model): array {
        $rates=['gpt-6.1-sol'=>['input'=>2000,'write'=>2500,'cached'=>100,'output'=>10000],'gpt-6-luna'=>['input'=>100,'write'=>125,'cached'=>10,'output'=>500]];
        if (!isset($rates[$model])) throw new RuntimeException('request_changed');
        return ['version'=>$model==='gpt-6.1-sol'?'sol-high-standard-v1':'luna-high-standard-v1','model'=>$model,'reasoning'=>'high','tier'=>'default','prompt_version'=>$model==='gpt-6-luna'?'uk-neutral-v2':'uk-neutral-v1','rates'=>$rates[$model]];
    }
    public static function policy_hash(array $policy): string { return hash('sha256',(string)wp_json_encode($policy)); }
    public static function request_policy(array $request): array {
        $model=$request['model']??null;if (!is_string($model)) throw new RuntimeException('request_changed');$policy=self::policy($model);$digest=$request['metadata']['zp_en_policy_hash']??null;
        if ($digest===null) { if ($model!==self::MODEL) throw new RuntimeException('request_changed'); }
        elseif (!is_string($digest)||!hash_equals(self::policy_hash($policy),$digest)) throw new RuntimeException('request_changed');
        return $policy;
    }
    public static function chunk_policy(array $chunk): array {
        if (!is_array($chunk['request']??null)) throw new RuntimeException('request_changed');$policy=self::request_policy($chunk['request']);
        if (!array_key_exists('policy',$chunk)) { if ($policy['model']!==self::MODEL||isset($chunk['request']['metadata']['zp_en_policy_hash'])) throw new RuntimeException('request_changed'); }
        elseif (($chunk['policy']??null)!==$policy||($chunk['policy_hash']??null)!==self::policy_hash($policy)||($chunk['request']['metadata']['zp_en_policy_hash']??null)!==$chunk['policy_hash']) throw new RuntimeException('request_changed');
        return $policy;
    }
    public static function valid_id($id): bool { return is_string($id) && preg_match('/^resp_[A-Za-z0-9_-]{1,180}$/D', $id) === 1; }
    public static function request_hash(array $request): string { unset($request['metadata']['zp_en_request_hash']); return hash('sha256', (string)wp_json_encode($request)); }
    public static function reservation(array $request): int {
        $policy=self::request_policy($request);$rates=$policy['rates'];
        return intdiv((strlen((string)wp_json_encode($request))+512)*$rates['write']+self::MAX_OUTPUT*$rates['output']+999,1000);
    }
    public static function usage($usage,?array $policy=null): ?array {
        $policy=$policy??self::policy(self::MODEL);$model=$policy['model']??null;if (!is_string($model)||$policy!==self::policy($model)) return null;
        if (!is_array($usage)||!is_int($usage['input_tokens']??null)||!is_int($usage['output_tokens']??null)||$usage['input_tokens']<0||$usage['input_tokens']>200000||$usage['output_tokens']<0||$usage['output_tokens']>self::MAX_OUTPUT) return null;
        $input=$usage['input_tokens'];$output=$usage['output_tokens'];$details=$usage['input_tokens_details']??null;$rates=$policy['rates'];
        if (array_key_exists('input_tokens_details',$usage)&&$details!==null&&!is_array($details)) return null;
        if (is_array($details)) foreach (['cached_tokens','cache_write_tokens'] as $field) if (array_key_exists($field,$details)&&(!is_int($details[$field])||$details[$field]<0||$details[$field]>$input)) return null;
        if (is_array($details)&&isset($details['cached_tokens'],$details['cache_write_tokens'])) {
            $cached=$details['cached_tokens'];$writes=$details['cache_write_tokens'];if ($cached+$writes>$input) return null;
            $scaled=($input-$cached-$writes)*$rates['input']+$cached*$rates['cached']+$writes*$rates['write']+$output*$rates['output'];$basis='api_cache_breakdown';
        } else { $scaled=$input*$rates['write']+$output*$rates['output'];$basis='input_upper_bound'; }
        return ['input_tokens'=>$input,'output_tokens'=>$output,'micro_usd'=>intdiv($scaled+999,1000),'pricing_basis'=>$basis];
    }
    /** Receipt callback must save/read-back the accepted response ID BEFORE status or output parsing. */
    public static function step(array $chunk, string $key, callable $save_receipt): array {
        $request = $chunk['request']??null;
        $policy=self::chunk_policy($chunk);
        if (!is_array($request) || ($request['model']??null)!==$policy['model'] || ($request['reasoning']['effort']??null)!=='high' || ($request['service_tier']??null)!=='default'
            || ($request['max_output_tokens']??null)!==self::MAX_OUTPUT || ($request['background']??null)!==true || ($request['store']??null)!==true
            || !is_string($chunk['id']??null) || !is_string($chunk['revision']??null) || ($request['metadata']['zp_en_chunk']??null)!==$chunk['id'] || ($request['metadata']['zp_en_revision']??null)!==$chunk['revision'] || ($request['metadata']['zp_en_request_hash']??null)!==($chunk['request_hash']??null) || !hash_equals($chunk['request_hash']??'',self::request_hash($request))) throw new RuntimeException('request_changed');
        if ($key==='' || !hash_equals($chunk['key_fingerprint']??'',hash('sha256',$key))) throw new RuntimeException('connection_changed');
        $id = $chunk['response_id']??''; $known = $id!=='';
        if ($known && !self::valid_id($id)) throw new RuntimeException('response_identity');
        if ($known && (!is_int($chunk['received_at']??null) || $chunk['received_at']<time()-86400)) throw new RuntimeException('response_expired');
        if ($known && ((int)($chunk['poll_count']??0)>=120 || (int)($chunk['poll_window_at']??0)<time()-7200)) throw new RuntimeException('poll_limit');
        if (!$known && ($chunk['send_state']??null)!=='authorized_flight') throw new RuntimeException('unknown_paid_attempt');
        $args=['timeout'=>10,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>2000000,'headers'=>['Authorization'=>'Bearer '.$key,'Content-Type'=>'application/json']];
        $started=microtime(true);
        if ($known) { $response=wp_remote_get('https://api.openai.com/v1/responses/'.$id,$args); }
        else { $args['body']=wp_json_encode($request); $response=wp_remote_post('https://api.openai.com/v1/responses',$args); }
        $diag=['method'=>$known?'GET':'POST','at'=>time(),'elapsed_ms'=>(int)round((microtime(true)-$started)*1000),'http_code'=>is_wp_error($response)?0:(int)wp_remote_retrieve_response_code($response)];
        if (is_wp_error($response)) {
            if (!$known) throw new RuntimeException('unknown_paid_attempt');
            return ['waiting'=>true,'error'=>'poll_transport','diagnostic'=>$diag];
        }
        $code=$diag['http_code'];
        if ($code!==200) {
            if ($known && ($code===429 || $code>=500 || $code===408)) return ['waiting'=>true,'error'=>'poll_http','diagnostic'=>$diag];
            throw new RuntimeException($known && $code===404?'response_not_found':(in_array($code,[401,403,429],true)?'api_http_'.$code:($known?'api_http':'unknown_paid_attempt')));
        }
        $raw=wp_remote_retrieve_body($response);
        if (!is_string($raw) || strlen($raw)>2000000) throw new RuntimeException($known?'response_invalid':'unknown_paid_attempt');
        $data=json_decode($raw,true,64);
        if (!is_array($data) || json_last_error()!==JSON_ERROR_NONE || !self::valid_id($data['id']??null) || ($known && $data['id']!==$id)) throw new RuntimeException($known?'response_identity':'unknown_paid_attempt');
        // Even malformed successful envelopes retain a known ID. A bad status must never cause another POST.
        $receipt=['response_id'=>$data['id'],'received_at'=>$known?$chunk['received_at']:time(),'request_hash'=>$chunk['request_hash'],
            'key_fingerprint'=>$chunk['key_fingerprint'],'revision'=>$chunk['revision'],'chunk_id'=>$chunk['id']];
        if (isset($chunk['policy_hash'])) $receipt['policy_hash']=$chunk['policy_hash'];
        $save_receipt($receipt);
        $metadata=$data['metadata']??null;
        if (!is_array($metadata) || ($metadata['zp_en_chunk']??null)!==$chunk['id'] || ($metadata['zp_en_revision']??null)!==$chunk['revision']
            || ($metadata['zp_en_request_hash']??null)!==$chunk['request_hash']) throw new RuntimeException('response_identity');
        if (isset($chunk['policy_hash'])&&($metadata['zp_en_policy_hash']??null)!==$chunk['policy_hash']) throw new RuntimeException('response_identity');
        $status=$data['status']??null;
        if (!is_string($status) || !in_array($status,['queued','in_progress','completed','failed','cancelled','incomplete'],true)) throw new RuntimeException('response_invalid');
        $out=['waiting'=>in_array($status,['queued','in_progress'],true),'status'=>$status,'receipt'=>$receipt,'diagnostic'=>$diag,'usage'=>self::usage($data['usage']??null,$policy)];
        if ($out['waiting']) return $out;
        if ($status!=='completed') { $out['error']='response_'.$status; return $out; }
        if (!empty($data['error']) || !empty($data['incomplete_details']) || !is_array($data['output']??null)) throw new RuntimeException('response_invalid');
        $texts=[];
        foreach ($data['output'] as $message) {
            if (!is_array($message)) throw new RuntimeException('response_invalid');
            if (($message['type']??'')==='reasoning') continue;
            if (($message['type']??null)!=='message' || ($message['role']??null)!=='assistant' || !is_array($message['content']??null)) throw new RuntimeException('response_invalid');
            foreach ($message['content'] as $item) {
                if (!is_array($item)) throw new RuntimeException('response_invalid');
                if (($item['type']??null)==='refusal') throw new RuntimeException('response_refusal');
                if (($item['type']??null)!=='output_text' || !is_string($item['text']??null)) throw new RuntimeException('response_invalid');
                $texts[]=$item['text'];
            }
        }
        if (count($texts)!==1 || strlen($texts[0])>180000) throw new RuntimeException('response_invalid');
        $json=json_decode($texts[0],true,32);
        if (!is_array($json) || json_last_error()!==JSON_ERROR_NONE) throw new RuntimeException('translation_json');
        $out['result']=$json; return $out;
    }
}
