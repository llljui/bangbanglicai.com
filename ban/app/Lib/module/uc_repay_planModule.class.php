<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_repay_planModule extends SiteBaseModule
{		
	public function index()
	{
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$user_id = $GLOBALS['user_info']['id'];
		$status = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 3;
		
		$time = isset($_REQUEST['time']) ?  to_timespan($_REQUEST['time'],"Ymd") : "";
		$deal_name = strim($_REQUEST['deal_name']);
		$condition = "";
		if($deal_name!=""){
			
			$condition.=" and d.name = '".$deal_name."' ";
			$GLOBALS['tmpl']->assign('deal_name',$deal_name);
		}
		if($time!=""){
			
			$condition.=" and dlr.repay_time = ".$time." ";
			$GLOBALS['tmpl']->assign('time',to_date($time,"Y-m-d"));
			$GLOBALS['tmpl']->assign('time_format',to_date($time,"Ymd"));
		}
		
		$result = getUcRepayPlan($user_id,$status,$limit,$condition);
		
		if($result['rs_count'] > 0){
			
			$page = new Page($result['rs_count'],app_conf("PAGE_SIZE"));   //初始化分页对象
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign('list',$result['list']);
		}
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_REPAY_PLAN']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_repay_plan.html");
		
				
		$GLOBALS['tmpl']->assign("status",$status);
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		$status =  isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 3;   //1.待还款 2.已还款 3.近期还款 4.全部
		
		$user_id = $GLOBALS['user_info']['id'];		
		$time = isset($_REQUEST['time']) ?  to_timespan($_REQUEST['time'],"Ymd") : "";
		$deal_name = strim($_REQUEST['deal_name']);
		
		$condition = "";
		if($deal_name!=""){
			$condition.=" and d.name = '".$deal_name."' ";
		}
		if($time!=""){
			$condition.=" and dlr.repay_time = ".$time." ";
		}
		
		$result = getUcRepayPlan($user_id,$status,$limit,$condition);
		
		$list = $result['list'];
		//定义条件
		if(!$list)
		{
			showErr("无导出信息");
		}
		
		foreach($list as $k=>$v){
			if($list[$k]['has_repay']!=1){
				$list[$k]['shiji_money'] = 0;
			}
			
		}
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_1'), $page+1);
			$repay_value = array('name'=>'""','l_key_index'=>'""','repay_money_format'=>'""','manage_interest_money_format'=>'""','interest_money_format'=>'""','shiji_money'=>'""','repay_date'=>'""','status_format'=>'""');
			 
			$content = "";
			$contentss = iconv("utf-8","gbk","贷款名称,第几期,待收款,利息管理费,预期收益,实际收益,还款日,还款状态");
			$content  .= $contentss . "\n";
			foreach($list as $k=>$v)
			{
				$repay_value = array();
				$repay_value['name'] = iconv('utf-8','gbk','"' . $v['name'] . '"');
				$repay_value['l_key_index'] = iconv('utf-8','gbk','"' . $v['l_key_index'] . '"');
				$repay_value['repay_money_format'] = iconv('utf-8','gbk','"' . $v['repay_money_format'] . '"');
				$repay_value['manage_interest_money_format'] = iconv('utf-8','gbk','"' . $list[$k]['manage_interest_money_format'] . '"');
				$repay_value['interest_money_format'] = iconv('utf-8','gbk','"' . $v['interest_money_format'] . '"');
				$repay_value['shiji_money'] = iconv('utf-8','gbk','"' . $list[$k]['shiji_money'] . '"');
				$repay_value['repay_date'] = iconv('utf-8','gbk','"' . $v['repay_date'] . '"');
				$repay_value['status_format'] = iconv('utf-8','gbk','"' . $v['status_format'] . '"');
				$content .= implode(",", $repay_value) . "\n";
			}
			 
			header("Content-Disposition: attachment; filename=uc_repay_plan.csv");
			echo $content;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	
	}
}
?>