<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_del_address
{
	public function index(){
		
		$root = get_baseroot();
		
		$address_id = intval($GLOBALS['request']['id']);
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			if($address_id){
				$GLOBALS['db']->query("delete from ".DB_PREFIX."user_address  where id = ".$address_id);
				$root['response_code'] = 1;
				$root["show_err"] = "删除成功";
			}else{
				$root["show_err"] = "删除失败";
			}
			
			output($root);
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "收货地址删除";
		output($root);		
	}
}
?>
