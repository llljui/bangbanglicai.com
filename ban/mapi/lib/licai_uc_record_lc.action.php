<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_record_lc
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['program_title'] = "购买记录";
		
		if ($user_id >0){
			
			require_once APP_ROOT_PATH.'system/libs/licai.php';
			require_once APP_ROOT_PATH.'app/Lib/page.php';
		
			$root['user_login_status'] = 1;
			
			$id = intval($GLOBALS['request']['id']);
			if(!$id)
			{
				$root['show_err'] = "操作失败，请重试";
				output($root);
			}
			
			$link_date = to_date(TIME_UTC,"Y-m-d");
			
			$root['link_date'] = $link_date;
			
			$vo = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."licai where id=".$id); 
			
			// (不考虑是否发放给发起人)
			$total_order = $GLOBALS["db"]->getRow("select 
			sum(money) as total_money,
			sum(site_buy_fee) as total_service_fee ,
			count(*) as total_count 
			from ".DB_PREFIX."licai_order lco 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lc.user_id =".$user_id." and lco.status > 0 and lc.id=".$id);
			
			$vo["name"] = $GLOBALS["db"]->getOne("select name from ".DB_PREFIX."licai where id = ".$id);
			
			$vo["licai_total_money"] = $total_order["total_money"] - $total_order["total_service_fee"];
	
			//成交总额 
			$vo["licai_total_money_format"] = $vo["licai_total_money"];
			
			$vo["licai_order_total_count"] = $total_order["total_count"];
			
			$vo["total_people_count"] = $GLOBALS["db"]->getOne("select count(distinct user_id) from ".DB_PREFIX."licai_order where licai_id=".$id);
			
			//print_r("select count(*) from ".DB_PREFIX."licai_order where licai_id=".$id." group by user_id");die;
			
			if(!$vo["total_people_count"])
			{
				$vo["total_people_count"] = 0;
			}
			
			//$vo["average_income_rate_format"] = $vo["average_income_rate"]."%";
			$vo["average_income_rate_format"] = number_format($vo["average_income_rate"], 2, '.', '')."%";
			
			//正在进行中的
			$total_ing_order = $GLOBALS["db"]->getRow("select 
			sum(money) as total_money,
			sum(site_buy_fee) as total_service_fee,
			sum(redempte_money) as total_redempte_money  
			from ".DB_PREFIX."licai_order lco 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lc.user_id =".$user_id." and lco.status in (1,2) and lc.id=".$id);
			
			$vo["licai_total_ing_money"] = $total_ing_order["total_money"] - $total_ing_order["total_service_fee"] - $total_ing_order['total_redempte_money'];
			
			//开始
			$conditon = " and lco.status>0 ";
			
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$search = array();
			
			//购买开始时间
			
			$buy_begin_time = strim($GLOBALS['request']['buy_begin_time']);
			$buy_end_time = strim($GLOBALS['request']['buy_end_time']);
	
			$d = explode('-',$buy_begin_time);
			if (isset($GLOBALS['request']['buy_begin_time']) && $buy_begin_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
				$root['show_err'] = "开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)";
				output($root);
			}
			
			$d = explode('-',$buy_end_time);
			if ( isset($GLOBALS['request']['buy_end_time']) && strim($buy_end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
				
				$root['show_err'] = "结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)";
				output($root);
			}
			
			if ($buy_begin_time!="" && strim($buy_end_time) !="" && to_timespan($buy_begin_time) > to_timespan($buy_end_time)){
				
				$root['show_err'] = "开始时间不能大于结束时间:'.$buy_begin_time.'至'.$buy_end_time";
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
				
				$root['show_err'] = "开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)";
				output($root);
			}
			
			$d = explode('-',$end_time);
			if ( isset($GLOBALS['request']['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
				
				$root['show_err'] = "结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)";
				output($root);
			}
			
			if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
				$root['show_err'] = "开始时间不能大于结束时间:'.$start_time.'至'.$end_time";
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
	
			$root['search'] = $search;
			
			$count = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."licai_order lco 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lc.id = ".$id." and lc.user_id = ".$user_id.$condition );
			
			//结束
	
			$list = $GLOBALS["db"]->getAll("select lco.* from ".DB_PREFIX."licai_order lco 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lc.id = ".$id." and lc.user_id = ".$user_id.$condition." order by id desc limit ".$limit);
			
			
			
			foreach($list as $k=>$v)
			{
				$list[$k]["have_money"] = $v["have_money"] = $v["money"] - $v["site_buy_fee"] - $v["redempte_money"];
				
				$list[$k]["have_money_format"] = format_price($v["have_money"]);
				
				//$list[$k]["interest_rate_format"] = $v["interest_rate"]."%";
				
				$list[$k]["interest_rate_format"] = number_format($v["interest_rate"], 2, '.', '')."%";
	
				if($v["status"] == 0)
				{
					$list[$k]["status_format"] = "未支付";
				}
				elseif($v["status"] == 1)
				{
					$list[$k]["status_format"] = "已支付";
				}
				elseif($v["status"] == 2)
				{
					$list[$k]["status_format"] = "部分赎回";
				}
				elseif($v["status"] == 3)
				{
					$list[$k]["status_format"] = "已完结";
				}
				$app_url = APP_ROOT."/index.php?ctl=licai&act=i_contract&is_sj=1&id=".$list[$k]["id"]."";
				$app_url = str_replace("/mapi", "", SITE_DOMAIN.$app_url);
				$list[$k]["licai_contract"] = $app_url;
			}
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			$root['vo'] = $vo;
			$root['list'] = $list;
			$root['id'] = $id;
			
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "购买记录";
		output($root);		
	}
}
?>
