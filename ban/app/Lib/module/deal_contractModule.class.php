<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class deal_contractModule extends SiteBaseModule
{
	
	public function contract(){
		$id = intval($_REQUEST['id']);
		if($id == 0){
			showErr("操作失败！");
		}
		$deal = get_deal($id);
		if(!$deal){
			showErr("操作失败！");
		}
//		$load_user_id = $GLOBALS['db']->getOne("select user_id FROM ".DB_PREFIX."deal_load WHERE deal_id=".$id." and user_id=".$GLOBALS['user_info']['id']." ORDER BY create_time ASC");
//		if($load_user_id == 0  && $deal['user_id']!=$GLOBALS['user_info']['id'] ){
//			showErr("操作失败！");
//		}
				
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
		}
		
		$GLOBALS['tmpl']->assign('loan_list',$loan_list);
		
		$u_info = get_user("*",$deal['user_id']);
		$GLOBALS['tmpl']->assign('user_info',$u_info);
		if($u_info['sealpassed'] == 1){
			$credit_file = get_user_credit_file($deal['user_id']);
			$GLOBALS['tmpl']->assign('user_seal_url',$credit_file['credit_seal']['file_list'][0]);
		}
		
		
		$GLOBALS['tmpl']->assign('SITE_URL',str_replace(array("https://","http://"),"",SITE_DOMAIN));
		$GLOBALS['tmpl']->assign('SITE_TITLE',app_conf("SITE_TITLE"));
		$GLOBALS['tmpl']->assign('CURRENCY_UNIT',app_conf("CURRENCY_UNIT"));
		
		
		$contract = $GLOBALS['tmpl']->fetch("str:".get_contract($deal['contract_id']));
		
		
		$GLOBALS['tmpl']->assign('contract',$contract);
		
		$GLOBALS['tmpl']->display("deal_contract.html");	
	}
	
	
	
}
?>
