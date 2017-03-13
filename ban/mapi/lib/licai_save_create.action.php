<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_save_create
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$order = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."licai where user_id =".$user_id." and status =0 and verify = 0");
			
			if($order)
			{
				$root['show_err'] ="您已经有申请的理财在审核，请耐心等待";
				$root['response_code'] = 0;
				output($root);	
			}
			
			$data = array();
			$data["name"] = $GLOBALS['request']["name"];
			$lc_sn = $GLOBALS["db"]->getOne("select max(id) from ".DB_PREFIX."licai");
			$data['sort'] = $lc_sn+1;
			$data["licai_sn"] = "LC".to_date(TIME_UTC,"Y")."".str_pad($lc_sn+1,7,0,STR_PAD_LEFT);
			$data["user_id"] = $user_id;
			$data['img'] = strim($GLOBALS['request']['img']);
			
			$data['begin_buy_date'] = strim($GLOBALS['request']['begin_buy_date']);
			$data['end_buy_date'] = strim($GLOBALS['request']['end_buy_date']);
			$data['begin_interest_date'] = strim($GLOBALS['request']['begin_interest_date']);
			$data['end_date'] = strim($GLOBALS['request']['end_date']);
			$data['min_money'] = floatval($GLOBALS['request']['min_money']);
			$data['max_money'] = floatval($GLOBALS['request']['max_money']);
			
			$data['scope'] = strim($GLOBALS['request']['scope']);
			
			$data['profit_way'] = strim($GLOBALS['request']['profit_way']);
			
			$data['time_limit'] = intval($GLOBALS['request']['time_limit']);
			
			$data['begin_interest_type'] = intval($GLOBALS['request']['begin_interest_type']);
			
			$data['product_size'] = strim($GLOBALS['request']['product_size']);
			
			$data['type'] = intval($GLOBALS['request']['type']);
			
			$data['status'] = 0;
			
			$data['purchasing_time'] = strim($GLOBALS['request']['purchasing_time']);
	
			$data['description'] = replace_public(btrim($GLOBALS['request']['description']));
	    	$data['description'] = valid_tag($data['description']);
			
			$data['brief'] = replace_public(btrim($GLOBALS['request']['brief']));
	    	$data['brief'] = valid_tag($data['brief']);
	
			$data['rule_info'] = replace_public(btrim($GLOBALS['request']['rule_info']));
	    	$data['rule_info'] = valid_tag($data['rule_info']);
			
			$data['net_value'] = strim($GLOBALS['request']['net_value']);
			$data['fund_key'] = strim($GLOBALS['request']['fund_key']);
			$data['fund_type_id'] = intval($GLOBALS['request']['fund_type_id']);
			$data['fund_brand_id'] = intval($GLOBALS['request']['fund_brand_id']);
			
			
			//$data['risk_rank'] = intval($_REQUEST['risk_rank']); //风险等级
			
			$data['verify'] = 0;
			
			if($data['name']=="")
			{
				$root['show_err'] ="请输入名称";
				$root['response_code'] = 0;
				output($root);
			}
			if($data['begin_buy_date'] == "" || $data['begin_buy_date'] == '00000000')
			{
				$root['show_err'] ="请选择理财开始购买时间";
				$root['response_code'] = 0;
				output($root);
			}
			if($data['max_money'] == 0)
			{
				$root['show_err'] ="单笔最大购买限额";
				$root['response_code'] = 0;
				output($root);
			}
			//余额宝
			if($data['type'] == 0)
			{
				
				if($data['end_date'] == "" || $data['end_date'] == '00000000')
				{
					$root['show_err'] ="请选择理财结束时间";
					$root['response_code'] = 0;
					output($root);
				}
			}
			//定存宝
			else
			{
				if($data['begin_interest_date'] == ""|| $data['begin_interest_date'] == '00000000')
				{
					$root['show_err'] ="请选择起息时间";
					$root['response_code'] = 0;
					output($root);
				}
				if($data['time_limit'] && ($data['end_date'] == ""|| $data['end_date'] == '00000000'))
				{
					$root['show_err'] ="项目结束时间和理财期限至少填写一个";
					$root['response_code'] = 0;
					output($root);
				}
			}
			$GLOBALS['db']->autoExecute(DB_PREFIX."licai",$data,"INSERT");
			
			$root['show_err'] ="提交成功，等待管理员审核";
			$root['response_code'] = 1;
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "发起理财";
		output($root);		
	}
}
?>
