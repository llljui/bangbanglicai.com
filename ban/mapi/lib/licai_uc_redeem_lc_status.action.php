<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_redeem_lc_status
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$id = intval($GLOBALS['request']["id"]);
		
			$vo =  $GLOBALS["db"]->getRow("select lcr.* from ".DB_PREFIX."licai_redempte lcr  
			left join ".DB_PREFIX."licai_order lco on lco.id = lcr.order_id 
			left join ".DB_PREFIX."licai lc on lc.id= lco.licai_id  
			where lcr.id =".$id." and lc.user_id =".$user_id);
			
			$root['vo'] = $vo;
			$root['id'] = $id;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "发放理财";
		output($root);		
	}
}
?>
