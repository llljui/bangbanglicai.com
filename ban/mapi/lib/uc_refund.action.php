<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_refund
{
	public function index(){
		
		$root = get_baseroot();
		
		
		$page = intval($GLOBALS['request']['page']);
		$status = intval($GLOBALS['request']['status']);//0:还款列表;1:已还清借款
		$root['status'] = $status;
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$deal_status = 4;
			$root['program_title'] = "偿还贷款";
			if($status == 1){
				$root['program_title'] = "已还清的贷款";
				$deal_status = 5;
			}
			
			$result = get_deal_list($limit,0,"deal_status =$deal_status AND user_id=".$user_id,"id DESC",$email,$user['user_pwd']);
				
			 
			$root['item'] = $result['list'];
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		output($root);		
	}
}
?>
