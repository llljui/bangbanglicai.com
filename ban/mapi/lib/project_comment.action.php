\<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_comment
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
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		$deal_info = cache_project_extra($deal_info);
		
		$deal_info = init_project_page_wap($deal_info);
		
		$root["deal_info"] = $deal_info;
		
		$comment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_comment where deal_id = ".$id." and log_id = 0 and status=1 order by create_time asc");
		
		foreach($comment_list as $k => $v)
		{
			$comment_list[$k]["user_avatar"] = get_abs_wap_avatar_url_root(get_user_avatar_wap($v["user_id"],"small"));
		}
	
		$comment_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_comment where deal_id = ".$id." and log_id = 0 and status=1");
		
		$save_url=wap_url("index","project_savecomment");
		//$save_url=wap_url("index","savedealcomment#deal");
		$root["program_title"]="项目评论";
		$root['save_url']=$save_url;
		$root['deal_id']=$id;
		$root["comment_list"]=$comment_list;
		
		output($root);
		
	}
}
?>
