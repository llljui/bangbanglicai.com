<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_refund_detail
{
	public function index(){
		
		$root = get_baseroot();
		
		$id = intval($GLOBALS['request']['id']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			require APP_ROOT_PATH.'app/Lib/deal.php';
			$root['user_login_status'] = 1;
			$deal = get_deal($id);
			$root['deal'] = $deal;
			
			$transaction_money = $deal['borrow_amount'] * $deal['services_fee'] *0.01;
			//还款列表

			$loan_list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_repay where deal_id=$id ORDER BY repay_time ASC");
			$manage_fee = 0;
			$impose_money = 0;
			$repay_money = 0;
			$manage_impose_fee = 0;
			$mortgage_fee=0;
			foreach($loan_list as $k=>$v){
				$manage_fee += $v['true_manage_money'];
                $mortgage_fee += $v['true_mortgage_fee'];
				$impose_money += $v['impose_money'];
				$repay_money += $v['true_repay_money'];
				$manage_impose_fee +=$v['manage_impose_money'];
				
				
				//第几期
				$loan_list[$k]['l_key_index'] = $v['l_key']+1;	
				
				//还款日
				$loan_list[$k]['repay_time_format'] = to_date($v['repay_time'],'Y-m-d');
				$loan_list[$k]['true_repay_time_format'] = to_date($v['true_repay_time'],'Y-m-d');
		
				//已还本息
				$loan_list[$k]['repay_money_format'] = format_price($v['true_repay_money']);
				
				//逾期费用
				$loan_list[$k]['impose_money_format'] = format_price($v['impose_money']);
				
				//借款管理费
				$loan_list[$k]['manage_money_format'] = format_price($v['true_manage_money']);
				
				$loan_list[$k]['manage_impose_money_format'] = format_price($v['manage_impose_money']);
				 
				$loan_list[$k]['mortgage_fee_format'] = format_price($v['true_mortgage_fee']);
                
				//状态
				if($v['status'] == 0){
					$loan_list[$k]['status_format'] = '提前还款';
				}elseif($v['status'] == 1){
					$loan_list[$k]['status_format'] = '正常还款';
				}elseif($v['status'] == 2){
					$loan_list[$k]['status_format'] = '逾期还款';
				}elseif($v['status'] == 3){
					$loan_list[$k]['status_format'] = '严重逾期';
				}
				
			}
			$root['transaction_money_format'] = format_price($transaction_money);
			$root['manage_fee'] = $manage_fee;
            $root['mortgage_fee'] = $mortgage_fee;
			$root['impose_money'] = $impose_money;
			$root['repay_money'] = $repay_money;
			$root['loan_list'] = $loan_list;
			
			$root['agree_url'] = wap_url("index","deal_contract",array("id"=>$id));
			
			$inrepay_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id");
			$root['inrepay_info'] = $inrepay_info;
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "还款详情";
		output($root);		
	}
}
?>
