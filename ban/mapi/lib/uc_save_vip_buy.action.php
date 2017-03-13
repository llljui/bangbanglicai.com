<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_save_vip_buy
{
	public function index(){
		
		$root = array();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$root['user_login_status'] = 1;		
			$root['response_code'] = 1;
			require_once APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$paypassword = strim($GLOBALS['request']['paypassword']);
			$amount = intval($GLOBALS['request']['years']);
			$vip_id = intval($GLOBALS['request']['vip_id']);
			
			$status = getUcSaveVipBuy($amount,$paypassword,$vip_id);
			
			if($status['status'] == 0){
				$root['status']= 0;
				$root['show_err']= $status['show_err'];
			}
			else
			{
				$root['status']= 1;
				$root['show_err']= "VIP购买成功";
			}
			
		}else{
			$root['status']=0;
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "VIP购买执行";
		output($root);		
	}
}
?>
