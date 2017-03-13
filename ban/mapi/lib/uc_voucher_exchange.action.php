<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_voucher_exchange
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$nowtime = TIME_UTC; 
			$root['user_login_status'] = 1;		
			$root['response_code'] = 1;
			
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
				
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$result = get_exchange_voucher_list($limit);
			
			$root['nowtime'] = $nowtime;
			$root['response_code'] = 1;
			$root['item'] = $result['list'];
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "我的红包";
		output($root);		
	}
}
?>
