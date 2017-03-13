<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_delete
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
		
		$GLOBALS['db']->query("delete from ".DB_PREFIX."project where id = ".$id." and is_edit = 1 and user_id = ".$user_id." and is_effect = 0 and is_delete = 0");
		if($GLOBALS['db']->affected_rows()>0)
		{
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_item where deal_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_item_image where deal_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_comment where deal_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_faq where deal_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_focus_log where deal_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_log where deal_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_pay_log where deal_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_support_log where deal_id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_visit_log where deal_id = ".$id);
			$root['status'] = 1;
			$root['show_err'] = "删除成功";
		}
		else
		{
			$root['status'] = 0;
			$root['show_err'] = "删除失败";
		}
		
		
		output($root);
	
	}
}
?>
