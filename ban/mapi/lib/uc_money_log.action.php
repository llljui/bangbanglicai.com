<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_money_log
{
	public function index(){
		
		$root = get_baseroot();
		
		
		$page = intval($GLOBALS['request']['page']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$result = get_user_log($limit,$GLOBALS['user_info']['id'],'money');
			$list = $result['list'];
			foreach($list as $k=>$v)
			{
				$list[$k]['log_info'] =  strip_tags($v['log_info']);
				$list[$k]['log_time_format'] = to_date($v['log_time'],"Y-m-d H:i:s");
				$list[$k]['money_format'] = format_price($v['money']);
				$list[$k]['lock_money_format'] = format_price($v['lock_money']);
			}
			
			$root['item'] = $list;
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "操作日志";
		output($root);		
	}
}
?>
