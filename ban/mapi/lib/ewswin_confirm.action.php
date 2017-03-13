<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class ewswin_confirm
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		require_once APP_ROOT_PATH.'system/libs/peizi.php';
		//申请资金
		//风险保证金 倍率
		//开始交易时间
		$borrow_money = intval($GLOBALS['request']['borrow_money']);
		$is_today = intval($GLOBALS['request']['is_today']);
		$lever = intval($GLOBALS['request']['lever']);
		$rate_id = intval($GLOBALS['request']['rate_id']);
		$conf_id = intval($GLOBALS['request']['conf_id']);
		$ctype = intval($GLOBALS['request']['ctype']);
		
		$time_limit_num = intval($GLOBALS['request']['time_limit_num']);
		$root['time_limit_num'] = $time_limit_num;
		
		$sql = "select type from ".DB_PREFIX."peizi_conf where id = ".$conf_id." limit 1";
		$type = intval($GLOBALS['db']->getOne($sql));//配资类型;0:天;1周；2月
		if($type == 2){
			//$time_limit_num = intval($GLOBALS['request']['time_limit_num']);//资金使用期限
			$parma = get_peizi_conf($conf_id,$borrow_money,$lever,$time_limit_num,$rate_id);
		}else{
			$parma = get_peizi_conf($conf_id,$borrow_money,$lever,0,$rate_id);
		}
		//$root['parma'] = $parma;
		//$root['peizi_conf'] = $parma['peizi_conf'];
		$root['type'] = $type;
		$root['borrow_money'] = $parma['borrow_money'];
		$root['total_money'] = $parma['total_money'];
		$root['warning_line'] = $parma['warning_line'];
		$root['open_line'] = $parma['open_line'];
		$root['warning_coefficient'] = $parma['warning_coefficient'];
		$root['open_coefficient'] = $parma['open_coefficient'];
		$root['borrow_money_format'] = $parma['borrow_money_format'];
		$root['total_money_format'] = $parma['total_money_format'];
		$root['warning_line_format'] = $parma['warning_line_format'];
		$root['open_line_format'] = $parma['open_line_format'];
		$root['rate'] = $parma['rate'];
		$root['rate_format'] = $parma['rate_format'];
		$root['rate'] = $parma['rate'];
		
		$peizi_conf = load_auto_cache("peizi_conf",array('id'=>$conf_id));
		$root['merge_rate_show'] = $peizi_conf['merge_rate_show'];
		
		$root['site_rate'] = $parma['site_rate'];
		$root['site_rate_format'] = $parma['site_rate_format'];
		$root['invest_payoff_rate'] = $parma['invest_payoff_rate'];
		$root['payoff_rate'] = $parma['payoff_rate'];
		$root['payoff_rate_format'] = $parma['payoff_rate_format'];
		$root['limit_info'] = $parma['limit_info'];
		$root['is_show_today'] = $parma['is_show_today'];
		
		$root['cost_money'] = $parma['cost_money'];
		$root['cost_money_format'] = $parma['cost_money_format'];
		$rate_money = $parma['rate_money'];
		$rate_money_fromat = getPeiziMoneyFormat($rate_money);
		
		$root['rate_money'] = $rate_money;	//利息
		$root['rate_money_fromat'] = $rate_money_fromat;
		$root['site_money'] = $parma['site_money'];	//服务费 = 借款金额×管理费率【按天或月收取】【平台收取的】
		$root['site_money_fromat'] = getPeiziMoneyFormat($parma['site_money']);	
		$sever_money = $parma['rate_money'] + $parma['site_money'];
		$root['sever_money'] = $sever_money;
		$root['sever_money_fromat'] = getPeiziMoneyFormat($sever_money);
		
		$root['lever'] = $lever;
		$root['rate_id'] = $rate_id;
		$root['is_today'] = $is_today;
		$root['conf_id'] = $conf_id;	
		$peizi_conf = $parma['peizi_conf'];// load_auto_cache("peizi_conf",array('type'=>0));
		$root['name'] = $peizi_conf['name'];//名称
		$root['brief'] = $peizi_conf['brief'];//简介
		$root['manage_money'] = $peizi_conf['manage_money'];//一次性业务审核费	
		$root['pre_deposit_info'] = $peizi_conf['pre_deposit_info'];//预存款简介
		if($type == 2){
			$total_rate_money = $rate_money;
			$total_site_money = $parma['site_money'];
		}else{
			$total_rate_money = $rate_money*$time_limit_num;
			$total_site_money = $parma['site_money']*$time_limit_num;
		}
		
		$root['total_rate_money'] = getPeiziMoneyFormat($total_rate_money);
		
		$root['total_site_money'] = getPeiziMoneyFormat($total_site_money);
		
		$total_sever_money = $total_rate_money + $total_site_money;
		$root['total_sever_money'] = getPeiziMoneyFormat($total_sever_money);
		
		$total_win_money = $root['cost_money']+$total_rate_money+$total_site_money+$peizi_conf['manage_money'];
		
		$root['total_win_money'] = getPeiziMoneyFormat($total_win_money);
		
		if($type == 0){
			$day_list = $peizi_conf['day_list'];
			$root['is_holiday_fee'] = $peizi_conf['is_holiday_fee'];//按天收取，周末节假日免费
			$root['day_list'] = $day_list;//预存管理费天数
			$root['time_limit_num_day'] = $time_limit_num.'日';
		}elseif($type == 1){
			$root['time_limit_num_day'] = $time_limit_num.'日';
		}elseif($type == 2){
			$root['time_limit_num_day'] = $time_limit_num.'个月';
		}
		$root['program_title'] = "确定操盘";
		
		//判断用户是否登陆
		if($user_id == 0){
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}else{
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
		}
		
		output($root);		
	}
}
?>
