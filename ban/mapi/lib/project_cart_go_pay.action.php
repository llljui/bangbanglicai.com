<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_cart_go_pay
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		if(!$GLOBALS['user_info'])
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		 
		$id = intval($GLOBALS["request"]['id']);
		$paypassword=strim($GLOBALS["request"]['paypassword']);

		if($paypassword==''){
			$data['pay_status'] = 0;
			$data['pay_info'] = '请输入支付密码.';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}
		
		if(md5($paypassword)!=$GLOBALS['user_info']['paypassword']){
			$data['pay_status'] = 0;
			$data['pay_info'] = '支付密码错误.';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}

		$consignee_id = intval($GLOBALS["request"]['consignee_id']);
		$ecv_id = intval($GLOBALS["request"]['ecv_id']);
		$pay_score =intval($GLOBALS["request"]['pay_score']);
		
		if($pay_score >0)
		{
			$score_array=score_to_money($pay_score);
			$pay_score_money=$score_array['score_money'];
			$pay_score=$score_array['score'];
		}else
		{
			$pay_score=0;
			$pay_score_money=0;
		}


		$memo = strim($GLOBALS["request"]['memo']);
		//@by slf
		//$payid = intval($GLOBALS["request"]['payid']);
		$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_item where id = ".$id);
		if(!$deal_item)
		{
			$data['pay_status'] = 0;
			$data['pay_info'] = '订单错误，请返回重试.';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}
		elseif($deal_item['support_count']>=$deal_item['limit_user']&&$deal_item['limit_user']!=0)
		{
			$data['pay_status'] = 0;
			$data['pay_info'] = '项目未开始或者已过期，请返回重试';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}
		
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where is_delete = 0 and is_effect = 1 and id = ".$deal_item['deal_id']);
		if(!$deal_info)
		{
			$data['pay_status'] = 0;
			$data['pay_info'] = '订单错误，请返回重试';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}
		elseif($deal_info['begin_time']>TIME_UTC||($deal_info['end_time']<TIME_UTC&&$deal_info['end_time']!=0))
		{
			$data['pay_status'] = 0;
			$data['pay_info'] = '订单错误，请返回重试';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}
		
		if(intval($consignee_id)==0&&$deal_item['is_delivery']==1)
		{
			$data['pay_status'] = 0;
			$data['pay_info'] = '请选择配送方式';
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}
		
		//更改红包
		$ecv_money = 0;
		if($ecv_id > 0){
			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."ecv SET use_count = use_count + 1 WHERE (begin_time =0 OR (begin_time > 0 AND begin_time < ".TIME_UTC.")) AND (end_time = 0 OR (end_time > 0  AND (end_time +24*3600 - 1) > ".TIME_UTC.")) AND (use_limit =0  OR (use_limit >0 AND use_limit - use_count > 0)) AND id=".$ecv_id." AND user_id=".$user_id);
			if($GLOBALS['db']->affected_rows()){
				$ecv_money = $GLOBALS['db']->getOne("SELECT `money` FROM ".DB_PREFIX."ecv WHERE id=".$ecv_id);
				$order_info['ecv_id'] = $ecv_id;
			}
		}
		
		//无私奉献
		if($deal_item['type']==1){
			$pay_money=floatval($GLOBALS["request"]['pay_money']);
			if($pay_money<=0){
				$data['pay_status'] = 0;
				$data['pay_info'] = '您输入的金额有误';
				$data['show_pay_btn'] = 0;
				$root=$data;
				output($root);
			}
			$deal_item['price']=$pay_money;
			$credit = $pay_money;
			$order_info['type'] = 2;
  		}
  		else{
  			$order_info['type']=$deal_info['type'];
			if($pay_score_money == 0)   //无积分兑换 判断红包
			{
				if($deal_item['price'] < $ecv_money)  //红包大于支付金额
				{
					$credit = 0;
				}
				else
				{
					$credit = $deal_item['price'] + $deal_item['delivery_fee'] - $ecv_money;
				}
			}
			else
			{
				$credit = floatval($deal_item['price']+$deal_item['delivery_fee']-$pay_score_money - $ecv_money);
				if($credit<0)
				{
					$data['pay_status'] = 0;
					$data['pay_info'] = '支付失败,请重试';
					$data['show_pay_btn'] = 0;
					$root=$data;
					output($root);
				}
			}
  		}
		
		$order_info['deal_id'] = $deal_info['id'];
		$order_info['deal_item_id'] = $deal_item['id'];
		$order_info['user_id'] = $user_id;
		$order_info['user_name'] = $user['user_name'];
		$order_info['deal_price'] = $deal_item['price'];
		$order_info['support_memo'] = $memo;
		
		
		$order_info['total_price'] = $deal_item['price']+$deal_item['delivery_fee'];
		$order_info['delivery_fee'] = $deal_item['delivery_fee'];
		
		if($deal_item['is_share'] ==1)//有分红  chh 15/06/25
		{
			$order_info['share_fee']=$deal_item['share_fee'];
			$order_info['share_status']=0;
		}else{
			$order_info['share_fee']=0;
		}
		/*
		$max_credit= $order_info['total_price']<$GLOBALS['user_info']['money']?$order_info['total_price']:$GLOBALS['user_info']['money'];
		$credit = $credit>$max_credit?$max_credit:$credit;
		*/
		if(!$is_tg)
		{
			$credit_score_money=$pay_score_money + $credit;
			if($credit> $GLOBALS['user_info']['money'])
			{
				$data['pay_status'] = 0;
				$data['pay_info'] = "余额最多只能用".format_price($GLOBALS['user_info']['money']);
				$data['show_pay_btn'] = 0;
				$root=$data;
				output($root);
			}
			if($pay_score > $GLOBALS['user_info']['score'])
			{
				$data['pay_status'] = 0;
				$data['pay_info'] = "积分最多只能用".$GLOBALS['user_info']['score'];
				$data['show_pay_btn'] = 0;
				$root=$data;
				output($root);
			}
			if( $credit_score_money > $order_info['total_price'])
			{
				$data['pay_status'] = 0;
				$data['pay_info'] = "支付超出";
				$data['show_pay_btn'] = 0;
				$root=$data;
				output($root);
			}
		}
			
		if ($credit > 0 && $GLOBALS['user_info']['money'] >= $credit)
			$order_info ['credit_pay'] = $credit;
			
		if ($pay_score > 0 &&$GLOBALS['user_info']['score'] >= $pay_score)
		{
			$order_info['score'] = $pay_score;
			$order_info['score_money'] = $pay_score_money;
		}
		
//		$order_info['credit_pay'] = $credit;
		$order_info['online_pay'] = 0;
		$order_info['deal_name'] = $deal_info['name'];
		$order_info['order_status'] = 0;
		$order_info['create_time']	= TIME_UTC;
		if($consignee_id>0)
		{
			$consignee_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_address where id = ".$consignee_id." and user_id = ".$user_id);
			if(!$consignee_info&&$deal_item['is_delivery']==1)
			{
				$data['pay_status'] = 0;
				$data['pay_info'] = "请选择配送方式";
				$data['show_pay_btn'] = 0;
				$root=$data;
				output($root);
			}
			$order_info['consignee'] = $consignee_info['name'];
			$order_info['zip'] = $consignee_info['zip_code'];
			$order_info['address'] =$consignee_info['provinces_cities'] . $consignee_info['address'];
			$order_info['mobile'] = $consignee_info['phone'];
		}
		$order_info['is_success'] = $deal_info['is_success'];
		$GLOBALS['db']->autoExecute(DB_PREFIX."project_order",$order_info);
		
		$order_id = $GLOBALS['db']->insert_id();
		if($order_id>0)
		{
			$result = pay_order($order_id);
			if($result['status']==0)
			{
				$data['pay_status'] = 0;
				$data['pay_info'] = "订单支付失败";
				$data['show_pay_btn'] = 0;
				$root=$data;
				output($root);
			}
			elseif($result['status']==1)
			{
				$data['pay_status'] = 0;
				$data['pay_info'] = "订单过期";
				$data['show_pay_btn'] = 0;
				$root=$data;
				output($root);
 				
 				
			}elseif($result['status']==2)
			{
				$data['pay_status'] = 0;
				$data['pay_info'] = "订单无库存";
				$data['show_pay_btn'] = 0;
				$root=$data;
				output($root);
 			}
			else
			{
				$data['pay_status'] = 1;
				$data['pay_info'] = '订单支付成功.';
				$data['show_pay_btn'] = 0;
 				$root=$data;
 			}
		}
		else
		{
			$data['pay_status'] = 0;
			$data['pay_info'] = "下单失败";
			$data['show_pay_btn'] = 0;
			$root=$data;
			output($root);
		}
		
		$root['program_title'] = "";
		
		output($root);		
	}
}
?>
