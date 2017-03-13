<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class licai_deal_detail
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
			
		$root['licai'] = $licai;
		
		
		$root['program_title'] = "详细信息";
		
		output($root);		
	}
}
?>
