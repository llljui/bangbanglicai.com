<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_del_item
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
		
		
		$id = intval($GLOBALS["request"]['id']);
		$item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_item where id = ".$id);
		$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where is_edit = 1 and (is_effect = 0 or is_effect = 2) and is_delete = 0 and id = ".$item['deal_id']." and user_id = ".$user_id);
		if($deal_item&&$item)
		{		
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_item where id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_item_image where deal_item_id = ".$id);
			
			
			$root["status"] = 1;
			$root["show_err"] = "删除成功";
			
		}
		else
		{
			$root["status"] = 0;
			$root["show_err"] = "删除成功";
		}
		
		output($root);
	
	}
}
?>
