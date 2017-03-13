<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

/**
 * 每天自动运行，扣除利息
 */
function auto_charging_rate_money($allow_arrearage = true,$allow_send_msg = true){

	$cur_date = to_date(TIME_UTC,"Y-m-d");

	$sql = "select id,rate_type,user_id,invest_user_id,order_sn, stock_sn,site_money,borrow_money,rate_money,begin_date,last_fee_date,next_fee_date,rate_money,type,is_holiday_fee,
			p_invest_user_id,invite_invest_interest_rate,invite_invest_commission_rate,p_user_id,invite_borrow_interest_rate,invite_borrow_commission_rate,
			invest_commission_rate,site_rate,rate
			from ".DB_PREFIX."peizi_order where (site_money > 0 or rate_money > 0) and `status` = 6 and rate_type = 0 and next_fee_date <= '".$cur_date."'";

	if ($allow_arrearage == false){
		$sql .= " and is_arrearage = 0 ";
	}
	$sql .= " order by next_fee_date asc";

	//echo $sql;exit;
	$peizi_order_list = $GLOBALS['db']->getAll($sql);


	require_once APP_ROOT_PATH.'system/libs/user.php';


	foreach ($peizi_order_list as $k => $v) {



		$user_id = intval($v['user_id']);
		$order_id = intval($v['id']);
		$user_info =get_user_info("*","id = ".$user_id);
		$money = floatval($user_info['money']);
		
		$rate_money = floatval($v['rate_money']);
		$site_money = floatval($v['site_money']);
		
		/*
		if ($v['rate_type'] == 1){			
			$sql_str = "select id,trade_money from ".DB_PREFIX."peizi_order_stock_money where peizi_order_id = ".$order_id." and stock_date = '".$v['next_fee_date']."'";
			$osm =	$GLOBALS['db']->getRow($sql_str);
			
			if (intval($osm['id']) == 0){
				//还未导入:当天的计息金额;
				continue;
			}
						
			$trade_money = $osm['trade_money'];
			$rate_money = $trade_money * $v['rate'];
			$site_money = $trade_money * $v['site_rate'];
		}*/
		
		$sever_money = $rate_money + $site_money;
		
		//print_r($user_info);exit;
		if ($sever_money >= $money){
			//扣费失败，余额不足
			$sql = "update ".DB_PREFIX."peizi_order set is_arrearage = 1 where id = ".$order_id;
			$GLOBALS['db']->query($sql);

			if ($allow_send_msg && app_conf("SMS_ON") == 1 && app_conf('SMS_CHARGING_FAILED_MSG')==1){
				//通知用户扣费失败
				$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_CHARGING_FAILED_MSG'");
				$tmpl_content = $tmpl['content'];
					
				$notice['site_name'] = app_conf("SHOP_TITLE");
				$notice['user_name'] = $user_info["user_name"];
				$notice['order_sn'] = $v['order_sn'];
				$notice['fee_date'] = $v['next_fee_date'];
				$notice['rate_money'] = format_price($sever_money);
					
				$GLOBALS['tmpl']->assign("notice",$notice);
					
				$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
					
				$msg_data['dest'] = $user_info['mobile'];
				$msg_data['send_type'] = 0;
				$msg_data['title'] = "配资自动扣费失败通知";
				$msg_data['content'] = addslashes($msg);;
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] = $user_info['id'];
				$msg_data['is_html'] = $tmpl['is_html'];
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
			}
		}else{
			pay_fee($order_id,$v['borrow_money'],$rate_money,$site_money,$allow_send_msg);
		}
	}

	//查询，是否还有未扣费的
	$sql = "select count(*) from ".DB_PREFIX."peizi_order where is_arrearage = 0 and (site_money > 0 or rate_money > 0) and `status` = 6 and rate_type = 0 and next_fee_date <= '".$cur_date."'";

	$num = intval($GLOBALS['db']->getOne($sql));
	if ($num > 0){
		//return $num;
		auto_charging_rate_money(false,$allow_send_msg);
	}else{
		return 1;//to_date(TIME_UTC);
	}
}

//id,user_id,type,begin_date,next_fee_date,is_holiday_fee,invest_user_id,borrow_money
function pay_fee($order_id,$trade_money,$rate_money,$site_money,$allow_send_msg = true){
	
	$sql = "select id,rate_type,user_id,invest_user_id,order_sn, stock_sn,site_money,borrow_money,rate_money,begin_date,last_fee_date,next_fee_date,rate_money,type,is_holiday_fee,
			p_invest_user_id,invite_invest_interest_rate,invite_invest_commission_rate,p_user_id,invite_borrow_interest_rate,invite_borrow_commission_rate,
			invest_commission_rate,site_rate,rate	from ".DB_PREFIX."peizi_order where id = ".intval($order_id);

	$v = $GLOBALS['db']->getRow($sql);
	
	$user_id = intval($v['user_id']);	
	$sever_money = $rate_money + $site_money;
	
	//自动继费到下期
	if ($v['type'] == 2){
		//type 配资类型;0:天;1周；2月
	
		$y1 = intval(to_date(to_timespan($v['begin_date']),'Y'));
		$m1 = intval(to_date(to_timespan($v['begin_date']),'m'));
			
		$y2 = intval(to_date(to_timespan($v['next_fee_date']),'Y'));
		$m2 = intval(to_date(to_timespan($v['next_fee_date']),'m'));
			
		$month = ($y2 - $y1) * 12 + ($m2 - $m1) + 1;
	
		//按自然月计算，如使用1个月，1月8日到2月8日，当月日期没有,则该按月的最后一天计算，包含各类节假日
		$next_fee_date = add_month($v['begin_date'], $month);
	}else{
		$next_fee_date = get_peizi_end_date($v['next_fee_date'], 1,$v['type'],$v['is_holiday_fee']);
	}
	
	
	
	$sql = "update ".DB_PREFIX."peizi_order set total_rate_money = total_rate_money + $rate_money,total_site_money = total_site_money + $site_money, is_arrearage = 0, last_fee_date = '".$v['next_fee_date']."', next_fee_date = '".$next_fee_date."' where  next_fee_date = '".$v['next_fee_date']."' and id = ".$order_id;
	//echo $sql; exit;
	$GLOBALS['db']->query($sql);
	
	if($GLOBALS['db']->affected_rows()){
	
		$fee_data = array();
		$fee_data['user_id'] = $user_id;
		$fee_data['invest_user_id'] = $v['invest_user_id'];
		$fee_data['peizi_order_id'] = $order_id;
		$fee_data['create_date'] = to_date(TIME_UTC);
		$fee_data['fee_date'] = $v['next_fee_date'];
		//计息金额,用于计算rate_money,site_money的基数,按借款金额计算=fanwe_peiziorder.borrow_money；如果按实际使用资金计息方式则为=\r\n1、当天新增+	===》利息，佣金\r\n2、当天卖出+	===》利息，佣金\r\n3、当天持有+（不含：当天新增，卖出部分）===》利息
		$fee_data['trade_money'] = $trade_money;
	
		$fee_data['borrow_money'] = $v['borrow_money'];
	
		$fee_data['rate_money'] = $rate_money;//投资人 收取的利息费用
		$fee_data['site_money'] = $site_money;//平台 收取的 服务费用
	
	
		/*
		 //平台获得的交易佣金  平台获得交易佣金比率;比如:  每次交易收取配资者 万份之8 作为交易佣金；其中券商收取：万份之3；投资者收取：万份之2；平台收取：万份之3；
		$fee_data['site_commission_money'] = ($fee_data['buy_money'] + $fee_data['sell_money']) * $v['site_commission_rate'];
		//投资者获得的交易佣金 投资者获得交易佣金比率;比如:  每次交易收取配资者 万份之8 作为交易佣金；其中券商收取：万份之3；投资者收取：万份之2；平台收取：万份之3；
		$fee_data['invest_commission_money'] = ($fee_data['buy_money'] + $fee_data['sell_money']) * $v['invest_commission_rate'];
		*/
		//投资者推荐人
		$fee_data['p_invest_user_id'] = $v['p_invest_user_id'];
		//投资推荐人（p_invest_user_id）获得的: 投资人利息收益的n%返利 = rate_money * fanwe_peiziorder.invite_invest_interest_rate
		$fee_data['invite_invest_interest_money'] = $fee_data['rate_money'] * $v['invite_invest_interest_rate'];
		//投资推荐人（p_invest_user_id）获得的: 投资人佣金收益的n%返利 = invest_commission_money * fanwe_peiziorder.invite_invest_commission_rate
		//$fee_data['invite_invest_commission_money'] = $fee_data['invest_commission_money'] * $v['invite_invest_commission_rate'];
	
		//配资者推荐人
		$fee_data['p_user_id'] = $v['p_user_id'];
		//借款推荐人（p_user_id）获得的: 平台利息收益的n%返利 = site_money * fanwe_peiziorder.invite_borrow_interest_rate
		$fee_data['invite_borrow_interest_money'] = $fee_data['site_money'] * $v['invite_borrow_interest_rate'];
		//借款推荐人（p_user_id）获得的: 平台佣金收益的n%返利 = site_commission_money * fanwe_peiziorder.invite_borrow_commission_rate
		//$fee_data['invite_borrow_commission_money'] = $fee_data['site_commission_money'] * $v['invite_borrow_commission_rate'];
	
		$fee_data['memo'] = '后台自动扣费';
		$fee_data['has_pay'] = 1;//是否支付;0:未支付;1:已支付
	
		$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_fee_list",$fee_data,"INSERT");
	
		if($GLOBALS['db']->affected_rows()){
			require_once APP_ROOT_PATH.'system/libs/user.php';
			
			//费用类型;1:业务审核费;2:利息;3:服务费;4:其它费用
			if ($fee_data['rate_money'] > 0){
				//30:配资本金(冻结); 31:配资预交款(冻结);32:配资审核费(冻结);33:配资服务费(平台收入);34:配资利息(投资者收入);35:配资平仓收益;36:配资投资
				modify_account(array("money"=>-$rate_money), $user_id,'自动扣费利息,配资编号:'.$order_id,34);
					
	
				modify_account(array("money"=>-$rate_money), $fee_data['invest_user_id'],'配资利息收入,配资编号:'.$order_id,34);
			}
	
			if ($fee_data['site_money'] > 0){
				//30:配资本金(冻结); 31:配资预交款(冻结);32:配资审核费(冻结);33:配资服务费(平台收入);34:配资利息(投资者收入);35:配资平仓收益;36:配资投资
				modify_account(array("money"=>-$site_money,'site_money'=>$site_money), $user_id,'自动扣费服务费,配资编号:'.$order_id,33);
			}
	
			/*
			 //平台获得的交易佣金  券商直接从 配资人中，收取，然后部分返还给 平台
			if ($fee_data['site_commission_money'] > 0){
			modify_account(array('site_money'=>$fee_data['site_commission_money']), $user_id,'平台获得的交易佣金,从券商中返回:'.$order_id,38);
			}
	
			//投资者获得的交易佣金;  平台垫付给投资人；平台再从券商中收取
			if ($fee_data['invest_commission_money'] > 0){
			modify_account(array("money"=>$fee_data['invest_commission_money'],'site_money'=>-$fee_data['invest_commission_money']), $fee_data['invest_user_id'],'交易佣金,配资编号:'.$order_id,38);
			}*/
	
	
			//投资推荐人（p_invest_user_id）获得的: 投资人利息收益的n%返利 = rate_money * fanwe_peiziorder.invite_invest_interest_rate
			if ($fee_data['invite_invest_interest_money'] > 0){
				modify_account(array("money"=>$fee_data['invite_invest_interest_money'],'site_money'=>-$fee_data['invite_invest_interest_money']), $fee_data['p_invest_user_id'],'投资人利息收益返利,配资编号:'.$order_id,23);
			}
	
			/*
			 //投资推荐人（p_invest_user_id）获得的: 投资人佣金收益的n%返利 = invest_commission_money * fanwe_peiziorder.invite_invest_commission_rate
			if ($fee_data['invite_invest_commission_money'] > 0){
			modify_account(array("money"=>$fee_data['invite_invest_commission_money'],'site_money'=>-$fee_data['invite_invest_commission_money']), $fee_data['p_invest_user_id'],'投资人交易佣金收益返利,配资编号:'.$order_id,23);
			}*/
	
			//借款推荐人（p_user_id）获得的: 平台利息收益的n%返利 = site_money * fanwe_peiziorder.invite_borrow_interest_rate
			if ($fee_data['invite_borrow_interest_money'] > 0){
				modify_account(array("money"=>$fee_data['invite_borrow_interest_money'],'site_money'=>-$fee_data['invite_borrow_interest_money']), $fee_data['p_user_id'],'平台服务费收益返利,配资编号:'.$order_id,23);
			}
			/*
			 //借款推荐人（p_user_id）获得的: 平台佣金收益的n%返利 = site_commission_money * fanwe_peiziorder.invite_borrow_commission_rate
			if ($fee_data['invite_borrow_commission_money'] > 0){
			modify_account(array("money"=>$fee_data['invite_borrow_commission_money'],'site_money'=>-$fee_data['invite_borrow_commission_money']), $fee_data['p_user_id'],'平台交易佣金收益返利,配资编号:'.$order_id,23);
			}
			*/
		}
	
		//通知用户扣费成功
		if ($allow_send_msg && app_conf("SMS_ON") == 1 && app_conf('SMS_CHARGING_SUCCESS_MSG')==1){
			$user_info =get_user_info("*","id = ".$user_id);
			
			$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_CHARGING_SUCCESS_MSG'");
			$tmpl_content = $tmpl['content'];
	
			$notice['site_name'] = app_conf("SHOP_TITLE");
			$notice['user_name'] = $user_info["user_name"];
			$notice['order_sn'] = $v['order_sn'];
			$notice['fee_date'] = $v['next_fee_date'];
			$notice['next_fee_date'] = $next_fee_date;
			$notice['rate_money'] = format_price($sever_money);
	
			$GLOBALS['tmpl']->assign("notice",$notice);
	
			$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
	
			$msg_data['dest'] = $user_info['mobile'];
			$msg_data['send_type'] = 0;
			$msg_data['title'] = "配资自动扣费成功通知";
			$msg_data['content'] = addslashes($msg);;
			$msg_data['send_time'] = 0;
			$msg_data['is_send'] = 0;
			$msg_data['create_time'] = TIME_UTC;
			$msg_data['user_id'] = $user_info['id'];
			$msg_data['is_html'] = $tmpl['is_html'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
		}
	}
	
}


/**
 * 返回： 开始交易时间 是否显示：今天 
 * @return number 1:显示；0：不显示
 */
function get_peizi_show_today(){
	//开始交易时间，是否显示：今天(节假日，周末及下午1:30后，也不显示）
	$i_time = intval(to_date(TIME_UTC,'Hi'));
	if ($i_time>1430){ //超过下午2点半后，今天不显示
		$is_show_today = 0;
	}else{
		if (get_peizi_is_holiday(to_date(TIME_UTC,'Y-m-d'))){
			$is_show_today = 0;//节假日今天也不显示
		}else{
			$is_show_today = 1;			
		}
	}
	
	return $is_show_today;
}
/**
 * 更新帐户金额
 * @param unknown_type $order_id
 * @param unknown_type $user_id
 * @param unknown_type $stock_date
 * @param unknown_type $stock_money
 */
function set_peizi_order_stock_money($order_id,$user_id,$stock_date,$stock_money){
	
	$sql = "update ".DB_PREFIX."peizi_order set stock_money = '$stock_money', stock_date = '$stock_date' where id = ".$order_id;
	$GLOBALS['db']->query($sql);
	
	$sql = "select id from ".DB_PREFIX."peizi_order_stock_money where peizi_order_id = ".$order_id." and stock_date = '".$stock_date."'";
	$id = intval($GLOBALS['db']->getOne($sql));
	
	$sql = "select warning_line,open_line from ".DB_PREFIX."peizi_order where id = ".$order_id;
	$peizi_order = $GLOBALS['db']->getRow($sql);
	
	$stock = array();
	//$stock['user_id'] = $user_id;
	$stock['peizi_order_id'] = $order_id;
	$stock['stock_date'] = $stock_date;
	$stock['stock_money'] = $stock_money;
	$stock['warning_line'] = $peizi_order['warning_line'];
	$stock['open_line'] = $peizi_order['open_line'];
	if ($id == 0){
		$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_stock_money",$stock); //插入
		
		$id = $GLOBALS['db']->insert_id();
	}else{
		$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_stock_money",$stock,'UPDATE',' id ='.$id); //更新
	}
	
	return $id;
}

/**
 * 格式化  peizi_order 展示数据
 * @param unknown_type $vo
 * @return unknown
 */
function get_peizi_order_fromat($vo,$get_user = false){
	
	
	$vo['total_money'] = $vo['cost_money']+$vo['borrow_money'];
	//配资类型
	$vo['type_format'] = get_peizi_type($vo['type'],true);

	$vo['status_format'] = get_peizi_status($vo['status']);
	$vo['status_format2'] = get_peizi_status2($vo['status']);
	
	
	//$vo['user_name'] = get_user_name($vo['user_id']);

	//$vo['time_limit_num_format'] = $vo['time_limit_num'].$vo['type_format'];
	$vo['time_limit_num_format'] = $vo['time_limit_num'];
	
	$vo['is_today_format'] = get_peizi_is_today($vo['is_today']);
	
	$vo['rate_format'] = getPeiziRateFormat($vo['rate'],$vo['type']);
	//盈亏金额
	$vo['loss_money'] = $vo["stock_money"] - ($vo["cost_money"] + $vo["borrow_money"]);
	
	$vo['loss_money_format'] = format_price($vo['loss_money']);
	
	$vo['loss_rate'] = $vo['loss_money']/($vo["cost_money"] + $vo["borrow_money"]);
	
	$vo['loss_rate_format'] = number_format($vo['loss_rate'] * 100, 2, '.', '') ."%";
	
	$vo['total_money_format'] = format_price($vo['total_money']);//总操盘资金
	
	$vo['stock_money_format'] = format_price($vo['stock_money']);//股票总值
	
	$vo['payoff_rate_format'] =  ($vo['payoff_rate'] * 100).'%';
	$vo['invest_payoff_rate_format'] =  ($vo['invest_payoff_rate'] * 100).'%';
	
	$vo['cost_money_format'] = format_price($vo['cost_money']);//保证金
	$vo['re_cost_money_format'] = format_price($vo['re_cost_money']);//返还保证金
	$vo['user_payoff_fee_format'] = format_price($vo['user_payoff_fee']);//用户盈利
	$vo['site_payoff_fee_format'] = format_price($vo['site_payoff_fee']);//平台盈利
	$vo['invest_payoff_fee_format'] = format_price($vo['invest_payoff_fee']);//投资者盈利
	$vo['other_fee_format'] = format_price($vo['other_fee']);//其它费用
	$vo['manage_money_format'] = format_price($vo['manage_money']);//业务审核费

	$vo['warning_line_format'] = format_price($vo['warning_line']);//亏损警戒线
	$vo['open_line_format'] = format_price($vo['open_line']);//亏损平仓线
	
	$vo['rate_money_format'] = format_price($vo['rate_money']);//投次者收取的： 每日或每月利息费用
	$vo['all_rate_money_format'] = format_price($vo['rate_money'] * $vo['time_limit_num']);//预期总收益
	
	$vo['first_rate_money_format'] = format_price($vo['first_rate_money']);//首次收取的利息费用(或预存款)
	$vo['borrow_money_format'] = format_price($vo['borrow_money']);//借款金额
	$vo['total_rate_money_format'] = format_price($vo['total_rate_money']);//已收利息总额
	
	$vo['site_money_format'] = format_price($vo['site_money']);//平台收取的 日(月)管理费
	
	$vo['invite_invest_money_format'] = format_price($vo['invite_invest_money']);
	$vo['invite_borrow_money_format'] = format_price($vo['invite_borrow_money']);
	
	//平台累计，收到：佣金
	$vo['total_site_commission_money_format'] = format_price($vo['total_site_commission_money']);
	//投资者累计，收到：佣金
	$vo['total_invest_commission_money_format'] = format_price($vo['total_invest_commission_money']);
	
	//投资返利【投资推荐人p_invest_user_id获得的: 投资金额返利 = borrow_money * invite_invest_money_rate】
	$vo['invite_borrow_money_format'] = format_price($vo['invite_borrow_money']);
	
	//借款返利【借款推荐人p_user_id获得的: 借款金额返利 = borrow_money * invite_borrow_money_rate】
	$vo['invite_invest_money_format'] = format_price($vo['invite_invest_money']);
	
	//已收服务费
	$vo['total_site_money_format'] = format_price($vo['total_site_money']);
	
	
	//交易开始时间(0:下一交易日;1:今天)	
	if ($vo['is_today'] == 1){
		$vo['is_today_format'] = '今天';
	}else{
		$vo['is_today_format'] = '下一交易日';
	}
	
	if (isset($vo['user_money']))
	$vo['user_money_format'] = format_price($vo['user_money']);//用户帐户余额
	/*
	//type=2时，有效;0:按月收取;1:一次性收取
	if ($vo['type'] == 2){		
		$vo['rate_type_format'] = '按月收取';	
	}else if ($vo['type'] == 1){
		$vo['rate_type_format'] = '按收益比收取';
	}else if ($vo['type'] == 0){
		if ($vo['rate_type'] == 1){
			$vo['rate_type_format'] = '一次性收取';
		}else{
			$vo['rate_type_format'] = '按日收取';
		}
	}*/
	
	if ($vo['is_holiday_fee'] == 1){
		$vo['is_holiday_fee_format'] = '是';
	}else{
		$vo['is_holiday_fee_format'] = '否';
	}
	
	if ($get_user){
		require_once APP_ROOT_PATH."app/Lib/common.php";
		$vo['user'] = get_user("*",$vo['user_id']);
	}
	
	$vo['url'] = url("peizi","peizi#detail",array("id"=>$vo['id']));;
	
	return $vo;
}

function get_peizi_conf($conf_id,$borrow_money,$lever,$month,$rate_id){
	//$peizi_conf = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."peizi_conf where type = ".$type." limit 1");

	$peizi_conf = load_auto_cache("peizi_conf",array('id'=>$conf_id));

	$sql = "select lm.* from ".DB_PREFIX."peizi_conf_lever_coefficient_list lm where lm.pid = ".intval($peizi_conf['id'])." and lm.lever = ".$lever;
	$lm = $GLOBALS['db']->getRow($sql);
	
	if ($peizi_conf['type']  == 2){
		$sql = "select * from ".DB_PREFIX."peizi_conf_rate_list rl where pid = ".intval($peizi_conf['id'])." and min_lever <= ".$lever." and ".$lever." <= max_lever and min_month <= ".$month." and ".$month." <= max_month and min_money <= ".$borrow_money." and ".$borrow_money." <= max_money";		
	}else{
		$sql = "select * from ".DB_PREFIX."peizi_conf_rate_list rl where pid = ".intval($peizi_conf['id'])." and min_lever <= ".$lever." and ".$lever." <= max_lever and min_money <= ".$borrow_money." and ".$borrow_money." <= max_money";		
	}
	
	$rate_row = $GLOBALS['db']->getRow($sql);	
		//print_r($sql);exit;
	//风险保证金(本金)
	//实盘资金
	//总操盘资金
	//亏损警戒线
	//亏损平仓线
	//账户管理费
	//
	/*
	* total_money:总操盘资金
	* warning_line:亏损警戒线
	* open_line:亏损平仓线
	* rate_id: 利率ID
	* rate: 利率
	* rate_format: 利率格式化
	* rate_money:账户管理费
	* rate_money_format: 账户管理费格式化后
	* site_rate: 服务费利率
 	* site_money: 服务费
 	* site_rate_format: 服务费利率格式化
 	* site_money_format: 服务费格式化后 
	* limit_info: 仓位限制消息
	* payoff_rate: 盈利比如：0.7则，实际盈利的70%归操盘者；30%归平台
	* payoff_rate_format
	*/
	$parma = array();
	$parma['peizi_conf'] = $peizi_conf;

	$parma['borrow_money'] = $borrow_money;
	$parma['cost_money'] = floor($borrow_money / $lever);
	$parma['total_money'] = $borrow_money + $parma['cost_money'];



	$parma['warning_line'] = floor($borrow_money + $borrow_money * $lm['warning_coefficient']);
	$parma['open_line'] = floor($borrow_money + $borrow_money * $lm['open_coefficient']);

	$parma['warning_coefficient'] = $lm['open_coefficient'];
	$parma['open_coefficient'] = $lm['open_coefficient'];
	

	$parma['borrow_money_format'] = getPeiziMoneyFormat($parma['borrow_money']);
	$parma['cost_money_format'] = getPeiziMoneyFormat($parma['cost_money']);
	$parma['total_money_format'] = getPeiziMoneyFormat($parma['total_money']);
	$parma['warning_line_format'] = getPeiziMoneyFormat($parma['warning_line']);
	$parma['open_line_format'] = getPeiziMoneyFormat($parma['open_line']);


	if ($rate_id <= 0 || $rate_id > 4){
		$rate_id = 1;
	}

	$parma['rate_id'] = $rate_id;
	$parma['rate'] = $rate_row['rate'.$rate_id];
	$parma['rate_format'] = getPeiziRateFormat($parma['rate']);

	$parma['rate_money'] = $borrow_money * $parma['rate'];
	$parma['rate_money_format'] = getPeiziMoneyFormat($parma['rate_money']);
	
	$parma['rate'] = $rate_row['rate'.$rate_id];
	$parma['rate_format'] = getPeiziRateFormat($parma['rate']);
	
	
	$parma['site_rate'] = $rate_row['site_rate'];
	$parma['site_rate_format'] = getPeiziRateFormat($parma['site_rate']);

	$parma['site_money'] = $borrow_money * $parma['site_rate'];
	$parma['site_money_format'] = getPeiziMoneyFormat($parma['site_money']);	
	
	
	$parma['invest_payoff_rate'] = $rate_row['invest_payoff_rate'];
	$parma['payoff_rate'] = $rate_row['payoff_rate'];
	
	if ($parma['payoff_rate'] == 0) $parma['payoff_rate'] = 1;
	
	if ($parma['payoff_rate'] == 1){
		$parma['payoff_rate_format'] = '全';
	}else{
		$parma['payoff_rate_format'] =  ($parma['payoff_rate'] * 100).'%';
	}
	
	$parma['limit_info'] = str_replace("{payoff_format}",$parma['payoff_rate_format'],$rate_row['limit_info']);

	$parma['is_show_today'] = $rate_row['is_show_today'];
	
	return $parma;

}


function getPeiziMoneyFormat($money) {
	if ($money == 0)
		return '免费';
	else
		return number_format($money,2);
}

	//利率格式化
function getPeiziRateFormat($rate,$type) {
	$rate_format = rate;
	
	if ($rate == 0){
		$rate_format = '免';
	}else{
		if ($type == 2){
			$rate_format = ($rate * 100).'分/每月';
		}else{
		$rate_format = ($rate * 100).'分/每日';
			}
	}

	return $rate_format;
}
	
 /**
  * 返回配资类型
  * @param unknown_type $type,$is_time
  * @return string 配资类型;0:天;1周；2月
  */
 function get_peizi_type($type,$is_time = false){
	if ($type == 0){
 		return '天';
 	}else if ($type == 1){
 		if ($is_time)
 			return '天';
 		else	
 			return '周';
 	}else if ($type == 2){
 		return '月';
 	}else{
 		return '未知';
 	}
 }
 
 
/**
 * 开始交易时间
 * @param unknown_type $is_today
 * @return string
 */
 function get_peizi_is_today($is_today){
 	if ($is_today == 0){
 		return '今天';
 	}else{
 		return '下个交易日';
 	}
 }
 
 
 /**
  * 订单状态 status:0:正在申请;1:支付成功;2:审核通过;3:审核失败;4:筹款成功;5:筹款失败;6:开户成功;7:开户失败;8:平仓结束;9:已撤消
  * @param unknown_type $status
  * @return string
  */
 function get_peizi_status($status){
 	switch($status){
 		case 0 :
 			return '在申请';
 			break;
 		case 1 :
 			//return '支付成功';
 			return '审核中';
			break;
 		case 2 :
 			//return '审核通过';
 			return '募资中';
			break;
 		case 3 :
 			return '审核失败';
 			break;
 		case 4 :
 			//return '筹款成功';
 			return '待开户';
			break;
 		case 5 :
 			return '筹款失败';
 			break;
 		case 6 :
 			//return '开户成功';
			return '操盘中';
 			break;
 		case 7 :
 			return '开户失败';
 			break;
 		case 8 :
 			return '平仓结束';
 			break;
 		case 9 :
 			return '已撤消';
 			break;
 		default :
 			return '未知';
 			break;
 	}
 	
 } 
 
 function get_peizi_status2($status){
 	switch($status){
 		case 0 :
 			return '在申请';
 			break;
 		case 1 :
 			return '支付成功';
 			break;
 		case 2 :
 			return '筹款中';
 			break;
 		case 3 :
 			return '审核失败';
 			break;
 		case 4 :
 			return '等待开启';
 			break;
 		case 5 :
 			return '筹款失败';
 			break;
 		case 6 :
 			return '操盘中';
 			break;
 		case 7 :
 			return '开户失败';
 			break;
 		case 8 :
 			return '操盘结束';
 			break;
 		case 9 :
 			return '已撤消';
 			break;
 		default :
 			return '未知';
 			break;
 	}
 
 } 

/**
 * 配资费用类型
 * @param unknown_type $type
 * @return string
 */
 function get_peizi_fee_type($type){
 	//费用类型;1:业务审核费;2:利息;3:服务费;4:其它费用
 	if ($type == 1){
 		return '业务审核费';
 	}else if ($type == 2){
 		return '利息';
 	}else if ($type == 3){
 		return '服务费';
 	}else if ($type == 4){
 		return '其它费用';
 	}else{
 		return '未知';
 	}
 }
 
 /**
  * 返回配资操作类型
  * @param unknown_type $type
  * @return string 0:追加保证金;1:申请延期;2:申请增资;3:申请减资;4:提取盈余;5:申请结束配资
  */
 function get_peizi_op_type($type){
 	if ($type == 0){
 		return '追加保证金';
 	}else if ($type == 1){
 		return '申请延期';
 	}else if ($type == 2){
 		return '申请增资';
 	}else if ($type == 3){
 		return '申请减资';
 	}else if ($type == 4){
 		return '提取盈余';
 	}else if ($type == 5){
 		return '申请结束配资';
 	}else{
 		return '未知';
 	}
 }
 
 /**
  * 配资申请操作，审核状态;0:未审核;1:投资人通过;2:投资人未通过;3:平台通过;4:平台未通过;5:撤消申请
  * $op_type 0:追加保证金;1:申请延期;2:申请增资;3:申请减资;4:提取盈余;5:申请结束配资
  * @param unknown_type $status
  * @return string
  */
 function get_peizi_op_status($status,$op_type){
 	if ($status == 0){
 		return '未审核';
 	}else if ($status == 1){
 		if ($op_type == 1 || $op_type == 2){
 			return '投资人通过';//初审通过
 		}else{
 			return '初审通过';//初审通过
 		}
 		
 	}else if ($status == 2){ 		
 	 	if ($op_type == 1 || $op_type == 2){
 			return '投资人未通过';//初审通过
 		}else{
 			return '初审未通过';//初审通过
 		} 		
 	}else if ($status == 3){
 		return '复审通过';
 	}else if ($status == 4){
 		return '复审未通过';
 	}else if ($status == 5){
 		return '撤消申请';
 	}else{
 		return '未知';
 	}
 }
 
 /**
  * 配资申请操作描述
  * @param unknown_type $v fanwe_peizi_order_op
  * @param unknown_type $type_format (fanwe_peizi_order.type 日，周，月)
  * @return string
  */
 function get_peizi_op_val_info($v,$type_format){
 	//描述
 	$op_val_info = $v['op_val'];
 	//0:追加保证金;1:申请延期;2:申请增资;3:申请减资;4:提取盈余;5:申请结束配资
 	if ($v['op_type'] == 0){
 		$op_val_info = '追加保证金:'.format_price($op_val_info);
 	}else if ($v['op_type'] == 1){
 		$op_val_info = '延期:'. $op_val_info.$type_format;
 	}else if ($v['op_type'] == 2){
 		$op_val_info = '倍率旧:'.$v['lever'] .';新倍率:'. $op_val_info.';预计增资:'.format_price(($v['op_val'] - $v['lever']) * $v['cost_money']);
 	}else if ($v['op_type'] == 3){
 		$op_val_info = '倍率旧:'.$v['lever'] .';新倍率:'. $op_val_info.';预计减资:'.format_price(($v['lever'] - $v['op_val']) * $v['cost_money']);
 	}else if ($v['op_type'] == 4){
 		$op_val_info = '提取盈余:'. $op_val_info;
 	}else if ($v['op_type'] == 5){
 		$op_val_info = '预计剩总值:'.$op_val_info;
 	}
 	
 	return $op_val_info;
 }
 
 
 
 /**
  * 下一交易日
  */
 function get_peizi_next_date(){
 	
 	$date = to_date(TIME_UTC);
 	for($i = 1; $i < 30; $i ++){
 		$cur_date = dec_date($date, -$i);
 		
 		if (get_peizi_is_holiday($cur_date) == false){
 			return $cur_date;
 		} 		
 	}	
 	
 	return null;
 }
 
 /**
  * 判断是否交易日
  * @param unknown_type $date
  */
 function get_peizi_is_holiday($date){
 	//判断是否是：周末 	
 	$w = to_date(TIME_UTC,'w');
 	//echo $w;exit;
 	if ($w == 0 || $w == 6){
 		return true;
 	} 
 	
 	//判断是否为节假日
 	$sql = "select id from ".DB_PREFIX."peizi_holiday where holiday = '".$date."'";
 	if (intval($GLOBALS['db']->getOne($sql)) > 0){
 		return true;
 	}else{
 		return false;
 	} 
 }
 
 
 /**
  * 按自然月计算，如使用1个月，1月8日到2月8日，当月日期没有,则按该月的最后一天计算
  * @param unknown_type $begin_date
  * @param unknown_type $num
  */
 function add_month($begin_date,$num){
 	$y = to_date(to_timespan($begin_date),'Y');//当前年份
 	$m = to_date(to_timespan($begin_date),'m');//当前月份
 	$d = to_date(to_timespan($begin_date),'d');//当前几号
 	
 	$new_y = $y +  intval(($m + $num) / 12);
 	
 	$new_m = ($m + $num) % 12;
 	if ($new_m == 0) {
 		$new_m = 12;
 		$new_y = $new_y - 1;
 	}
 	
 	$t = to_date(to_timespan($new_y.'-'.$new_m.'-01','Y-m-d'),'t');//本月共有
 	
 	if ($t <= $d){
 		$new_d = $t;
 	}else{
 		$new_d = $d;
 	}
 	
 	return to_date(to_timespan($new_y.'-'.$new_m.'-'.$new_d,'Y-m-d'),'Y-m-d');
 	
 }
 
 /**
  * 预计配资结束时间
  * @param date $begin_date 开始时间
  * @param int $num 时长(天,月)
  * @param int $type 类型; 配资类型;0:天;1周；2月(按自然月计算，如使用1个月，1月8日到2月8日，当月日期没有,则该按月的最后一天计算，包含各类节假日)
  * @param int $is_holiday_fee 周末节假日免费;type=0时有效;0:不免费;1:免费
  */
 function get_peizi_end_date($begin_date,$num,$type = 0,$is_holiday_fee = 0){
 	
 	if ($type == 2){
 		//如果日期不存在，则取当月最大一天
 		//exit; 		
		return	add_month($begin_date,$num);
 	}else{
 		
 		if ($is_holiday_fee == 1){
 			//周末节假日免费		
 			$sql = "select holiday from ".DB_PREFIX."peizi_holiday where `year` = ".to_date(TIME_UTC,"Y"). " or `year` = ".(to_date(TIME_UTC,"Y") + 1) ." order by holiday";
 			$holiday_list = $GLOBALS['db']->getAll($sql);
 			//echo $sql;exit;
 			$max_num = count($holiday_list) + $num;
 			//echo 'max_num:'.$max_num."<br>";
 			
 			$day_num = $num;
 			for($i = 1; $i <= $max_num; $i ++){
 				$cur_date = dec_date($begin_date, -$i); 				
 				//echo $cur_date."<br>";
 				
 				$is_holiday = false;
 				//判断是否是：周末
 				$w = date("w",strtotime($cur_date));//date('w',$cur_date);
 				//echo 'w:'.$w."<br>";
 				if ($w == 0 || $w == 6) $is_holiday = true;
 				
 				if ($is_holiday == false){
	 				foreach ($holiday_list as $k => $v) {
	 					if ($v['holiday'] == $cur_date){
	 						$is_holiday = true;
	 					}
	 				}		
 				}
 				
 				//echo 'is_holiday:'.$is_holiday.'<br>';
 				if ($is_holiday == false){
 					$day_num = $day_num - 1;
 				}
 				
 				if ($day_num == 0){
 					break;
 				} 				
 			}
 			
 			
 			return $cur_date; 			
 			
 		}else{
 			return dec_date($begin_date, -$num);
 		}
 	}
 }
 
 
 function do_peizi_pc_calc_1($order_id,$stock_money,$other_fee,$other_memo){
 	$sql = "select id,user_id,borrow_money,cost_money,payoff_rate,invest_payoff_rate,invest_payoff_fee from ".DB_PREFIX."peizi_order where id = ".intval($order_id);
 	
 	$data = $GLOBALS['db']->getRow($sql);
 	$user_id = $data['user_id'];
 	$cost_money = $data['cost_money'];
 	
 	$data['status']=8;
 	$data['other_fee'] = floatval($other_fee);
 	$data['stock_money'] = floatval($stock_money);
 	$data['end_date'] = to_date(TIME_UTC,'Y-m-d');
 	$data['other_memo'] = $other_memo;
 	
 	$total_payoff_fee = $data['stock_money'] - ($data['borrow_money'] + $cost_money + $data['other_fee']);
 	//盈亏
 	$data['total_payoff_fee'] = $total_payoff_fee;
 		
 	if($total_payoff_fee >0)
 	{
 		//实际盈利了
 		$data['re_cost_money'] = $cost_money; //返还保证金
 		$data['user_payoff_fee'] = $total_payoff_fee * $data['payoff_rate']; //用户获利
 	
 		$payoff_fee = $total_payoff_fee - $data['user_payoff_fee'];//醒资者分配完，剩下部分
 	
 		$data['invest_payoff_fee'] = $payoff_fee * $data['invest_payoff_rate']; //投资者获得
 		$data['site_payoff_fee'] = $payoff_fee - $data['invest_payoff_fee']; //平台获得
 	}else {
 		//亏本
 		$data['re_cost_money'] = $cost_money + $total_payoff_fee; //返还保证金, 有可能投现负数; 投现负数时,则说明：配资者的本金不够还亏损
 		$data['user_payoff_fee'] = $total_payoff_fee; //用户获利（亏损)
 		$data['site_payoff_fee'] = 0; //平台获得
 		$data['invest_payoff_fee'] = 0; //投资者获得
 	}
 	
 	return $data;
 }
 
 /**
  * 平仓操作2
  * @param unknown_type $order_id
  */
 function do_peizi_pc_calc_2($order_id){ 	
 	
 	$sql = "select * from ".DB_PREFIX."peizi_order where id = ".intval($order_id);
 	$data = $GLOBALS['db']->getRow($sql);
 	$user_id = $data['user_id'];
 	$cost_money = $data['cost_money'];
 	
 	set_peizi_order_stock_money($order_id,$user_id,$data['stock_date'],$data['stock_money']);
 	
 	//冻结：本金 cost_money array('money'=>-$data['money'],'lock_money'=>$data['money'])
 	$msg = '配资平仓解冻配资本金,配资编号:'.$order_id;
 	if ($data['user_payoff_fee'] < 0){
 		$msg = '平仓解冻配资本金:'.format_price($cost_money).';亏损:'.format_price($data['user_payoff_fee']).';剩:'.format_price($data['re_cost_money']).',配资编号:'.$order_id;
 	}
 	//解冻：本金cost_money，返还 $data['re_cost_money']
 	modify_account(array('money'=>$data['re_cost_money'],'lock_money'=>-$cost_money), $user_id,$msg,30);
 	
 	
 	//配资平仓,用户收益
 	if ($data['user_payoff_fee'] > 0)
 		modify_account(array('money'=>$data['user_payoff_fee']), $user_id,'配资平仓,用户收益,配资编号:'.$order_id,35);
 		
 	//配资平仓,平台收益
 	if ($data['site_payoff_fee'] > 0)
 		modify_account(array('site_money'=>$data['site_payoff_fee']), $user_id,'配资平仓,平台收益,配资编号:'.$order_id,35);
 	
 	//退还：投资人投资金额
 	$invest_user_id = intval($data['invest_user_id']);
 	if ($invest_user_id > 0){
 		modify_account(array('money'=>$data['borrow_money']), $invest_user_id,'配资投资款返还,配资编号:'.$order_id,36);
 			
 		if ($data['invest_payoff_fee'] > 0)
 			modify_account(array('money'=>$data['invest_payoff_fee']), $invest_user_id,'配资投资,平台收益,配资编号:'.$order_id,35);
 	}
 	
 	/*
 	if ($data['other_fee'] > 0){
 		$fee_data = array();
 		$fee_data['user_id'] = $user_id;
 		$fee_data['peizi_order_id'] = $order_id;
 		$fee_data['create_date'] = to_date(TIME_UTC);
 		$fee_data['fee_date'] = to_date(TIME_UTC);
 		$fee_data['fee'] = $data['other_fee'];
 		$fee_data['fee_type'] = 4;//费用类型;4:其它费用
 		$fee_data['memo'] = $data['other_memo'];
 		$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_fee_list",$fee_data,"INSERT");
 	}*/
 	
 }
 
 
function dopeizi_bid($id,$user_id,$paypassword){
	$result  = array("status"=>0,"info"=>"");
	if($id==0){
		$result['info'] = "配资不存在";
		return $result;
	}
	if($user_id==0){
		$result['info'] = "请先登录";
		return $result;
	}
	
	if($paypassword==""){
		$result['info'] = "请输入支付符密码";
		return $result;
	}
	
	$user_info = get_user_info("*","id=".$user_id);
	if(!$user_info){
		$result['info'] = "投资人不存在";
		return $result;
	}
	
	if($user_info['paypassword']!=md5($paypassword)){
		$result['info'] = "支付密码错误";
		return $result;
	}
	
	$sql_str = "select * from ".DB_PREFIX."peizi_order where status=2 AND id = ".$id;
	$peizi = $GLOBALS['db']->getRow($sql_str);
	
	if(!$peizi){
		$result['info'] = "配资不存在";
		return $result;
	}
	
	
	if($peizi['user_id']==$user_id){
		$result['info'] = "不能投自己的配资";
		return $result;
	}
	
	if(intval($peizi['invest_user_id']) != 0 ){
		$result['info'] = "投资人已经存在";
		return $result;
	}
	
	//有切换新的投资人或添加首次投资人，需要判断：投资人余额足不足
	if ($peizi['borrow_money'] > $user_info['money']){
		$result['info'] = "投资人余额不足，需要:".format_price($peizi['borrow_money']).';实际:'.format_price($user_info['money']);
		return $result;
	}
	
	$data = array();
	$data['invest_user_id'] = $user_id;
	$data['invest_end_time'] = to_date(TIME_UTC);
	$data['status'] = 4;
	
	$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order",$data,"UPDATE","invest_user_id=0 AND status=2 AND id=".$id);
	if($GLOBALS['db']->affected_rows() > 0){
		//冰结投资人 金额
		require_once APP_ROOT_PATH.'system/libs/user.php';
		modify_account(array('money'=>-$peizi['borrow_money'],'lock_money'=>$peizi['borrow_money']), $user_id,'配资投资冰结,配资编号:'.$id,36);				
		$result['status'] = 1;
		$result['info'] = "投资成功";
		return $result;
	}
	else{
		$result['status'] = 0;
		$result['info'] = "投资失败";
		return $result;
	}
}
	

//将gbk转为utf8格式
function gbk2utf8(&$data) {
	
	if (!defined('MB_CONV_ENCODE'))
		define('MB_CONV_ENCODE',function_exists('mb_convert_encoding')?True:False);
	
	
	foreach ( $data as $k => $v ) {
		if (is_array ( $v )) {
			gbk2utf8 ($data [$k]);
		} else {
			if(MB_CONV_ENCODE) {
				$encode = mb_detect_encoding($v, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
				//echo $encode;
				if ($encode != 'UTF-8'){
					$data [$k] = mb_convert_encoding($v, 'UTF-8', 'GBK');
				}
			}else{
				$data[$k] = iconv('GBK', 'UTF-8', $v);
			}
		}
	}
}

//解析csv文件，返回二维数组，第一维是一共有多少行csv数据，第二维是键名为csv列名，值为当前行当前列的csv数据值
function input_csv($csv_file) {
	$csv_key_name_arr = array();
	$result_arr = array ();
	$i = 0;
	while ($data_line = _fgetcsv($csv_file, 900000,',')) {
		//echo print_r($data_line)."<br>";
		if($i == 0){
			$csv_key_name_arr = $data_line;
			gbk2utf8($csv_key_name_arr);			
			//print_r($csv_key_name_arr);
			$i++;
			continue;
		}
		
		$v = array();
		foreach($csv_key_name_arr as $csv_key_num=>$csv_key_name){
			$v[$csv_key_name] = strim($data_line[$csv_key_num]);
		}
		
		$result_arr[] = $v;	
	}
	return $result_arr;
}

function _fgetcsv(& $handle, $length = null, $d = ',', $e = '"') {
	$d = preg_quote($d);
	$e = preg_quote($e);
	$_line = "";
	$eof=false;
	while ($eof != true) {
		$_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
		$itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
		if ($itemcnt % 2 == 0)
			$eof = true;
	}
	$_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));
	$_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
	preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
	$_csv_data = $_csv_matches[1];
	for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
		$_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1' , $_csv_data[$_csv_i]);
		$_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
	}
	return empty ($_line) ? false : $_csv_data;
}


function outputCSV($data) {
	$outputBuffer = fopen("php://output", 'w');
	foreach($data as $val) {
		foreach ($val as $key => $val2) {
			$val[$key] = iconv('utf-8', 'gbk', $val2);// CSV的Excel支持GBK编码，一定要转换，否则乱码
		}
		fputcsv($outputBuffer, $val);
	}
	fclose($outputBuffer);
}

function get_peizi_title(){
	
	$title = array();
	$title['order_sn'] = '配资编号';
	$title['user_name'] = '借款人';
	$title['invest_user_name'] = '投资人';
	$title['stock_sn'] = '股票帐户';
	$title['total_money_format'] = '总操盘资金';
	$title['cost_money_format'] = '保证金';
	$title['borrow_money_format'] = '借款金额';
	$title['lever'] = '倍率';
	$title['rate_format'] = '利率';
	$title['rate_money_format'] = '月(日)利息';//投次者收取的： 每日或每月利息费用
	$title['site_money_format'] = '管理费';//平台收取的 日(月)管理费
	$title['manage_money_format'] = '业务审核费';//
	$title['conf_type_name'] = '配资类型';
	$title['time_limit_num_format'] = '期限';
	$title['status_format'] = '状态';
	$title['is_today_format'] = '交易时间类型';
	$title['warning_line_format'] = '亏损警戒线';
	$title['open_line_format'] = '亏损平仓线';
	$title['create_time'] = '申请时间';
	$title['begin_date'] = '开始交易日';
	$title['end_date'] = '预计平仓日期';
	$title['last_fee_date'] = '扣费日期';
	$title['next_fee_date'] = '下次扣费日期';
	$title['stock_money_format'] = '总资产';
	$title['stock_date'] = '资产统计日期';
	$title['total_rate_money_format'] = '已收利息';
	$title['total_site_money_format'] = '已收服务费';
	$title['total_site_commission_money_format'] = '平台佣金收入';
	$title['total_invest_commission_money_format'] = '投资者佣金收入';
	$title['is_holiday_fee_format'] = '节假日免费';
	$title['payoff_rate_format'] =  '收益分成比';
	$title['invest_payoff_rate_format'] = '投资者与平台分成比';
	$title['re_cost_money_format'] = '返还保证金';
	$title['user_payoff_fee_format'] = '用户盈利';
	$title['site_payoff_fee_format'] = '平台盈利';
	$title['invest_payoff_fee_format'] = '投资者盈利';
	$title['other_fee_format'] = '其它费用';
	$title['loss_money_format'] = '盈亏金额';
	
	return $title;
}
?>