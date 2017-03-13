<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class uc_project_del_recommend
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		if(!$GLOBALS['user_info'])
  		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
  		}
		
		/*new start*/
		$result=array('status'=>'','info'=>'','url'=>'','html'=>'');
		$id=intval($GLOBALS["request"]['id']);
		if($id>0){
			if($GLOBALS['db']->query("delete from ".DB_PREFIX."project_recommend where id = ".$id)>0){
				$root['status'] = 1;
				$root['show_err'] ="删除成功";
				$root['response_code'] = 1;
			}else{
				$root['status'] = 0;
				$root['show_err'] ="删除失败";
				$root['response_code'] = 0;
			}
		}else{
			$root['status'] = 0;
			$root['show_err'] ="系统繁忙,请您稍后重试！";
			$root['response_code'] = 0;
		}
		
		output($root);
		
	}
}
?>
