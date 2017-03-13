<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_published_lc
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['program_title'] = "发起的理财";
		
		if ($user_id >0){
			
			require_once APP_ROOT_PATH.'system/libs/licai.php';
			require_once APP_ROOT_PATH.'app/Lib/page.php';
		
			$root['user_login_status'] = 1;
			
			// (不考虑是否发放给发起人)
			$total_order = $GLOBALS["db"]->getRow("select 
			sum(money) as total_money,
			sum(site_buy_fee) as total_service_fee 
			from ".DB_PREFIX."licai_order lco 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lc.user_id =".$user_id." and lco.status > 0");
			
			$vo["licai_total_money"] = $total_order["total_money"] - $total_order["total_service_fee"];
			//成交总额 
			$vo["licai_total_money_format"] = $vo["licai_total_money"];
			
			$licai_count = $GLOBALS["db"]->getRow("select sum(total_people) as total_people_count,count(*) as total_count from ".DB_PREFIX."licai where user_id=".$user_id);
			
			$vo["total_people_count"] = $licai_count["total_people_count"]?$licai_count["total_people_count"]:0;
			
			$vo["licai_total_count"] = $licai_count["total_count"];
			
			//正在进行中的
			$total_ing_order = $GLOBALS["db"]->getRow("select 
			sum(money) as total_money,
			sum(site_buy_fee) as total_service_fee,
			sum(redempte_money) as total_redempte_money 
			from ".DB_PREFIX."licai_order lco 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lc.user_id =".$user_id." and lco.status in (1,2)");
			
			$vo["licai_total_ing_money"] = $total_ing_order["total_money"] - $total_ing_order["total_service_fee"] - $total_ing_order["total_redempte_money"];
			
			$root['vo'] = $vo;
			$condition =" and status = 1";
			
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
				$root['in_status'] = 0;
				$root['show_err'] = "开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)";
				//output($root);
			}
			
			$d = explode('-',$buy_end_time);
			if ( isset($GLOBALS['request']['buy_end_time']) && strim($buy_end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
				$root['in_status'] = 0;
				$root['show_err'] = "结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)";
				//output($root);
			}
			
			if ($buy_begin_time!="" && strim($buy_end_time) !="" && to_timespan($buy_begin_time) > to_timespan($buy_end_time)){
				$root['in_status'] = 0;
				$root['show_err'] = "开始时间不能大于结束时间:'.$buy_begin_time.'至'.$buy_end_time";
				//output($root);
			}
			if(strim($buy_begin_time)!="")
			{
				$condition .= " and lc.begin_buy_date >= '".strim($buy_begin_time)."'";
				$search["buy_begin_time"] = strim($GLOBALS['request']["buy_begin_time"]);
				
			}
			if(strim($buy_end_time) !="")
			{
				$condition .= " and lc.begin_buy_date <= '".  strim($buy_end_time)."'";
				$search["buy_end_time"] = strim($GLOBALS['request']["buy_end_time"]);
			}
			
			//项目结束时间
			$start_time = strim($GLOBALS['request']['begin_time']);
			$end_time = strim($GLOBALS['request']['end_time']);
	
			$d = explode('-',$start_time);
			if (isset($GLOBALS['request']['begin_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
				$root['in_status'] = 0;
				$root['show_err'] = "开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)";
				//output($root);
			}
			
			$d = explode('-',$end_time);
			if ( isset($GLOBALS['request']['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
				$root['in_status'] = 0;
				$root['show_err'] = "结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)";
				//output($root);
			}
			
			if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
				$root['in_status'] = 0;
				$root['show_err'] = "开始时间不能大于结束时间:'.$start_time.'至'.$end_time";
				//output($root);
			}
			if(strim($start_time)!="")
			{
				$condition .= " and lc.end_date >= '".$start_time."'";
				$search["begin_time"] = $start_time;
			}
			if(strim($end_time) !="")
			{
				$condition .= " and lc.end_date <= '".  $end_time."'";
				$search["end_time"] = $end_time;
			}
			
			//$root['status'] = 1;
			
			$count = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."licai lc where user_id ='".$user_id."'  ".$condition );
					
			$list = $GLOBALS["db"]->getAll("select * from ".DB_PREFIX."licai lc where user_id ='".$user_id."'  ".$condition." order by id desc limit ".$limit );
			
			foreach($list as $k=>$v)
			{
				$list[$k] = licai_item_format($v);
				$list[$k]["total_money"] = $GLOBALS["db"]->getOne("select sum(money)-sum(site_buy_fee) from ".DB_PREFIX."licai_order where licai_id = ".$v["id"]); 
				$list[$k]["total_money_format"] = format_price($list[$k]["total_money"]); 
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
		output($root);		
	}
}
?>
