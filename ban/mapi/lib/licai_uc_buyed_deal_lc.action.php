<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_buyed_deal_lc
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$id = intval($GLOBALS['request']["id"]);
			$root['id'] = $id;
			
			if(!$id)
			{
				$root["status"] = 0;
				$root["show_err"] = "操作失败，请重试";
				output($root);
			}
			
			$vo = $GLOBALS["db"]->getRow("select lc.*,lco.id as order_id from ".DB_PREFIX."licai lc left join ".DB_PREFIX."licai_order lco on lc.id = lco.licai_id where lco.id=".$id." and lco.user_id = ".$user_id);
			
			//$vo["average_income_rate_format"] = $vo["average_income_rate"]."%";
			$vo["average_income_rate_format"] = number_format($vo["average_income_rate"], 2, '.', '')."%";
			
			$vo_info =  $GLOBALS["db"]->getRow("select count(*) as licai_total_count,
			sum(case when lcr.status = 1 then (lcr.money+lcr.earn_money-lcr.fee) else 0 end) as licai_all_redempte,
			sum(case when lcr.status = 0 then lcr.money else 0 end) as licai_ing_money,
			sum(case when lcr.status = 1 then lcr.earn_money-lcr.fee else 0 end) as total_earn_money  
			from ".DB_PREFIX."licai_redempte lcr 
			left join ".DB_PREFIX."licai_order lco on lcr.order_id = lco.id 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lcr.user_id = ".$user_id." and lcr.status in (0,1) and lco.id=".$id); 
			
			//请求总数
			$vo["licai_total_count"] = $vo_info["licai_total_count"];
			//已赎回总额
			$vo["licai_all_redempte_format"] = format_price($vo_info["licai_all_redempte"]);
			//收益
			$vo["total_earn_money_format"] = format_price($vo_info["total_earn_money"]);
			//进行中的
			$vo["licai_ing_money_format"] = format_price($vo_info["licai_ing_money"]);
			
	
			$list = $GLOBALS["db"]->getAll("select lcr.* from ".DB_PREFIX."licai_redempte lcr 
			left join ".DB_PREFIX."licai_order lco on lco.id = lcr.order_id 
			where lcr.order_id = ".$id." order by id desc");
			
			foreach($list as $k=>$v)
			{
				$list[$k]["money_format"] = format_price($v["money"]);
				$list[$k]["earn_money_format"] = format_price($v["earn_money"]);
				$list[$k]["fee_format"] = format_price($v["fee"]);
				$list[$k]["real_money"] = $v["real_money"] = $v["money"]+ $v["earn_money"] - $v["fee"];
				$list[$k]["real_money_format"] = format_price($v["real_money"]);
				
				if($v["type"] == 0)
				{
					$list[$k]["type_format"] = "预热期";
				}
				elseif($v["type"] == 1)
				{
					$list[$k]["type_format"] = "理财期";
				}
				elseif($v["type"] == 2)
				{
					$list[$k]["type_format"] = "已结清";
				}
				if($v["status"] == 0)
				{
					$list[$k]["status_format"] = "未赎回";
				}
				elseif($v["status"] == 1)
				{
					$list[$k]["status_format"] = "已赎回";
				}
				elseif($v["status"] == 2)
				{
					$list[$k]["status_format"] = "已拒绝";
				}
				elseif($v["status"] == 3)
				{
					$list[$k]["status_format"] = "已取消";
				}
			}
	
			$root['list'] = $list;
			$root['vo'] = $vo;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "购买的理财产品详情";
		output($root);		
	}
}
?>
