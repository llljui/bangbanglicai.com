<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_learn_dobid
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			$deal_id = intval($GLOBALS['request']['deal_id']);	
			
			$root['deal_id'] = $deal_id;
			
			$learn_deal = get_deal($deal_id);
			$learn_id = intval($GLOBALS['request']['learn_id']);
			if($learn_deal['use_learn']==1){
				$learn_money = $GLOBALS['db']->getOne("SELECT `money` FROM ".DB_PREFIX."learn_send_list WHERE id=".$learn_id);
			}
			$bid_money = floatval($GLOBALS['request']['bid_money'])+$learn_money;
			
			if($learn_money > (round($learn_deal['borrow_amount'],2) - round($learn_deal['load_money'],2))){
				$root["status"] = 0;
				$root["show_err"] = "体验金金额大于可投金额，投标失败！";
				output($root);
			}
			if($bid_money > (round($learn_deal['borrow_amount'],2) - round($learn_deal['load_money'],2))){
				$root["status"] = 0;
				$root["show_err"] = "投资金额大于可投金额，投标失败！";
				output($root);
			}
						
//			if($bid_money > (round($learn_deal['borrow_amount'],2) - round($learn_deal['load_money'],2))){
//				$bid_money = (round($learn_deal['borrow_amount'],2) - round($learn_deal['load_money'],2));
//			}
			$bid_paypassword = strim($GLOBALS['request']['bid_paypassword']);
		   	
		   	$status = dobid2($deal_id,$bid_money,$bid_paypassword,1,0,$learn_id);
			$root['status'] = $status['status'];
			
			if($status['status'] == 0){
				$root['show_err'] =$status['show_err'];
			}elseif($status['status'] == 2){
				ajax_return($status);
			}elseif($status['status'] == 3){
				$root['show_err'] ="余额不足，请先去充值";
			}else{
				$root['show_err'] = $GLOBALS['lang']['DEAL_BID_SUCCESS'];
			}
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "体验金投资";
		output($root);		
	}
}
?>
