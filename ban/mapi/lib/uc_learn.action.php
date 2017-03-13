<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_learn
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			$now_time = to_date(TIME_UTC,"Y-m-d");
			
			$root['user_data'] = $user;
			
			$learn_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."learn where is_effect = 1 and begin_time <= '$now_time' and '$now_time' <= end_time order by id desc limit 1 ");
			
			$has_send_money = $GLOBALS['db']->getOne("select sum(money) FROM ".DB_PREFIX."learn_load WHERE learn_id ='".$learn_info['id']."'  ");
			if((int)$has_send_money == (int)$learn_info['load_money']){
				$learn_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."learn where is_effect = 1 and begin_time <= '$now_time' and '$now_time' <= end_time and id < '".$learn_info['id']."' order by id desc limit 1 ");
				$has_send_money = $GLOBALS['db']->getOne("select sum(money) FROM ".DB_PREFIX."learn_load WHERE learn_id ='".$learn_info['id']."'  ");
				if((int)$has_send_money == (int)$learn_info['load_money']){
					$learn_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."learn where is_effect = 1 and begin_time <= '$now_time' and '$now_time' <= end_time and id < '".$learn_info['id']."' order by id desc limit 1 ");
					$has_send_money = $GLOBALS['db']->getOne("select sum(money) FROM ".DB_PREFIX."learn_load WHERE learn_id ='".$learn_info['id']."'  ");
				}
			}
			
			$learn_balance = $GLOBALS['db']->getOne("select sum(e.money) FROM ".DB_PREFIX."learn_send_list as e left join ".DB_PREFIX."learn_type as et on e.type_id = et.id WHERE e.is_use = 0 and et.invest_type = 0 and e.user_id='".$user_id."' and e.is_recycle = 0 and e.begin_time <= '$now_time' and '$now_time' <= e.end_time  and et.is_effect = 1 ");
			if(empty($learn_balance) || $learn_balance == 0){
				$learn_balance = 0.00;
			}
			
			$learn_id = $learn_info['id'];
			$is_interest = $GLOBALS['db']->getOne("select count(*) FROM ".DB_PREFIX."learn_load WHERE learn_id = $learn_id and user_id='".$user_id." '");
			
			$today=to_date(TIME_UTC,"Y-m-d");
			$uc_interest = $learn_balance * $learn_info['rate'] * 0.01 * $learn_info['time_limit']/365; 
			
			$has_lead_interest = $GLOBALS['db']->getOne("select sum(interest) FROM ".DB_PREFIX."learn_load WHERE is_send = 1 and user_id='$user_id' ");
			if(empty($has_lead_interest) || $has_lead_interest == 0){
				$has_lead_interest = 0.00;
			}
			
			$no_lead_interest = $GLOBALS['db']->getOne("select sum(interest) FROM ".DB_PREFIX."learn_load WHERE is_send = 0 and DATE_ADD(create_date,INTERVAL time_limit DAY) <= '$today' and  DATE_ADD(create_date,INTERVAL (time_expire_limit+time_limit) DAY) > '$today'  and user_id='$user_id' ");
			
			if(empty($no_lead_interest) || $no_lead_interest == 0){
				$no_lead_interest = 0.00;
			}
			
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$t = strim($GLOBALS['request']['t']); 
			$root['t'] = $t;
			if($t=="load"){
				$learn_load_list = $GLOBALS['db']->getAll("select ll.*,l.name from ".DB_PREFIX."learn_load ll left join ".DB_PREFIX."learn l on ll.learn_id = l.id where  ll.user_id = '$user_id' order by id desc  limit ".$limit);		
				$learn_load_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."learn_load where user_id = ".$user_id);
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
					$learn_load_list[$k]['key'] = $k+1;
					
				}
				
			}else{
				$learn_send_list = $GLOBALS['db']->getAll("select lsl.*,lt.type from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id where lt.is_effect = 1 and lt.invest_type = 0 and  lsl.user_id = ".$user_id." order by id desc  limit ".$limit);	
				$learn_send_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id   where lt.invest_type = 0 and lsl.user_id = ".$user_id);	
				$learn_count = $learn_send_count;
				foreach($learn_send_list as $k => $v)
				{
					$learn_send_list[$k]['begin_date'] = $v['begin_time'];
					$learn_send_list[$k]['end_date'] = $v['end_time'];
					$learn_send_list[$k]['key'] = $k+1;
				}
			}
			$root['page'] = array("page"=>$page,"page_total"=>ceil($learn_count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
			$root['uc_interest'] = $uc_interest;
			$root['has_send_money'] = $has_send_money;
			$root['has_lead_interest'] = $has_lead_interest;
			$root['no_lead_interest'] = $no_lead_interest;
			$root['learn_balance'] = $learn_balance;
			if($learn_info){
				$root['learn_info'] = $learn_info;
			}else{
				$root['learn_info'] = null ;
			}
			
			$root['is_interest'] = $is_interest;
			$root['learn_send_list'] = $learn_send_list;
			$root['learn_load_list'] = $learn_load_list;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "理财体验金";
		output($root);		
	}
}
?>
