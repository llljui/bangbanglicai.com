<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_inrepay_refund
{
	public function index(){
		
		$root = get_baseroot();
		
		
		$id = intval($GLOBALS['request']['id']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			
			$root['user_login_status'] = 1;

			$status = getUcInrepayRefund($id);
			if ($status['status'] == 1){
				//$deal = $status['deal'];
				$root['deal'] = $status['deal'];
				$root['true_all_manage_money'] = $status['true_all_manage_money'];					
				$root['impose_money'] = $status['impose_money'];
				$root['total_repay_money'] = $status['total_repay_money'];			
				$root['true_total_repay_money'] = $status['true_total_repay_money'];
				$root['true_all_mortgage_fee'] = $status['true_all_mortgage_fee'];
					
				$root['true_all_manage_money_format'] = $status['true_all_manage_money_format'];
				$root['impose_money_format'] = $status['impose_money_format'];
				$root['total_repay_money_format'] = $status['total_repay_money_format'];
				$root['true_total_repay_money_format'] = $status['true_total_repay_money_format'];
				$root['status'] = 1;
				$root['show_err'] = "提前还款会将借款一次性清，并计算违约金！";
			}else{				
				$root['show_err'] = $status['show_err'];
				$root['status'] = 0;
				$root['response_code'] = 0;
			}
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "提前还款";
		output($root);		
	}
}
?>
