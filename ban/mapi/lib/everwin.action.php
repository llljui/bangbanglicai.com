<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class everwin
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
			$sql = "select id from ".DB_PREFIX."peizi_conf where type = 0 limit 1";
			$conf_id = intval($GLOBALS['db']->getOne($sql));
		}
		$root['type'] = 0;
		
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$conf_id);			
		$peizi_conf = load_auto_cache("peizi_conf",array('id'=>$conf_id));
		$day_list = $peizi_conf['day_list'];
		$root['day_list'] = $day_list;
		$max_money = ($peizi_conf['max_money']/10000)."万";
		$root['max_money'] = $max_money;
		
		$root['conf_id'] = $conf_id;
		$root['peizi_conf_json'] = json_encode($peizi_conf);
		$root['peizi_conf'] = $peizi_conf;
		$root['is_holiday_fee'] = $peizi_conf['is_holiday_fee'];
		$root['is_show_today'] = get_peizi_show_today();
		$root['SHOP_TEL'] = app_conf('SHOP_TEL');
		$root['contract_title'] = $peizi_conf['contract_title'];
		$durl = "/index.php?ctl=peizi&act=contract&is_sj=1&id=".$peizi_conf['contract_id'];
		$root['contract_url'] = str_replace ( "/mapi", "", SITE_DOMAIN . APP_ROOT . $durl );
			
		$root['program_title'] = "按天操盘";
		output($root);		
	}
}
?>
