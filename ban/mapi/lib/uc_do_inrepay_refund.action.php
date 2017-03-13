<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//执行提前还款
class uc_do_inrepay_refund
{
	public function index(){
		
		$root = array();
		
		$id = intval($GLOBALS['request']['id']);
			
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$paypassword = strim($GLOBALS['request']['paypassword']);	
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';

			$root['user_login_status'] = 1;
			
			if($paypassword==""){
				$root['response_code'] = 0;
				$root["show_err"] = $GLOBALS['lang']['PAYPASSWORD_EMPTY'];
				output($root);
			}
		
			if(md5($paypassword)!=$GLOBALS['user_info']['paypassword']){
				$root['response_code'] = 0;
				$root["show_err"] = $GLOBALS['lang']['PAYPASSWORD_ERROR'];
				output($root);
			}
									
			$result = getUCInrepayRepayBorrowMoney($id);
			$root['status'] = $result['status'];
			if($result['status'] == 2){
				$root['response_code'] = 1;
				$root['app_url'] = $result['jump'];
			}else{
				$root['response_code'] = $result['status'];
				$root['show_err'] = $result['show_err'];
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
