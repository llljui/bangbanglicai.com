<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class peizi_invest_detail
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info']; 
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$id = intval($GLOBALS['request']["id"]);
			if($id>0)
			{
				$trader_info = $GLOBALS['db']->getRow("select po.*,AES_DECRYPT(po.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd,pc.name as conf_type_name,u.user_name from ".DB_PREFIX."peizi_order po left join ".DB_PREFIX."peizi_conf as pc on po.peizi_conf_id = pc.id left join ".DB_PREFIX."user u on  po.user_id = u.id where  po.invest_user_id = ".intval($GLOBALS['user_info']['id'])." and po.id=".$id);		
			
				$trader_info = get_peizi_order_fromat($trader_info);
				
				if($trader_info["status"] != 6 && $trader_info["status"] != 8)
				{
					$trader_info["loss_money_format"] = "￥0.00";
				}
	
				/*操盘列表*/
				$op_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_op where user_id = ".intval($trader_info['user_id'])." and peizi_order_id=".$id." order by id desc ");		
				
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
				$fee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_fee_list where user_id = ".intval($trader_info['user_id'])." and peizi_order_id=".$id." order by id desc");		
				
				/*历史金额*/
				$history_list = $GLOBALS['db']->getAll("select m.stock_date,m.stock_money from ".DB_PREFIX."peizi_order_stock_money m left join ".DB_PREFIX."peizi_order po on m.peizi_order_id = po.id where po.user_id = ".intval($trader_info['user_id'])." and peizi_order_id=".$id." order by m.id asc");		
				
				$root['history_list'] = $history_list;
				$root['fee_list'] = $fee_list;
				$root['op_list'] = $op_list;
				$root['vo'] = $trader_info;
				
			}
			else
			{
				$root['show_err'] ="访问错误，请重试";
			}
				
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
