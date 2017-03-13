<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_expire_status
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			require_once APP_ROOT_PATH.'system/libs/licai.php';
			$root['user_login_status'] = 1;
			
			$id = intval($GLOBALS['request']["id"]);
		
			$vo =  $GLOBALS["db"]->getRow("select lco.*,lc.type from ".DB_PREFIX."licai_order lco
			left join ".DB_PREFIX."licai lc on lc.id= lco.licai_id  
			where lco.id =".$id." and lc.user_id = ".$user_id);
	
			$vo["money"] = $vo["money"]-$vo['redempte_money']-$vo["site_buy_fee"];
			
			$money = $vo["money"];
			if($vo["type"]>0)
				$licai_interest=get_licai_interest($vo['licai_id'],$money);
			else
				$licai_interest = get_licai_interest_yeb($vo["licai_id"],$vo["begin_interest_date"],$vo["end_interest_date"]);
			
			$vo['before_interest_enddate'] = to_timespan($vo['before_interest_enddate']);
			$vo['before_interest_date'] = to_timespan($vo['before_interest_date']);
			$vo['end_interest_date'] = to_timespan($vo['end_interest_date']);
			$vo['begin_interest_date'] = to_timespan($vo['begin_interest_date']);	
			if($vo["type"]>0)
			{
				$day_before=intval(($vo['before_interest_enddate']-$vo['before_interest_date'])/24/3600);
				
				if($day_before < 0)
				{
					$day_before = 0;
				}
				
				$before_earn_money=$money*$day_before*$licai_interest['before_rate']*0.01/365;
				
				$day_begin=intval(($vo['end_interest_date']-$vo['begin_interest_date'])/24/3600)+1;
				if($day_begin < 0)
				{
					$day_begin = 0;
				}
				$begin_earn_money=$money*$day_begin*$licai_interest['interest_rate']*0.01/365;
				
				$vo['earn_money']= round($before_earn_money+$begin_earn_money,2);
				$vo['fee']= round($money*($day_before+$day_begin)*$licai_interest['redemption_fee_rate']*0.01/365,2);
				$vo['organiser_fee']= round($money*($day_before+$day_begin)*$licai_interest['platform_rate']*0.01/365,2);
			}
			else
			{
				$days = intval(($vo['end_interest_date']-$vo['begin_interest_date'])/24/3600)+1;
				
				$vo['earn_money']= round($money*$licai_interest["interest_rate"]/365/100,2);
				$vo['fee']= round($money*$days*$licai_interest['redemption_fee_rate']*0.01/365,2);
				$vo['organiser_fee']= round($money*$days*$licai_interest['platform_rate']*0.01/365,2);
				
			}
			$root['vo'] = $vo;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "到期赎回管理";
		output($root);		
	}
}
?>
