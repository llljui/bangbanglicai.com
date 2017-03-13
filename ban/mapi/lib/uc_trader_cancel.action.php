<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_cancel
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info']; 
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$id = intval($GLOBALS['request']['id']);
			if(!$id)
			{
				
				$root["status"] = 0;
				$root["info"] = "操作失败，请重试";
				output($root);
			}
			$info = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."peizi_order where id =".$id." and status = 1 and user_id = ".$user_id);
			if(!$info)
			{
				
				$root["status"] = 0;
				$root["info"] = "操作失败，请重试";
				output($root);
			}
			else
			{
				//更改状态
				$update_date = array();
				$update_date["status"] = "9";
				$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order",$update_date,"UPDATE","id=".$id);
				$root["status"] = 1;
				$root["info"] = "操作成功";
				
				//更新数据
				$cost_money = $info['cost_money'];
				$first_rate_money = $info['first_rate_money'];
				$manage_money = $info['manage_money'];
				$user_id = $info["user_id"];
				$order_id = $info["id"];
				
				require_once APP_ROOT_PATH.'system/libs/user.php';
				//退冻结：本金cost_money,首次付款 first_rate_money, 业务审核费manage_money
				//退 冻结 投资人的: 投资资金  borrow_money			
				//解冻：本金 cost_money
				modify_account(array('money'=>$cost_money,'lock_money'=>-$cost_money), $user_id,'配资申请失败解冻配资本金,配资编号:'.$order_id,30);
	
				//解冻：首次付款  first_rate_money
				modify_account(array('money'=>$first_rate_money,'lock_money'=>-$first_rate_money), $user_id,'配资申请失败解冻预交款,配资编号:'.$order_id,31);
				
				//解冻：业务审核费  manage_money
				if ($manage_money > 0)
					modify_account(array('money'=>$manage_money,'lock_money'=>-$manage_money), $user_id,'配资申请失败解冻服务费,配资编号:'.$order_id,32);
			
				output($root);	
			}
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['info'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
			
		$root['program_title'] = "撤销";
		
		output($root);		
		
	}
}
?>
