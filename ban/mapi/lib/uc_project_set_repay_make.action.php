<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_project_set_repay_make
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;

		if(!$user)
		{
			$root['status'] = 2;
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		
		$order_id = intval($GLOBALS["request"]['id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_order where repay_time>0 and user_id = ".$user_id." and id = ".$order_id);
		
		if(!$order_info)
		{
			$root["status"] = 0;
			$root['show_err'] = "无效的订单";
			output($root);
		}
		else
		{
			if($order_info['repay_make_time']==0)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."project_order set repay_make_time =  ".TIME_UTC." where id = ".$order_info['id']." and user_id = ".$user_id);
			
				$root["status"] = 1;
				$root['show_err'] = "操作成功";
			}
			else
			{
				$root["status"] = 0;
				$root['show_err'] = "操作失败";
			}
		}
		
		output($root);		
	}
}
?>
