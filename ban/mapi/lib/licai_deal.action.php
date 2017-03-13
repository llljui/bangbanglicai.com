<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class licai_deal
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		require_once APP_ROOT_PATH.'system/libs/licai.php';
		require_once APP_ROOT_PATH.'app/Lib/page.php';

		$id = intval($GLOBALS['request']['id']);
		$licai = get_licai($id);
		
		$licai["fund_brand_name"] = $GLOBALS["db"]->getOne("select name from ".DB_PREFIX."licai_fund_brand where id =".$licai["fund_brand_id"]);
		
		if(!$licai || $licai['status'] == 0){
			$root['show_err'] = "理财产品不存在";
			output($root);
		}
		
		if($user_id){
			$root['user_info'] = $user;
		}
		
		$min_interest_rate=0;
		if($licai['type'] > 0){
			$licai_interest_json = json_encode($licai['licai_interest']);
			$min_interest_rate = $licai['licai_interest'][0]['interest_rate'];
			$max_interest_rate = $licai['licai_interest'][count($licai['licai_interest'])-1]['interest_rate'];
		}
		else{
			$licai_interest_json =json_encode($licai['licai_interest']);// $licai['average_income_rate'];
		}
		
		//为客户创造收益
		//$user_income = doubleval($GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."user_log WHERE `type`=9 "));
		$user_income = doubleval($GLOBALS['db']->getOne("select sum(earn_money) from ".DB_PREFIX."licai_redempte"));
		$root['user_income'] = $user_income;
		
		$condition = " where lc.id = ".$id;
		//图表
		//七天
		//$condition .= " and lh.history_date >= '".to_date(TIME_UTC-7*3600*24,"Y-m-d")."' and lh.history_date <= '".to_date(TIME_UTC,"Y-m-d")."'";
		if($licai["type"] == 0)
		{
			$licai_end_time = to_timespan($licai['end_date'],"Y-m-d");
			if($licai_end_time>=TIME_UTC)
			{
				$licai_end_time = to_date(TIME_UTC,"Y-m-d");
			}
			else
			{
				$licai_end_time = to_date($licai_end_time,"Y-m-d");
			}
			
			$condition .= " and history_date <= '".$licai_end_time."' and history_date >= '".$licai['begin_buy_date']."'";
			
			$data_table_count = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."licai_history lh left join ".DB_PREFIX."licai lc on lc.id=lh.licai_id ".$condition);
			
			if($data_table_count >= 7)
			{
				$limit = " limit ".($data_table_count-7).",7 ";
			}
			else
			{
				$limit = "";
			}
			
			$data_table = $GLOBALS['db']->getAll("select lh.history_date,lh.rate from ".DB_PREFIX."licai_history lh left join ".DB_PREFIX."licai lc on lc.id = lh.licai_id ".$condition." order by lh.history_date asc ".$limit);
			
			if($data_table_count == 1)
			{
				array_unshift($data_table,array("history_date"=>$data_table[0]["history_date"],"rate"=>$data_table[0]["rate"]));
			}
			foreach ($data_table as $k=>$v)
			{
				$history_date= explode("-",$data_table[$k]["history_date"]);
				$data_table[$k]["history_md"] = $history_date['1']."-".$history_date['2'];
			}
			if(to_timespan($licai['begin_buy_date'])-TIME_UTC<0){
				$root['table_status'] = 1;
				$root['data_table'] = $data_table;
			}else{
				$root['table_status'] = 0;
			}	
			
		}
		
		if($licai['scope'])	{
			$root['scope_format'] = $licai['scope'];
		}else{
			if($licai['type']==0){
				$root['scope_format'] = number_format($licai['average_income_rate'],2)."%";
			}else{
				$root['scope_format'] = number_format($min_interest_rate,2)."%起";
			}
		}
		if($licai['type']>0){
			if($licai['time_limit']){
				$root['licai_qx'] = $licai['time_limit']."个月";
			}else{
				$root['licai_qx'] = "无限期";
			}
			
		}else{
			$root['licai_qx'] = $licai['end_date'];
		}
		//$licai['TIME_UTC'] = to_date(TIME_UTC);
		if(to_timespan($licai['end_buy_date'])-TIME_UTC>0){
			$licai['end_buy_time']=to_timespan($licai['end_buy_date'])-TIME_UTC;
		}else{
			$licai['end_buy_time']=0;
		}
		
		$today = to_date(TIME_UTC,"Y-m-d");	
		$root['today'] = $today;
		
		$begin_buy_date = $licai['begin_buy_date'];
		$end_buy_date = $licai['end_buy_date'];
		
		if(($today>=$begin_buy_date)&&($end_buy_date>=$today)){
			$licai['c_status'] =1;
			$licai['status_format'] = "进行中";
		}elseif($end_buy_date<$today&&$end_buy_date>0){
			$licai['c_status'] =0;
			$licai['status_format'] = "已结束";
		}elseif($begin_buy_date>$today){
			$licai['c_status'] =0;
			$licai['status_format'] = "未开始";
		}else{
			$licai['c_status'] =1;
			$licai['status_format'] = "进行中";
		}
		
		$root['licai'] = $licai;
		
		$root['licai_interest_json'] = $licai_interest_json;
		
		$root['min_interest_rate'] = $min_interest_rate;
		$root['max_interest_rate'] = $max_interest_rate;
		$root['view_data'] = SITE_DOMAIN.wap_url("index","licai_deal#index",array("id"=>$id,"view_data"=>1,"app"=>1));
		$root['view_data'] = str_replace("/mapi","", $root['view_data']);
		$root['url_detail'] = SITE_DOMAIN.wap_url("index","licai_deal_detail#index",array("id"=>$id,"app"=>1));
		$root['url_detail'] = str_replace("/mapi","", $root['url_detail']);
		$root['program_title'] = "理财详情";
		
		output($root);		
	}
}
?>
