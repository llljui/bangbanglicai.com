<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_address
{
	public function index(){
		
		$root = get_baseroot();
		
		$address_id = intval($GLOBALS['request']['id']);
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
					
			$sql = "SELECT * from ".DB_PREFIX."user_address where id= '$address_id' and user_id = ".$user_id ;
			$user_address = $GLOBALS['db']->getRow($sql);
			$root['user_address'] = $user_address;
								
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		$root['program_title'] = "收货信息";
		
		
		
		output($root);		
	}
}
?>
