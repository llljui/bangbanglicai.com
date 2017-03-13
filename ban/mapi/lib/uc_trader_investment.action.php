<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_investment
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info']; 
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$page = intval($GLOBALS['request']['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			$result = array();
			$status = intval($GLOBALS['request']['status']);
			if(!$status){
				$status = 1;
			}
			$root['status'] = $status;
			
			// $status=2 //投资单
			if(isset($status) && $status=="2"){
				$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where invest_user_id = ".$user_id." ORDER BY id DESC");
		
				if($result['count'] > 0){
					
					$result['list'] = $GLOBALS['db']->getAll("SELECT o.*,u.user_name FROM ".DB_PREFIX."peizi_order o left join ".DB_PREFIX."user u on o.user_id= u.id WHERE o.invest_user_id=".$user_id." ORDER BY o.id DESC");
				}
				foreach($result['list'] as $k => $v)
				{
					$result['list'][$k]["trader_money"] = $v["cost_money"] + $v["borrow_money"];
					$result['list'][$k]["loss_money"] = $v["stock_money"] - ($v["cost_money"] + $v["borrow_money"]);
					$result['list'][$k]["loss_money_format"] = format_price($v["loss_money"]);
					$result['list'][$k]["loss_ratio"] = $v["stock_money"]/($v["cost_money"] + $v["borrow_money"]);
					$result['list'][$k]["status_format"] = get_peizi_status($v["status"]);
					$result['list'][$k]["borrow_money_format"] = format_price($v["borrow_money"]);
					$result['list'][$k]["rate_money_format"] = format_price($v["rate_money"]);
					$result['list'][$k]["total_rate_money_format"] = format_price($v["total_rate_money"]);
					
					$result['list'][$k]["next_fee_date"] = $v["next_fee_date"];
					if($v["status"] == 4){
						$result['list'][$k]["status_format1"] = "收钱中";
					}elseif($v["status"] == 6){
						$result['list'][$k]["status_format1"] = "开户中";
					}elseif($v["status"] == 8){
						$result['list'][$k]["status_format1"] = "已还完";
					}
					if($v["type"] == 2){
						$result['list'][$k]["type_format"] = "月";
					}else{
						$result['list'][$k]["type_format"] = "日";
					}
				}
			// $status=3 //配资单变更审核	
			}elseif(isset($status) && $status=="3"){
				$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order_op op left join ".DB_PREFIX."peizi_order o on op.peizi_order_id = o.id  where op.op_type in (1,2) and op.op_status = 0 and o.invest_user_id = ".$user_id);

				if($result['count'] > 0){
					
					$result['list'] = $GLOBALS['db']->getAll("select op.*,o.order_sn from ".DB_PREFIX."peizi_order_op op left join ".DB_PREFIX."peizi_order o on op.peizi_order_id = o.id  where op.op_type in (1,2) and op.op_status = 0 and o.invest_user_id = ".$user_id);
				}
				
				foreach($result['list'] as $k => $v)
				{
					$result['list'][$k]["op_type_format"] = get_peizi_op_type($v["op_type"]);
					$result['list'][$k]["op_status_format"] = get_peizi_op_status($v["op_status"]);
					//$result['list'][$k]["borrow_money_format"] = getPeiziMoneyFormat($v["borrow_money"]);
				}
			//  //待投资列表
			}else{
				$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where status = 2 and invest_user_id = 0 ");

				if($result['count'] > 0){
					
					$result['list'] = $GLOBALS['db']->getAll("SELECT o.*,u.user_name FROM ".DB_PREFIX."peizi_order o left join ".DB_PREFIX."user u on o.user_id= u.id WHERE status = 2 and invest_user_id = 0 and o.user_id <>".$user_id." ORDER BY o.invest_begin_time DESC");
				}
				foreach($result['list'] as $k => $v)
				{
					$result['list'][$k] = get_peizi_order_fromat($v);
					//$result['list'][$k]["borrow_money_format"] = getPeiziMoneyFormat($v["borrow_money"]);
					
				}
			}
				
			
			$root['trader_list'] = $result['list'];
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
			
		$root['program_title'] = "配资投资";
		
		output($root);		
		
	}
}
?>
