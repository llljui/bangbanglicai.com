<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_interestrate_do_send
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$id = intval($GLOBALS['request']["id"]);
			$user_name = strim($GLOBALS['request']["user_name"]);
			if($user_name == "")
			{
				$root['status']=0;
				$root['response_code'] = 0;
				$root['show_err'] = "请填写会员名";
			}
			else
			{
				$to_user_id = get_user_info("id","user_name = '".$user_name."'","ONE");
				if(!$to_user_id)
				{
					$root['status']=0;
					$root['response_code'] = 0;
					//$root['show_err'] = $GLOBALS['lang']['INVALID_INTERESTRATE'];
					$root['show_err'] = "用户不存在";
				}
				elseif($to_user_id ==$user_id)
				{
					$root['status']=0;
					$root['response_code'] = 0;
					//$root['show_err'] = $GLOBALS['lang']['INVALID_INTERESTRATE'];
					$root['show_err'] = "不能转赠给自己";
				}
				else
				{
					$result = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."interestrate where id=".$id." and user_id=".$user_id." and to_user_id = 0 and (begin_time =0 OR (begin_time > 0 AND begin_time < ".TIME_UTC.")) AND (end_time = 0 OR (end_time > 0  AND (end_time +24*3600 - 1) > ".TIME_UTC.")) AND (use_limit =0  OR (use_limit >0 AND use_limit - use_count > 0))");
					if($result)
					{
						$GLOBALS['db']->query("update ".DB_PREFIX."interestrate set to_user_id = ".$to_user_id." where id = ".$id);
						$root['status']=1;
						$root['response_code'] = 0;
						$root['show_err'] = $GLOBALS['lang']['SUCCESS_INTERESTRATE'];
					}
					else
					{
						$root['status']=0;
						$root['response_code'] = 0;
						$root['show_err'] = $GLOBALS['lang']['INVALID_INTERESTRATE'];
					}
				}
			}
		}else{
			$root['status']=0;
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "我的加息券";
		output($root);		
	}
}
?>
