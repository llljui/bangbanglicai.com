<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_carry_money
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			$root['user_login_status'] = 1;
			
			$bid = intval($GLOBALS['request']['bid']);
			
			$bank_list = $GLOBALS['db']->getAll("SELECT b.icon,u.id,u.bank_id,u.bankcard,u.real_name, b.name as bank_name FROM ".DB_PREFIX."user_bank u left join ".DB_PREFIX."bank b on b.id = u.bank_id where u.user_id=".$user_id."  AND u.id=".$bid."  ORDER BY u.id ASC");
			foreach($bank_list as $k=>$v){
				$bank_list[$k]['bankcode'] = str_replace(" ","",$v['bankcard']);
				$bank_list[$k]['img'] = str_replace("/mapi","",SITE_DOMAIN.APP_ROOT.$v['icon']);
				$bank_list[$k]['uimg'] = str_replace("/mapi","",SITE_DOMAIN.APP_ROOT.$v['icon']);
			}
			
			$root['bank_carry'] = $bank_list;

			$root['money'] = $user['money'];
			//$root['money'] = $user['money_encrypt'];
			$root['nmc_amount'] = $user['nmc_amount'];
			
			$carry_total_money = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry WHERE user_id=".$user_id." AND status=1");
			$root['carry_total_money'] = $carry_total_money;
			
			$vip_id = 0;
			if($user['vip_id'] > 0 && $user['vip_state'] == 1){
				$vip_id = $user['vip_id'];
			}
			
			$root['vip_id'] = $vip_id;
			//手续费
			$fee_config = load_auto_cache("user_carry_config",array("vip_id"=>$vip_id));
			$json_fee = array();
			foreach($fee_config as $k=>$v){
				$json_fee[] = $v;
				if($v['fee_type']==1)
					$fee_config[$k]['fee_format'] = $v['fee']."%";
				else
					$fee_config[$k]['fee_format'] = format_price($v['fee']);
			}
			$root['fee_config'] = $fee_config;
			$root['json_fee'] = json_encode($json_fee);
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "提现申请";
		output($root);		
	}
}
?>
