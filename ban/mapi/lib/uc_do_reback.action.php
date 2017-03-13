<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_do_reback
{
	public function index(){
		
		$root = array();
		
		$dltid = intval($GLOBALS['request']['dltid']);
				
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$root['user_login_status'] = 1;									
		
			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."deal_load_transfer SET status=0,callback_count = callback_count+1 where id=".$dltid." AND t_user_id=0 and user_id = ".$user_id);
		
			if($GLOBALS['db']->affected_rows()){
				$root['response_code'] = 1;
				$root['show_err'] = "撤销操作成功";
			}
			else{
				$root['response_code'] = 0;
				$root['show_err'] = "撤销操作失败";
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
