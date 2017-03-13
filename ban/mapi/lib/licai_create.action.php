<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_create
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$fund_brand = $GLOBALS['db']->getAll("SELECT * from ".DB_PREFIX."licai_fund_brand where status = 1 ");
			$root['fund_brand'] = $fund_brand;
			
			$fund_type = $GLOBALS['db']->getAll("SELECT * from ".DB_PREFIX."licai_fund_type where status = 1 ");
			$root['fund_type'] = $fund_type;
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
