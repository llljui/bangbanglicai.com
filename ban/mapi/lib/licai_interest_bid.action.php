<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class licai_interest_bid
{
	public function index(){
		
		$root = array();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		require_once APP_ROOT_PATH.'system/libs/licai.php';
		$id = intval($GLOBALS['request']["id"]);
		$licai_id = intval($GLOBALS['request']['licai_id']);
		$licai = get_licai($licai_id);
		
		$redeem_money = floatval($GLOBALS['request']['redeem_money']);
		if($licai['type']>0){
			$licai_interest_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."licai_interest WHERE licai_id=".$licai_id." ORDER BY max_money DESC limit 1 ");
			if($licai_interest_info['max_money']<$redeem_money){
			$licai_interest_info = $licai_interest_info;
			$redeem_money = $licai_interest_info['max_money'];
			$response_code = 0;
			}else{
			$licai_interest_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."licai_interest WHERE licai_id=".$licai_id." and (min_money <= ".$redeem_money." and ".$redeem_money." <= max_money ) ");
			$response_code = 1;
			}
		}
		
		$vo = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."licai_order where user_id =".$user_id." and id=".$id);
		
			$now=get_gmtime();
			if($licai['type'] > 0){
				
				$vo['before_interest_date']=to_timespan($vo['before_interest_date']);
				$vo['before_interest_enddate']=to_timespan($vo['before_interest_enddate']);
				$vo['begin_interest_date']=to_timespan($vo['begin_interest_date']);
				$vo['end_interest_date']=to_timespan($vo['end_interest_date']);
				$vo["create_time"] = to_timespan($vo["create_time"]);
				
				if($vo['before_interest_date']>$now)
				{
					$vo["licai_status"] = 0;
					$vo["before_days"] = 0;
					$vo["days"] = 0;
				}elseif($vo['before_interest_date']<$now&&$vo['before_interest_enddate']>$now){
					//小于起息时间，就是预热期就赎回
					$vo["licai_status"] = 0;
					$day=intval(($now-$vo['before_interest_date'])/24/3600)+1;
					if($day<=0){
						$day=0;
					} 
					$vo["before_days"] = $day;
					$vo["days"] = 0;
					
				}elseif($vo['before_interest_enddate']<=$now&&$vo['begin_interest_date']>$now){
					//完成预期期间，未进入正式起息时间
					$vo["licai_status"] = 1;
					$day=intval(($now-$vo['before_interest_date'])/24/3600)+1;
					if($day<=0){
						$day=0;
					}
					$vo["before_days"] = $day;
					$vo["days"] = 0;
					 
				}elseif($vo['begin_interest_date']<=$now&&$vo['end_interest_date']>$now){
					//进入正式起息时间,违约
					$vo["licai_status"] = 1;
					$vo["before_days"] = intval(($vo['before_interest_enddate']-$vo['before_interest_date'])/24/3600);
					if($vo["before_days"]<=0){
						$vo["before_days"]=0;
					}
					$day=intval(($now-$vo['begin_interest_date'])/24/3600)+1;
					if($day<=0){
						$day=0;
					}
					$vo["days"] = $day;	
							
				}elseif($vo['end_interest_date']<=$now){
					//正常结束
					$vo["licai_status"] = 2;
					$vo["before_days"] = intval(($vo['before_interest_enddate']-$vo['before_interest_date'])/24/3600);
					if($vo["before_days"]<0)
					{
						$vo["before_days"] = 0;
					}
					$vo["days"] = intval(($vo['end_interest_date']-$vo['begin_interest_date'])/24/3600)+1;
					if($vo["days"]<0)
					{
						$vo["days"] = 0;
					}
				}
			}
			else
			{
				$licai_interest_json = $GLOBALS["db"]->getOne("select sum(rate) from ".DB_PREFIX."licai_history where licai_id = ".$licai_id." and history_date >= '".$vo["begin_interest_date"]."' and history_date<= '".$vo["end_interest_date"]."'  and history_date<='".to_date(TIME_UTC,"Y-m-d")."'");
				$licai_interest_json = floatval($licai_interest_json);
//				
				$vo['begin_interest_date']=to_timespan($vo['begin_interest_date']);
				$vo['end_interest_date']=to_timespan($vo['end_interest_date']);
				if($vo['end_interest_date']<=TIME_UTC)
				{
					$vo["days"] = intval(($vo['end_interest_date']-$vo['begin_interest_date'])/24/3600)+1;
				}
				else
				{
					$vo["days"] = intval((TIME_UTC-$vo['begin_interest_date'])/24/3600)+1;
				}
				if($vo["days"]<0)
				{
					$vo["days"] = 0;
				}
			}
			//
			if($licai['type']>0){
				if($vo["licai_status"] == 0)
				{
					$vo["back_status_format"] = "预热期提前";
					$vo['before_arrival_earn'] = $redeem_money * $licai_interest_info['before_breach_rate']/365/100 *$vo["before_days"];
					$vo['arrival_earn'] = 0;
				}
				elseif($vo["licai_status"] == 1)
				{
					$vo["back_status_format"] = "理财期提前";
					$vo['before_arrival_earn'] = $redeem_money * $licai_interest_info['before_rate']/365/100 *$vo["before_days"];
					$vo['arrival_earn'] =$redeem_money * $licai_interest_info['breach_rate']/365/100 *$vo["days"];
				}
				elseif($vo["licai_status"] == 2)
				{
					$vo["back_status_format"] = "理财结束";
					$vo['before_arrival_earn'] = $redeem_money * $licai_interest_info['before_rate']/365/100 *$vo["before_days"];
					$vo['arrival_earn'] =$redeem_money * $licai_interest_info['interest_rate']/365/100 *$vo["days"];
				}
			}else{
				$vo["have_money"] = $vo["money"] - $vo["redempte_money"] - $vo["site_buy_fee"] - $vo["redempte_wait_pay"];
				 if($redeem_money>$vo['have_money']){
				 	$redeem_money = $vo['have_money'];
				 	$response_code = 0;
				 }else{
				 	$response_code = 1;
				 }
				 $vo['before_arrival_earn'] = 0;
				 $vo['arrival_earn'] = $licai_interest_json * $redeem_money/365/100;
			}
			
			$before_arrival_earn = number_format($vo['before_arrival_earn'],2);
			$arrival_earn = number_format($vo['arrival_earn'],2);
			$vo['arrival_amount'] = $redeem_money + $before_arrival_earn + $arrival_earn;
			$arrival_amount = number_format($vo['arrival_amount'],2);
			
			$root['before_arrival_earn'] = $before_arrival_earn;
			$root['arrival_earn'] = $arrival_earn;
			$root['arrival_amount'] = $arrival_amount;
		
		$root['response_code'] = $response_code;
		
		output($root);	
	}
}
?>