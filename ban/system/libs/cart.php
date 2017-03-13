<?php 
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

/**
 * 
 * 创建付款单号
 * @param $money 付款金额
 * @param $order_id 订单ID
 * @param $payment_id 付款方式ID
 * @param $memo 付款单备注
 * return payment_notice_id 付款单ID
 * 
 */
function make_payment_notice($money,$order_id,$payment_id,$memo='')
{
	$notice['create_time'] = TIME_UTC;
	$notice['order_id'] = $order_id;
	$notice['user_id'] = $GLOBALS['db']->getOne("select user_id from ".DB_PREFIX."deal_order where id = ".$order_id);
	$notice['payment_id'] = $payment_id;
	$notice['memo'] = $memo;
	$notice['money'] = $money;
	do{
		$notice['notice_sn'] = to_date(TIME_UTC,"Ymdhis").rand(10,99);
		$GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$notice,'INSERT','','SILENT'); 
		$notice_id = intval($GLOBALS['db']->insert_id());
	}while($notice_id==0);
	return $notice_id;
}

/**
 * 付款单的支付
 * @param unknown_type $payment_notice_id
 * 当超额付款时在此进行退款处理
 */
function payment_paid($payment_notice_id, $outer_notice_sn = '')
{
	$payment_notice_id = intval($payment_notice_id);
	$now = TIME_UTC;

	$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set pay_time = ".$now.", pay_date = '".to_date($now,'Y-m-d')."',outer_notice_sn = '".$outer_notice_sn."',is_paid = 1 where id = ".$payment_notice_id." and is_paid = 0");	
	
	$rs = $GLOBALS['db']->affected_rows();
	if($rs)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_notice['payment_id']);
		
		$GLOBALS['db']->query("update ".DB_PREFIX."payment set total_amount = total_amount + ".$payment_notice['money']." where class_name = '".$payment_info['class_name']."'");									
		
			
		if (intval($payment_notice['order_id']) == 0 || (intval($payment_notice['order_id']) > 0 && intval($payment_notice['debit_type']) > 0)){	
			
			//充值
			require_once APP_ROOT_PATH."system/libs/user.php";
			if($payment_info['online_pay'] == 0){
				$msg = '线下充值';// sprintf($GLOBALS['lang']['PAYMENT_INCHARGE'],$payment_notice['notice_sn']);
			}else{
				$msg = '在线充值';// sprintf($GLOBALS['lang']['PAYMENT_INCHARGE'],$payment_notice['notice_sn']);
			}
			
			$fee_amount = $payment_notice['fee_amount'];
			
			$money = $payment_notice['money'];
			
			
			modify_account(array('money'=>$money - $fee_amount,'fee_amount'=>$fee_amount,'score'=>0),$payment_notice['user_id'],$msg,1);
			
			if(intval($payment_notice['debit_type']) > 0)
			{
				$first_relief = 0;
				require APP_ROOT_PATH.'app/Lib/deal.php';
				if($payment_notice["debit_type"] == 1)
				{
					//首单 减钱
					$deal_repay = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_repay where id = ".$payment_notice["order_id"]." and has_repay = 0 ");
					if($deal_repay["l_key"]==0)//$deal_repay["l_key"] == 0 (!$deal_repay["l_key"] )
					{
						$deal_admin = $GLOBALS["db"]->getOne("select admin_id from ".DB_PREFIX."deal where id = ".$deal_repay["deal_id"]);
						if($deal_admin)
						{
							$first_relief = $GLOBALS["db"]->getOne("select first_relief from ".DB_PREFIX."debit_conf");
							modify_account(array('money'=>$first_relief,'fee_amount'=>0,'score'=>0),$payment_notice['user_id'],"首单减免",29);
						}
					}
					//正常还款
					$status = getUcRepayBorrowMoney($deal_repay["deal_id"],$deal_repay["l_key"],$deal_repay['user_id']);
				}
				else
				{
					//首单 减钱
					$ids = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."deal_repay where deal_id = ".$payment_notice["order_id"]." and has_repay = 1");
					//print_r($ids);die;
					if(!$ids)
					{
						//首单 减钱
						$deal_admin = $GLOBALS["db"]->getOne("select admin_id from ".DB_PREFIX."deal where id = ".$payment_notice["order_id"]);
						if($deal_admin)
						{
							$first_relief = $GLOBALS["db"]->getOne("select first_relief from ".DB_PREFIX."debit_conf");
							modify_account(array('money'=>$first_relief,'fee_amount'=>0,'score'=>0),$payment_notice['user_id'],"首单减免",29);
						}
					}
					$status = getUCInrepayRepayBorrowMoney($payment_notice["order_id"],$payment_notice['user_id']);
				}
			}
			//在此处开始生成付款的短信及邮件
			send_payment_sms($payment_notice_id);
			send_payment_mail($payment_notice_id);
		}		
	}
	return $rs;
}

//同步订单支付状态
function order_paid($order_id)
{	
		$order_id  = intval($order_id);
		
		if ($order_id == 0){
			return true;
		}else{						
			return true;
		}
}


?>