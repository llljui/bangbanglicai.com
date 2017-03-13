<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_redeem
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['program_title'] = "赎回申请";
		if ($user_id >0){
			
			require_once APP_ROOT_PATH.'system/libs/licai.php';
			$root['user_login_status'] = 1;
			$id = intval($GLOBALS['request']["id"]);
			if(!$id)
			{
				$root['show_err'] = "操作失败，请重试";
				output($root);
			}
			
			$vo = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."licai_order where user_id =".$user_id." and id=".$id);
			
			if(!$vo)
			{
				$root['show_err'] = "操作失败，请重试";
				output($root);
			}
			
			if(to_timespan($vo["end_interest_date"]) <= TIME_UTC)
			{
				$root['show_err'] = "等待发起人发放理财";
				output($root);
			}
			
			//申请了 还未赎回
			$vo["redempte_wait_pay"] = $GLOBALS["db"]->getOne("select sum(money) from ".DB_PREFIX."licai_redempte where status = 0 and user_id =".$user_id." and order_id = ".$id);
			
			$vo["redempte_wait_pay_format"] = format_price($vo["redempte_wait_pay"]);
			
			if(floatval($vo["redempte_money"])>=floatval($vo["money"]-$vo["site_buy_fee"])) 
			{
				$root['show_err'] = "已无可赎回金额";
				output($root);
			}
			
			//赎回到账时间		
			$vo["purchasing_time"] = $GLOBALS["db"]->getOne("select lc.purchasing_time from ".DB_PREFIX."licai lc 
			left join ".DB_PREFIX."licai_order lco on lco.licai_id = lc.id 
			left join ".DB_PREFIX."licai_redempte lcr on lcr.order_id = lco.id where lcr.id=".$id);
			
			//$vo["back_rate_format"] = $vo["back_rate"]."%";
			
			//$vo["back_interest_money"] = format_price($vo["money"]*$vo["back_rate"]);
			//持有金额 包括未赎回
			$vo["money_format"] = format_price($vo["money"] - $vo["site_buy_fee"]-$vo["redempte_money"]);
			
			$vo["principal_money"] = $vo["money"] - $vo["site_buy_fee"]-$vo["redempte_money"];
			//可赎回金额
			$vo["have_money"] = $vo["money"] - $vo["redempte_money"] - $vo["site_buy_fee"] - $vo["redempte_wait_pay"];
			
			$vo["have_money_format"] = format_price($vo["have_money"]);
			
			//json
			$licai = get_licai($vo["licai_id"]);
			
			if(!$licai || $licai['status'] == 0){
				$root['show_err'] = "理财产品不存在";
				output($root);
			}
			$root['licai'] = $licai;
			
			if($licai['type'] > 0){
				$licai_interest_json = json_encode($licai['licai_interest']);
			}
			else{
				$licai_interest_json = $GLOBALS["db"]->getOne("select sum(rate) from ".DB_PREFIX."licai_history where licai_id = ".$licai["id"]." and history_date >= '".$vo["begin_interest_date"]."' and history_date<= '".$vo["end_interest_date"]."'  and history_date<='".to_date(TIME_UTC,"Y-m-d")."'");
				
				$licai_interest_json = floatval($licai_interest_json);
			}
			//理财状态
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
			if($vo["licai_status"] == 0)
			{
				$vo["back_status_format"] = "预热期提前";
				//$vo["back_rate_format"] = ;
				
			}
			elseif($vo["licai_status"] == 1)
			{
				$vo["back_status_format"] = "理财期提前";
				//$vo["back_rate_format"] = ;
			}
			else
			{
				$vo["back_status_format"] = "理财结束";
				//$vo["back_rate_format"] = ;
			}
			
			$root['licai_interest_json'] = $licai_interest_json;
			//json end
			$root['vo'] = $vo;
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		output($root);		
	}
}
?>
