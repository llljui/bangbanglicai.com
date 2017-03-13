<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_yeb_lc
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			$today=to_date(TIME_UTC,"Y-m-d");
			$id = intval($GLOBALS['request']["id"]);
			
			if(!$id)
			{
				$root["status"] = 0;
				$root["info"] = "操作失败，请重试";
				output($root);
			}
			
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			
			$vo = $GLOBALS["db"]->getRow("select lco.*,lc.name from ".DB_PREFIX."licai_order lco left join ".DB_PREFIX."licai lc on lc.id=lco.licai_id where lco.id=".$id);
			
			$vo["have_money"] = $vo["money"]-$vo["redempte_money"]-$vo["site_buy_fee"];
			
			$list = $GLOBALS["db"]->getAll("select history_date,rate,net_value,rate/100/365*".$vo["have_money"]." as money from ".DB_PREFIX."licai_history where licai_id=".$vo["licai_id"]." and history_date >='".$vo["begin_interest_date"]."' and history_date <='".$vo["end_interest_date"]."' and history_date <='".$today."'  order by history_date desc ");
			
			$vo["interest_money"] = $GLOBALS["db"]->getOne("select sum(rate/100/365*".$vo["have_money"].") from ".DB_PREFIX."licai_history where licai_id=".$vo["licai_id"]." and history_date >='".$vo["begin_interest_date"]."' and history_date <='".$vo["end_interest_date"]."'");
			
			$count = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."licai_history where licai_id=".$vo["licai_id"]." and history_date >='".$vo["begin_interest_date"]."' and history_date <='".$vo["end_interest_date"]."' and history_date <='".$today."' ");;
			
			$vo["have_money_format"] = format_price($vo["have_money"]);
			
			$vo["site_buy_fee_format"] = format_price($vo["site_buy_fee"]);
			
			/*foreach($list as $k=>$v)
			{
				$list[$k]["money"] = $v["rate"]*($vo["have_money"]);
			}*/
			
			$vo["interest_money_format"] = format_price($vo["interest_money"]);
			
			foreach($list as $k => $v)
			{
				$list[$k]["net_value_format"] = format_price($v["net_value"]);
				$list[$k]["money_format"] = format_price($v["money"]);
			}
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
			$root['list'] = $list;
			$root['vo'] = $vo;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "收益详情";
		output($root);		
	}
}
?>
