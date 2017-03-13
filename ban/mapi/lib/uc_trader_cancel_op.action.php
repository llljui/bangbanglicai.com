<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_cancel_op
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info']; 
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$id = intval($GLOBALS['request']['id']);
			$info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where id = ".$id." and op_status = 0 and user_id = ".$user_id);
			if($info)
			{
				$update_date = array();
				$update_date["op_status"] = 5;
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_op",$update_date,"UPDATE","id=".$id);
				$root["status"] = 1;
				$root["msg"] = "操作成功";
			}
			else
			{
				$root["status"] = 0;
				$root["msg"] = "保存失败，请刷新重新操作";
			}
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['msg'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		$root['program_title'] = "追加保证金";
		output($root);		
		
	}
}
?>
