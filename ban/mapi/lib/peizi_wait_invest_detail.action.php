<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class peizi_wait_invest_detail
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
				$trader_info = $GLOBALS['db']->getRow("select po.*,AES_DECRYPT(po.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd,pc.name as conf_type_name,u.user_name from ".DB_PREFIX."peizi_order po left join ".DB_PREFIX."peizi_conf as pc on po.peizi_conf_id = pc.id left join ".DB_PREFIX."user u on  po.user_id = u.id where po.id=".$id);		
			
				$trader_info = get_peizi_order_fromat($trader_info);
				
				if($trader_info["status"] != 6 && $trader_info["status"] != 8)
				{
					$trader_info["loss_money_format"] = "￥0.00";
				}
				
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
