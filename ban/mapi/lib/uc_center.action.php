<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_center
{
	public function index(){
		
		$root = get_baseroot();
	
		$user =  $GLOBALS['user_info']; //user_check($email,$pwd);
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require_once APP_ROOT_PATH."system/libs/user.php";
			$root['user_login_status'] = 1;									
			//签到数据
			$t_begin_time = to_timespan(to_date(TIME_UTC,"Y-m-d"));  //今天开始
			$t_end_time = to_timespan(to_date(TIME_UTC,"Y-m-d"))+ (24*3600 - 1);  //今天结束
			$y_begin_time = $t_begin_time - (24*3600); //昨天开始
			$y_end_time = $t_end_time - (24*3600);  //昨天结束
			$user['group_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_group where id = ".$user['group_id']." ");
			$root['vip_id'] = $user['vip_id'];
			$t_sign_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_sign_log where user_id = ".$user_id." and sign_date between ".$t_begin_time." and ".$t_end_time);
			if($t_sign_data)
			{			
				$root['t_sign_data'] = $t_sign_data;
			}
			if($user['vip_id'] == 0){
				$user['vip_grade'] = "您还不是VIP会员";
			}else{
				$user['vip_grade'] = $GLOBALS['db']->getOne("select vip_grade from ".DB_PREFIX."vip_type where id = ".$user['vip_id']." ");
			}
			
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
						
			
			$province_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$user['province_id']);
			$city_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$user['city_id']);
			if($province_str.$city_str=='')
				$user_location = $GLOBALS['lang']['LOCATION_NULL'];
			else
				$user_location = $province_str." ".$city_str;
			
			$user['user_location'] = $user_location;
			$user['money_format'] = format_price($user['money']);//可用资金			
			$user['lock_money_format'] = format_price($user['lock_money']);//冻结资金			
			$user['total_money'] = $user['money'] + $user['lock_money'];//资金余额
			$user['total_money_format'] = format_price($user['total_money']);//资金余额					
			$user['create_time_format'] = to_date($user['create_time'],'Y-m-d'); //注册时间
			
			$root['id'] = $user_id;
			$root['response_code'] = 1;
			$root['vip_grade'] = $user['vip_grade'];
			$root['user_location'] = $user['user_location'];
			$root['user_name'] = $user['user_name'];
			$root['group_name'] = $user['group_name'];
			$root['money_format'] = $user['money_format'];
			$root['money'] = $user['money'];
			$root['lock_money_format'] = $user['lock_money_format'];
			$root['lock_money'] = $user['lock_money'];
			$root['total_money'] = $user['total_money'];
			$root['total_money_format'] = $user['total_money_format'];
			$root['create_time_format'] = $user['create_time_format'];
			$root['score'] = $user['score'];
			$root['idno'] = $user['idno'];
			$root['real_name'] = $user['real_name'];
			$root['point'] = $user['point'];
			$root['quota'] = $user['quota'];
			//我的红包
			$root['voucher_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM ".DB_PREFIX."ecv WHERE user_id=".$user_id." AND if(end_time > 0, (end_time+24*3600-1) > ".TIME_UTC.",1=1) AND if(use_limit > 0,(use_limit - use_count) > 0,1=1)");
		
			//我的加息券
			$root['interestrate_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM ".DB_PREFIX."interestrate i left join  ".DB_PREFIX."interestrate_type it on i.ecv_type_id = it.id WHERE ((i.user_id=".$user_id." and i.to_user_id = 0) or i.to_user_id = ".$user_id.") AND if(i.end_time > 0, (i.end_time+24*3600-1) > ".TIME_UTC.",1=1) AND if(i.use_limit > 0,(i.use_limit - i.use_count) > 0,1=1) and it.use_type = 1");
		
			
			if(intval(app_conf("OPEN_IPS")) > 0){
				$app_url = APP_ROOT."/index.php?ctl=collocation&act=CreateNewAcct&user_type=0&user_id=".$user_id."&from=".$GLOBALS['request']['from'];
				//申请
				$root['app_url'] = str_replace("/mapi", "", SITE_DOMAIN.$app_url);
				$root['acct_url'] = $root['app_url'];				
			}		
			
			$root['ips_acct_no'] = $user['ips_acct_no'];
			$root['open_ips'] = intval(app_conf("OPEN_IPS"));
			
			//第三方托管标
			if (!empty($user['ips_acct_no']) && intval(app_conf("OPEN_IPS")) > 0){
				$result = GetIpsUserMoney($user_id,0);
					
				$root['ips_balance'] = $result['pBalance'];//可用余额
				$root['ips_lock'] = $result['pLock'];//冻结余额
				$root['ips_needstl'] = $result['pNeedstl'];//未结算余额
			}else{
				$root['ips_balance'] = 0;//可用余额
				$root['ips_lock'] = 0;//冻结余额
				$root['ips_needstl'] = 0;//未结算余额
			}
			
			$root['ips_balance_format'] = format_price($root['ips_balance']);
			$root['ips_lock_format'] = format_price($root['ips_lock']);
			$root['ips_needstl_format'] = format_price($root['ips_needstl']);
			$root['program_title'] = "会员中心";
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			$root['program_title'] = "登陆";
		}
		
		output($root);		
	}
}
?>
