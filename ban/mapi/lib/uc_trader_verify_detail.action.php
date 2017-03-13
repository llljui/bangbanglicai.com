<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_verify_detail
{
	public function index(){
		
			$root = get_baseroot();
			$user =  $GLOBALS['user_info']; 
			$root['session_id'] = es_session::id();
			$user_id  = intval($user['id']);
			$id = intval($GLOBALS['request']['id']);
			if ($user_id >0){
			
			$root['user_name'] = $user['user_name'];
			require_once APP_ROOT_PATH.'system/libs/peizi.php';	
			$trader_info = $GLOBALS['db']->getRow("select po.*,AES_DECRYPT(po.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd,pc.name as conf_type_name from ".DB_PREFIX."peizi_order po left join ".DB_PREFIX."peizi_conf as pc on po.peizi_conf_id = pc.id where  po.user_id = ".$user_id." and po.id=".$id);		
			
			$trader_info = get_peizi_order_fromat($trader_info);
			
			if($trader_info["status"] != 6 && $trader_info["status"] != 8)
			{
				$trader_info["loss_money_format"] = "￥0.00";
			}
			
			//总标志 6全部禁用 4启用
			$main_flag = true;
			//0:追加保证金;1:申请延期;2:申请增资;3:申请减资;4:提取赢余;5:申请结束配资'
			$trader_info["flag_0"] = true;
			$trader_info["flag_1"] = true;
			$trader_info["flag_2"] = true;
			$trader_info["flag_3"] = true;
			$trader_info["flag_4"] = true;
			$trader_info["flag_5"] = true;
			 
			if($trader_info["status"]==8 || $trader_info["status"]== 3|| $trader_info["status"]== 5|| $trader_info["status"]==7)
			{
				$main_flag = false;
			}
			elseif($trader_info["status"]==6)
			{
				$main_flag = true;
			}
			else
			{
				$main_flag = false;
			}
			$order_op = $GLOBALS["db"] -> getAll("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$id);
		
			foreach($order_op as $k => $v)
			{
				/*if( $v["op_status"] != 3 || $v["op_status"] != 5)
				{
					$trader_info["flag_".$v["op_type"]] = false;
				}*/
				if( $v["op_type"] == 0 && $v["op_status"] != 1 && $v["op_status"] != 5 )
				{
					$trader_info["flag_2"] = false;
					$trader_info["flag_3"] = false;
				}
			}
			if($trader_info["type"] == 1)
			{
				$trader_info["flag_1"] = false;
				$trader_info["flag_2"] = false;
				$trader_info["flag_3"] = false;
			}
			if($main_flag == false)
			{
				$trader_info["flag_0"] = false;
				$trader_info["flag_1"] = false;
				$trader_info["flag_2"] = false;
				$trader_info["flag_3"] = false;
				$trader_info["flag_4"] = false;
				$trader_info["flag_5"] = false;
			}
			
			/*操盘列表*/
			$op_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_op where user_id = ".$user_id." and peizi_order_id=".$id." order by id desc ");		
			
			foreach($op_list as $k => $v)
			{
				
				$op_list[$k]["op_type_format"] = get_peizi_op_type($v["op_type"]);
				$op_list[$k]["op_status_format"] = get_peizi_op_status($v["op_status"],$v["op_type"]);
				
				if($v["op_status"] == 3 || $v["op_status"] == 4)
				{
					$op_list[$k]["op_date"] = $v["op_date2"];
				}
				else
				{
					$op_list[$k]["op_date"] = $v["op_date1"];
				}
			}
			/*资金列表*/
			$fee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_fee_list where user_id = ".$user_id." and peizi_order_id=".$id." order by id desc");		
	
			/*历史金额*/
			$history_list = $GLOBALS['db']->getAll("select m.stock_date,m.stock_money from ".DB_PREFIX."peizi_order_stock_money m left join ".DB_PREFIX."peizi_order po on m.peizi_order_id = po.id where po.user_id = ".$user_id." and peizi_order_id=".$id." order by m.id asc");		
			$root['history_list'] = $history_list;
			$root['fee_list'] = $fee_list;
			$root['op_list'] = $op_list;
			$root['vo'] = $trader_info;
			$durl = "/index.php?ctl=uc_trader&act=contract&is_sj=1&id=".$trader_info['id'];
			$root['contract_url'] = str_replace ( "/mapi", "", SITE_DOMAIN . APP_ROOT . $durl );
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
			
		$root['program_title'] = "股票配资详情";
		
		output($root);		
		
	}
	
	
}
?>
