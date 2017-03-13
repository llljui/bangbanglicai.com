<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require_once  APP_ROOT_PATH.'app/Lib/project_func.php';
class check_project_cart
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;

		if(!$GLOBALS['user_info'])
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		
		$root['user_login_status'] = 1;
		$root['response_code'] = 1;
		
		$root['user_info'] = get_user_info("*","id=".$user_id,"ROW");
		
		$id = intval($GLOBALS["request"]['id']);
		
		$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_item where id = ".$id);
		
		if(!$deal_item)
		{
			$root['response_code'] = 0; 
			$root['show_err'] = "项目不存在或者未通过审核，请返回重新操作!";
			output($root);
		}
		elseif(($deal_item['support_count']+$deal_item['virtual_person'])>=$deal_item['limit_user']&&$deal_item['limit_user']!=0)
		{
			$root['response_code'] = 0; 
			$root['show_err'] = "项目已满，请选择其他项目!";
			output($root);
		}
		
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where is_delete = 0 and is_effect = 1 and id = ".$deal_item['deal_id']);
		
		if($deal_info['user_id'] == $user_id)
		{
			$root['response_code'] = 0; 
			$root['show_err'] = "不能购买自己的产品";
			output($root);
		}
		
		if(!$deal_info)
		{
			$root['response_code'] = 0; 
			$root['show_err'] = "项目不存在或者未通过审核，请返回重新操作!";
			output($root);
		}
		
		$deal_info = cache_project_extra($deal_info);
		
		$deal_info = init_project_page_wap($deal_info);
		
		if($deal_info['begin_time']>TIME_UTC||($deal_info['end_time']<TIME_UTC&&$deal_info['end_time']!=0))
		{
			$root['response_code'] = 0; 
			$root['show_err'] = "项目未开始或者已结束，请返回重新操作!";
			output($root);
		}
		
		$deal_item['consigee_url']=wap_url("index","uc_address");
		
		//无私奉献
		if($deal_item['type']==1){
			$pay_money=floatval($GLOBALS["request"]['pay_money']);
			if($pay_money<=0){
				$root['response_code'] = 0; 
				$root['show_err'] = "您输入的金额错误";
				output($root);
			}
  		} 
		
		output($root);
	}
}
?>
