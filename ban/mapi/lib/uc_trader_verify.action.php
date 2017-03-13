<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_verify
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info']; 
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		$status = intval($GLOBALS['request']['status']);//需要借款金额
		$root['status'] = $status;
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$user["total_money"] = number_format(floatval($user["money"]) + floatval($user["lock_money"]), 2);
			$status_url = array(
					array(
							"id" => 0,
							"name" => "不限",
					),
					array(
							"id" => 1,
							"name" => "审核中",
					),
					array(
							"id" => 2,
							"name" => "募资中",
					),
					array(
							"id" => 3,
							"name" => "开户中",
					),
					array(
							"id" => 4,
							"name" => "操盘中",
					),
					array(
							"id" => 5,
							"name" => "历史配资",
					),
					
			);
			
			$root['status_url'] = $status_url;
			
			if($status==0){
				$l_condition = " 1 = 1 ";
				$c_condition = " 1 = 1 ";
			}elseif ($status==1){
				$l_condition = " po.status = 1";
				$c_condition = " status = 1";
			}elseif ($status==2){
				$l_condition = " po.status = 2";
				$c_condition = " status = 2";
			}elseif ($status==3){
				$l_condition = " po.status = 4";
				$c_condition = " status = 4";
			}elseif ($status==4){
				$l_condition = " po.status = 6";
				$c_condition = " status = 6";
			}elseif ($status==5){
				$l_condition = " po.status in(3,5,7,8)";
				$c_condition = " status in(3,5,7,8)";
			}else{
				$l_condition = "1 = 1 ";
				$c_condition = "1 = 1 ";
			}
			
			//$root['user_data'] = $user;
			$page = intval($GLOBALS['request']['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$trader_list = $GLOBALS['db']->getAll("select po.*,pc.name as conf_type_name from ".DB_PREFIX."peizi_order po left join ".DB_PREFIX."peizi_conf pc on po.peizi_conf_id = pc.id where $l_condition and  po.user_id = ".$user_id." order by order_sn desc limit ".$limit);		
			
			$trader_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where $c_condition and  user_id = ".$user_id);		
			
			foreach($trader_list as $k => $v)
			{
				$trader_list[$k] = 	get_peizi_order_fromat($trader_list[$k]);
				if($v["status"] != 4 && $v["status"] != 6)
				{
					$trader_list[$k]["loss_money_format"] = "￥0.00";
				}
				if($status==5){
					$trader_list[$k]["trader_money"] = $v["cost_money"] + $v["borrow_money"];
					$trader_list[$k]["loss_money"] = $v["stock_money"] - ($v["cost_money"] + $v["borrow_money"]);
					$trader_list[$k]["loss_money_format"] = format_price($trader_list[$k]["loss_money"]);
					$trader_list[$k]["loss_ratio"] = $v["stock_money"]/($v["cost_money"] + $v["borrow_money"]);
					$trader_list[$k]["status_format"] = get_peizi_status($v["status"]);
					$trader_list[$k]["re_cost_money_format"] = format_price($trader_list[$k]["re_cost_money"]);
					
				}
			}
	
			$root['page'] = array("page"=>$page,"page_total"=>ceil($trader_count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			$root['trader_list'] = $trader_list;
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		if($status=="1"){
			$program_title = "审核中";
		}elseif($status=="2"){
			$program_title = "募资中";
		}elseif($status=="3"){
			$program_title = "开户中";
		}elseif($status=="4"){
			$program_title = "操盘中";
		}elseif($status=="5"){
			$program_title = "历史配资";
		}else{
			$program_title = "我的配资";
		}
			
		$root['program_title'] = $program_title;
		
		output($root);		
		
	}
}
?>
