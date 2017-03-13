<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class peizi_detail
{
	public function index(){
		
		$root = get_baseroot();
		$id = intval($GLOBALS['request']['id']);
		$sql_str = "select pc.name as conf_type_name,a.*,u.user_name,iu.user_name as invest_user_name
					,iu.user_name as invest_user_name from ".DB_PREFIX."peizi_order a
					LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
					LEFT JOIN ".DB_PREFIX."user iu on iu.id = a.invest_user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = a.peizi_conf_id
					where  a.status in (2,4,6,8) and a.id = ".$id;
		
		require_once APP_ROOT_PATH.'system/libs/peizi.php';
		$peizi = $GLOBALS['db']->getRow($sql_str);
		
		$peizi = get_peizi_order_fromat($peizi);
		
		if ($peizi['type'] == 2){
			//借款时间
			$peizi['borrow_time'] = $peizi['time_limit_num'].'个月';
			//借款年利率
			$peizi['year_rate_format'] = ($peizi['rate'] * 12 * 100).'%';
		}else{
			$peizi['borrow_time'] = '01-'.$peizi['time_limit_num'].'起';
			$peizi['year_rate_format'] = ($peizi['rate'] * 365 * 100).'%';
		}
		
		$peizi['before_time'] = getBeforeTimelag(to_timespan($peizi['invest_begin_time']));
		
		$sql_str = "select * from ".DB_PREFIX."peizi_order_stock_money where peizi_order_id = ".$id;
		$stock_money_list = $GLOBALS['db']->getAll($sql_str);		
//		$GLOBALS['tmpl']->assign("stock_money_list",$stock_money_list);
//		$GLOBALS['tmpl']->assign('peizi',$peizi);
//		$GLOBALS['tmpl']->display("peizi/detail.html");
		$root['stock_money_list'] = $stock_money_list;
		$root['peizi'] = $peizi;
		
//		$root['program_title'] = "配资列表";
		output($root);		
	}
}
?>
