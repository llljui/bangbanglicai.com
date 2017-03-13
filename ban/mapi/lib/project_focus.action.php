<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class project_focus
{
	public function index(){
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		if(!$GLOBALS['user_info'])
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}		
		
		
		$id = intval($GLOBALS["request"]['id']);
		//-------------
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$id." and is_delete = 0 and is_effect = 1");
		if(!$deal_info)
		{
			$root['status'] = 3;	
			$root['show_err'] = "项目不存在";
			output($data);
		}
		
		$focus_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_focus_log where deal_id = ".$id." and user_id = ".$user_id);
		if($focus_data)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."project set focus_count = focus_count - 1 where id = ".$id." and is_effect = 1");
			if($GLOBALS['db']->affected_rows()>0)
			{
				$GLOBALS['db']->query("delete from ".DB_PREFIX."project_focus_log where id = ".$focus_data['id']);
				///$GLOBALS['db']->query("update ".DB_PREFIX."user set focus_count = focus_count - 1 where id = ".intval($GLOBALS['user_info']['id']));
				
				//删除准备队列
			//	$GLOBALS['db']->query("delete from ".DB_PREFIX."user_deal_notify where user_id = ".intval($GLOBALS['user_info']['id'])." and deal_id = ".$id);
				$root['status'] = 2;
				$root['show_err'] = "取消关注成功";
				
			}	
			else
			{
				$root['status'] = 3;	
				$root['show_err'] = "项目未上线";
			}		
		}
		else
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."project set focus_count = focus_count + 1 where id = ".$id." and is_effect = 1");
			if($GLOBALS['db']->affected_rows()>0)
			{
				$focus_data['user_id'] = $user_id;
				$focus_data['deal_id'] = $id;
				$focus_data['create_time'] = TIME_UTC;
				$GLOBALS['db']->autoExecute(DB_PREFIX."project_focus_log",$focus_data);
				//$GLOBALS['db']->query("update ".DB_PREFIX."user set focus_count = focus_count + 1 where id = ".intval($GLOBALS['user_info']['id']));
				
				//关注项目成功，准备加入准备队列
			/*	if($deal_info['is_success'] == 0 && $deal_info['begin_time'] < NOW_TIME && ($deal_info['end_time']==0 || $deal_info['end_time']>NOW_TIME))
				{
					//未成功的项止准备生成队列
					$notify['user_id'] = $GLOBALS['user_info']['id'];
					$notify['deal_id'] = $deal_info['id'];
					$notify['create_time'] = NOW_TIME;
					$GLOBALS['db']->autoExecute(DB_PREFIX."user_deal_notify",$notify,"INSERT","","SILENT");
				}
				*/
				$root['status'] = 1;
				$root['show_err'] = "关注成功";
				
			}
			else
			{
				$root['status'] = 3;
				$root['show_err'] = "项目未上线";
			}
		}
		
		output($root);
	
	}
}
?>
