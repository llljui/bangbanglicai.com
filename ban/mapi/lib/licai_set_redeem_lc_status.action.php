<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_set_redeem_lc_status
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			//$result["jump"] = url("licai#uc_expire_lc");
			require_once APP_ROOT_PATH.'system/libs/licai.php';
			
			$redempte_id = intval($GLOBALS['request']["redempte_id"]);
			$status = 1;
			$earn_money = strim($GLOBALS['request']["earn_money"]);
			$fee = strim($GLOBALS['request']["fee"]);
			$pay_type = 0; //0不允许垫付
			$web_type = 2; //0前台
			
			$redempte_info = $GLOBALS['db']->getRow("select lcr.* from ".DB_PREFIX."licai_redempte lcr 
			left join ".DB_PREFIX."licai_order lco on lco.id = lcr.order_id 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lcr.id =".$redempte_id." and lc.user_id = ".$user_id);
	
			if(!$redempte_info)
			{
				$result["status"] = 0;
				$reuslt["show_err"] = "操作失败，请重试";
				output($root);
			}
			
			$root['redempte_info'] = $redempte_info;
			$result = deal_redempte($redempte_id,$status,$earn_money,$fee,$redempte_info["organiser_fee"],$pay_type,$web_type);
			
			$root["status"] = $result['status'];
			$root["show_err"] = $result['info'] == "" ? "操作成功" : $result['info'];
			
			output($root);
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "到期赎回更新";
		output($root);		
	}
}
?>
