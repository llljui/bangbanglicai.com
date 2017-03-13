<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_learn_bid
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			$id = intval($GLOBALS['request']['id']);
			$return = array("status"=>0,"info"=>"");
				
			$learn_deal = get_deal($id);
			if(!$learn_deal){
				$root["status"] = 0;
				$root["show_err"] = "贷款不存在";
				output($root);
			}
			
			if($learn_deal['user_id'] == $user_id){
				$root["status"] = 0;
				$root["show_err"] = $GLOBALS['lang']['CANT_BID_BY_YOURSELF'];
				output($root);
			}
			
			if($learn_deal['ips_bill_no']!="" && $user['ips_acct_no']==""){
				$root["status"] = 0;
				$root["show_err"] = "此标为第三方托管标，请先绑定第三方托管账户";
				output($root);
			}
			
			//判断是否是新手专享
			if($learn_deal['is_new']==1 &&  $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_load  WHERE user_id=".$user_id." ") > 0){
				$root["status"] = 0;
				$root["show_err"] = "此标为新手专享标，您已经投过贷款了";
				output($root);
			}
			
			$root["id"] = $id;
			$root["deal"] = $learn_deal;
			$root["user_id"] = $user_id;
			$root["use_learn"] = $learn_deal['use_learn'];
			
			if($learn_deal['use_learn'] == 1){
				//体验金抵用
				$now_time = to_date(TIME_UTC,"Y-m-d");
				$lsql = "select lsl.* FROM ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id WHERE lt.invest_type = 1 and lsl.is_use = 0 and lsl.user_id='".$user_id."' and lsl.is_recycle = 0 and lsl.begin_time <= '$now_time' and '$now_time' <= lsl.end_time  and lt.is_effect = 1 ";
				$lecv_list = $GLOBALS['db']->getAll($lsql);
				$root["lecv_list"] = $lecv_list;
			}
			
			$return["status"] = 1;
		
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "体验金投资";
		output($root);		
	}
}
?>
