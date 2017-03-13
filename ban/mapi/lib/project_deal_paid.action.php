<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_deal_paid
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
			app_redirect(url_wap("user#login"));
		}
		
		$deal_id = intval($GLOBALS["request"]['id']);
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$deal_id." and is_delete = 0 and is_effect = 1 and is_success = 1 and user_id = ".$user_id);
		
		if(!$deal_info)
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		$root["deal_info"]=$deal_info;
		
		$page_size = app_conf("DEAL_PAGE_SIZE");
		$page = intval($GLOBALS["request"]['p']);
		if($page==0)$page = 1;		
		$limit = (($page-1)*$page_size).",".$page_size	;

		$paid_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_pay_log where deal_id = ".$deal_id." and is_delete = 0 order by create_time desc limit ".$limit);
		
		
		$paid_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_pay_log where deal_id = ".$deal_id." and is_delete = 0");
		
		
		$root["paid_list"]=$paid_list;
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($paid_count/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));

		$root["program_title"]="发放记录";
		
		output($root);
		
	}
}
?>
