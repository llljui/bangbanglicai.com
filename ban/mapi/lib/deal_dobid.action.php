<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class deal_dobid
{
	public function index(){
		
		$root = array();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$id = intval($GLOBALS['request']['id']);
			
			$deal = get_deal($id);
			$bid_money = floatval($GLOBALS['request']["bid_money"]);
			$buy_number = $GLOBALS['request']["buy_number"];
			if($deal['uloadtype'] == 1 && $buy_number > 1){
				$bid_money = $buy_number * $bid_money;
			}
			
			$bid_paypassword = strim($GLOBALS['request']['bid_paypassword']);
			$ecv_id = intval($GLOBALS['request']['ecv_id']);
			$interestrate_id = intval($GLOBALS['request']['interestrate_id']);
			$use_interestrate = intval($GLOBALS['request']['use_interestrate']);

			$status = dobid2($id,$bid_money,$bid_paypassword,0,$ecv_id,0,$interestrate_id,$use_interestrate);
			$root['status'] = $status['status'];
			if($status['status'] == 2){
				$root['response_code'] = 1;
				$root['app_url'] = $status['jump'];
			}else if($status['status'] != 1){
				$root['response_code'] = 0;
				$root['show_err'] = $status['show_err'];
			}else{
				$root['response_code'] = 1;
				$root['show_err'] = $GLOBALS['lang']['DEAL_BID_SUCCESS'];
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
