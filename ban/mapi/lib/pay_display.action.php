<?php
class pay_display{
	public function index(){
		
		$user =  $GLOBALS['user_info'];
		$user_id  = intval($user['id']);		
		$root['session_id'] = es_session::id();
			
		$root = array();
		$root['response_code'] = 1;
		$root['program_title'] = "支付信息";
		if($user_id>0)
		{
			$class_name = strim($GLOBALS['request']['class_name']);			
			
			$root['user_login_status'] = 1;
			
			
			//创建了支付单号，通过支付接口创建支付数据
			if(file_exists(APP_ROOT_PATH."system/payment/".$class_name."_payment.php")){
				require_once APP_ROOT_PATH."system/payment/".$class_name."_payment.php";
				$payment_class = $class_name."_payment";
				$payment_object = new $payment_class();
				$info = $payment_object->get_display_code();
				
				$root['code'] = $info;
				$root['response_code'] = 1;
			}
			else{
				$root['response_code'] = 0;
			}
			
			
				
			
			output($root);
		}
		else {
			$root['user_login_status'] = 0;
			output($root);
		}		
	}
}
?>