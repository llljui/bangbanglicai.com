<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_do_investment
{
	public function index(){
		
		$root = array();
		$user =  $GLOBALS['user_info']; 
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$id = intval($GLOBALS['request']["id"]);
			$bid_paypassword = strim($GLOBALS['request']['bid_paypassword']);
			//$bid_paypassword = strim(FW_DESPWD($GLOBALS['request']['bid_paypassword']));
			
			if($bid_paypassword==""){
				$root['status'] = 0;
				$root["info"] = $GLOBALS['lang']['PAYPASSWORD_EMPTY'];
				output($root);
			}
			
			if(md5($bid_paypassword)!=$GLOBALS['user_info']['paypassword']){
				$root['status'] = 0;
				$root["info"] = $GLOBALS['lang']['PAYPASSWORD_ERROR'];
				output($root);
			}
			
			$order_info = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."peizi_order where status = 2 and invest_user_id = 0");
			if(!$order_info)
			{
				$root['status'] = 0;
				$root["info"] = "已经有人投资";
				output($root);
			}
			if($order_info["user_id"]==$user_id)
			{
				$root['status'] = 0;
				$root["info"] = "不能投资自己的配资";
				output($root);
			}
			
//			$root["info"] = $user['money'];
//			output($root);
			
			if($order_info["borrow_money"] > $user['money']){
				$root['status'] = 2;
				$root["info"] = $GLOBALS['lang']['MONEY_NOT_ENOUGHT'];
				$root["jump"] = url("index","uc_money#incharge");
				output($root);
			}
			
			$update = array();
			$update["invest_user_id"] = $user_id;
			$update["status"] = 4;
			$update['invest_end_time'] = to_date(TIME_UTC);
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order",$update,"UPDATE","id=".$id." and  status = 2  and invest_user_id = 0");
			if($GLOBALS['db']->affected_rows())
			{
				require_once APP_ROOT_PATH."system/libs/user.php";
				modify_account(array('money'=>-$order_info['borrow_money'],'lock_money'=>$order_info['borrow_money']), $user_id,'配资投资冻结,配资编号:'.$order_info["id"],36);
		
				$root['status'] = 1;
				$root["info"] = "操作成功";
				$root["jump"] = url("index","uc_trader#wait_investment");
				output($root);
			}
			else
			{
				$root['status'] = 0;
				$root["info"] = "已经有人投资";
				output($root);
			}
				
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['info'] ="未登录";
			$root['user_login_status'] = 0;
		}
			
		$root['program_title'] = "投资执行";
		
		output($root);		
		
	}
}
?>
