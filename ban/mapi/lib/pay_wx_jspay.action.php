<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class pay_wx_jspay
{
	public function index(){
		
		$root = array();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){

			$notice_id = intval($GLOBALS['request']['id']);
			$notice_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$notice_id."   and user_id = ".intval($GLOBALS['user_info']['id']));
 		
	 		if($notice_info['is_paid']==1)
			{
				$data['pay_status'] = 1;
				$data['pay_info'] = '已支付.';
				$data['show_pay_btn'] = 0;
				$root['data'] = $data;
			}else{
				$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$notice_info['payment_id']);
		 		$class_name = $payment_info['class_name']."_payment";
	 			require_once APP_ROOT_PATH."system/payment/".$class_name.".php";
	 			$o = new $class_name;
		  		$pay= $o->get_payment_code($notice_id);
	  	  		//$GLOBALS['tmpl']->assign('jsApiParameters',$pay['parameters']);
	  	  		
	  	  		$root['jsApiParameters'] = $pay['parameters'];
	  	  		$notice_info['money_format'] = format_price($notice_info['money']);
		  		$notice_info['pay_status'] = 0;
				$notice_info['pay_info'] = '未支付.';
				$notice_info['show_pay_btn'] = 1;
//				$notice_info['deal_id'] = $notice_info['deal_id'];
				$root['data'] = $notice_info;
//	 	  		$GLOBALS['tmpl']->assign('data',$notice_info);
				$root['is_wap_url']=$pay['is_wap_url'];
				$root['wap_notify_url']=$pay['notify_url'];
				
	 	  		$payment_info['config'] = unserialize($payment_info['config']);
	 	  		$root['type'] = $payment_info['config']['type'];
	 	  		
//	 	  		$GLOBALS['tmpl']->assign('type',$payment_info['config']['type']);
//		  		$GLOBALS['tmpl']->display('pay_wx_jspay.html');
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
