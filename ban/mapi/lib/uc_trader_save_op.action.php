<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_save_op
{
	public function index(){
		
		$root = array();
		$user =  $GLOBALS['user_info']; 
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$peizi_order_id = intval($GLOBALS['request']["id"]);
			$op_type = intval($GLOBALS['request']["type"]);
			$op_val = strim($GLOBALS['request']["op_val"]);
			$memo = strim($GLOBALS['request']["memo"]);
			
			if($peizi_order_id > 0)
			{
				$info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order where id = ".$peizi_order_id." and user_id = ".$user_id);
				if($info)
				{
					$op_info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$peizi_order_id." and user_id = ".$user_id." and op_status in (0,1)");
					if($op_info)
					{
						$root["status"] = 0;
						$root["msg"] = "您还有申请未审核通过，请等待申请通过后操作";
					}
					//$op_type_1 = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$peizi_order_id." and user_id = ".$GLOBALS["user_info"]["id"]." and op_status not in (2,5) ");
				
					//有成功追加：保证金 外,不让：增资，减资 了
					$op_type_1 = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$peizi_order_id." and op_type = 0 and user_id = ".$user_id." and op_status = 3 ");
					if($op_type_1 && ($op_type==2||$op_type==3))
					{
						$root["status"] = 0;
						$root["msg"] = "提交错误，请刷新重试";
					}
					else
					{
						$data = array();
						$data["peizi_order_id"] = $peizi_order_id;
						$data["op_type"] = $op_type;
						$data["create_date"] = to_date(TIME_UTC);
						$data["op_val"] = $op_val;
						$data["memo"] = $memo;										
						$data["user_id"] = $user_id;
						
						$data["lever"] = $info["lever"];
						$data['cost_money'] = $info["cost_money"];
						
						$data["change_memo"] = get_peizi_op_val_info($data,get_peizi_type($info['type'],true));
						
						$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_op",$data,"INSERT");
						$root["status"] = 1;
						$root["msg"] = "提交成功，请等待管理员审核";
					}
				}
				else
				{
					$root["status"] = 0;
					$root["msg"] = "操作失败请重试";
				}
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
