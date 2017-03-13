<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_add_item
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
			$root['status']=0;
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}		
		
		
		$id = intval($GLOBALS["request"]['id']);
		$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where is_delete = 0 and id = ".$id." and user_id = ".$user_id);
		if($deal_item)
		{		

			$deal_item_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_item where deal_id = ".$deal_item['id']." order by price asc");
			$root["deal_item_list"]=$deal_item_list;
			$root["deal_item"]=$deal_item;
			$root["program_title"]="回报设置 - ".$deal_item['name'];
		}
		
		output($root);
	
	}
}
?>
