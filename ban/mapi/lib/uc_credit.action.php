<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class uc_credit
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			require_once APP_ROOT_PATH.'app/Lib/common.php';
			$type = "credit_identificationscanning";
			$is_ajax=1;
	    	$credit_type= load_auto_cache("credit_type");
	    	$credit_info =  $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_credit_file where type='credit_identificationscanning' and user_id =".$user_id);
			if($credit_info){
				if($credit_info['status'] == 0){
					$root['credit_status'] = 2;
					$root['credit_show'] = "待审核";
				}elseif($credit_info['status'] == 1){
					if($credit_info['passed'] == 1){
						$root['credit_status'] = 1;
						$root['credit_show'] = "已认证";
					}elseif($credit_info['passed'] == 2){
						$root['credit_status'] = 3;
						$root['credit_show'] = "审核失败";
					}
				}
			}else{
				$root['credit_status'] = 0;
				$root['credit_show'] = "未认证";
			}
			$file_listss = unserialize($credit_info['file']);
			if($file_listss['0']){
				$file_list['one'] = str_replace ( "/mapi.", "", SITE_DOMAIN . APP_ROOT . $file_listss['0'] );
			}
			if($file_listss['1']){
				$file_list['two'] = str_replace ( "/mapi.", "", SITE_DOMAIN . APP_ROOT . $file_listss['1'] );
			}
			
			$root['image'] = $file_list;
			
			$credit_info['file'] = unserialize($credit_info['file']);
			
			$root['credit_info'] = $credit_info;
			$root['user_info'] = $user;
			
	    	$credit_type['list'][$type]['description_format'] = $GLOBALS['tmpl']->fetch("str:".$credit_type['list'][$type]['description']);
	    	$root['credit'] = $credit_type['list'][$type];
	    	
	    	if($type=="credit_contact" || $type=="credit_residence"){
	    		//地区列表
					$work =  $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_work where user_id =".$user_id);
					$root['work'] = $work;
					
					$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2");  //二级地址
					foreach($region_lv2 as $k=>$v)
					{
						if($v['id'] == intval($work['province_id']))
						{
							$region_lv2[$k]['selected'] = 1;
							break;
						}
					}
					$root['region_lv2'] = $region_lv2;
					
					$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($work['province_id']));  //三级地址
					foreach($region_lv3 as $k=>$v)
					{
						if($v['id'] == intval($work['city_id']))
						{
							$region_lv3[$k]['selected'] = 1;
							break;
						}
					}
					$root['region_lv3'] = $region_lv3;
	    	}
	    	
	    	$root['is_ajax'] = $is_ajax;
	    	$user_type = $GLOBALS['db']->getOne("SELECT user_type FROM ".DB_PREFIX."user WHERE id=".$GLOBALS['user_info']['id']);
	    	$root['user_type'] = $user_type;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "身份认证";
		output($root);		
	}
}
?>
