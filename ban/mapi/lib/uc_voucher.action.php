<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_voucher
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
			
			
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$result = get_voucher_list($limit,$user_id);
			
			foreach($result['list'] as $k=>$v){
				$result['list'][$k]['limit_time'] = "";
				if($v['begin_time'] == 0){
					$result['list'][$k]['limit_time'] .="无限";
				}
				else
					$result['list'][$k]['limit_time'] .=to_date($v['begin_time'],"Y-m-d");
					
				if ($v['end_time'] > 0)
					$result['list'][$k]['limit_time'] .=" ". $GLOBALS['lang']['TO'] ." ". to_date($v['end_time'],"Y-m-d");
				
				
				$result['list'][$k]['status_format'] = "未使用";
				$result['list'][$k]['status'] = 0;	
				
				if($v['use_limit'] > 0 && ($v['use_limit'] - $v['use_count']) == 0){
					$result['list'][$k]['status'] = 1;	
					$result['list'][$k]['status_format'] = "已使用";
				}
				elseif ($v['end_time'] > 0 && ($v['end_time']+24*3600 - 1) < TIME_UTC){
					$result['list'][$k]['status'] = 2;	
					$result['list'][$k]['status_format'] = "已过期";
				}
			}
			
			
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
