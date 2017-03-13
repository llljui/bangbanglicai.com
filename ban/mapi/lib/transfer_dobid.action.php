<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class transfer_dobid
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$paypassword = strim($GLOBALS['request']['paypassword']);
			$id = intval($GLOBALS['request']['id']);
			
			$status = dotrans($id,$paypassword);
			
			$root['status'] = $status['status'];
			if($status['status'] == 2){
				$root['response_code'] = 1;
				$root['app_url'] = $status['jump'];
			}else if($status['status'] != 1){
				$root['response_code'] = 0;
				$root['show_err'] = $status['show_err'];
			}else{
				$root['response_code'] = 1;
				$root['show_err'] = $status['show_err'];
				$root['id'] = $id;
			}
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>
