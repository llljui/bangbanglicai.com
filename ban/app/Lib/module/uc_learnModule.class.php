<?php

require_once APP_ROOT_PATH.'app/Lib/deal_func.php';
require_once APP_ROOT_PATH.'app/Lib/uc.php';
require_once APP_ROOT_PATH.'system/libs/learn.php';
require_once APP_ROOT_PATH.'app/Lib/page.php';

class uc_learnModule extends SiteBaseModule {

    //首页
	public function index()
	{
		$now_time = to_date(TIME_UTC,"Y-m-d");
		
		$user_info = $GLOBALS['user_info'];
		
		$GLOBALS['tmpl']->assign('user_data',$user_info);
		
		$learn_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."learn where is_effect = 1 and begin_time <= '$now_time' and '$now_time' <= end_time order by id desc limit 1 ");
		
		$has_send_money = 0;
		if($learn_info){
			$has_send_money = $GLOBALS['db']->getOne("select sum(money) FROM ".DB_PREFIX."learn_load WHERE learn_id ='".$learn_info['id']."'  ");
		}
		if((int)$has_send_money == (int)$learn_info['load_money']){
				$learn_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."learn where is_effect = 1 and begin_time <= '$now_time' and '$now_time' <= end_time and id < '".intval($learn_info['id'])."' order by id desc limit 1 ");
				$has_send_money = $GLOBALS['db']->getOne("select sum(money) FROM ".DB_PREFIX."learn_load WHERE learn_id ='".$learn_info['id']."'  ");
				if((int)$has_send_money == (int)$learn_info['load_money']){
					$learn_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."learn where is_effect = 1 and begin_time <= '$now_time' and '$now_time' <= end_time and id < '".$learn_info['id']."' order by id desc limit 1 ");
					$has_send_money = $GLOBALS['db']->getOne("select sum(money) FROM ".DB_PREFIX."learn_load WHERE learn_id ='".$learn_info['id']."'  ");
				}
		}
			
		$learn_balance = $GLOBALS['db']->getOne("select sum(e.money) FROM ".DB_PREFIX."learn_send_list as e left join ".DB_PREFIX."learn_type as et on e.type_id = et.id WHERE e.is_use = 0 and et.invest_type = 0 and e.user_id='".intval($GLOBALS['user_info']['id'])."' and e.is_recycle = 0 and e.begin_time <= '$now_time' and '$now_time' <= e.end_time  and et.is_effect = 1 ");
		
		if(empty($learn_balance) || $learn_balance == 0){
			$learn_balance = 0.00;
		}
		
		if($learn_info){
			$learn_id = $learn_info['id'];
			$is_interest = $GLOBALS['db']->getOne("select count(*) FROM ".DB_PREFIX."learn_load WHERE learn_id = $learn_id and user_id='".intval($GLOBALS['user_info']['id'])."' ");
			
			$today=to_date(TIME_UTC,"Y-m-d");
			$uc_interest = $learn_balance * $learn_info['rate'] * 0.01 * $learn_info['time_limit']/365; 
		}
		
		$has_lead_interest = $GLOBALS['db']->getOne("select sum(interest) FROM ".DB_PREFIX."learn_load WHERE is_send = 1 and user_id='".intval($GLOBALS['user_info']['id'])."' ");
		if(empty($has_lead_interest) || $has_lead_interest == 0){
			$has_lead_interest = 0.00;
		}
		
		$no_lead_interest = $GLOBALS['db']->getOne("select sum(interest) FROM ".DB_PREFIX."learn_load WHERE is_send = 0 and DATE_ADD(create_date,INTERVAL time_limit DAY) <= '$today' and  DATE_ADD(create_date,INTERVAL (time_expire_limit+time_limit) DAY) > '$today'  and user_id='".intval($GLOBALS['user_info']['id'])."' ");
		
		if(empty($no_lead_interest) || $no_lead_interest == 0){
			$no_lead_interest = 0.00;
		}
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$t = strim($_REQUEST['t']); 
		$GLOBALS['tmpl']->assign("t",$t);
		if($t=="load"){
			$learn_load_list = $GLOBALS['db']->getAll("select ll.*,l.name from ".DB_PREFIX."learn_load ll left join ".DB_PREFIX."learn l on ll.learn_id = l.id where  ll.user_id = ".intval($GLOBALS['user_info']['id'])." order by id desc  limit ".$limit);		
			$learn_load_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."learn_load where user_id = ".intval($GLOBALS['user_info']['id']));
			$learn_count = $learn_load_count;
			foreach($learn_load_list as $k => $v)
			{
				$learn_end_time[$k] = strtotime($learn_load_list[$k]['create_date']) + $learn_load_list[$k]['time_limit'] * 24 * 3600 ;
				$learn_load_list[$k]['end_date'] = to_date($learn_end_time[$k],'Y-m-d');
				if($today<$learn_load_list[$k]['end_date']){
					$learn_load_list[$k]['state'] = "理财中";
				}else{
					$learn_load_list[$k]['state'] = "已结束";
				}
				
			}
			
		}else{
			$learn_send_list = $GLOBALS['db']->getAll("select lsl.*,lt.type from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id where lt.is_effect = 1 and lt.invest_type = 0 and  lsl.user_id = ".intval($GLOBALS['user_info']['id'])." order by id desc  limit ".$limit);	
			$learn_send_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id   where lt.invest_type = 0 and lsl.user_id = ".intval($GLOBALS['user_info']['id']));	
			$learn_count = $learn_send_count;
			
		}
		$page = new Page($learn_count,app_conf("PAGE_SIZE"));  
		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("uc_interest",$uc_interest);
		
		$GLOBALS['tmpl']->assign("has_send_money",$has_send_money);
		
		$GLOBALS['tmpl']->assign("has_lead_interest",$has_lead_interest);
		
		$GLOBALS['tmpl']->assign("no_lead_interest",$no_lead_interest);
		
		$GLOBALS['tmpl']->assign("learn_balance",$learn_balance);
		
		$GLOBALS['tmpl']->assign("learn_info",$learn_info);
		
		$GLOBALS['tmpl']->assign("is_interest",$is_interest);
		
		
		$GLOBALS['tmpl']->assign("learn_send_list",$learn_send_list);
		$GLOBALS['tmpl']->assign("learn_load_list",$learn_load_list);
		
		$GLOBALS['tmpl']->assign("page_title","理财体验金");
		
		$GLOBALS['tmpl']->assign("inc_file","learn/learn_index.html");
		$GLOBALS['tmpl']->display("page/uc.html");
		
	}
	
	/**
	 * 领取收益
	 */
	public function do_interest(){
		$dltid = intval($_REQUEST['dltid']);
		do_receive_benefits();
		
	}
	
	/**
	 * 体验金投资
	 */
	public function do_invest(){
		$learn_id = intval($_REQUEST['learn_id']);
		$money = intval($_REQUEST['money']);
		
		$result = learn_invest($learn_id,$money);
		if($result['status'] == 1 ){
			showSuccess("投资成功",1);
		}else{
			showErr($result['info'],1);
		}
		return $result;
		
	}
	
	public function deal_invest(){
		$now_time = to_date(TIME_UTC,"Y-m-d");
		
		$user_info = $GLOBALS['user_info'];
		
		$GLOBALS['tmpl']->assign('user_data',$user_info);
		
		if((int)app_conf("SHOW_EXPRIE_DEAL") == 0){
			$extW = " AND (if(deal_status = 1, start_time + enddate*24*3600 > ".TIME_UTC .",1=1)) ";
		}
			
		//可用体验金投资标
		$deal_list =  get_deal_list(5,0,"publish_wait =0 AND deal_status = 1 AND use_learn = 1 AND start_time <=".TIME_UTC." and is_hidden = 0 ".$extW," deal_status ASC, is_recommend DESC,sort DESC,id DESC");
		$GLOBALS['tmpl']->assign("deal_list",$deal_list['list']);
			
		$learn_balance = $GLOBALS['db']->getOne("select sum(lsl.money) FROM ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id WHERE lt.invest_type =1 and lsl.is_use = 0 and lsl.user_id='".intval($GLOBALS['user_info']['id'])."' and lsl.is_recycle = 0 and lsl.begin_time <= '$now_time' and '$now_time' <= lsl.end_time  and lt.is_effect = 1 ");
		
		if(empty($learn_balance) || $learn_balance == 0){
			$learn_balance = 0.00;
		}
		
		$GLOBALS['tmpl']->assign("learn_balance",$learn_balance);
		
		$today=to_date(TIME_UTC,"Y-m-d");
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$learn_send_list = $GLOBALS['db']->getAll("select lsl.*,lt.type from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id where lt.is_effect = 1 and lt.invest_type = 1 and  lsl.user_id = ".intval($GLOBALS['user_info']['id'])." order by id desc  limit ".$limit);	
		$learn_send_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id  where lt.invest_type = 1 and lsl.user_id = ".intval($GLOBALS['user_info']['id']));	
		$learn_count = $learn_send_count;
		
		$page = new Page($learn_count,app_conf("PAGE_SIZE"));  
		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
				
		$GLOBALS['tmpl']->assign("learn_send_list",$learn_send_list);
		
		$GLOBALS['tmpl']->assign("page_title","体验金投资");
		
		$GLOBALS['tmpl']->assign("inc_file","learn/learn_deal.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	function bid(){
		$id = intval($_REQUEST['id']);
		$return = array("status"=>0,"info"=>"");
		
		if(!$GLOBALS['user_info']){
			$return["status"] = 2;
			$return["info"] = $GLOBALS['lang']['PLEASE_LOGIN_FIRST'];
			$return["jump"] = url("index","user#login"); 
			ajax_return($return);
		}
			
		$deal = get_deal($id);
		if(!$deal){
			$return["status"] = 0;
			$return["info"] = "贷款不存在";
			ajax_return($return);
		}
		
		if($deal['user_id'] == $GLOBALS['user_info']['id']){
			$return["status"] = 0;
			$return["info"] = $GLOBALS['lang']['CANT_BID_BY_YOURSELF'];
			ajax_return($return);
		}
		
		if($deal['ips_bill_no']!="" && $GLOBALS['user_info']['ips_acct_no']==""){
			$return["status"] = 0;
			$return["info"] = "此标为第三方托管标，请先绑定第三方托管账户";
			$return["jump"] = url("index","uc_center");
			ajax_return($return);
		}
		
		//判断是否是新手专享
		if($deal['is_new']==1 &&  $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_load  WHERE user_id=".$GLOBALS['user_info']['id']." ") > 0){
			$return["status"] = 0;
			$return["info"] = "此标为新手专享标，您已经投过贷款了";
			ajax_return($return);
		}
		
		$has_bid_money = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."deal_load WHERE deal_id=".$id);
		
		$GLOBALS['tmpl']->assign("id",$id);
		$GLOBALS['tmpl']->assign("deal",$deal);
		
		if($deal['use_learn'] == 1){
			//体验金抵用
			$now_time = to_date(TIME_UTC,"Y-m-d");
			$user_id = intval($GLOBALS['user_info']['id']);
			$lsql = "select lsl.* FROM ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id WHERE lt.invest_type = 1 and lsl.is_use = 0 and lsl.user_id='".$user_id."' and lsl.is_recycle = 0 and lsl.begin_time <= '$now_time' and '$now_time' <= lsl.end_time  and lt.is_effect = 1 ";
			$lecv_list = $GLOBALS['db']->getAll($lsql);
			$GLOBALS['tmpl']->assign("lecv_list",$lecv_list);
		}
		
		$return["status"] = 1;
	
		$return["info"] = $GLOBALS['tmpl']->fetch("learn/learn_deal_bid.html");
		ajax_return($return);
	}
	
	function dobid(){
		$ajax = intval($_REQUEST["ajax"]);
		$id = intval($_REQUEST["id"]);	
		
		$deal = get_deal($id);
		$learn_id = intval($_REQUEST["learn_id"]);
		if($deal['use_learn']==1){
			$learn_money = $GLOBALS['db']->getOne("SELECT `money` FROM ".DB_PREFIX."learn_send_list WHERE id=".$learn_id);
		}
		$bid_money = floatval($_REQUEST["bid_money"])+$learn_money;
		
		if($learn_money > (round($deal['borrow_amount'],2) - round($deal['load_money'],2))){
			showErr("体验金金额大于可投金额，投标失败！",$ajax,url("index","uc_learn#deal_invest"));
		}
		if($bid_money > (round($deal['borrow_amount'],2) - round($deal['load_money'],2))){
			showErr("投资金额大于可投金额，投标失败！",$ajax,url("index","uc_learn#deal_invest"));
		}
					
//		if($bid_money > (round($deal['borrow_amount'],2) - round($deal['load_money'],2))){
//			$bid_money = (round($deal['borrow_amount'],2) - round($deal['load_money'],2));
//		}
		$bid_paypassword = strim($_REQUEST['bid_paypassword']);
		$ecv_id = intval($_REQUEST["ecv_id"]);
		
	   	$status = dobid2($id,$bid_money,$bid_paypassword,1,$ecv_id,$learn_id);
		
		if($status['status'] == 0){
			showErr($status['show_err'],$ajax);
		}elseif($status['status'] == 2){
			ajax_return($status);
		}elseif($status['status'] == 3){
			showSuccess("余额不足，请先去充值",$ajax,url("index","uc_money#incharge"));
		}else{
			showSuccess($GLOBALS['lang']['DEAL_BID_SUCCESS'],$ajax,url("index","uc_invest"));
		}		
	}
	
}
?>