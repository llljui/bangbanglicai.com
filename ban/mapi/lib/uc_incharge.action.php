<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_incharge
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
					
			
			//输出支付方式
			if((int)$GLOBALS['request']['is_sdk']==1){
				$not_in = "'Wwxjspay'";
				$payment_list = $GLOBALS['db']->getAll("select id, name as class_name,class_name  as  iclass_name,name,fee_amount,description,logo,fee_type,config from ".DB_PREFIX."payment where is_effect = 1 and online_pay in(2,3) AND class_name not in(".$not_in.") order by sort desc");
			}else{
				if((int)$GLOBALS['request']['is_weixin']==0){
					$extW = "AND class_name not in ('Wwxjspay')";
				}
				$payment_list = $GLOBALS['db']->getAll("select id, name as class_name,class_name  as  iclass_name,name,fee_amount,description,logo,fee_type,config from ".DB_PREFIX."payment where is_effect = 1 and online_pay = 2 ".$extW." order by sort desc");
			}
			
			foreach($payment_list as $k=>$v){
				$payment_list[$k]['logo'] = SITE_DOMAIN.APP_ROOT.'/../'.$v['logo'];
				//$payment_list[$k]['config_format'] = unserialize(($payment_list[$k]['config']));
				$payment_list[$k]['img'] = SITE_DOMAIN.APP_ROOT.$v['logo'];
				$payment_list[$k]['img'] = str_replace("/mapi.", "", $payment_list[$k]['img']);
			}
			
			$root['payment_list'] = $payment_list;
			
			//判断是否有线下支付
			$below_payment = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where is_effect = 1 and class_name = 'Otherpay'");
			$b_pay = array();
			if($below_payment){
				$below_payment['config'] = unserialize($below_payment['config']);
				/*
				$count = count($payment_item['config']['pay_name']);
				for($kk=0;$kk<$count;$kk++){
					$pay = array();
					$pay['id'] = $payment_item['id'];
					$pay['id'] = $payment_item['id'];
					
					$html .= "<div class='clearfix'>";
					$html .= "<label class='f_l w140'><input type='radio' name='payment' value='".$payment_item['id']."' onclick='set_bank(\"".$kk."\")' />".
							$payment_item['config']['pay_name'][$kk]."</label>".
							"<div class='f_l' style='line-height:24px'>收款人：".$payment_item['config']['pay_account_name'][$kk]."&nbsp;&nbsp;&nbsp;&nbsp;" .
							"收款帐号：".$payment_item['config']['pay_account'][$kk]."&nbsp;&nbsp;&nbsp;&nbsp;开户行：".$payment_item['config']['pay_bank'][$kk]."</div>";
					$html .="</div><div class='blank'></div>";
				}
				*/
				
				$count = count($below_payment['config']['pay_name']);
				for($kk=0;$kk<$count;$kk++){
					$pay = array();
					$pay['pay_id'] = $below_payment['id'];
					$pay['bank_id'] = $kk;
					$pay['pay_name'] = $below_payment['config']['pay_name'][$kk];
					$pay['pay_account_name'] = $below_payment['config']['pay_account_name'][$kk];
					$pay['pay_account'] = $below_payment['config']['pay_account'][$kk];
					$pay['pay_bank'] = $below_payment['config']['pay_bank'][$kk];
					
					$b_pay[] = $pay;
					
				}
			}
			
			$root['below_payment'] = $b_pay;
			
			
			
			
			$ips_bank_list = array();
			$root['open_ips'] = intval(app_conf("OPEN_IPS"));
			$root['ips_acct_no'] = $user['ips_acct_no'];
			$root['idno'] = $user['idno'];//身份证号
			$root['real_name'] = $user['real_name'];
			
			if (intval(app_conf("OPEN_IPS")) > 0){
				
				$list = GetIpsBankList();
				
				if($list['BankList']){
					$ips_bank_list = $list['BankList'];
				}
				
				$app_url = APP_ROOT."/index.php?ctl=collocation&act=CreateNewAcct&user_type=0&user_id=".$user_id."&from=".$GLOBALS['request']['from'];
				$root['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$app_url);
				$root['acct_url'] = $root['app_url'];
				if($GLOBALS['request']['from']=="wap"){
					$pTrdAmt = floatval($GLOBALS['request']['pTrdAmt']);
					$app_url = APP_ROOT."/index.php?ctl=collocation&act=DoDpTrade&user_type=0&pTrdBnkCode=parm_bnk&pTrdAmt=".$pTrdAmt."&user_id=".$user_id."&from=".$GLOBALS['request']['from'];
				}else{
					$app_url = APP_ROOT."/index.php?ctl=collocation&act=DoDpTrade&user_type=0&pTrdBnkCode=parm_bnk&pTrdAmt=parm_amt&user_id=".$user_id."&from=".$GLOBALS['request']['from'];
				}
				//充值 http://p2p.fanwe.net/index.php?ctl=collocation&act=DoDpTrade&user_type=0&from=app&user_id=44&pTrdBnkCode=00004&pTrdAmt=10000
				
				$root['dp_url'] = str_replace("/mapi", "", SITE_DOMAIN.$app_url);
												
			}
			
			if (count($ips_bank_list)==0){			
				$ips_bank_list = array();
			}
			
			$root['ips_bank_list'] = $ips_bank_list;
			
			$xianshang = 0;
			if($payment_list){
				$xianshang = 1;
			}
			$root['c_one'] = $xianshang;
			
			$xianxia = 0;
			if($below_payment){
				$xianxia = 1;
			}
			$root['c_two'] = $xianxia;
			$disanfang = 0;
			if($ips_bank_list){
				$disanfang = 1;
			}
			$root['c_three'] = $disanfang;
			$root['c_number'] = $xianshang + $xianxia + $disanfang ;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "充值";
		output($root);		
	}
}
?>
