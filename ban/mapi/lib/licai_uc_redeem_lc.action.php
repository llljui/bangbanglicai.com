<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class licai_uc_redeem_lc
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			require_once APP_ROOT_PATH.'system/libs/licai.php';
		
			$root['user_login_status'] = 1;
			
			$id = intval($GLOBALS['request']["id"]);
		
			$where = " ";
			
			if($id)
			{
				$where = " and lc.id = ".$id." ";
			}
			
			//待赎回本金
			$wait_order = $GLOBALS["db"]->getRow("select sum(lcr.money) as licai_wait_money, count(*) as wait_count 
			from ".DB_PREFIX."licai_redempte lcr 
			left join ".DB_PREFIX."licai_order lco on lco.id = lcr.order_id 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lc.user_id = '".$user_id."' and lcr.status = 0
			".$where);
			
			$vo["licai_wait_money_format"] = format_price($wait_order["licai_wait_money"]);
			
			$vo["licai_wait_count"] = $wait_order["wait_count"];
			
			//已赎回本金
			$vo["licai_pass_money"] = $GLOBALS["db"]->getOne("select sum(lcr.money) 
			from ".DB_PREFIX."licai_redempte lcr 
			left join ".DB_PREFIX."licai_order lco on lco.id = lcr.order_id 
			left join ".DB_PREFIX."licai lc on lc.id = lco.licai_id 
			where lc.user_id = '".$user_id."' and lcr.status = 1
			".$where);
			
			$vo["licai_pass_money_format"] = format_price($vo["licai_pass_money"]);
			
			
			//收益总额
			//$vo["licai_total_earn_money_format"] = format_price($vo["licai_total_earn_money"]-$vo["licai_total_fee"]);
			//
			//总额
			$vo["licai_all_money"] = $vo["licai_total_money"] + $vo["licai_total_earn_money"] - $vo["licai_total_fee"];
			
			$vo["licai_all_money_format"] = format_price($vo["licai_all_money"]);
			
			$root['vo'] = $vo;
			
			$condition ="  ".$where;
			
			$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$search = array();
			
			
			if(strim($GLOBALS['request']["deal_name"])!="")
			{
				$condition .= " and lc.name like '%".strim($GLOBALS['request']["deal_name"])."%'";
				$search["deal_name"] = strim($GLOBALS['request']["deal_name"]);
			}
			
			if(strim($GLOBALS['request']["user_name"])!="")
			{
				$condition .= " and lcr.user_name like '%".strim($GLOBALS['request']["user_name"])."%'";
				$search["user_name"] = strim($GLOBALS['request']["user_name"]);
			}
			
			if(strim($GLOBALS['request']["licai_type"])!=="" && intval($GLOBALS['request']["licai_type"])!=-1)
			{
				$condition .= " and lc.type = ".intval($GLOBALS['request']["licai_type"]);
				$search["licai_type"] = intval($GLOBALS['request']["licai_type"]);
			}
			else
			{
				$search["licai_type"] = -1;
			}
			
			//项目结束时间
			$start_time = strim($GLOBALS['request']['begin_time']);
			$end_time = strim($GLOBALS['request']['end_time']);	
	
			$d = explode('-',$start_time);
			if (isset($GLOBALS['request']['begin_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
				$root['show_err'] ="开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)";
				output($root);	
			}
			
			$d = explode('-',$end_time);
			if ( isset($GLOBALS['request']['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
				$root['show_err'] ="结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)";
				output($root);
			}
			
			if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
				$root['show_err'] ="开始时间不能大于结束时间:'.$start_time.'至'.$end_time";
				output($root);
			}
			
			if(strim($start_time)!="")
			{
				$condition .= " and lcr.create_date >= '".$start_time."'";
				$search["begin_time"] = $start_time;
			}
			if(strim($end_time) !="")
			{
				$condition .= " and lcr.create_date <= '".  $end_time."'";
				$search["end_time"] = $end_time;
			}
			
			$count = $GLOBALS["db"]->getOne("select count(*) 
			from ".DB_PREFIX."licai_redempte lcr 
			left join ".DB_PREFIX."licai_order lco on lco.id = lcr.order_id 
			left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id 
			where lc.user_id ='".$user_id."' ".$condition );
					
			$list = $GLOBALS["db"]->getAll("select lcr.*,lc.name as licai_name,lc.type as licai_type,lco.begin_interest_date,
			lco.money as all_money,lco.licai_id,lco.site_buy_fee as o_site_buy_fee ,lco.redempte_money as o_redempte_money 
			from ".DB_PREFIX."licai_redempte lcr 
			left join ".DB_PREFIX."licai_order lco on lco.id = lcr.order_id 
			left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id 
			where lc.user_id ='".$user_id."'  ".$condition." order by lcr.id desc limit ".$limit );
			
			foreach($list as $k=>$v)
			{
				//$list[$k]["all_money_format"] = format_price($v["all_money"]);
				$list[$k]["money_format"] = format_price($v["money"]);
				if($v["licai_type"] > 0)
				{
					$licai_interest = get_licai_interest($v["licai_id"],$v["money"]);
					
					if($v["type"] == 0 )
					{
						$list[$k]["rate"] = $v["rate"] =  $licai_interest["before_breach_rate"];
						$list[$k]["rata_format"] = number_format($v["rate"],2)."%";
					}
					elseif($v["type"] == 1)
					{
						$list[$k]["rate"] = $v["rate"] =  $licai_interest["breach_rate"];
						$list[$k]["rata_format"] = number_format($v["rate"],2)."%";
					}
					else
					{
						$list[$k]["rate"] = $v["rate"] =  $licai_interest["interest_rate"];
						$list[$k]["rata_format"] = number_format($v["rate"],2)."%";
					}
				}
				else
				{
					$licai_interest = get_licai_interest_yeb($v["licai_id"],$v["create_date"],$v["begin_interest_date"]);
					$list[$k]["rate"] = $v["rate"] =  floatval($licai_interest["avg_interest_rate"]);
					$list[$k]["rata_format"] = number_format($v["rate"],2)."%";
				}
				
				
				if($v["type"] == 0)
				{
					$list[$k]["type_format"] = "预热期赎回";
				}
				elseif($v["type"] == 1)
				{
					$list[$k]["type_format"] = "理财期赎回";
				}
				elseif($v["type"] == 2)
				{
					$list[$k]["type_format"] = "正常到期赎回";
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
					$list[$k]["status_format"] = "已赎回";
				}
				
				$list[$k]["have_money"] = $v["have_money"] = $v["all_money"] - $v["o_site_buy_fee"] - $v["o_redempte_money"];
				$list[$k]["have_money_format"] = format_price($v["have_money"]);
				
				if($v["licai_type"] == 0)
				{
					$list[$k]["licai_type_format"] = "余额宝";
				}
				elseif($v["licai_type"] == 1)
				{
					$list[$k]["licai_type_format"] = "固定定存";
				}
			}
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
			$root['list'] = $list;
			$root['search'] = $search;
			
			$root['response_code'] = 1;
			
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "赎回管理";
		output($root);		
	}
}
?>
