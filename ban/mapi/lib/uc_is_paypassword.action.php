<?php

class uc_is_paypassword{
	
	public function index()
	{
		
		$root = array();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		if ($user_id >0){
			
			$sql = "select count(*) from ".DB_PREFIX."user where id = '".$user_id."' and paypassword != '' and is_delete = 0";
			$count = $GLOBALS['db']->getOne($sql);
			if($count>0){
				$root['mobile'] = hideMobile($user['mobile']);
				$root['response_code'] = 1;
			}else{
				$root['response_code'] = 0;
				$root['show_err'] = "请设置支付密码！";
				output($root);
			}
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}


		$root['program_title'] = "支付密码";
		output($root);

	}
}
?>