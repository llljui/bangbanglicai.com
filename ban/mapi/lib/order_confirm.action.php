<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class order_confirm
{
	public function index(){
		
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		require_once APP_ROOT_PATH.'system/libs/peizi.php';
		
		$root["money"] = 0;//status=2;余额不足，请充值，充值金额
		$root["status"] = 0;//0:出错;1:正确;2:余额不足，请充值;3:请先登陆
		$root["jump"] = url('index');
		
		if($user_id == 0){
			$root["jump"] = url("index","user#login");
			$root["status"] = 3;
			$root["info"] = "请先登陆";
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
//			echo json_encode($root);
//			exit;
			output($root);	
		}
		
		$conf_id = intval($GLOBALS['request']['conf_id']);
		$is_today = intval($GLOBALS['request']['is_today']);
		$borrow_money = intval($GLOBALS['request']['borrow_money']);//需要借款金额
		$lever = intval($GLOBALS['request']['lever']);//	
		$rate_id = intval($GLOBALS['request']['rate_id']);//
		
		$sql = "select type from ".DB_PREFIX."peizi_conf where id = ".$conf_id." limit 1";
		$type = intval($GLOBALS['db']->getOne($sql));//配资类型;0:天;1周；2月
		
		
		$month = 0;
		if ($type == 1){
			$time_limit_num = 5;//周周
		}else{
			$time_limit_num = intval($GLOBALS['request']['time_limit_num']);//资金使用期限			
			$month = $time_limit_num;					
		}

		if ($type == 2){
			$parma = get_peizi_conf($conf_id,$borrow_money,$lever,$month,$rate_id);
		}else{
			$parma = get_peizi_conf($conf_id,$borrow_money,$lever,0,$rate_id);
			
			if ($time_limit_num == 0){
				$time_limit_num = $parma['peizi_conf']['min_day'];
			}
		}
		
		$peizi_conf = $parma['peizi_conf'];
		
		//0;type=0时有效;1周末节假日免费
		$is_holiday_fee = intval($peizi_conf['is_holiday_fee']);
		
	
		//一次性业务审核费
		$manage_money = intval($peizi_conf['manage_money']);
		
		//成本;
		$cost_money = intval($parma['cost_money']);
		//日,月 利息
		$rate_money = floatval($parma['rate_money']);
		//日,月 服务费
		$site_money = floatval($parma['site_money']);
		
		//type=2时，有效;0:按月收取;1:一次性收取
		if ($type == 2){
			$first_rate_money = $rate_money + $site_money;
		}else{
			$first_rate_money = ($rate_money + $site_money) * $time_limit_num;
		}
		
		
		$total_money = $manage_money + $cost_money + $first_rate_money;
		//当前用户余额	
		
		
		$user_total_money = floatval(get_user_info("AES_DECRYPT(money_encrypt,'".AES_DECRYPT_KEY."') AS money","id = ".$user_id,"ONE"));		
		if($user_total_money< $total_money){
			$root["total_money"] = $total_money;
			$root["status"] = 2;
			$root["info"] = "余额不足，请充值:".format_price($total_money - $user_total_money);// ';manage_money:' .$manage_money.';cost_money:' .$cost_money . ';first_rate_money:' . $first_rate_money;
			$root["jump"] = SITE_DOMAIN.url("member","uc_money#incharge",array('money'=>$total_money));//member.php?ctl=uc_money&act=incharge
//			echo json_encode($root);
//			exit;
			output($root);	
		}
		
		
		$order = array();		
		$order['type'] = $type;
		$order['peizi_conf_id'] = $conf_id;
		$order['user_id'] = $user_id;
		$order['manage_money'] = $manage_money;
		$order['cost_money'] = $cost_money;
		$order['borrow_money'] = $borrow_money;
		$order['stock_money'] = 0;
		$order['lever'] = $lever;
		$order['is_today'] = $is_today;
		
		$order['warning_line'] = intval($parma['warning_line']);
		$order['open_line'] = intval($parma['open_line']);
		
		$order['warning_coefficient'] = intval($parma['warning_coefficient']);
		$order['open_coefficient'] = intval($parma['open_coefficient']);
		
		$order['rate_type'] = $parma['rate_type'];//type=0时，有效; 0:按借款金额收取利率；1按每天的实际交易金额,收取利率
		
		$order['rate'] = $parma['rate'];//利率
		$order['rate_money'] = $parma['rate_money'];//每日或每月利息费用
		
		$order['site_rate'] = $parma['site_rate'];//服务费率【每日/月收取】[平台收取的]
		$order['site_money'] = $parma['site_money'];//服务费 = 借款金额×管理费率【按天或月收取】【平台收取的】
				
		$order['time_limit_num'] = $time_limit_num;
		$order['create_time'] = to_date(TIME_UTC);
		$order['status'] = 0;//0:在申请；
		$order['memo'] = $parma['limit_info'];
		

		$order['first_rate_money'] = $first_rate_money;
		
		$order['contract_id'] = $peizi_conf['contract_id'];
		
		$order['is_holiday_fee'] = $is_holiday_fee;//是否周末节假日免费
		
		$order['payoff_rate'] = $parma['payoff_rate'];
		$order['invest_payoff_rate'] = $parma['invest_payoff_rate'];
		
		
		
		//平台获得交易佣金比率
		//$order['site_commission_rate'] = $parma['site_commission_rate'];
		
		//积分
		$order['score'] = intval(app_conf("PEIZI_SCORE_TYPE"))== 0  ? app_conf("PEIZI_SCORE") : floatval(app_conf("PEIZI_SCORE")) * ($cost_money + $borrow_money);
		$order['load_score'] = intval(app_conf("PEIZI_LOAD_SCORE_TYPE"))== 0  ? app_conf("PEIZI_LOAD_SCORE") : floatval(app_conf("PEIZI_LOAD_SCORE")) *  $borrow_money;
		
		//投资者获得交易佣金比率
		$order['invest_commission_rate'] = $parma['invest_commission_rate'];
		
		if ($is_today == 1){
			$order['begin_date'] = to_date(TIME_UTC,"Y-m-d");					
		}else{
			$order['begin_date'] = get_peizi_end_date(to_date(TIME_UTC,"Y-m-d"),1,0,1);//下一交易日
		}
		if ($type == 2){
			//按自然月计算，如使用1个月，1月8日到2月7日，当月日期没有,则该按月的最后一天计算，包含各类节假日
			$order['end_date'] = dec_date(add_month($order['begin_date'], $month),1);
		}else{
			$order['end_date'] = get_peizi_end_date($order['begin_date'],$time_limit_num - 1,$type,$is_holiday_fee);
		}
		
		//借款推荐人
		$p_user_id = intval($GLOBALS['user_info']['pid']);
		
		if ($p_user_id > 0){
			$peizi_invite = load_auto_cache("peizi_invite",array('type'=>1,'user_id'=>$p_user_id));
		
			$order['p_user_id'] = $p_user_id;
			$order['invite_borrow_money_rate'] = floatval($peizi_invite['money_rate']);
			$order['invite_borrow_interest_rate'] = floatval($peizi_invite['interest_rate']);
			$order['invite_borrow_commission_rate'] = floatval($peizi_invite['commission_rate']);
			
			//借款返利【借款推荐人p_user_id获得的: 借款金额返利 = borrow_money * invite_borrow_money_rate】
			$order['invite_borrow_money'] = floatval($borrow_money * $order['invite_borrow_money_rate']);
		}
		
		
		if ($borrow_money > 0)
			$borrow_money_fromat = number_format($borrow_money / 1000,2).'万元';
		else
			$borrow_money_fromat = number_format($borrow_money).'元';
		
		$total_money_fromat = $borrow_money + $cost_money;
		if ($total_money_fromat > 0)
			$total_money_fromat = number_format($total_money_fromat / 1000,2).'万元';
		else
			$total_money_fromat = number_format($total_money_fromat).'元';

		
		$peizi_name = '【'.$GLOBALS['user_info']['user_name'].'】借款'.$borrow_money_fromat.'|市值'.$total_money_fromat.'元股票做抵押';
		//$peizi_name =  $GLOBALS['user_info']['real_name'];
		
		$order['peizi_name'] = $peizi_name;
		$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order",$order,"INSERT");
		$order_id = $GLOBALS['db']->insert_id();
		
		if ($order_id > 0){
			//30:配资本金(冻结); 31:配资预交款(冻结);32:配资审核费(冻结);33:配资日利息(平台收入);34:配资月利息(平台收入);35:配资审核费(平台收入)
			//require_once APP_ROOT_PATH.'system/libs/user.php';
			
			//冻结：本金 cost_money array('money'=>-$data['money'],'lock_money'=>$data['money'])
			modify_account(array('money'=>-$cost_money,'lock_money'=>$cost_money), $user_id,'冻结配资本金,配资编号:'.$order_id,30);
			
			//冻结：首次付款  first_rate_money
			modify_account(array('money'=>-$first_rate_money,'lock_money'=>$first_rate_money), $user_id,'冻结预交款,配资编号:'.$order_id,31);
			
			//冻结：业务审核费 (32借款服务费) manage_money
			modify_account(array('money'=>-$manage_money,'lock_money'=>$manage_money), $user_id,'冻结服务费,配资编号:'.$order_id,32);
			
			$order_sn = to_date(TIME_UTC,"Y")."".str_pad($order_id,7,0,STR_PAD_LEFT);
			$data = array();
			$data['order_sn'] = $order_sn;
			$data['status'] = 1;					
			$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order",$data,"UPDATE","id=".$order_id);
			
			$root["status"] = 1;
			$root["jump"] = url("member","uc_trader#verify");
			$root["info"] = "订单已提交,等待审核";
			//echo json_encode($root);
						
		}else{
								
			$root["status"] = 0;
			$root["info"] = "配资单创建失败,请重试";
			//echo  json_encode($root);
		}
			
		$root['program_title'] = "配资订单确认";
		output($root);		
	}
}
?>
