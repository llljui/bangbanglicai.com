<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_deal_support
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		if(!$user)
		{
			app_redirect(url_wap("user#login"));
		}
		$root["program_title"]="支持列表";
		
		$deal_id = intval($GLOBALS["request"]['id']);
		$type = intval($_REQUEST['type']);
		$user_name = strim($_REQUEST['user_name']);
		$mobile = strim($_REQUEST['mobile']);
		$repay_status = intval($_REQUEST['repay_status']);
		
		$parameter=array();
		$parameter[]="type=".$type;
		$parameter[]="id=".$deal_id;
		
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$deal_id." and is_delete = 0 and is_effect = 1 and is_success = 1 and user_id = ".$user_id);
		
		if(!$deal_info)
		{
			app_redirect_preview();
		}
		$root["deal_info"]=$deal_info;
		$where='';
		
		
		$page_size = app_conf("DEAL_PAGE_SIZE");
		$page = intval($GLOBALS['request']['p']);
		if($page==0)$page = 1;		
		$limit = (($page-1)*$page_size).",".$page_size;
		
		if($type ==1)
		{
			$support_list = $GLOBALS['db']->getAll("select d.*,i.stock_value as investment_stock_value,i.type as invest_type,pi.is_delivery from ".DB_PREFIX."project_order as d "." left join ".DB_PREFIX."investment_list as i on i.id = d.invest_id left join ".DB_PREFIX."project_item pi on p.deal_item_id = pi.id  where d.deal_id = ".$deal_id." and d.order_status = 3 and d.invest_id >0 ".$where." order by d.create_time desc limit ".$limit);
			$support_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_order as d left join ".DB_PREFIX."investment_list as i on i.id = d.invest_id left join ".DB_PREFIX."project_item pi on p.deal_item_id = pi.id  where d.deal_id = ".$deal_id." and d.order_status = 3  and d.invest_id >0 ".$where);
		}
		else
		{
			$support_list = $GLOBALS['db']->getAll("select d.*,di.description as item_description,di.is_delivery from ".DB_PREFIX."project_order as d left join ".DB_PREFIX."project_item as di on di.id=d.deal_item_id where d.deal_id = ".$deal_id." and d.order_status = 3 and d.deal_item_id >0 ".$where." order by d.create_time desc limit ".$limit);
			$support_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_order as d left join ".DB_PREFIX."project_item as di on di.id=d.deal_item_id where d.deal_id = ".$deal_id." and d.order_status = 3 and d.deal_item_id >0 ".$where);
		}

		$root["support_list"] = $support_list;
		
		$parameter_str="&".implode("&",$parameter);
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($support_count/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
		
		$root['deal_id']=$deal_id;
		$root['type']=$type;
		$root['user_name']=$user_name;
		$root['mobile']=$mobile;
		$root['repay_status']=$repay_status;
		
		output($root);
	}
}
?>
