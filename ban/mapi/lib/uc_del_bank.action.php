<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_del_bank
{
	public function index(){
		
		$root = get_baseroot();
		
		$id = strim($GLOBALS['request']['id']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){

			$root['user_login_status'] = 1;
			
			$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."user_bank where user_id=".$user_id." and id in (".$id.")");
			
			if($GLOBALS['db']->affected_rows()){
				$root['response_code'] = 1;
				$root['show_err'] = $GLOBALS['lang']['DELETE_SUCCESS'];
			}else{
				$root['response_code'] = 0;
				$root['show_err'] = "删除失败";
			}		
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>
