<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_del_collect
{
	public function index(){
		
		$root = get_baseroot();
		
		$ids = strim($GLOBALS['request']['id']);
				
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$root['user_login_status'] = 1;									
		
			$sql = "delete from ".DB_PREFIX."deal_collect where deal_id in (".$ids.") and user_id = ".$user_id;
			//$root['sql'] = $sql;
			$GLOBALS['db']->query($sql);
			$root['response_code'] = 1;
			$root['show_err'] = $GLOBALS['lang']['DELETE_SUCCESS'];
			/*
			if($GLOBALS['db']->affected_rows()){
				$root['response_code'] = 1;
				$root['show_err'] = $GLOBALS['lang']['DELETE_SUCCESS'];
			}
			else{
				$root['response_code'] = 0;
				$root['show_err'] = $GLOBALS['lang']['删除失败'];
			}
			*/
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>
