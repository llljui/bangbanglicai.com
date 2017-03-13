<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_deal_lc
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
			$vo = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."licai where id=".$id);
			require_once APP_ROOT_PATH.'system/libs/licai.php';
			$vo['begin_interest_date'] = $vo['begin_buy_date'];
			$vo = licai_item_format($vo,1);
			
			if($vo["type"] > 0)
			{
				$list = $GLOBALS["db"]->getAll("select * from ".DB_PREFIX."licai_interest where licai_id=".$id." order by id asc ");
				foreach($list as $k => $v)
				{
					$list[$k] = interest_item_format($v);
				}
			}
			else
			{
				$list = $GLOBALS["db"]->getAll("select * from ".DB_PREFIX."licai_history where licai_id=".$id." and history_date <='".$today."' order by history_date desc ");
				foreach($list as $k => $v)
				{
					$list[$k]["net_value_format"] = format_price($v["net_value"]);
					$list[$k]["rate_format"] = number_format($v["rate"],2)."%";
				}
			}
			
			$root['vo'] = $vo;
			$root['details'] = $vo['description'];
			
			$root['list'] = $list;
			$root['response_code'] = 1;
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "基本信息";
		output($root);		
	}
}
?>
