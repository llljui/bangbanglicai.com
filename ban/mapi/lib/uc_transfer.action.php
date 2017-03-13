<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_transfer
{
	public function index(){
		
		$root = get_baseroot();
		
		$page = intval($GLOBALS['request']['page']);
		
		$status = strim($GLOBALS['request']['status']);//0:全部;1:可转让;2:转让中;3:已转让;4:已撤销;
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			$result = getUcTransferList($page,$status);
			$results = getUcTransferBuys($page,$status);
			 
			$root['item'] = $result['list'];	//债权转让
			$root['items'] = $results['list'];  //购买记录
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['status'] = $status;
		$root['program_title'] = "债权转让";
		output($root);		
	}
}
?>
