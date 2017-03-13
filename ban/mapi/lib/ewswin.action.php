<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class ewswin
{
	public function index(){
		
		$root = get_baseroot();

		require_once APP_ROOT_PATH.'system/libs/peizi.php';
		
		$GLOBALS['tmpl']->caching = true;
		
		$sql = "select id,name,type from ".DB_PREFIX."peizi_conf where  is_effect = 1 order by sort";
		
			$peizi_list = array();
			$conf_list = $GLOBALS['db']->getAll($sql);
			foreach($conf_list as $k=>$v){
				$peizi_list[] = array('id'=>$v['id'],'type'=>$v['type'],'name'=>$v['name']);
			}
		$ctype = intval($GLOBALS['request']['ctype']);	
		
		$root['peizi_list'] = $peizi_list;
		$conf_id = intval($GLOBALS['request']['id']);
		if ($conf_id == 0){
			$peizi_id = $peizi_list['0']['id'];
			$conf_id = $peizi_id;
		}
		$sql = "select type from ".DB_PREFIX."peizi_conf where id = ".$conf_id." limit 1";
		$type = intval($GLOBALS['db']->getOne($sql));//配资类型;0:天;1周；2月
		$root['type'] = $type;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$conf_id);			
		$peizi_conf = load_auto_cache("peizi_conf",array('id'=>$conf_id));
		$max_money = ($peizi_conf['max_money']/10000)."万";
		$root['max_money'] = $max_money;
		$day_list = $peizi_conf['day_list'];
		$root['day_list'] = $day_list;
			
		foreach ( $peizi_conf['rate_list'] as $k => $v )
			{
				$peizi_conf['rate_list'] [$k] ['site_rate_str'] = strval($peizi_conf['rate_list'] [$k] ['site_rate']);
				$peizi_conf['rate_list'] [$k] ['rate1_str'] = strval($peizi_conf['rate_list'] [$k] ['rate1']);
				$peizi_conf['rate_list'] [$k] ['rate2_str'] = strval($peizi_conf['rate_list'] [$k] ['rate2']);
				$peizi_conf['rate_list'] [$k] ['rate3_str'] = strval($peizi_conf['rate_list'] [$k] ['rate3']);
				$peizi_conf['rate_list'] [$k] ['rate4_str'] = strval($peizi_conf['rate_list'] [$k] ['rate4']);
				$peizi_conf['rate_list'] [$k] ['payoff_rate_str'] = strval($peizi_conf['rate_list'] [$k] ['payoff_rate']);
				$peizi_conf['rate_list'] [$k] ['invest_payoff_rate_str'] = strval($peizi_conf['rate_list'] [$k] ['invest_payoff_rate']);
				$peizi_conf['rate_list'] [$k] ['payoff_rate_100'] = strval($peizi_conf['rate_list'] [$k] ['payoff_rate']*100)."%";
				$peizi_conf['rate_list'] [$k] ['limit_info'] = str_replace("{payoff_format}", $peizi_conf['rate_list'] [$k] ['payoff_rate_100'], $peizi_conf['rate_list'] [$k] ['limit_info']);
			}
		$root['conf_id'] = $conf_id;
		if(empty($ctype)){
			$root['peizi_conf_json'] = json_encode($peizi_conf);
		}
		
		$root['peizi_conf'] = $peizi_conf;
		if($type == 2){
			$month_list = $peizi_conf['month_list'];
			$root['month_list'] = $month_list;	//预存管理费天数
			$program_title = "按月操盘";
		}elseif($type == 1){
			$program_title = "按周操盘";
		}else{
			$root['is_holiday_fee'] = $peizi_conf['is_holiday_fee'];
			$program_title = "按天操盘";
		}
		
		$root['is_show_today'] = get_peizi_show_today();
		$root['SHOP_TEL'] = app_conf('SHOP_TEL');
		$root['contract_title'] = $peizi_conf['contract_title'];
		$durl = "/index.php?ctl=peizi&act=contract&is_sj=1&id=".$peizi_conf['contract_id'];
		$root['contract_url'] = str_replace ( "/mapi", "", SITE_DOMAIN . APP_ROOT . $durl );
		
		$root['program_title'] = $program_title;
		output($root);		
		
	}
}
?>
