<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class uc_project_del_order
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		if(!$user)
		{
			$root['status'] = 2;
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		
		$order_id = intval($GLOBALS["request"]['id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_order where order_status = 0 and user_id = ".$user_id." and id = ".$order_id);
		
		if(!$order_info)
		{
			$root['response_code'] = 0;
			$root['show_err'] = "无效的订单";
			output($root);
		}
		else
		{
			$money = $order_info['credit_pay'];
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_order where id = ".$order_id." and user_id = ".$user_id." and order_status = 0");
			if($GLOBALS['db']->affected_rows()>0)
			{
				if($money>0)
				{
					require_once APP_ROOT_PATH."system/libs/user.php";
					modify_account(array("money"=>$money),$user_id,"删除".$order_info['deal_name']."项目支付，退回支付款。",51);	
				}
			}
			
			$root["status"] = 1;
			$root['response_code'] = 1;
			$root['show_err'] = "删除成功";
		}
		
		output($root);
	
	}
}
?>
