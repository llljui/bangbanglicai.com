<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_expire_lc
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			require_once APP_ROOT_PATH.'system/libs/licai.php';
		
			$root['user_login_status'] = 1;
		
			$vo = $GLOBALS["db"]->getRow("select 
			sum(lco.money) as licai_total_money,
			count(*) as licai_total_count ,
			sum(lco.interest_rate*lco.money) as interest_rate_money 
			from ".DB_PREFIX."licai_order lco left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id 
			where lc.user_id = '".$user_id."' and lco.end_interest_date = date(now())");
			
			//成交总额
			$vo["licai_total_money_format"] = format_price($vo["licai_total_money"]);
			//利息和
			$vo["interest_rate_money_format"] = format_price($vo["interest_rate_money"]);
			//发起总额
			$vo["licai_all_money"] = $vo["licai_total_money"] + $vo["interest_rate_money"];
			
			$vo["licai_all_money_format"] = format_price($vo["licai_all_money"]);
			
			$root['vo'] = $vo;
			
			$condition =" and lco.status in (1,2,3) ";
			
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
			
			if(strim($GLOBALS['request']["user_name"])!="")
			{
				$condition .= " and lco.user_name like '%".strim($GLOBALS['request']["user_name"])."%'";
				$search["user_name"] = strim($GLOBALS['request']["user_name"]);
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
			
			if(!$start_time)
			{
				$start_time = to_date(TIME_UTC-15*24*3600,"Y-m-d");
			}
			if(!$end_time)
			{
				$end_time = to_date(TIME_UTC,"Y-m-d");
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
			where lc.user_id ='".$user_id."' ".$condition );
							
			$list = $GLOBALS["db"]->getAll("select lco.*,lc.name as licai_name ,lc.type as licai_type 
			from ".DB_PREFIX."licai_order lco
			left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id 
			where lc.user_id ='".$user_id."'  ".$condition." order by lco.id desc limit ".$limit );
	
			foreach($list as $k=>$v)
			{
				$list[$k]["money_format"] = format_price($v["money"]-$v['redempte_money']-$v["site_buy_fee"]);
				if($v["licai_type"] > 0)
				{
					$list[$k]["before_rate_format"] = number_format($v["before_rate"],2)."%";
					$list[$k]["interest_rate_format"] = number_format($v["interest_rate"],2)."%";
				}
				else
				{
					$licai_interest = get_licai_interest_yeb($v['licai_id'],$v["begin_interest_date"],$v["end_interest_date"]);
					$list[$k]["rate_format"] = number_format($licai_interest["avg_interest_rate"],2)."%";
				}
				if($v["licai_type"] == 0)
				{
					$list[$k]["type_format"] = "余额宝";
				}
				elseif($v["licai_type"] == 1)
				{
					$list[$k]["type_format"] = "固定定存";
				}
				$list[$k]["create_time"] = to_date(to_timespan($v["create_time"]),"Y-m-d");
				//$list[$k] = licai_item_format($v);
			}
		
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
			$root['list'] = $list;
			$root['search'] = $search;
			$root['response_code'] = 1;
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		$root['program_title'] = "快到期的理财发放";
		output($root);		
	}
}
?>
