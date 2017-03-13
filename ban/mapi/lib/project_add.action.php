<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_add
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		require APP_ROOT_PATH.'system/utils/tree.php';
                
		if(!$user)
		{
			app_redirect(wap_url("index","user#login"));
		}
			
		$root["program_title"]="发起项目";
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by id asc");  //二级地址
		$root["region_lv2"]=$region_lv2;
	
		$cate_list_str = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_cate order by sort asc");

		$tree=new tree();
		
		$cate_list=$tree->toFormatTree($cate_list_str);
		
		$root["cate_list"]=$cate_list;
		
		$deal_image =  es_session::get("deal_image");
		
		$root["deal_image"]=$deal_image;
		
		output($root);
	
	}
}
?>
