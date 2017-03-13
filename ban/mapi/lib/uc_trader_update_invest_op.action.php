<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_update_invest_op
{
	public function index(){
		
		$root = array();
		$user =  $GLOBALS['user_info']; 
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$id = intval($GLOBALS['request']["id"]);
		$type = intval($GLOBALS['request']["type"])==1?1:2; //1为同意 2为不同意
		
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$info = $GLOBALS['db']->getRow("select op.* from ".DB_PREFIX."peizi_order_op op left join ".DB_PREFIX."peizi_order o on op.peizi_order_id = o.id  where op.op_type in (1,2) and op.op_status = 0 and o.invest_user_id = ".$user_id)." and id =".$id;
			if($info)
			{
				$update_date = array();
				$update_date["op_status"] = $type;
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_op",$update_date,"UPDATE","id=".$id);
				$root["status"] = 1;
				$root["info"] = "操作成功";
				output($root);	
			}
			else
			{
				$root["status"] = 0;
				$root["info"] = "操作失败，请稍后重试";
				output($root);	
			}
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		$root['program_title'] = "配资单变更审核";
		output($root);		
		
	}
}
?>
