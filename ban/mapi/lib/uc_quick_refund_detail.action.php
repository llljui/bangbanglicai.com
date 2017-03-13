<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_quick_refund_detail
{
	public function index(){
		
		$root = get_baseroot();
		
		$deal_id = intval($GLOBALS['request']['id']);
		$l_key = intval($GLOBALS['request']['l_key']);
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			
			$root['user_login_status'] = 1;
									
			$deal = get_deal($deal_id,0);
			$root['deal'] = $deal;
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
				
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			$count = $GLOBALS['db']->getOne(" SELECT count(*) FROM ".DB_PREFIX."deal_load_repay dlr where dlr.deal_id ='$deal_id' and dlr.l_key='$l_key' ");

			$load_user = get_deal_user_load_list($deal,0,$l_key,-1,0,0,1,$limit);
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			$root['load_user'] = $load_user['item'];
			
			$root['response_code'] = 1;
			$root['show_err'] = '';
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "查看明细";
		output($root);		
	}
}
?>
