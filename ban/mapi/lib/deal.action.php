<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class deal
{
	public function index(){
		
		$root = get_baseroot();
		
		$id = intval($GLOBALS['request']['id']);
	
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$root['is_faved'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_collect WHERE deal_id = ".$id." AND user_id=".$user_id);	
		}else{
			$root['is_faved'] = 0;//0：未关注;>0:已关注
		}
		$root['response_code'] = 1;
		$deal = get_deal($id);	
		//format_deal_item($deal,$email,$pwd);
		//print_r($deal);
		//exit;		
		$root['deal'] = $deal;
		$root['open_ips'] = intval(app_conf("OPEN_IPS"));
		$root['ips_acct_no'] = $user['ips_acct_no'];
		$root['ips_bill_no'] = $deal['ips_bill_no'];
		
//		function bid_calculate(){
//			require_once APP_ROOT_PATH."app/Lib/deal_func.php";
//			echo bid_calculate($_POST);
//		}
		
		$root['ecv_list'] = array();
		if($deal['use_ecv'] == 1){
			//红包抵用
			$user_id = intval($GLOBALS['user_info']['id']);
			$sql = "select e.*,et.name from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as et on e.ecv_type_id = et.id where e.user_id = ".$user_id." AND if(e.use_limit > 0 ,(e.use_limit - e.use_count) > 0,1=1) AND if(e.begin_time >0 , e.begin_time < ".TIME_UTC.",1=1) AND if(e.end_time>0,(e.end_time + 24*3600 - 1) > ".TIME_UTC.",1=1) AND et.use_type !=1  order by e.id desc ";
			$root['ecv_list'] = $GLOBALS['db']->getAll($sql);
			
		}
		
		if($deal['use_interestrate'] == 1){
			//加息券抵用
			$user_id = intval($GLOBALS['user_info']['id']);
			$sql = "select e.*,et.name from ".DB_PREFIX."interestrate as e left join ".DB_PREFIX."interestrate_type as et on e.ecv_type_id = et.id where ((e.user_id = ".$user_id." and e.to_user_id = 0) or e.to_user_id = ".$user_id.") AND if(e.use_limit > 0 ,(e.use_limit - e.use_count) > 0,1=1) AND if(e.begin_time >0 , e.begin_time < ".TIME_UTC.",1=1) AND if(e.end_time>0,(e.end_time + 24*3600 - 1) > ".TIME_UTC.",1=1) and (et.use_type=1 or et.use_type=2) order by e.id desc ";
			$list = $GLOBALS['db']->getAll($sql);
			foreach($list as $k => $v)
			{
				$list[$k]["rate_format"] = number_format($v["rate"],2)."%";
			}
			
			$root['interestrate_list'] = $list;
			
		}
		
		if (!empty($root['ips_bill_no'])){
			//第三方托管标
			if (!empty($user['ips_acct_no'])){
				$result = GetIpsUserMoney($user_id,0);
					
				$root['user_money'] = $result['pBalance'];
			}else{
				$root['user_money'] = 0;
			}
		}else{
			$root['user_money'] = $user['money'];
		}
		
		if($deal['uloadtype'] == 1){
			$root['has_bid_money'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."deal_load WHERE deal_id=".$id);
			$root["has_bid_portion"] = intval($has_bid_money)/($deal['borrow_amount']/$deal['portion']);
		}
			
		$root['user_money_format'] = format_price($user['user_money']);//用户金额
		
		//data.deal.name
		$root['program_title'] = "投标详情";
		output($root);		
	}
}
?>

