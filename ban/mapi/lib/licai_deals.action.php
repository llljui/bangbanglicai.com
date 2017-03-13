<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class licai_deals
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		$today = to_date(TIME_UTC,"Y-m-d");	
		
		$root['today'] = $today;
		
		require_once APP_ROOT_PATH.'system/libs/licai.php';
		//require_once APP_ROOT_PATH.'app/Lib/page.php';
		$filter_parms =array();
		
		$filter_parms['type'] = $type = isset($GLOBALS['request']['type']) ? intval($GLOBALS['request']['type']) : 0;
		//起购金额
		$filter_parms['money'] = $money = isset($GLOBALS['request']['money']) ? intval($GLOBALS['request']['money']) : 0;
		//年化收益
		$filter_parms['rate'] = $rate = isset($GLOBALS['request']['rate']) ? intval($GLOBALS['request']['rate']) : 0;
		
		$filter_parms['sortby'] = $sortby = isset($GLOBALS['request']['sortby']) ? strim($GLOBALS['request']['sortby']) : "";
		$filter_parms['descby'] = $descby = isset($GLOBALS['request']['descby']) ?strtoupper(strim($GLOBALS['request']['descby'])) : "DESC";
		
		$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$condition = " status = 1 ";
		if($type!=""){
			$condition .= " AND `type` = $type  ";
		}
		
		if($money != 0){
			switch($money){
				case 1:
					$condition.=" AND min_money <= 1000 ";
					break;
				case 2:
					$condition.=" AND min_money >= 1000 AND min_money <=10000  ";
					break;
				case 3:
					$condition.=" AND min_money >= 10000 AND min_money <=30000  ";
					break;
				case 4:
					$condition.=" AND min_money >= 30000 AND min_money <=50000  ";
					break;
				case 5:
					$condition.=" AND min_money >= 50000 AND min_money <=100000  ";
					break;
				case 6:
					$condition.=" AND min_money >= 100000 AND min_money <=150000  ";
					break;
				case 7:
					$condition.=" AND min_money >= 150000 AND min_money <=200000  ";
					break;
				case 8:
					$condition.=" AND min_money >= 200000 ";
					break;
			}
		}
		
		if($rate != 0){
			switch($rate){
				case 1:
					$condition.=" AND average_income_rate <= 4.5 ";
					break;
				case 2:
					$condition.=" AND average_income_rate between 4.5 AND  5.6  ";
					break;
				case 3:
					$condition.=" AND average_income_rate between 5.6 AND 6  ";
					break;
				case 4:
					$condition.=" AND average_income_rate between 6 AND  7  ";
					break;
				case 5:
					$condition.=" AND average_income_rate between 7 AND  8  ";
					break;
				case 6:
					$condition.=" AND average_income_rate between 8 AND 9  ";
					break;
				case 7:
					$condition.=" AND average_income_rate >= 9  ";
					break;
			}
		}
		
		$orderBy = "`sort` DESC,id DESC";
		if($sortby!=""){
			$orderBy = $sortby." ".$descby.", `sort` DESC,id DESC ";
		}


		$result = get_licai_list($condition,$orderBy,$limit);
		
		foreach ( $result ['list'] as $k => $v )
		{
			$begin_buy_date = $result ['list'][$k]['begin_buy_date'];
			$end_buy_date = $result ['list'][$k]['end_buy_date'];
			
			if(($today>=$begin_buy_date)&&($end_buy_date>=$today)){
				$result ['list'][$k]['c_status'] =1;
				$result ['list'][$k]['status_format'] = "进行中";
			}elseif($end_buy_date<$today&&$end_buy_date>0){
				$result ['list'][$k]['c_status'] =0;
				$result ['list'][$k]['status_format'] = "已结束";
			}elseif($begin_buy_date>$today){
				$result ['list'][$k]['c_status'] =0;
				$result ['list'][$k]['status_format'] = "未开始";
			}else{
				$result ['list'][$k]['c_status'] =1;
				$result ['list'][$k]['status_format'] = "进行中";
			}
			if($result ['list'][$k]['time_limit']){
				$result ['list'][$k]['time_limit_format'] = $result ['list'][$k]['time_limit']."个月";
			}else{
				$result ['list'][$k]['time_limit_format'] = "无期限";
			}
			

		}
		
		$root['list'] =  $result['list'];
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($result['rs_count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
		
		//为客户创造收益
		//$user_income = doubleval($GLOBALS['db']->getOne("select sum(earn_money) from ".DB_PREFIX."user_log WHERE `type`=9 "));
		$user_income = round($GLOBALS['db']->getOne("select sum(earn_money) from ".DB_PREFIX."licai_redempte"),2);
		 
		$root['user_income'] = $user_income;
		$root['program_title'] = "理财列表";
		
		output($root);		
	}
}
?>
