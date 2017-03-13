<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_earningsModule extends SiteBaseModule
{
    function index() {
    	$user_statics = sys_user_status($GLOBALS['user_info']['id']);
    	$user_statics['all_load_money'] = $user_statics['load_earnings'] + $user_statics['reward_money'] + $user_statics['load_tq_impose'] + $user_statics['load_yq_impose'] + $user_statics['rebate_money'] + $user_statics['referrals_money'] - $user_statics['carry_fee_money']- $user_statics['incharge_fee_money'];
		$GLOBALS['tmpl']->assign("user_statics",$user_statics);
    	
    	$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_EARNINGS']);
    	
    	
    	$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_earnings.html");
		$GLOBALS['tmpl']->display("page/uc.html");
    }
}
?>