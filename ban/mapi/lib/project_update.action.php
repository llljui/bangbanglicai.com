\<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_update
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		$id = intval($GLOBALS["request"]['id']);
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$id." and is_delete = 0 and (is_effect = 1 or (is_effect = 0 and user_id = ".$user_id."))");
		$deal_info['deal_type']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."project_cate where id=".$deal_info['cate_id']);
		if(!$deal_info)
		{
			app_redirect(url_wap("index"));
		}	
		
		$deal_info = cache_project_extra($deal_info);
		
		$deal_info = init_project_page_wap($deal_info);	
		
		
		$root["deal_info"] = $deal_info;

		$root["current_page"]=$page;
		
		$log_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_log where deal_id = ".$deal_info['id']." order by create_time desc");				
		$log_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_log where deal_id = ".$deal_info['id']);
		
		
		if(!$log_list||(($page-1)*$page_size+($step-1)*$step_size)+count($log_list)>=$log_count)
		{
			//最后一页
			$log_list[] = array("deal_id"=>$deal_info['id'],
								"create_time"=>$deal_info['begin_time']+1,
								"id"=>0);
		}
		
		$last_time_key = "";
		foreach($log_list as $k=>$v)
		{
			$log_list[$k]['pass_time'] = pass_date($v['create_time']);
			$online_time = online_date($v['create_time'],$deal_info['begin_time']);
			$log_list[$k]['online_time'] = $online_time['info'];
			if($online_time['key']!=$last_time_key)
			{
				$last_time_key = $log_list[$k]['online_time_key'] = $online_time['key'];				
			}
			
			$log_list[$k]["user_avatar"] = get_abs_wap_avatar_url_root(get_user_avatar_wap($v["user_id"],"small"));
			
			$log_list[$k]["image"] = get_abs_url_root($v["image"]);
			
			$log_list[$k] = cache_log_comment($log_list[$k]);
			
		}

		$root["log_list"]=$log_list;		
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
		
		$root['user_income'] = $user_income;
		$root['program_title'] = msubstr($deal_info["name"],0,8);
		
		output($root);
		
	}
	
	

	
}
?>
