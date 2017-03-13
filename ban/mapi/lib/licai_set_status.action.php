<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_set_status
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			//$result["jump"] = url("licai#uc_expire_lc");
			require_once APP_ROOT_PATH.'system/libs/licai.php';
			$id = intval($GLOBALS['request']["id"]);
			$status = 1;
			$earn_money = strim($GLOBALS['request']["earn_money"]);
			$fee = strim($GLOBALS['request']["fee"]);
			$pay_type = 0; //0不允许垫付
			$web_type = 2; //0前台
			
			$licai_order = $GLOBALS["db"]->getRow("select lco.*,u.user_name,lc.type as licai_type from ".DB_PREFIX."licai_order lco 
			left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id 
			left join ".DB_PREFIX."user u on u.id = lco.user_id 
			where lco.id=".$id." and lc.user_id = ".$user_id);
			
			if(!$licai_order)
			{
				$root["status"] = 0;
				$root["show_err"] = "操作失败，请重试";
				output($root);
			}
			
			$redempte = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."licai_redempte 
			where status = 0 and order_id =".$id." and type = 2");
			
			$redempte_id = $redempte["id"];
			
			if(!$redempte)
			{
				$licai_redempte_data = array();
				
				$money = $licai_redempte_data["money"] = $licai_order["money"] - $licai_order["redempte_money"] - $licai_order["site_buy_fee"];
				
				$licai_redempte_data["create_date"] = to_date(TIME_UTC);
				$licai_redempte_data["order_id"] = $licai_order["id"];
				$licai_redempte_data["user_id"] = $licai_order["user_id"];
				$licai_redempte_data["user_name"] = $licai_order["user_name"];
				$licai_redempte_data["status"] = 0;
				$licai_redempte_data["type"] = 2;	
				
				if($licai_order["licai_type"]>0)
					$licai_interest = get_licai_interest($licai_order["licai_id"],$money);
				else
					$licai_interest = get_licai_interest_yeb($licai_order["licai_id"],$licai_order['begin_interest_date'],$licai_order['end_interest_date']);
				
				$licai_order['before_interest_enddate'] = to_timespan($licai_order['before_interest_enddate']);
				$licai_order['before_interest_date'] = to_timespan($licai_order['before_interest_date']);
				$licai_order['begin_interest_date'] = to_timespan($licai_order['begin_interest_date']);
				$licai_order['end_interest_date'] = to_timespan($licai_order['end_interest_date']);
				
				
				if($licai_order['licai_type']>0)
				{
					$day_before=intval(($licai_order['before_interest_enddate']-$licai_order['before_interest_date'])/24/3600);
					
					if($day_before < 0)
					{
						$day_before = 0;
					}
					
					$before_earn_money=$licai_order["money"]*$day_before*$licai_interest['before_rate']*0.01/365;
					
					$day_begin=intval(($licai_order['end_interest_date']-$licai_order['begin_interest_date'])/24/3600)+1;
					
					if($day_begin < 0)
					{
						$day_begin = 0;
					}
					
					$begin_earn_money=$licai_order["money"]*$day_begin*$licai_interest['interest_rate']*0.01/365;
					
					
					$licai_redempte_data["organiser_fee"] = $licai_interest["platform_rate"] * $money * ($day_before+$day_begin)/100/365;
					
					
				}
				else
				{
					$days = intval(($licai_order['end_interest_date']-$licai_order['begin_interest_date'])/24/3600)+1;
					
					$licai_redempte_data["organiser_fee"] = $licai_interest["platform_rate"] * $money * $days/100/365;
					
				}
				$GLOBALS['db']->autoExecute(DB_PREFIX."licai_redempte",$licai_redempte_data,"INSERT");
				
				$redempte_id = $GLOBALS['db']->insert_id();
				
				$result = deal_redempte($redempte_id,$status,$earn_money,$fee,$licai_redempte_data["organiser_fee"],$pay_type,$web_type);
				
			}
			else
			{
				
				$result = deal_redempte($redempte_id,$status,$earn_money,$fee,$redempte["organiser_fee"],$pay_type,$web_type);
			}
					
			$root["show_err"] = "操作成功!";
			
			output($root);
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "到期赎回更新";
		output($root);		
	}
}
?>
