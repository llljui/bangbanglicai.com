\<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
class project_add_update_save
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		if(!$user)
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
	
	
		$id = intval($GLOBALS["request"]['id']);
		
		$root["id"] = $id;
		
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$id." and is_delete = 0 and is_effect = 1 and user_id = ".$user_id);
		
		if(!$deal_info)
		{
			$root["status"] = 0;
			$root["show_err"] = "不能更新该项目的动态";
		}
		else
		{
			$data['log_info'] = strim($GLOBALS["request"]['log_info']);
			if($data['log_info']=="")
			{
				$root["status"] = 0;
				$root["show_err"] = "请输入更新的内容";
				output($root);
			}
			/**/
			$file = strim($GLOBALS["request"]["file"]);
			
			$root['status'] = 0; 
			
			if(count($file)==0){
				$root['show_err'] = "提交失败,请选择图片！"; 
				$root['status'] = 0; 
				output($root);
			}
			/**/
			$data['image'] = $file;
			
			$data['vedio'] = strim($GLOBALS["request"]['vedio']);
			if($data['vedio']!="")
			{
				require_once APP_ROOT_PATH."system/utils/vedio.php";
				$vedio = fetch_vedio_url($_REQUEST['vedio']);
				if($vedio!="")
				{
					$data['source_vedio'] =  $vedio;
				}
				else
				{
					$root["status"] = 0;
					$root["show_err"] = "非法的视频地址";
					output($root);
				}
			}
			$data['user_id'] = intval($GLOBALS['user_info']['id']);
			$data['deal_id'] = $id;
			$data['create_time'] = TIME_UTC;
			$data['user_name'] = $GLOBALS['user_info']['user_name'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."project_log",$data);
			$GLOBALS['db']->query("update ".DB_PREFIX."project set log_count = log_count + 1 where id = ".$deal_info['id']);
			
			
		}
		$root["status"] = 1;
		$root["show_err"] = "保存成功";
			
		output($root);
		
	}
}
?>
