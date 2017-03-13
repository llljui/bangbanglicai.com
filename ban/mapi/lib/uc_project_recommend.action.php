<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_project_recommend
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new*/
		if(!$GLOBALS['user_info'])
  		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
  		}
  		$page_size = app_conf("DEAL_PAGE_SIZE");
  		$page = intval($GLOBALS["request"]['p']);
  		if($page==0)$page = 1;
  		$limit = (($page-1)*$page_size).",".$page_size;
  		$user_id=$user_id;
  		if(app_conf('INVEST_STATUS') == 0)
		{
			$recommend_info=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."project_recommend WHERE user_id=".$user_id." ORDER BY create_time DESC limit $limit");
  			$recommend_count=$GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."project_recommend WHERE user_id=".$user_id);
  		}
		elseif(app_conf('INVEST_STATUS') == 1)
		{
			$recommend_info=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."project_recommend WHERE user_id=".$user_id." and deal_type = 0 ORDER BY create_time DESC limit $limit");
  			$recommend_count=$GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."project_recommend WHERE  deal_type = 0 and user_id=".$user_id);
  		}
		else
		{
			$recommend_info=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."project_recommend WHERE user_id=".$user_id." and deal_type = 1 ORDER BY create_time DESC limit $limit");
  			$recommend_count=$GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."project_recommend WHERE deal_type = 1 and user_id=".$user_id);
  		}
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($recommend_count/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));

		
  		$root["recommend_info"]=$recommend_info;
  		$root["program_title"]="推荐的项目";
		
		output($root);		
	}
}
?>
