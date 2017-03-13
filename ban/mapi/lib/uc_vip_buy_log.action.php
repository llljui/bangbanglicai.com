<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_vip_buy_log
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$root['user_login_status'] = 1;		
			$root['response_code'] = 1;
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
	    	$vip_buy_log_list = $GLOBALS['db']->getAll("select vbl.*,vt.vip_grade from ".DB_PREFIX."vip_buy_log vbl LEFT JOIN ".DB_PREFIX."vip_type vt ON vbl.vip_id=vt.id  where vbl.buy_type = 0 and vbl.user_id = ".$user_id." order by vbl.id desc limit $limit ");
			$log_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."vip_buy_log  where buy_type = 0 and user_id = ".$user_id." ");
			
			foreach($vip_buy_log_list as $k => $v)
			{
				$vip_buy_log_list[$k]['vip_buy_date'] = to_date($v['vip_buytime'],'Y-m-d');
				$vip_buy_log_list[$k]['vip_end_date'] = to_date($v['vip_end_time'],'Y-m-d');
			}
			
			
			$root['vip_buy_log_list'] = $vip_buy_log_list;
			$root['page'] = array("page"=>$page,"page_total"=>ceil($log_count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			$root['status']= 1;
			
		}else{
			$root['status']= 0;
			$root['response_code'] = 0;
			$root['show_err'] = "未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "VIP购买日志";
		output($root);		
	}
}
?>
