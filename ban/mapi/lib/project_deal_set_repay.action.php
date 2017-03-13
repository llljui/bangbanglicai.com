<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_deal_set_repay
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		if(!$GLOBALS['user_info'])
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root["program_title"]="发放回报";
		$order_id= intval($GLOBALS["request"]['id']);
	
		$order_info = $GLOBALS['db']->getRow("select d.*,di.description as item_description,di.is_delivery from ".DB_PREFIX."project_order as d left join ".DB_PREFIX."project_item as di on di.id=d.deal_item_id where d.id = ".$order_id." and d.order_status = 3 and d.is_refund = 0 and d.deal_item_id >0 order by d.create_time desc");
		
		$root["order_info"]=$order_info;
		
		output($root);
	}
}
?>
