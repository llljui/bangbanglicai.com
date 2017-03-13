<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class peizi_index
{
	public function index(){
		
		$root = get_baseroot();

		require_once APP_ROOT_PATH.'system/libs/peizi.php';
		
		$GLOBALS['tmpl']->caching = false;

		$cache_id  = md5(MODULE_NAME.ACTION_NAME);
		
		$root['cache_id'] = $cache_id;
		if (!$GLOBALS['tmpl']->is_cached('peizi/index.html', $cache_id)||true)
		{
			$sql = "select id,name,type from ".DB_PREFIX."peizi_conf where  is_effect = 1 order by sort";
			
			$peizi_list = array();
			$conf_list = $GLOBALS['db']->getAll($sql);
			foreach($conf_list as $k=>$v){
				$peizi_conf = load_auto_cache("peizi_conf",array('id'=>$v['id']));
				$root['type'] = $v['type'];
				$root['peizi_conf_json'] = json_encode($peizi_conf);
				$root['peizi_conf'] = $peizi_conf;
				$root['conf_id'] = $v['id'];
				$root['is_holiday_fee'] = $peizi_conf['is_holiday_fee'];
				$root['is_show_today'] = get_peizi_show_today();
				$root['SHOP_TEL'] = app_conf('SHOP_TEL');
				$root['contract_title'] = $peizi_conf['contract_title'];
				$peizi_list[] = array('id'=>$v['id'],'name'=>$v['name']);
			}
			
			$root['peizi_list'] = $peizi_list;
			
			$show_sql = "select a.*,b.type,b.name as conf_name from ".DB_PREFIX."peizi_indexshow a left join ".DB_PREFIX."peizi_conf b on b.id = a.peizi_conf_id order by money desc";
			
			$show_list = $GLOBALS['db']->getAll($show_sql);
			foreach($show_list as $k => $v)
			{
				if($v["type"] == 0)
				{
					$show_list[$k]["url"] = url("index","peizi#everwin",array('id'=>$v['peizi_conf_id']));
 				}
				elseif($v["type"] == 1)
				{
					$show_list[$k]["url"] = url("index","peizi#weekwin",array('id'=>$v['peizi_conf_id']));
				}
				else
				{
					$show_list[$k]["url"] = url("index","peizi#scheme",array('id'=>$v['peizi_conf_id']));
				}							
			}
			$root['indexshow_list'] = $show_list;
			
			$sql = "select * from ".DB_PREFIX."peizi_conf where is_effect = 1 order by sort desc";			
			$conf_list = $GLOBALS['db']->getAll($sql);
			$root['conf_list'] = $conf_list;
			
			$root['is_show_today'] = get_peizi_show_today();
			$root['SHOP_TEL'] = app_conf('SHOP_TEL');
			
		}
			
		$root['program_title'] = "股票配资";
		output($root);		
	}
}
?>
