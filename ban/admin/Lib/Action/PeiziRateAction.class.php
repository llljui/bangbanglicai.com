<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require_once APP_ROOT_PATH."/system/libs/peizi.php";
class PeiziRateAction extends CommonAction{	
	
	//投资金额返利
	public function invest_money()
	{
		$condition = " and o.p_invest_user_id >0 ";
		if(strim($_REQUEST["user_name"])!="")
		{
			$condition = " and u.user_name like '%".strim($_REQUEST["user_name"])."%'";
			$this->assign("user_name",strim($_REQUEST["user_name"]));
		}
		if(intval($_REQUEST["peizi_conf_id"])!="" && intval($_REQUEST["peizi_conf_id"])!= -1)
		{
			$condition = " and o.peizi_conf_id = ".intval($_REQUEST["peizi_conf_id"]);
			$this->assign("peizi_conf_id",intval($_REQUEST["peizi_conf_id"]));
		}
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);

		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		if(strim($start_time)!="")
		{
			$condition .= " and UNIX_TIMESTAMP(o.begin_date) >=".to_timespan(strim($start_time));
			$this->assign("start_time",$start_time);
		}
		if(strim($end_time) !="")
		{
			$condition .= " and UNIX_TIMESTAMP(o.begin_date) <=".  to_timespan(strim($end_time));
			$this->assign("end_time",$end_time);
		}
		
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order o 
		left join ".DB_PREFIX."user u on u.id = o.p_invest_user_id 
		where o.status in (6,8)".$condition);

		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("SELECT o.*,c.name as conf_type_name ,u.user_name 
			FROM ".DB_PREFIX."peizi_order o 
			left join ".DB_PREFIX."peizi_conf c on o.peizi_conf_id = c.id 
			left join ".DB_PREFIX."user u on u.id = o.p_invest_user_id 
			where o.status in (6,8) ".$condition." limit ".$limit);
		}
		foreach($result['list'] as $k => $v)
		{
			$result['list'][$k] = get_peizi_order_fromat($v);
		}
		$this->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		
		$this->assign("main_title","投资金额返利");
		$this->display("invest_money");
		
	}
	//利息与佣金收益返利
	public function invest_fee_money()
	{
		$condition = " and fl.p_invest_user_id >0 ";
		if(strim($_REQUEST["user_name"])!="")
		{
			$condition = " and p_i_u.user_name like '%".strim($_REQUEST["user_name"])."%'";
			$this->assign("user_name",strim($_REQUEST["user_name"]));
		}
		/*if(intval($_REQUEST["peizi_conf_id"])!="" && intval($_REQUEST["peizi_conf_id"])!= -1)
		{
			$condition = " and fl.peizi_conf_id = ".intval($_REQUEST["peizi_conf_id"]);
			$this->assign("peizi_conf_id",intval($_REQUEST["peizi_conf_id"]));
		}*/
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);

		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		if(strim($start_time)!="")
		{
			$condition .= " and UNIX_TIMESTAMP(fl.fee_date) >=".to_timespan(strim($start_time));
			$this->assign("start_time",$start_time);
		}
		if(strim($end_time) !="")
		{
			$condition .= " and UNIX_TIMESTAMP(fl.fee_date) <=".  to_timespan(strim($end_time));
			$this->assign("end_time",$end_time);
		}
		
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order_fee_list fl
		left join ".DB_PREFIX."user p_i_u on p_i_u.id = fl.p_invest_user_id 
		where  has_pay = 1".$condition);

		if($result['count'] > 0){
			$sql = 	"select fl.*,u.user_name,i_u.user_name as invest_user_name,o.order_sn,p_i_u.user_name as p_invest_user_name 
			from ".DB_PREFIX."peizi_order_fee_list fl 
			left join ".DB_PREFIX."user u on fl.user_id = u.id 
			left join ".DB_PREFIX."user i_u on fl.invest_user_id = i_u.id 
			left join ".DB_PREFIX."user p_i_u on fl.p_invest_user_id = p_i_u.id 
			left join ".DB_PREFIX."peizi_order o on fl.peizi_order_id = o.id 
			where fl.has_pay = 1".$condition." limit ".$limit ;

			$result['list'] = $GLOBALS['db']->getAll($sql);
		}
		
		$this->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		
		$this->assign("main_title","利息与佣金收益返利");
		$this->display("invest_fee_money");	
	}
	//借款金额返利
	public function invite_borrow_money()
	{
		$condition = " and o.p_user_id >0 ";
		if(strim($_REQUEST["user_name"])!="")
		{
			$condition = " and p_u.user_name like '%".strim($_REQUEST["user_name"])."%'";
			$this->assign("user_name",strim($_REQUEST["user_name"]));
		}
		if(intval($_REQUEST["peizi_conf_id"])!="" && intval($_REQUEST["peizi_conf_id"])!= -1)
		{
			$condition = " and o.peizi_conf_id = ".intval($_REQUEST["peizi_conf_id"]);
			$this->assign("peizi_conf_id",intval($_REQUEST["peizi_conf_id"]));
		}
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);

		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		if(strim($start_time)!="")
		{
			$condition .= " and UNIX_TIMESTAMP(o.begin_date) >=".to_timespan(strim($start_time));
			$this->assign("start_time",$start_time);
		}
		if(strim($end_time) !="")
		{
			$condition .= " and UNIX_TIMESTAMP(o.begin_date) <=".  to_timespan(strim($end_time));
			$this->assign("end_time",$end_time);
		}
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order o 
		left join ".DB_PREFIX."user p_u on o.p_user_id = p_u.id 
		where  o.status in (6,8)".$condition);

		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("SELECT o.*,c.name as conf_type_name,p_u.user_name as p_user_name 
			FROM ".DB_PREFIX."peizi_order o 
			left join ".DB_PREFIX."peizi_conf c on o.peizi_conf_id = c.id 
			left join ".DB_PREFIX."user p_u on p_u.id = o.p_user_id
			where  o.status in (6,8)".$condition." limit ".$limit);
		}
		foreach($result['list'] as $k => $v)
		{
			$result['list'][$k] = get_peizi_order_fromat($v);
		}
		$this->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		$this->assign("main_title","借款金额返利");
		$this->display("invite_borrow_money");	
	}
	//平台利息收益返利
	public function borrow_interest_money()
	{
		$condition = " and fl.p_user_id >0 ";
		if(strim($_REQUEST["user_name"])!="")
		{
			$condition = " and p_u.user_name like '%".strim($_REQUEST["user_name"])."%'";
			$this->assign("user_name",strim($_REQUEST["user_name"]));
		}
		if(intval($_REQUEST["peizi_conf_id"])!="" && intval($_REQUEST["peizi_conf_id"])!= -1)
		{
			$condition = " and o.peizi_conf_id = ".intval($_REQUEST["peizi_conf_id"]);
			$this->assign("peizi_conf_id",intval($_REQUEST["peizi_conf_id"]));
		}
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);

		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		if(strim($start_time)!="")
		{
			$condition .= " and UNIX_TIMESTAMP(fl.fee_date) >=".to_timespan(strim($start_time));
			$this->assign("start_time",$start_time);
		}
		if(strim($end_time) !="")
		{
			$condition .= " and UNIX_TIMESTAMP(fl.fee_date) <=".  to_timespan(strim($end_time));
			$this->assign("end_time",$end_time);
		}
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		
		$result['count'] = $GLOBALS['db']->getOne("select count(*) 
		from ".DB_PREFIX."peizi_order_fee_list fl 
		left join ".DB_PREFIX."user p_u on fl.p_user_id = p_u.id 
		left join ".DB_PREFIX."peizi_order o on fl.peizi_order_id = o.id 
		where  has_pay = 1".$condition);
		if($result['count'] > 0){
			$sql = 	"select fl.*,u.user_name,i_u.user_name as invest_user_name,o.order_sn,p_u.user_name as p_user_name 
			from ".DB_PREFIX."peizi_order_fee_list fl 
			left join ".DB_PREFIX."user u on fl.user_id = u.id
			left join ".DB_PREFIX."user i_u on fl.invest_user_id = i_u.id 
			left join ".DB_PREFIX."user p_u on fl.p_user_id = p_u.id 
			left join ".DB_PREFIX."peizi_order o on fl.peizi_order_id = o.id 
			where fl.has_pay = 1".$condition." limit ".$limit ;
			$result['list'] = $GLOBALS['db']->getAll($sql);
		}
		
		$this->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		$this->assign("main_title","平台利息收益返利");
		$this->display("borrow_interest_money");	
	}
	//平台佣金收益返利
	public function borrow_commission_money()
	{
		$condition = " and fl.p_user_id >0 ";
		if(strim($_REQUEST["user_name"])!="")
		{
			$condition = " and p_u.user_name like '%".strim($_REQUEST["user_name"])."%'";
			$this->assign("user_name",strim($_REQUEST["user_name"]));
		}
		if(intval($_REQUEST["peizi_conf_id"])!="" && intval($_REQUEST["peizi_conf_id"])!= -1)
		{
			$condition = " and o.peizi_conf_id = ".intval($_REQUEST["peizi_conf_id"]);
			$this->assign("peizi_conf_id",intval($_REQUEST["peizi_conf_id"]));
		}
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);

		$d = explode('-',$start_time);
		if (isset($_REQUEST['start_time']) && $start_time !="" && checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("开始时间不是有效的时间格式:{$start_time}(yyyy-mm-dd)");
			exit;
		}
		
		$d = explode('-',$end_time);
		if ( isset($_REQUEST['end_time']) && strim($end_time) !="" &&  checkdate($d[1], $d[2], $d[0]) == false){
			$this->error("结束时间不是有效的时间格式:{$end_time}(yyyy-mm-dd)");
			exit;
		}
		
		if ($start_time!="" && strim($end_time) !="" && to_timespan($start_time) > to_timespan($end_time)){
			$this->error('开始时间不能大于结束时间:'.$start_time.'至'.$end_time);
			exit;
		}
		if(strim($start_time)!="")
		{
			$condition .= " and UNIX_TIMESTAMP(fl.fee_date) >=".to_timespan(strim($start_time));
			$this->assign("start_time",$start_time);
		}
		if(strim($end_time) !="")
		{
			$condition .= " and UNIX_TIMESTAMP(fl.fee_date) <=".  to_timespan(strim($end_time));
			$this->assign("end_time",$end_time);
		}
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		
		$result['count'] = $GLOBALS['db']->getOne("select count(*) 
		from ".DB_PREFIX."peizi_order_fee_list fl 
		left join ".DB_PREFIX."user p_u on fl.p_user_id = p_u.id 
		left join ".DB_PREFIX."peizi_order o on fl.peizi_order_id = o.id 
		where  has_pay = 1".$condition);
		if($result['count'] > 0){
			$sql = 	"select fl.*,u.user_name,i_u.user_name as invest_user_name,o.order_sn,p_u.user_name  as p_user_name 
			from ".DB_PREFIX."peizi_order_fee_list fl 
			left join ".DB_PREFIX."user u on fl.user_id = u.id 
			left join ".DB_PREFIX."user i_u on fl.invest_user_id = i_u.id 
			left join ".DB_PREFIX."user p_u on fl.p_user_id = p_u.id 
			left join ".DB_PREFIX."peizi_order o on fl.peizi_order_id = o.id 
			where fl.has_pay = 1".$condition." limit ".$limit ;
			$result['list'] = $GLOBALS['db']->getAll($sql);
		}
		$this->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('pages',$p);
		$this->assign("main_title","平台佣金收益返利");
		$this->display("borrow_commission_money");
	}
}
?>