<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_vip_buy
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$root['user_login_status'] = 1;		
			$root['response_code'] = 1;
			
			$vip_id = intval($user['vip_id']);
			$root['vip_id'] = $vip_id;
			$now_vip_grade = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vip_setting v LEFT JOIN ".DB_PREFIX."vip_type vt ON v.vip_id=vt.id  where v.id = '$vip_id'  ");
			$root['now_vip_grade'] = $now_vip_grade;
			$max_vip = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vip_type order by id desc limit 1 ");
			$root['max_vip'] = $max_vip;
			
			$root['money'] = $user['money'];
			
			if($max_vip['id'] == $vip_id){
				$vip_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."vip_setting v LEFT JOIN ".DB_PREFIX."vip_type vt ON v.vip_id=vt.id  where v.id = '$vip_id'  ");
				$vip_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vip_setting v LEFT JOIN ".DB_PREFIX."vip_type vt ON v.vip_id=vt.id  where v.id = '$vip_id' order by v.id limit 1  ");
			}else{
				$vip_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vip_setting v LEFT JOIN ".DB_PREFIX."vip_type vt ON v.vip_id=vt.id  where v.id > '$vip_id' order by v.id limit 1  ");
				$vip_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."vip_setting v LEFT JOIN ".DB_PREFIX."vip_type vt ON v.vip_id=vt.id  where v.id > '$vip_id'  ");
			}
			$vip_data = $vip_list;
			
			$root['vip_info'] = $vip_info;
			$root['vip_list'] = $vip_list;
			$root['vip_data'] = json_encode($vip_data);
			
		}else{
			$root['status']=0;
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "VIP购买";
		output($root);		
	}
}
?>
