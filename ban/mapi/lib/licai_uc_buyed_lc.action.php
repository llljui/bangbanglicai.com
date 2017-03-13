<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_buyed_lc
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/licai.php';
			
			$root['user_login_status'] = 1;
			
		
			$vo["interest_rate_money"] = $GLOBALS["db"]->getOne("select sum(earn_money-fee) from ".DB_PREFIX."licai_redempte where user_id = '".$user_id."' and status = 1");
		
			$vo["interest_rate_money_format"] = format_price($vo["interest_rate_money"]);
			
			$order_info = $GLOBALS["db"]->getRow("select 
			sum(money-site_buy_fee-redempte_money) as have_money,
			count(*) as licai_order_count 
			from ".DB_PREFIX."licai_order lco where user_id = '".$user_id."' and status in (1,2)");
			
			$vo["licai_order_count"] = intval($order_info["licai_order_count"]);
			
			$vo["have_money_format"] = format_price($order_info["have_money"]);
			
			$root['vo'] = $vo;
			
			$condition =" ";
			
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$search = array();
			
			
			if(strim($GLOBALS['request']["deal_name"])!="")
			{
				$condition .= " and lc.name like '%".strim($GLOBALS['request']["deal_name"])."%'";
				$search["deal_name"] = strim($GLOBALS['request']["deal_name"]);
			}
			
			if(strim($GLOBALS['request']["licai_type"])!=="" && intval($GLOBALS['request']["licai_type"])!=-1)
			{
				$condition .= " and lc.type = ".intval($GLOBALS['request']["licai_type"]);
				$search["licai_type"] = intval($GLOBALS['request']["licai_type"]);
			}
			else
			{
				$search["licai_type"] = -1;
			}
			
			//购买开始时间
			$buy_begin_time = strim($GLOBALS['request']['buy_begin_time']);
			$buy_end_time = strim($GLOBALS['request']['buy_end_time']);
	
			$d = explode('-',$buy_begin_time);
			if (isset($GLOBALS['request']['buy_begin_time']) && $buy_begin_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
				
				$root['show_err'] ="开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)";
				output($root);	
			}
			
			$d = explode('-',$buy_end_time);
			if ( isset($GLOBALS['request']['buy_end_time']) && strim($buy_end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
				$root['show_err'] ="结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)";
				output($root);
			}
			
			if ($buy_begin_time!="" && strim($buy_end_time) !="" && to_timespan($buy_begin_time) > to_timespan($buy_end_time)){
				$root['show_err'] ="开始时间不能大于结束时间:'.$buy_begin_time.'至'.$buy_end_time";
				output($root);
			}
			if(strim($buy_begin_time)!="")
			{
				$condition .= " and lco.create_date >= '".strim($buy_begin_time)."'";
				$search["buy_begin_time"] = strim($GLOBALS['request']["buy_begin_time"]);
				
			}
			if(strim($buy_end_time) !="")
			{
				$condition .= " and lco.create_date <= '".  strim($buy_end_time)."'";
				$search["buy_end_time"] = strim($GLOBALS['request']["buy_end_time"]);
			}
			
			//项目结束时间
			$start_time = strim($GLOBALS['request']['begin_time']);
			$end_time = strim($GLOBALS['request']['end_time']);
	
			$d = explode('-',$start_time);
			if (isset($GLOBALS['request']['begin_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
				
				$root['show_err'] ="开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)";
				output($root);
			}
			
			$d = explode('-',$end_time);
			if ( isset($GLOBALS['request']['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
				
				$root['show_err'] ="结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)";
				output($root);
			}
			
			if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
				
				$root['show_err'] ="开始时间不能大于结束时间:'.$start_time.'至'.$end_time";
				output($root);
			}
			if(strim($start_time)!="")
			{
				$condition .= " and lco.end_interest_date >= '".$start_time."'";
				$search["begin_time"] = $start_time;
			}
			if(strim($end_time) !="")
			{
				$condition .= " and lco.end_interest_date <= '".  $end_time."'";
				$search["end_time"] = $end_time;
			}
			
			$count = $GLOBALS["db"]->getOne("select count(*) 
			from ".DB_PREFIX."licai_order lco 
			left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id 
			where lco.user_id ='".$user_id."' ".$condition );
				
			$list = $GLOBALS["db"]->getAll("select lco.*,lc.name as licai_name,lc.time_limit ,lc.type 
			from ".DB_PREFIX."licai_order lco 
			left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id 
			where lco.user_id ='".$user_id."'  ".$condition." order by lco.id desc limit ".$limit );
	
			foreach($list as $k=>$v)
			{
				$list[$k]["money_format"] = format_price($v["money"]);
				
				$list[$k]["have_money"] = $v["have_money"] = floatval($v["money"]) - floatval($v["site_buy_fee"]) -floatval($v["redempte_money"]);
				
				$list[$k]["have_money_format"] = format_price($v["have_money"]);
				
				if($v["type"] > 0)
				{
					$list[$k]["before_rate_format"] = number_format($v["before_rate"],2)."%";
					$list[$k]["interest_rate_format"] = number_format($v["interest_rate"],2)."%";
				}
				else
				{
					$list[$k]["before_rate_format"] = "无";
					$licai_intereset = get_licai_interest_yeb($v["licai_id"],$v["begin_interest_date"],$v["end_interest_date"]);
					$list[$k]["interest_rate_format"] = number_format($licai_intereset["avg_interest_rate"],2)."%";
				}
				switch($v["status"])
				{
					case 0 :
						$list[$k]["status_format"] = "未支付";
						break;
					case 1 :
						$list[$k]["status_format"] = "已支付";
						break;
					case 2 :
						$list[$k]["status_format"] = "部分赎回";
						break;
					case 3 :
						$list[$k]["status_format"] = "已完结";
						break;
				}
				
				if(to_timespan($v["end_interest_date"]) > TIME_UTC)
				{
					$list[$k]["end_status"] = 0;
				}
				else
				{
					$list[$k]["end_status"] = 1;
				}
				
				if(to_timespan($v["begin_interest_date"]) > TIME_UTC)
				{
					$list[$k]["begin_status"] = 1;
				}
				else
				{
					$list[$k]["begin_status"] = 0;
				}
			
			
				if($v["type"]==0)
				{
					$list[$k]["type_format"] = "余额宝";
				}
				elseif($v["type"]==1)
				{
					$list[$k]["type_format"] = "固定定存";
				}
				
				$list[$k]["create_time"] = to_date(to_timespan($v["create_time"]),"Y-m-d");
				
				$redempte_money = $GLOBALS["db"]->getRow("select sum(money) from ".DB_PREFIX." where licai_id = ".$v["licai_id"]." and status = 0 and user_id =".$user_id);
				if($v["have_money"] <= $redempte_money)
				{
					$list[$k]["money_over"] = 1 ;
				}
				else
				{
					$list[$k]["money_over"] = 0 ;
				}
				//$list[$k] = licai_item_format($v);
			}
			
			$root['list'] = $list;
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
			$root['search'] = $search;
			$root['response_code'] = 1;
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		$root['program_title'] = "购买的理财";
		output($root);		
	}
}
?>
