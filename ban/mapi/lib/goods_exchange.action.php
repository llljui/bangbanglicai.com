<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class goods_exchange
{
	public function index(){
		
		$root = get_baseroot();
		
		$goods_id = intval($GLOBALS['request']['id']);
		$number =  intval($GLOBALS['request']['number']); 
		$user_id = intval($GLOBALS['user_info']['id']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			require APP_ROOT_PATH.'app/Lib/uc_goods_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			$goods = get_goods_info($goods_id,$user_id);
			
			$sql = "SELECT * from ".DB_PREFIX."user_address where user_id = '$user_id'  and is_default = 1 "  ;
			$user_address = $GLOBALS['db']->getRow($sql);
			
			$is_number = $GLOBALS['db']->getOne("SELECT count(*) from ".DB_PREFIX."user_address where user_id = '$user_id'  and is_default = 1");
			
			if($is_number!=0){
				$is_address = 1;
			}else{
				$is_address = 0;
			}
			
			$root['is_address'] = $is_address;
			
			$root['user_address'] = $user_address;	
			
			$root['goods'] = $goods;
			$root['goods_id'] = $goods_id;
			
			$root['number'] = $number;
			$root['needscore'] = $goods['score'] * $number;
			if($root['needscore']>$user['score']){
				$root['show_err'] ="积分不足";
				output($root);	
			}
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		
		$root['program_title'] = "确认兑换";
		output($root);		
	}
}
?>
