<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class licai_deal_bid
{
	public function index(){
		
		$root = array();
		
		require_once APP_ROOT_PATH.'system/libs/licai.php';
		
		$id = intval($GLOBALS['request']['id']);
		$money = floatval($GLOBALS['request']['money']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$licai = get_licai($id);
		$licai_interest_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."licai_interest WHERE licai_id=".$id." and (min_money <= ".$money." and ".$money." < max_money ) order by max_money desc limit 1 ");	
		if($licai['type']>0){
			$interest_rate = $licai_interest_info['interest_rate'];
			$before_rate = $licai_interest_info['before_rate'];
			$site_buy_fee_rate = $licai_interest_info['site_buy_fee_rate'];
			$redemption_fee_rate = $licai_interest_info['redemption_fee_rate'];
			$normal_rate = $interest_rate/100;
			$preheat_rate = $before_rate/100;
			$procedures_rate = $site_buy_fee_rate/100;
			$redemption_rate = $redemption_fee_rate/100;
			$new_money_val = $money - $money*$procedures_rate;
            $income_money = ($new_money_val*$normal_rate*$licai['buy_day'])/365 +($new_money_val*$preheat_rate*$licai['before_day']);         
            $redemption_money = (($new_money_val)*$redemption_rate*($licai['buy_day']+$licai['before_day']))/365; 
            $new_income_money = number_format(($income_money-$redemption_money),2);       
		}else{
			
			$redemption_fee_rate = $licai['licai_interest']['redemption_fee_rate'];
			$site_buy_fee_rate = $licai['licai_interest']['site_buy_fee_rate'];
			$platform_rate = $licai['licai_interest']['platform_rate'];
			$average_income_rate = $licai['licai_interest']['average_income_rate'];
			$procedures_rate = $site_buy_fee_rate/100;
			$redemption_rate = $redemption_fee_rate/100;
			$preheat_rate = $average_income_rate/100;
			$new_money_val = $money - $money*$procedures_rate;
			$income_money = ($new_money_val*$preheat_rate*$licai['buy_day'])/365;
			$redemption_money = ($new_money_val)*$redemption_rate*$licai['buy_day']/365;
			$new_income_money=number_format(($income_money-$redemption_money),2);
                    
		}
		
		$root['new_income_money'] = $new_income_money;
		$root['response_code'] = 1;
		
		output($root);	
	}
}
?>
