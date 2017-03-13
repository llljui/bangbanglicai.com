<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class learn_activity
{
	public function index(){
		
		$root = get_baseroot();
		$page = intval($GLOBALS['request']['page']);
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$status = intval($GLOBALS['request']['status']);
		$root['user_id'] = $user_id;
		$root['rrruid'] = intval($GLOBALS['request']['rrruid']);
		
		require APP_ROOT_PATH.'app/Lib/uc_func.php';
        require_once APP_ROOT_PATH.'system/libs/learn.php';
		
		$root['user_login_status'] = 1;
		$root['response_code'] = 1;
		
		if($status == 1){
			
			$root['user_id'] = $user_id;
			$total_referral_money = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."referrals where rel_user_id = ".$user_id." and pay_time > 0");
			
			$root['total_referral_money'] = $total_referral_money;
			
			$referral_user = get_user_info("count(*)","pid = ".$user_id." and is_effect=1 and is_delete=0 AND user_type in(0,1) ","ONE");
			$root['referral_user'] = $referral_user;
			
			$learn_invite = $GLOBALS['db']->getOne("select sum(lsl.money) from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id where lt.type = 1 and lt.is_effect = 1 and lsl.user_id ='".$user_id."' ");
			
			$root['learn_invite'] = $learn_invite;
			
			$share_url = SITE_DOMAIN.wap_url("index","register");
           
			if($user_id > 0){
				$share_url = $share_url."&"."r=".str_replace('+', '%2b',base64_encode($user_id));
				$root['share_url'] = $share_url;
				$root['share_url_img'] = get_abs_wap_url_root(get_abs_img_root(gen_qrcode($share_url,8)));
			}
			elseif($root['rrruid'] > 0){
				$share_url = $share_url."&"."r=".str_replace('+', '%2b',base64_encode($root['rrruid']));
				$root['share_url'] = $share_url;
				$root['share_url_img'] = get_abs_wap_url_root(get_abs_img_root(gen_qrcode($share_url,8)));
			}
			
			$name = "learnsrule";
			$info = get_article_buy_uname($name);
			
			$info['content'] = str_replace("./public/",SITE_DOMAIN.APP_ROOT."/../public/",$info['content']);
			
			$root['activity_info'] = $info;
			
			
		}elseif($status == 2){
	    	
			$name = "learnurule";	
			
			$info = get_article_buy_uname($name);
			$info['content']=$GLOBALS['tmpl']->fetch("str:".$info['content']);
			$root['activity_info'] = $info;
			
		}else{
			$status == 0;
			if($page==0)
			     $page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
            $now_time = to_date(TIME_UTC,"Y-m-d");
            
            $result = get_learn_list($limit);
	    	
	    	$learn_balance = 0;
            if($user){
               $learn_balance = $GLOBALS['db']->getOne("select sum(e.money) FROM ".DB_PREFIX."learn_send_list as e left join ".DB_PREFIX."learn_type as et on e.type_id = et.id WHERE e.is_use = 0 and et.invest_type = 0 and e.user_id='".$user_id."' and e.is_recycle = 0 and e.begin_time <= '$now_time' and '$now_time' <= e.end_time  and et.is_effect = 1 ");
            }
			
			$root['learn_balance'] = $learn_balance;
	    	$root['learn_list'] = $result['list'];
			$root['user_id'] = $user_id;
	
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['rs_count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
		}
		$root['status'] = $status;
		
		$root['program_title'] = "新手体验区";
		output($root);		
	}
}
?>
