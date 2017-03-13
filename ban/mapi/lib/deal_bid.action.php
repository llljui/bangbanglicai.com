<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/deal.php';
class deal_bid
{
	public function index(){
		
		$root = array();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$id = intval($GLOBALS['request']['id']);
			$deal = get_deal($id);
			if($deal){			
				if($deal['user_id'] == $user_id){				
					$root['response_code'] = 0;
					$root['show_err'] = $GLOBALS['lang']['CANT_BID_BY_YOURSELF'];  //当response_code != 0 时，返回：错误内容
				}else{				
					$root['deal'] = $deal;
					$root['user_money'] = $user['money'];//用户金额
					$root['user_money_format'] = format_price($user['money']);//用户金额
					$root['response_code'] = 1;
				}				
			}else{
				$root['response_code'] = 0;
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
