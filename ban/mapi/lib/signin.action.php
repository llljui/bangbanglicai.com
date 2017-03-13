<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class signin
{
	public function index(){
		
		$root = get_baseroot();
		
		$id = intval($GLOBALS['request']['id']);
	
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['result'] = signin($user_id);
			$root['response_code'] = 1;
			$root['show_err'] = '';
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		//data.deal.name
		$root['program_title'] = "签到";
		output($root);		
	}
}
?>

