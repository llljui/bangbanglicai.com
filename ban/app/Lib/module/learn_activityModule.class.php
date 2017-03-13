<?php

require_once APP_ROOT_PATH.'app/Lib/uc.php';
require_once APP_ROOT_PATH.'system/libs/learn.php';
require_once APP_ROOT_PATH.'app/Lib/page.php';

class learn_activityModule extends SiteBaseModule {

    public function index() {
    	$user_id = intval($GLOBALS['user_info']['id']);
    	$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
    	$result  = get_learn_list($limit);
		
		$page = new Page($result['rs_count'],app_conf("PAGE_SIZE"));  
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);	
		
    	$GLOBALS['tmpl']->assign("learn_list",$result['list']);
    	$GLOBALS['tmpl']->assign("page_title","理财体验金");
    	$GLOBALS['tmpl']->assign("user_id",$user_id);
		$GLOBALS['tmpl']->display("learn/learn_activity_index.html");
		
    }
    
    public function detail() {
    	
    	$id = intval($_REQUEST['id']);
    	$learn_info = get_learn($id);
        if(!$learn_info){
            app_redirect(url("index","learn_activity#index"));
        }
        
        
        $now_time = to_date(TIME_UTC,"Y-m-d");
        $type = $_REQUEST['type'];
    	
    	$user_id = intval($GLOBALS['user_info']['id']);
    	
    	$begin_time = to_timespan($learn_info['begin_time']);
    	
    	$end_time = to_timespan($learn_info['end_time']);
    	
    	$learn_limit=ceil(($end_time-$begin_time)/86400); 
    	
    	$learn_balance = 0;
        if($GLOBALS['user_info']){
    	   $learn_balance = $GLOBALS['db']->getOne("select sum(lsl.money) FROM ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id WHERE lt.invest_type = 0 and lsl.is_use = 0 and lsl.begin_time <= '".$now_time."' and '".$now_time."' <= lsl.end_time and lsl.user_id='".intval($GLOBALS['user_info']['id'])."' and lsl.is_recycle = 0 ");
        }
        
        
		
		$GLOBALS['tmpl']->assign("id",$id);
		
		$GLOBALS['tmpl']->assign("user_id",$user_id);
		
		$GLOBALS['tmpl']->assign("learn_cmoney",$learn_info['learn_money']);
		
		$GLOBALS['tmpl']->assign("begin_time",$begin_time);
		$GLOBALS['tmpl']->assign("end_time",$end_time);
		
		$GLOBALS['tmpl']->assign("learn_limit",$learn_limit);
		
    	$GLOBALS['tmpl']->assign("learn_info",$learn_info);
    	$GLOBALS['tmpl']->assign("learn_balance",$learn_balance);
    	$GLOBALS['tmpl']->assign("page_title","理财体验金投资");
    	
		$GLOBALS['tmpl']->display("learn/learn_activity_detail.html");
    }
    
    public function rule() {
    	$user_id = intval($GLOBALS['user_info']['id']);
    	
    	$GLOBALS['tmpl']->assign("user_id",$user_id);
    	
    	$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 6;  //首页缓存10分钟
		$name = trim($_REQUEST['u']) == "" ? "learnurule" : trim($_REQUEST['u']);
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$name);	
		if (!$GLOBALS['tmpl']->is_cached("learn/learn_activity_rule.html", $cache_id))
		{	
			$info = get_article_buy_uname($name);
			$info['content']=$GLOBALS['tmpl']->fetch("str:".$info['content']);
			$GLOBALS['tmpl']->assign("info",$info);
		}
    	$GLOBALS['tmpl']->assign("page_title","理财体验金活动规则");
		$GLOBALS['tmpl']->display("learn/learn_activity_rule.html",$cache_id);
    }
    
    public function do_invest(){
		$learn_id = intval($_REQUEST['learn_id']);
		$money = floatval($_REQUEST['money']);
		
		$result = learn_invest($learn_id,$money);
		if($result['status'] == 1 ){
			showSuccess("投资成功",1);
		}else{
			showErr($result['info'],1);
		}
		
	}
	
	public function invite_link(){
		$user_id = intval($GLOBALS['user_info']['id']);
		
		$total_referral_money = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."referrals where rel_user_id = ".$GLOBALS['user_info']['id']." and pay_time > 0");
		
		$GLOBALS['tmpl']->assign("total_referral_money",$total_referral_money);
		
		$referral_user = get_user_info("count(*)","pid = ".$GLOBALS['user_info']['id']." and is_effect=1 and is_delete=0 AND user_type in(0,1) ","ONE");
		$GLOBALS['tmpl']->assign("referral_user",$referral_user);
		
		$learn_invite = $GLOBALS['db']->getOne("select sum(lsl.money) from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id where lt.type = 1 and lsl.is_effect = 1 and lsl.user_id ='".$user_id."' ");
		
		$GLOBALS['tmpl']->assign("learn_invite",$learn_invite);
		
		if(intval(app_conf("URL_MODEL")) == 0)
			$depart="&";
		else
			$depart="?";	
		$share_url = SITE_DOMAIN.url("index","user#register");
		if($GLOBALS['user_info']){
			$share_url_mobile = $share_url.$depart."r=".str_replace('+', '%2b', base64_encode($GLOBALS['user_info']['mobile']));
			$share_url_username = $share_url.$depart."r=".str_replace('+', '%2b', base64_encode($GLOBALS['user_info']['user_name']));
			$GLOBALS['tmpl']->assign("share_url_mobile",$share_url_mobile);
			$GLOBALS['tmpl']->assign("share_url_username",$share_url_username);	
		}
		
		
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 6;  //首页缓存10分钟
		$name = trim($_REQUEST['u']) == "" ? "learnsrule" : trim($_REQUEST['u']);
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$name);	
		if (!$GLOBALS['tmpl']->is_cached("learn/learn_activity_invite_link.html", $cache_id))
		{	
			$info = get_article_buy_uname($name);
			$info['content']=$GLOBALS['tmpl']->fetch("str:".$info['content']);
			$GLOBALS['tmpl']->assign("info",$info);
		}
		
		$GLOBALS['tmpl']->assign("page_title","理财体验金邀请");
		
		$GLOBALS['tmpl']->assign("user_id",$user_id);
		$GLOBALS['tmpl']->display("learn/learn_activity_invite_link.html");
		
	}
	
    
}
?>