<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class uc_project_pay_order
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		$user = get_user_info("*","id=".$user_id,"ROW");
		
		if(!$user)
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		
		/*new start*/
		$id = intval($GLOBALS["request"]['order_id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_order where id = ".$id." and user_id = ".$user_id." and order_status = 0");
		$paypassword=strim($GLOBALS["request"]['paypassword']);
		
		if($paypassword==''){
			$data['pay_status'] = 0;
			$data['pay_info'] = '请输入支付密码.';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}
		if(md5($paypassword)!=$user['paypassword']){
			$data['pay_status'] = 0;
			$data['pay_info'] = '支付密码错误.';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}
		
		if(!$order_info)
		{
			$data['pay_status'] = 0;
			$data['pay_info'] = '项目支持已支付.';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}

			$pay_score =intval($GLOBALS["request"]['pay_score']);
			
			$ecv_id = intval($GLOBALS["request"]["ecv_id"]);
			
			if($order_info["ecv_id"] > 0)
			{
				$ecv_id = $order_info["ecv_id"];
			}
			elseif($ecv_id>0)
			{
				$ecv_id = intval($GLOBALS["request"]["ecv_id"]);
			}
			
			if($pay_score>0)
			{
				$score_array=score_to_money($pay_score);
				$pay_score_money=$score_array['score_money'];
			}
			else
			{
				$pay_score_money = 0;
			}
			
			if($order_info['type']==1)
			{
				$credit = $order_info["price"];
			}
			else
			{
				$credit = floatval($order_info['deal_price']+$order_info['delivery_fee']-$pay_score_money);
			}
			
			if(intval($GLOBALS["request"]["ecv_id"]) > 0){
				//红包抵用
				$sql = "select * from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as et on e.ecv_type_id = et.id where e.user_id = ".intval($GLOBALS['user_info']['id'])." AND e.id=".$ecv_id;
				$ecv = $GLOBALS['db']->getRow($sql);
				
				if(!$ecv){
					$data['pay_status'] = 0;
					$data['pay_info'] = '红包不存在';
					$data['show_pay_btn'] = 0;
					$root=$data;
					output($root);
				}
				if($ecv['use_limit'] > 0 && $ecv['use_limit'] - $ecv['use_count'] <=0 ){
					$data['pay_status'] = 0;
					$data['pay_info'] = '此红包已被使用过了';
					$data['show_pay_btn'] = 0;
					$root=$data;
					output($root);
				}
				if($ecv['begin_time'] > 0 && $ecv['begin_time'] > TIME_UTC){
					$data['pay_status'] = 0;
					$data['pay_info'] = '此红包还不能用';
					$data['show_pay_btn'] = 0;
					$root=$data;
					output($root);
				}
				if($ecv['end_time'] > 0 && ($ecv['end_time'] +24*3600 - 1) < TIME_UTC){
					$data['pay_status'] = 0;
					$data['pay_info'] = '此红包已过期';
					$data['show_pay_btn'] = 0;
					$root=$data;
					output($root);
				}
			}
			
			//更改红包
			$ecv_money = 0;
			if($ecv_id > 0){
				if(intval($GLOBALS["request"]["ecv_id"]))
				{
					$GLOBALS['db']->query("UPDATE ".DB_PREFIX."ecv SET use_count = use_count + 1 WHERE (begin_time =0 OR (begin_time > 0 AND begin_time < ".TIME_UTC.")) AND (end_time = 0 OR (end_time > 0  AND (end_time +24*3600 - 1) > ".TIME_UTC.")) AND (use_limit =0  OR (use_limit >0 AND use_limit - use_count > 0)) AND id=".$ecv_id." AND user_id=".$user_id);
					if($GLOBALS['db']->affected_rows()){
						$ecv_money = $GLOBALS['db']->getOne("SELECT `money` FROM ".DB_PREFIX."ecv WHERE id=".$ecv_id);
						$order_info['ecv_id'] = $ecv_id;
					}
				}
				else
				{
					$ecv_money = $GLOBALS['db']->getOne("SELECT `money` FROM ".DB_PREFIX."ecv WHERE id=".$ecv_id);
					$order_info['ecv_id'] = $ecv_id;
				}
				
			}
			
			if($credit >= $ecv_money) 
			{
				$credit = $credit - $ecv_money;
			}
			else
			{
				$credit = 0;
			}
			
			$score_trade_number=intval(app_conf("SCORE_TRADE_NUMBER"))>0?intval(app_conf("SCORE_TRADE_NUMBER")):0;
			$pay_score_money=intval($pay_score/$score_trade_number*100)/100;
			
			
			
			if(!$is_tg)
			{
				if($credit> $user['money'])
				{
					$data['pay_status'] = 0;
					$data['pay_info'] = "余额最多只能用".format_price($GLOBALS['user_info']['money']);
					$data['show_pay_btn'] = 0;
					$root=$data;
					output($root);
				}
				if($pay_score > $user['score'])
				{
					$data['pay_status'] = 0;
					$data['pay_info'] = "积分最多只能用".$GLOBALS['user_info']['score'];
					$data['show_pay_btn'] = 0;
					$root=$data;
					output($root);
				}
				if($pay_score_money+ $credit > $order_info['total_price'])
				{
					$data['pay_status'] = 0;
					$data['pay_info'] = "支付超出";
					$data['show_pay_btn'] = 0;
					$root=$data;
					output($root);
				}
			}
				
			if($credit>0){
				$order_data['credit_pay'] = $credit;
			}else
			{
				$order_data['credit_pay'] = 0;
			}
			
			if($pay_score>0)
			{
				$order_data['score'] = $pay_score;
				$order_data['score_money'] = $pay_score_money;
			}
			else
			{
				$order_data['score'] = 0;
				$order_data['score_money'] = 0;
			}
			if($ecv_id == 0)
			{
				$ecv_id = $order_info["ecv_id"];
			}
			
			$GLOBALS['db']->query("update ".DB_PREFIX."project_order set credit_pay = ".$order_data['credit_pay'].",score=".$order_data['score'].",score_money=".$order_data['score_money'].",ecv_id = ".$ecv_id." where id = ".intval($order_info['id'])." ");
			
			$result = pay_order($order_info['id']);

			if($result['status']==0)
			{
				$data['pay_status'] = 0;
				$data['pay_info'] = '订单支付失败';
				$data['show_pay_btn'] = 0;
 				$root=$data;
			}
			else
			{
				$data['pay_status'] = 1;
				$data['pay_info'] = "订单支付成功";
				$data['show_pay_btn'] = 0;
				$root=$data;
				output($root);
			}
		
		output($root);
	
	}
}
?>
