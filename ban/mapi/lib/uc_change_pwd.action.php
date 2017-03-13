<?php
class uc_change_pwd{
	public function index()
	{

		$new_pwd = strim($GLOBALS['request']['newpassword']);//新密码
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);			
			
		$root = get_baseroot();
				
		if($user_id>0)
		{
			$root['user_login_status'] = 1;	
				
			if (strlen($new_pwd) == 0){
				$root['response_code'] = 0;
				$root['show_err'] = "登陆密码不能为空";
			}else{
			
				$new_pwd = md5($new_pwd.$user['code']);			
				$sql = "update ".DB_PREFIX."user set user_pwd = '".$new_pwd."' where id = {$user_id}";
				$GLOBALS['db']->query($sql);
				$rs = $GLOBALS['db']->affected_rows();
				if ($rs > 0){
					$root['response_code'] = 1;									
					$root['show_err'] = "密码更新成功!";
				}else{
					$root['response_code'] = 0;
					$root['show_err'] = "密码更新失败!";
				}
			}
		}
		else
		{
			$root['response_code'] = 0;
			$root['user_login_status'] = 0;		
			$root['show_err'] = "原始密码不正确";
		}		
	
		output($root);
	}
}
?>