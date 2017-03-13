<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_edit
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		if(!$user)
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		
		
		$id = intval($GLOBALS["request"]['id']);
		$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$id." and is_delete = 0 and user_id = ".$user_id);
		if($deal_item)
		{
			$root["program_title"]=$deal_item['name'];
			$region_pid = 0;
			$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by id asc");  //二级地址
			foreach($region_lv2 as $k=>$v)
			{
				if($v['name'] == $deal_item['province'])
				{
					$region_lv2[$k]['selected'] = 1;
					$region_pid = $region_lv2[$k]['id'];
					break;
				}
			}
			$root["region_lv2"]=$region_lv2;

			if($region_pid>0)
			{
				$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$region_pid." order by id asc");  //三级地址
				foreach($region_lv3 as $k=>$v)
				{
					if($v['name'] == $deal_item['city'])
					{
						$region_lv3[$k]['selected'] = 1;
						break;
					}
				}
				$root["region_lv3"]=$region_lv3;
			}
			
			$deal_item['faq_list'] = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_faq where deal_id = ".$deal_item['id']." order by sort asc");
			
			$cate_list_str = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_cate order by sort asc");
			
			require APP_ROOT_PATH.'system/utils/tree.php';
			
			$tree=new tree();
			
			$cate_list=$tree->toFormatTree($cate_list_str);
			
			$root["cate_list"]=$cate_list;
			
			$deal_item["image"] = get_abs_url_root($deal_item["image"]);
			$root["deal_item"]=$deal_item;
		}
		
		output($root);
	
	}
}
?>
