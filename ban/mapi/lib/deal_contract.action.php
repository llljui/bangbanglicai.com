<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class deal_contract
{
	public function index(){
		$root = get_baseroot();
		
		$id = intval($GLOBALS['request']['id']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		require APP_ROOT_PATH.'app/Lib/uc.php';
		require APP_ROOT_PATH.'app/Lib/deal.php';
		require_once APP_ROOT_PATH.'system/common.php';
		
		$root['program_title'] = "借款合同";
		
		if($user_id==1){
			$root['user_login_status'] = 0;
			$root['response_code'] = 0;
			$root['contract'] = "";
			output($root);	
			die();
		}
		
		$root['user_login_status'] = 1;
			
		$root['response_code'] = 1;
		if($id == 0){
			$root['contract'] = "";
			output($root);	
			die();
		}
		$deal = get_deal($id);
		if(!$deal){
			$root['contract'] = "";
			output($root);	
			die();
		}
				
		$GLOBALS['tmpl']->assign('deal',$deal);
		
		$loan_list = $GLOBALS['db']->getAll("select * FROM ".DB_PREFIX."deal_load WHERE deal_id=".$id." ORDER BY create_time ASC");
		foreach($loan_list as $k=>$v){
			$vv_deal['borrow_amount'] = $v['money'];
			$vv_deal['rate'] = $deal['rate'];
			$vv_deal['repay_time'] = $deal['repay_time'];
			$vv_deal['loantype'] = $deal['loantype'];
			$vv_deal['repay_time_type'] = $deal['repay_time_type'];
			
			$deal_rs =  deal_repay_money($vv_deal);
			$loan_list[$k]['get_repay_money'] = $deal_rs['month_repay_money'];
			if(is_last_repay($deal['loantype'])==1)
				$loan_list[$k]['get_repay_money'] = $deal_rs['remain_repay_money'];
			
			$loan_list[$k]['user_name'] = $GLOBALS['user_info']['id'] == $v['user_id'] ?  $v['user_name'] :  utf_substr($v['user_name']);
		}
		
		
		$GLOBALS['tmpl']->assign('loan_list',$loan_list);
		
		if($deal['user']['sealpassed'] == 1){
			$credit_file = get_user_credit_file($deal['user_id']);
			$GLOBALS['tmpl']->assign('user_seal_url',$credit_file['credit_seal']['file_list'][0]);
		}
		
		
		$GLOBALS['tmpl']->assign('SITE_URL',str_replace(array("https://","http://"),"",SITE_DOMAIN));
		$GLOBALS['tmpl']->assign('SITE_TITLE',app_conf("SITE_TITLE"));
		$GLOBALS['tmpl']->assign('CURRENCY_UNIT',app_conf("CURRENCY_UNIT"));
		
		$contract = $GLOBALS['tmpl']->fetch("str:".get_contract($deal['contract_id']));
		$root['contract'] = $contract;
		
		output($root);		
	}
}
?>
