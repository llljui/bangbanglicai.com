<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class everwin_confirm
{
	public function index(){
		
		$root = array();
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
		$time_limit_num = intval($GLOBALS['request']['time_limit_num']);
		$root['time_limit_num'] = $time_limit_num;
		
		$parma = get_peizi_conf($conf_id,$borrow_money,$lever,0,$rate_id);
		
		//$root['parma'] = $parma;
		//$root['peizi_conf'] = $parma['peizi_conf'];
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
		
		$root['site_rate'] = $parma['site_rate'];
		$root['site_rate_format'] = $parma['site_rate_format'];
		$root['invest_payoff_rate'] = $parma['invest_payoff_rate'];
		$root['payoff_rate'] = $parma['payoff_rate'];
		$root['payoff_rate_format'] = $parma['payoff_rate_format'];
		$root['limit_info'] = $parma['limit_info'];
		$root['is_show_today'] = $parma['is_show_today'];
		
		$root['cost_money'] = $parma['cost_money'];
		$root['cost_money_format'] = $parma['cost_money_format'];
		$root['rate_money'] = $parma['rate_money'];//日利息
		$root['site_money'] = $parma['site_money'];//服务费 = 借款金额×管理费率【按天或月收取】【平台收取的】
		$root['sever_money'] = $parma['rate_money'] + $parma['site_money'];
		$root['rate_money_fromat'] = format_price($parma['rate_money']);//日利息
		$root['site_money_fromat'] = format_price($parma['site_money']);//服务费 = 借款金额×管理费率【按天或月收取】【平台收取的】
		$root['sever_money_fromat'] = format_price($parma['rate_money'] + $parma['site_money']);
		//$root['borrow_money'] = $borrow_money;
		$root['lever'] = $lever;
		$root['rate_id'] = $rate_id;
		$root['is_today'] = $is_today;
		$root['conf_id'] = $conf_id;
		
		$peizi_conf = $parma['peizi_conf'];// load_auto_cache("peizi_conf",array('type'=>0));

		$day_list = $peizi_conf['day_list'];
		
		$root['name'] = $peizi_conf['name'];//名称
		$root['brief'] = $peizi_conf['brief'];//简介
		$root['pre_deposit_info'] = $peizi_conf['pre_deposit_info'];//预存款简介
		$root['is_holiday_fee'] = $peizi_conf['is_holiday_fee'];//按天收取，周末节假日免费
		$root['manage_money'] = $peizi_conf['manage_money'];//一次性业务审核费
		$root['day_list'] = $day_list;//预存管理费天数
		
		//判断用户是否登陆
		if($user_id == 0){
			//$GLOBALS['tmpl']->assign("is_login",false);
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}else{
			//$GLOBALS['tmpl']->assign("is_login",true);
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
		}

		//$GLOBALS['tmpl']->display("peizi/everwin_confirm.html");
			
		$root['program_title'] = "按天操盘";
		output($root);		
	}
}
?>
