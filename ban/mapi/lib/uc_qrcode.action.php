<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_qrcode
{
	public function index(){
		
		$root = get_baseroot();
		$page = intval($GLOBALS['request']['page']);
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$root['user_id'] = $user_id;
			$total_referral_money = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."referrals where rel_user_id = ".$GLOBALS['user_info']['id']." and pay_time > 0");
			
			$root['total_referral_money'] = $total_referral_money;
			
			$referral_user = get_user_info("count(*)","pid = ".$user_id." and is_effect=1 and is_delete=0 AND user_type in(0,1) ","ONE");
			$root['referral_user'] = $referral_user;
		
			$share_url = SITE_DOMAIN.wap_url("index","register");
		
			$share_url = $share_url."&"."r=".str_replace('+', '%2b',base64_encode($user['id']));
			$root['share_url'] = $share_url;
			$root['share_url_img'] = get_abs_wap_url_root(get_abs_img_root(gen_qrcode($share_url,8)));
			
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "邀请二维码";
		output($root);		
	}
}
?>
