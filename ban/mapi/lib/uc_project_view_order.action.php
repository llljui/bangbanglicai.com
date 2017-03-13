<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class uc_project_view_order
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		$user = get_user_info("*","id=".$user_id,"ROW");
		
		$root['user_info'] = $user;
		
		/*new start*/
		if(!$GLOBALS['user_info'])
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		$root["program_title"]="支持的项目详情";
		$id = intval($GLOBALS["request"]['id']);
		$order_info = $GLOBALS['db']->getRow("select deo.*, di.is_delivery as is_delivery,d.transfer_share as transfer_share,d.limit_price as limit_price from ".DB_PREFIX."project_order as deo LEFT JOIN (".DB_PREFIX."project as d,".DB_PREFIX."project_item as di) on (deo.deal_id = d.id and  di.id = deo.deal_item_id )  where deo.id = ".$id." and deo.user_id = ".$user_id);
		
		if(!$order_info)
		{
			$root["status"] = 0;
			$root["msg"] = "无效的项目支持";
			output($root);
		}
		//========如果超过系统设置的时间，则自动设置收到回报 start
		if($order_info['repay_make_time']==0 && $order_info['repay_time']>0){
			$item=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_item where id=".$order_info['deal_item_id']);
			$item_day=intval($item['repaid_day']);
			if($item_day>0){
				$left_date=$item_day;
			}else{
				$left_date=intval(app_conf("REPAY_MAKE"));
			}
				
			$repay_make_date=$order_info['repay_time']+$left_date*24*3600;
			
			if($repay_make_date>TIME_UTC&&$order_info['repay_time']>0){
				$order_info['repay_make_date']=date('Y-m-d H:i:s',$repay_make_date);
				$order_info['repay_left_time'] = $repay_make_date - TIME_UTC;
			}else{
 				$GLOBALS['db']->query("update ".DB_PREFIX."project_order set repay_make_time =  ".TIME_UTC." where id = ".$id);
				$order_info['repay_make_time']=TIME_UTC;
			}
		}
		if($order_info['type']==1){
			//用户所占股份
			$order_info['user_stock']= number_format(($order_info['total_price']/$order_info['limit_price'])*$order_info['transfer_share'],2);			
			//项目金额
			$order_info['stock_value'] =number_format($order_info['limit_price'],2);
			//应付金额
			//$order_info['total_price'] =number_format($order_info['total_price'],2);
		}
		//=============如果超过系统设置的时间，则自动设置收到回报 end
		
		if($order_info['ecv_id'] > 0)
		{
			$ecv_money= $GLOBALS["db"]->getOne("select money from ".DB_PREFIX."ecv where id=".$order_info["ecv_id"]);
			if($ecv_money > $order_info['total_price'])
			{
				$order_info['ecv_money'] = $order_info['total_price'];
			}
			else
			{
				$order_info['ecv_money'] = $ecv_money;
			}
		}
		else
		{
			$ecv_money = 0 ;
		}
		
		$root["order_info"]=$order_info;
		//print_r($order_info);exit;
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$order_info['deal_id']." and is_delete = 0 and is_effect = 1");

		$root["deal_info"]= $deal_info;
		
		if($order_info['order_status'] == 0)
		{			
			$max_pay = $order_info['total_price'];
			$root["max_pay"]=$max_pay;
			
			$order_sm=array('credit_pay'=>0,'score'=>0,'score_money'=>0,'ecv_money'=>0);
			//$order_sm=array('credit_pay'=>0,'score'=>0,'score_money'=>0);
			if($order_info['credit_pay']>0)
			{
				$order_sm['credit_pay']=$order_info['credit_pay'];
			}
			if($order_info['score'] >0)
			{
				
				if($order_info['score'] <= $user['score'])
				{
					$order_sm['score']=$order_info['score'];
					$order_sm['score_money']=$order_info['score_money'];
				}else
				{
					$order_sm['score']=$user['score'];
					$score_array=score_to_money($user['score']);
					$order_sm['score_money']=$score_array['score_money'];
					$order_sm['score']=$score_array['score'];
				}
			}
			
			$order_sm['ecv_money'] = $ecv_money;
			
			$root["order_sm"]=json_encode($order_sm);
		}
		
		output($root);
		
	}
}
?>
