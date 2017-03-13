<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_do_collect
{
	public function index(){
		
		$root = array();
		
		
		$id = intval($GLOBALS['request']['id']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){

			$root['user_login_status'] = 1;
		
			$goods_info = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."deal where id = ".$id." and is_effect = 1 and is_delete = 0");
			if($goods_info)
			{
				$sql = "INSERT INTO `".DB_PREFIX."deal_collect` (`id`,`deal_id`, `user_id`, `create_time`) select '0','".$id."','".$user_id."','".TIME_UTC."' from dual where not exists (select * from `".DB_PREFIX."deal_collect` where `deal_id`= '".$id."' and `user_id` = ".$user_id.")";
				$GLOBALS['db']->query($sql);
				if($GLOBALS['db']->affected_rows()>0)
				{
					//添加到动态
					insert_topic("deal_collect",$id,$user_id,$GLOBALS['user_info']['user_name']);
					$root['show_err'] = $GLOBALS['lang']['COLLECT_SUCCESS'];
				}
				else
				{
					$root['show_err'] = $GLOBALS['lang']['GOODS_COLLECT_EXIST'];
				}
				$root['response_code'] = 1;
			}
			else
			{
				$root['response_code'] = 0;
				$root['show_err'] = $GLOBALS['lang']['INVALID_GOODS'];
			}
				
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		output($root);		
	}
}
?>
