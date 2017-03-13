<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
class uc_project_del_focus
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
			output($root);
		}
		
		$id = intval($GLOBALS["request"]['id']);
		
		$deal_id = $GLOBALS['db']->getOne("select deal_id from ".DB_PREFIX."project_focus_log where id = ".$id." and user_id = ".$user_id);
		
		$GLOBALS['db']->query("delete from ".DB_PREFIX."project_focus_log where id = ".$id." and user_id = ".$user_id);
		
		$GLOBALS['db']->query("update ".DB_PREFIX."project set focus_count = focus_count - 1 where id = ".intval($deal_id));
						
		$root['status'] = 1;
		$root['show_err'] ="取消成功";
		$root['response_code'] = 1;
		
		output($root);
		
	}
}
?>
