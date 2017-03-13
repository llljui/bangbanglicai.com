\<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_savecomment
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		$ajax = intval($GLOBALS["request"]['ajax']);
		if(!$user)
		{
			app_redirect(wap_url("index","index"));
		}
	
	
		$id = intval($GLOBALS["request"]['id']);
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$id." and is_delete = 0 and is_effect = 1 and user_id = ".$user_id);
		if(!$deal_info)
		{
			$root['response_code'] = 0;
			$root['show_err'] = "不能更新该项目的动态";
		}
		else
		{
			$data['log_info'] = strim($GLOBALS["request"]['log_info']);
			if($data['log_info']=="")
			{
				$root['response_code'] = 0;
				$root['show_err'] = "请输入更新的内容";
			}
			$data['image'] = strim($GLOBALS["request"]['image'])!=""?replace_public($GLOBALS["request"]['image']):"";
			$data['vedio'] = strim($GLOBALS["request"]['vedio']);
			if($data['vedio']!="")
			{
				require_once APP_ROOT_PATH."system/utils/vedio.php";
				$vedio = fetch_vedio_url($GLOBALS["request"]['vedio']);
				if($vedio!="")
				{
					$data['source_vedio'] =  $vedio;
				}
				else
				{
					$root['response_code'] = 0;
					$root['show_err'] = "非法的视频地址";
				}
			}
			$data['user_id'] = $user_id;
			$data['deal_id'] = $id;
			$data['create_time'] = TIME_UTC;
			$data['user_name'] = $user['user_name'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."project_log",$data);
			$GLOBALS['db']->query("update ".DB_PREFIX."project set log_count = log_count + 1 where id = ".$deal_info['id']);
			
			$root['response_code'] = 1;
			$root['show_err'] = $GLOBALS['lang']['EXCHANGE_SUCCESS'];
			$root['status']=1;
		}

		output($root);
		
	}
}
?>
