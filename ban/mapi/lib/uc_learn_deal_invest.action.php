<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_learn_deal_invest
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			$now_time = to_date(TIME_UTC,"Y-m-d H:i:s");
			$today=to_date(TIME_UTC,"Y-m-d");
			$page = intval($GLOBALS['request']['page']);
			
			require APP_ROOT_PATH.'app/Lib/deal.php';

			if((int)app_conf("SHOW_EXPRIE_DEAL") == 0){
				$extW = " AND (if(deal_status = 1, start_time + enddate*24*3600 > ".TIME_UTC .",1=1)) ";
			}
			
			//可用体验金投资标
			$result =  get_deal_list(5,0,"publish_wait =0 AND deal_status = 1 AND use_learn = 1 AND start_time <=".TIME_UTC." and is_hidden = 0 ".$extW," deal_status ASC, is_recommend DESC,sort DESC,id DESC");
			$root['deal_list'] = $result['list'];
				
			$learn_balance = $GLOBALS['db']->getOne("select sum(e.money) FROM ".DB_PREFIX."learn_send_list as e left join ".DB_PREFIX."learn_type as et on e.type_id = et.id WHERE e.is_use = 0 and et.invest_type = 1 and e.user_id='".$user_id."' and e.is_recycle = 0 and e.begin_time <= '$today' and '$today' <= e.end_time  and et.is_effect = 1 ");
			if(empty($learn_balance) || $learn_balance == 0){
				$learn_balance = 0.00;
			}
					
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$learn_send_list = $GLOBALS['db']->getAll("select lsl.*,lt.type from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id where lt.invest_type = 1 and lt.is_effect = 1 and  lsl.user_id = ".$user_id." order by id desc  limit ".$limit);	
			$learn_send_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id where lt.invest_type = 1 and lt.is_effect = 1 and lsl.user_id = ".$user_id);	
			foreach($learn_send_list as $k => $v)
			{
				$learn_send_list[$k]['key'] = $k+1;
			}
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($learn_send_count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			$root['learn_balance'] = $learn_balance;
			$root['learn_send_list'] = $learn_send_list;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "体验金投资";
		output($root);		
	}
}
?>
