<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class calc_bid
{
	public function index(){
		
		//$root = array();
		
		require_once APP_ROOT_PATH."app/Lib/deal_func.php";
		
		$id = intval($GLOBALS['request']['id']);
		$minmoney = floatval($GLOBALS['request']['money']);
		$number = floatval($GLOBALS['request']['number']);
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
				
		$deal = $GLOBALS['cache']->get("MOBILE_DEAL_BY_ID_".$id);
		if($deal===false)
		{
			$deal = get_deal($id);
			$GLOBALS['cache']->set("MOBILE_DEAL_BY_ID_".$id,$deal,300);	
		}
			
		$type_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_loan_type WHERE id=".$deal['type_id']);
			
		 if($type_info['costsetting'] && $user['vip_id']>0){
				$vo_list = explode("\n",$type_info['costsetting']);
				foreach($vo_list as $k=>$v){
					$vo_list[$k] = explode("|",$v);
					if($vo_list[$k]['0'] == $user['vip_id']){
						if($vo_list[$k]['1']>0 || $vo_list[$k]['2']>0 || $vo_list[$k]['3']>0 || $vo_list[$k]['4']>0 || $vo_list[$k]['5']>0 || $vo_list[$k]['6']>0 ){
							$deal['user_loan_manage_fee'] = $vo_list[$k]['3'];
							$deal['user_loan_interest_manage_fee'] = $vo_list[$k]['4'];
						}
					}
				}
			}else{
				if($type_info['levelsetting']){
					$vol_list = explode("\n",$type_info['levelsetting']);
					foreach($vol_list as $kl=>$vl){
						$vol_list[$kl] = explode("|",$vl);
						if($vol_list[$kl]['0'] == $user['level_id']){
							if($vol_list[$kl]['1']>0 || $vol_list[$kl]['2']>0 || $vol_list[$kl]['3']>0 || $vol_list[$kl]['4']>0 || $vol_list[$kl]['5']>0 || $vol_list[$kl]['6']>0){
								$deal['user_loan_manage_fee'] = $vol_list[$kl]['3'];
								$deal['user_loan_interest_manage_fee'] = $vol_list[$kl]['4'];
							}
						}
					}
				}
			}
		$parmas = array();
		//$parmas['uloantype'] = 1;
		
		$parmas['uloantype'] =  $deal['uloadtype'];
		if($deal['uloadtype'] == 1){
			$parmas['minmoney'] = $minmoney;
			$parmas['money'] = $number;
			//$parmas['money'] = $number * $minmoney;
		}else{
			$parmas['money'] = $minmoney;
		}
		
		//$parmas['loantype'] = 0;
		$parmas['loantype'] = $deal['loantype'];
		$parmas['rate'] = $deal['rate'];
		$parmas['repay_time'] = $deal['repay_time'];
		$parmas['repay_time_type'] = $deal['repay_time_type'];
		
		$parmas['user_loan_manage_fee'] = $deal['user_loan_manage_fee'];
		$parmas['user_loan_interest_manage_fee'] = $deal['user_loan_interest_manage_fee'];
		
		$root['profit'] = bid_calculate($parmas);
		$root['profit'] = "¥ ".$root['profit'] ;
		
		$root['response_code'] = 1;
		
		output($root);	
	}
}
?>
