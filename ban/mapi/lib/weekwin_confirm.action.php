<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class weekwin_confirm
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
	
		$parma = get_peizi_conf($conf_id,$borrow_money,$lever,0,$rate_id);
		$root['parma'] = $parma;
	
		$root['cost_money'] = $parma['cost_money'];
		$root['cost_money_format'] = $parma['cost_money_format'];
		$root['rate_money'] = $parma['rate_money'];	//日利息
		$root['site_money'] = $parma['site_money']; //服务费 = 借款金额×管理费率【按天或月收取】【平台收取的】
		$root['borrow_money'] = $borrow_money;
		$root['lever'] = $lever;
		$root['rate_id'] = $rate_id;
		$root['is_today'] = $is_today;
		$root['conf_id'] = $conf_id;
		
		$peizi_conf = $parma['peizi_conf'];// load_auto_cache("peizi_conf",array('type'=>0));
		
		$root['name'] = $peizi_conf['name'];	//名称
		$root['brief'] = $peizi_conf['brief'];	//简介	
		$root['manage_money'] = $peizi_conf['manage_money'];	//一次性业务审核费
		$root['pre_deposit_info'] = $peizi_conf['pre_deposit_info'];	//预存款简介
		
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

		//$GLOBALS['tmpl']->display("peizi/weekwin_confirm.html");
			
		$root['program_title'] = "按周确定操盘";
		output($root);		
	}
}
?>
