<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_u_buyed_deal_lc
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$id = intval($GLOBALS['request']["id"]);
		
			if(!$id)
			{
				$root["status"] = 0;
				$root["info"] = "操作失败，请重试";
				output($root);
			}
			
//			$page = intval($GLOBALS['request']['page']);
//			if($page==0)
//				$page = 1;
//			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			
			$vo = $GLOBALS["db"]->getRow("select lc.*,lco.id as order_id from ".DB_PREFIX."licai lc left join ".DB_PREFIX."licai_order lco on lc.id = lco.licai_id where lco.id=".$id." and lc.user_id = ".$user_id);
		
			//$vo["average_income_rate_format"] = $vo["average_income_rate"]."%";
			$vo["average_income_rate_format"] = number_format($vo["average_income_rate"], 2, '.', '')."%";
			
			$vo_info =  $GLOBALS["db"]->getRow("select sum(case when lcr.status = 0 then 1 else 0 end) as licai_total_count,sum(case when lcr.status = 1 then lcr.money else 0 end) as licai_total_money,sum(case when lcr.status = 1 then lcr.earn_money-lcr.fee else 0 end) as total_earn_money ,sum(case when lcr.status = 0 then lcr.money+lcr.earn_money-lcr.fee else 0 end) as wait_money
			from ".DB_PREFIX."licai_redempte lcr 
			left join ".DB_PREFIX."licai_order lco on lcr.order_id = lco.id 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lc.user_id = ".$user_id." and lco.id=".$id);
			
			//请求总数
			$vo["licai_total_count"] = $vo_info["licai_total_count"];
			//本金总和
			$vo["licai_total_money_format"] = format_price($vo_info["licai_total_money"]);
			//收益
			$vo["total_earn_money_format"] = format_price($vo_info["total_earn_money"]);
			//总额
			$vo["licai_all_money_format"] = format_price($vo_info["wait_money"]);
			
	
			$list = $GLOBALS["db"]->getAll("select lcr.* from ".DB_PREFIX."licai_redempte lcr 
			left join ".DB_PREFIX."licai_order lco on lco.id = lcr.order_id 
			where lcr.order_id = ".$id);
			
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
			
			//$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
			$root['list'] = $list;
			$root['vo'] = $vo;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "发起的理财赎回记录";
		output($root);		
	}
}
?>
