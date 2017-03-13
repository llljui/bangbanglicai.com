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
			$root['response_code'] = 0;
			$root['show_err'] = "请先登录";
		}
	
		$comment['deal_id'] = intval($GLOBALS["request"]['id']);
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$comment['deal_id']." and is_delete = 0 and is_effect = 1 ");
		if(!$deal_info)
		{
			$root['response_code'] = 0;
			$root['show_err'] = "该项目暂时不能评论";
		}
	
		if(!check_ipop_limit(get_client_ip(),"deal_savedealcomment",3))
			$root['response_code'] = 0;
			$root['show_err'] = "提交太快";
	
		$comment['content'] = strim($GLOBALS["request"]['content']);
		$comment['user_id'] = $user_id;
		$comment['create_time'] =  TIME_UTC;
		$comment['user_name'] = $user['user_name'];
		$comment['pid'] = intval($GLOBALS["request"]['pid']);
		$comment['deal_user_id'] = intval($GLOBALS['db']->getOne("select user_id from ".DB_PREFIX."project where id = ".$comment['deal_id']));
		$comment['reply_user_id'] = intval($GLOBALS['db']->getOne("select user_id from ".DB_PREFIX."project_comment where id = ".$comment['pid']));
		$comment['deal_user_name'] = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".intval($comment['deal_user_id']));
		$comment['reply_user_name'] = $GLOBALS['db']->getOne("select user_name from ".DB_PREFIX."user where id = ".intval($comment['reply_user_id']));
		if(app_conf("USER_MESSAGE_AUTO_EFFECT")){
			$comment['status']=1;
		}
		$GLOBALS['db']->autoExecute(DB_PREFIX."project_comment",$comment);
		
		$comment['id'] = $GLOBALS['db']->insert_id();
	
		$GLOBALS['db']->query("update ".DB_PREFIX."project set comment_count = comment_count+1 where id = ".$comment['deal_id']);
		
		if(intval($GLOBALS["request"]['syn_weibo'])==1)
		{
			$weibo_info = array();
			$weibo_info['content'] = $comment['content']." ".get_domain().url("deal#show",array("id"=>$comment['deal_id']));
			$img = $GLOBALS['db']->getOne("select image from ".DB_PREFIX."project where id = ".intval($comment['deal_id']));
			if($img)$weibo_info['img'] = APP_ROOT_PATH."/".$img;
			syn_weibo($weibo_info);
		}
		
		$root['response_code'] = 1;
		$root['show_err'] = $GLOBALS['lang']['EXCHANGE_SUCCESS'];
		$root['status']=1;

		output($root);
		
	}
}
?>
