<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class peizi_payment
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info']; //user_check($email,$pwd);
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$total_money = intval($GLOBALS['request']['total_money']);//需要借款金额
		if ($user_id >0){
			//$user['money_format'] = format_price($user['money']);//可用资金
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$money = getPeiziMoneyFormat($user['money']);
			$root['money'] = $money;
			
			$total_money = getPeiziMoneyFormat($total_money);
			$root['total_money'] = $total_money;
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
			
		$root['program_title'] = "支付中心";
		output($root);		
	}
}
?>
