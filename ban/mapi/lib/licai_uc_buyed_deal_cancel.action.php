<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_buyed_deal_cancel
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			require_once APP_ROOT_PATH.'system/libs/licai.php';
			
			$redempte_id = intval($GLOBALS['request']["redempte_id"]);
			
			$redempte_info = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."licai_redempte where id =".$redempte_id." and status=0 and user_id =".$user_id);
			
			if(!$redempte_info)
			{
				$root["status"] = 0;
				$root["info"] = "操作失败，请重试";
				output($root);
				
			}
			
			$status = 3;
			
			$earn_money = 0;
			$fee = 0;
			$organiser_fee = 0;
			$pay_type = 0;
			$web_type = 0;
			
			$result = deal_redempte($redempte_id,$status,$earn_money,$fee,$organiser_fee,$pay_type,$web_type);
			$root["info"] = "取消成功";
			$root['result'] = $result;
			
		}else{
			$root['response_code'] = 0;
			$root['info'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "赎回取消";
		output($root);		
	}
}
?>
