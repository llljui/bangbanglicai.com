<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class user_wx_register
{	
	
	public function index(){

//		$user =  $GLOBALS['user_info'];
//		$root['session_id'] = es_session::id();
//		$id = intval($GLOBALS['request']['id']);//商品ID
//		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
//		$user_id = intval($user['id']);

		$email = addslashes(trim($GLOBALS['request']['email']));//用户名或邮箱
		$pwd = trim($GLOBALS['request']['pwd']);//密码
		$id = intval($GLOBALS['request']['id']);//商品ID
		$city_name =strim($GLOBALS['request']['city_name']);//城市名称
		$user = user_check($email,$pwd,false);
		$user_id = intval($user['id']);
		if($user_id>0){
			app_redirect(wap_url("index#index"));
		}
        
        $wap_referer = "";
        
        if(isset($GLOBALS['request']['r'])){
            $referer_id = intval(base64_decode($GLOBALS['request']['r']));
            if($referer_id){
                $wap_referer = get_user_info("user_name","id=".$referer_id,"ONE");
                $root['wap_referer'] = $wap_referer;
            }
            
        }
		
		$root['city_name']=$city_name;
		$root['program_title']="绑定帐户";
		output($root);
		
	}
	
	
}
?>
