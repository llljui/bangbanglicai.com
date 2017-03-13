<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_deal_save_repay
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
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		
		$order_id = intval($GLOBALS["request"]['id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_order where id = ".$order_id." and order_status = 3 and is_refund = 0");
		if(!$order_info)
		{
			$root['show_err'] = "无权为该订单设置回报";
			$root['status'] = 0;
			output($root);
		}
		
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$order_info['deal_id']." and is_delete = 0 and is_effect = 1 and is_success = 1 and user_id = ".intval($GLOBALS['user_info']['id']));
		if(!$deal_info)
		{
			$root['show_err'] = "无权为该订单设置回报";
			$root['status'] = 0;
			output($root);
		}
		
		$order_info['repay_time'] = TIME_UTC;
		$order_info['repay_memo'] = strim($GLOBALS["request"]['repay_memo']);
		
		if($order_info['repay_memo']=="")
		{
			$root['show_err'] = "请输入回报内容";
			$root['status'] = 0;
			output($root);
		}
		$order_info['logistics_company'] = strim($GLOBALS["request"]['logistics_company']);
		$order_info['logistics_links'] = strim($GLOBALS["request"]['logistics_links']);
		$order_info['logistics_number'] = strim($GLOBALS["request"]['logistics_number']);
		$GLOBALS['db']->autoExecute(DB_PREFIX."project_order",$order_info,"UPDATE","id=".$order_info['id']);
		if($GLOBALS['db']->affected_rows()>0)
		{
			if($order_info['share_fee']>0 && $order_info['share_status'] ==0 )
			{
				require_once APP_ROOT_PATH."system/libs/user.php";
								
				modify_account(array("money"=>$order_info['share_fee']),intval($order_info['user_id']),$order_info['deal_name']."项目成功，(订单:".$order_info['id'].")回报所得分红。",52);						
				$GLOBALS['db']->query("update ".DB_PREFIX."project_order set share_status=1 where id=".intval($order_info['id'])." and share_status=0");
			}
			//send_notify($order_info['user_id'],"您支持的项目".$order_info['deal_name']."回报已发放","account#view_order","id=".$order_info['id']);

			if($order_info['type']==1){
				$root['show_err'] = "回报设置成功";
				$root['status'] = 1;
				output($root);
			}
			else{
				$root['show_err'] = "回报设置成功";
				$root['status'] = 1;
				output($root);
			}
		}else
		{
			$root['show_err'] = "回报设置失败";
			$root['status'] = 0;
			output($root);
		}
		
		output($root);
	}
}
?>
