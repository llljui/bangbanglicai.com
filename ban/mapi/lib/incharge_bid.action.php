<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class incharge_bid
{
	public function index(){
		
		$root = get_baseroot();
		
		//require_once APP_ROOT_PATH."app/Lib/deal_func.php";
		
		$id = intval($GLOBALS['request']['id']);
		$user =  $GLOBALS['user_info'];
		$vip_id = intval($user['vip_id']);
		$payment_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."payment WHERE id=".$id);
		$root['payment_info'] = $payment_info;
		if($payment_info){
			if($vip_id>0){
				$interface_class = $payment_info['class_name'];
				$recharge_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_recharge_config WHERE interface_class='$interface_class' and vip_id = ".$vip_id);
				if($recharge_info){
					$root['fee_type'] = floatval($recharge_info['fee_type']);
					$root['fee_amount'] = floatval($recharge_info['fee']);
				}else{
					$root['fee_type'] = floatval($payment_info['fee_type']);
					$root['fee_amount'] = floatval($payment_info['fee_amount']);
				}
			}else{
				$root['fee_type'] = floatval($payment_info['fee_type']);
				$root['fee_amount'] = floatval($payment_info['fee_amount']);
			}
			
		}
		
		output($root);	
	}
}
?>
