<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_edit_item
{
	public function index(){
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		if(!$GLOBALS['user_info'])
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}		
		
		
		$id = intval($GLOBALS["request"]['id']);
		
		$item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_item where id = ".$id);
		$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where is_edit = 1 and is_delete = 0 and id = ".$item['deal_id']." and user_id = ".$user_id);
		
		if($deal_item&&$item)
		{		
			$deal_item_images = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_item_image where deal_id = ".$deal_item['id']." and deal_item_id = ".$item['id']);
			
			foreach($deal_item_images as $k => $v)
			{
				$deal_item_images[$k]["image"] = get_abs_url_root($v["image"]);
			}
			
			$root["deal_item_images"]=$deal_item_images;
			
			$root["deal_item"]=$deal_item;
			$root["item"]=$item;
			$root["program_title"]="回报设置 - ".$deal_item['name'];
		}
		
		output($root);
	
	}
}
?>
