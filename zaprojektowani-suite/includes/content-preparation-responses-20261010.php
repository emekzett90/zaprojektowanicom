<?php
/** One background Responses create or read per worker; never replay a known response. */
if(!defined('ABSPATH'))exit;
final class ZP_Preparation_Background_Response_20261010 {
    public static function valid_id($id): bool {return is_string($id)&&preg_match('/^resp_[A-Za-z0-9_-]{1,180}$/D',$id)===1;}
    private static function diagnostic($response,float $started,string $method): array {
        $out=['method'=>$method,'at'=>time(),'elapsed_ms'=>(int)round((microtime(true)-$started)*1000),'http_code'=>0,'transport'=>'ok','request_id'=>''];
        if(is_wp_error($response)){
            $code=method_exists($response,'get_error_code')?(string)$response->get_error_code():'';
            $message=method_exists($response,'get_error_message')?(string)$response->get_error_message():'';
            $out['transport']=preg_match('/timed?\s*out|timeout|cURL error 28/i',$message)?'timeout':(in_array($code,['http_request_failed','http_request_not_executed','connect_timeout'],true)?$code:'transport_error');
        }else{
            $out['http_code']=(int)wp_remote_retrieve_response_code($response);
            $id=wp_remote_retrieve_header($response,'x-request-id');if(is_string($id)&&preg_match('/^req_[A-Za-z0-9_-]{1,150}$/D',$id))$out['request_id']=$id;
        }
        return $out;
    }
    /** $persist stores an accepted ID before parsing a terminal result. */
    public static function step(array &$job,string $key,array $request,callable $persist): ?array {
        $id=$job['response_id']??'';$has=$id!=='';
        if($has&&!self::valid_id($id))throw new RuntimeException('response_identity');
        $hash=hash('sha256',wp_json_encode($request));
        if($has&&!empty($job['response_request_hash'])&&!hash_equals($job['response_request_hash'],$hash))throw new RuntimeException('response_changed');
        if($has&&!empty($job['response_key_fingerprint'])&&!hash_equals($job['response_key_fingerprint'],hash('sha256',$key)))throw new RuntimeException('response_changed');
        if($has&&!empty($job['response_received_at'])&&$job['response_received_at']<time()-86400)throw new RuntimeException('response_expired');
        $window=$job['response_poll_window_at']??$job['response_received_at']??0;if($has&&((int)($job['response_poll_count']??0)>=120||($window&&$window<time()-7200)))throw new RuntimeException('response_poll_limit');
        $args=['timeout'=>45,'redirection'=>0,'sslverify'=>true,'limit_response_size'=>2000000,'headers'=>['Authorization'=>'Bearer '.$key,'Content-Type'=>'application/json']];
        $started=microtime(true);
        if($has){$job['response_poll_count']=(int)($job['response_poll_count']??0)+1;$response=wp_remote_get('https://api.openai.com/v1/responses/'.$id,$args);}
        else{
            $job['response_attempt']=(int)($job['response_attempt']??1);$request['background']=true;$request['store']=true;$request['metadata']=['zp_job'=>$job['id'],'zp_request_hash'=>$hash,'zp_attempt'=>(string)$job['response_attempt']];$args['body']=wp_json_encode($request);
            $response=wp_remote_post('https://api.openai.com/v1/responses',$args);
        }
        $job['transport']['text']=self::diagnostic($response,$started,$has?'GET':'POST');
        if(is_wp_error($response)){
            if(!$has)throw new RuntimeException('api_ambiguous');
            return self::poll_wait($job,'response_poll_transport');
        }
        $http=(int)wp_remote_retrieve_response_code($response);
        if($http<200||$http>=300){
            if($has&&($http===429||$http>=500))return self::poll_wait($job,'response_poll_http');
            if($has&&$http===404)throw new RuntimeException('response_not_found');
            throw new RuntimeException(in_array($http,[401,403,429],true)?'api_http_'.$http:'api_failed');
        }
        $data=json_decode(wp_remote_retrieve_body($response),true);
        if(!is_array($data)||json_last_error()!==JSON_ERROR_NONE)throw new RuntimeException('api_response');
        if(!self::valid_id($data['id']??null)||($has&&$data['id']!==$id))throw new RuntimeException('response_identity');
        if(!$has){
            $job['response_id']=$data['id'];$job['response_request_hash']=$hash;$job['response_key_fingerprint']=hash('sha256',$key);$job['response_received_at']=time();$job['response_poll_count']=0;$job['response_scope_verified']=true;
            $persist($job);
        }
        if(!empty($job['response_scope_verified'])&&(($data['metadata']['zp_job']??null)!==$job['id']||($data['metadata']['zp_request_hash']??null)!==$hash||($data['metadata']['zp_attempt']??null)!==(string)($job['response_attempt']??1)))throw new RuntimeException('response_identity');
        $status=$data['status']??'';if(!is_string($status)||!in_array($status,['queued','in_progress','completed','incomplete','failed','cancelled'],true))throw new RuntimeException('response_invalid');
        $job['response_status']=$status;$job['response_poll_errors']=0;$job['response_poll_error']='';$job['transport']['text']['response_id']=$job['response_id'];
        if(in_array($status,['queued','in_progress'],true)){$job['response_next_poll_at']=time()+30;return null;}
        unset($job['response_next_poll_at']);$job['usage']['text']=$data['usage']??[];$job['response_result']=self::terminal_result($data);
        if($status==='failed')throw new RuntimeException('response_failed');
        if($status==='cancelled')throw new RuntimeException('response_cancelled');
        if($status==='incomplete'){$reason=$data['incomplete_details']['reason']??'';$job['response_incomplete_reason']=in_array($reason,['max_output_tokens','content_filter'],true)?$reason:'other';throw new RuntimeException('response_incomplete');}
        return $data;
    }
    private static function terminal_result(array $data): array {
        $out=['id'=>$data['id'],'status'=>$data['status'],'usage'=>$data['usage']??[],'output'=>[]];$budget=180000;
        foreach($data['output']??[] as $message){if(!is_array($message))continue;$content=[];foreach($message['content']??[] as $item){if(!is_array($item))continue;$type=$item['type']??'';$key=$type==='output_text'?'text':($type==='refusal'?'refusal':'');if($key===''||!is_string($item[$key]??null))continue;$value=mb_strcut($item[$key],0,$budget,'UTF-8');$budget-=strlen($value);$content[]=['type'=>$type,$key=>$value];if($budget<=0)break;}if($content)$out['output'][]=['content'=>$content];if($budget<=0)break;}
        if(isset($data['incomplete_details']['reason']))$out['incomplete_reason']=in_array($data['incomplete_details']['reason'],['max_output_tokens','content_filter'],true)?$data['incomplete_details']['reason']:'other';return $out;
    }
    private static function poll_wait(array &$job,string $error): ?array {
        $job['response_poll_errors']=(int)($job['response_poll_errors']??0)+1;$job['response_poll_error']=$error;
        if($job['response_poll_errors']>=5)throw new RuntimeException('response_poll_failed');
        $job['response_next_poll_at']=time()+min(300,30*(2**($job['response_poll_errors']-1)));return null;
    }
}
