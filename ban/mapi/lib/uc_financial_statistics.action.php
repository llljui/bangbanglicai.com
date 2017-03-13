<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_financial_statistics
{
	public function index(){
		
		$root = get_baseroot();
		
		
		$id = intval($GLOBALS['request']['id']);
		//$user_id = intval($GLOBALS['user_info']['id']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			//充值已支付
			$root['incharge_count'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where  is_paid = 1 and user_id = ".$user_id." ");
			$root['incharge_count'] = format_price($root['incharge_count']);
			
			//提现成功
			$root['carry_money'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry where  status = 1 and user_id = ".$user_id." ");
			$root['carry_money'] = format_price($root['carry_money']);
			
			$user_statistics = sys_user_status($user_id);
			$user_statics["load_earnings"] = number_format($user_statics['load_earnings'] + $user_statics['reward_money'] + $user_statics['load_tq_impose'] + $user_statics['load_yq_impose'] + $user_statics['rebate_money'] + $user_statics['referrals_money'] - $user_statics['carry_fee_money']- $user_statics['incharge_fee_money'], 2);
			
			$root['user_statistics'] = $user_statistics;
			//已付管理费
			//$root['true_repay_manage_money'] = $GLOBALS['db']->getOne("SELECT sum(true_repay_manage_money) FROM ".DB_PREFIX."deal_load_repay where  has_repay = 1 and user_id = ".$user_id." ");
			$root['true_repay_manage_money'] = $user_statistics['load_manage_money'];
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "理财统计";
		output($root);		
	}
}
?>
