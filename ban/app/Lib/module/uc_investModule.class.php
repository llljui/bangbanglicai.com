<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_investModule extends SiteBaseModule
{
	public function index(){
		$this->getlist("index");
	}
	public function invite(){
		$this->getlist("invite");
	}
	public function flow(){
		$this->getlist("flow");
	}
	public function ing(){
		$this->getlist("ing");
	}
	public function over(){
		$this->getlist("over");
	}
	public function bad(){
		$this->getlist("bad");
		
	}
	
    private function getlist($mode = "index") {
    	
    	$result = getInvestList($mode,intval($GLOBALS['user_info']['id']),intval($_REQUEST['p']));
    	
    	$list = $result['list'];
    	foreach($list as $k=>$v){
    		//当为天的时候
			if($v['repay_time_type'] == 0){
				$true_repay_time = 1;
			}
			else{
				$true_repay_time = $v['repay_time'];
			}
			
			$deal['borrow_amount'] = $v['u_load_money'];
			$deal['rate'] = $v['rate'];
			$deal['loantype'] = $v['loantype'];
			$deal['repay_time'] = $v['repay_time'];
	    	$deal['repay_time_type'] = $v['repay_time_type'];
	    	$deal['repay_start_time'] = $v['repay_start_time'];
			
			$deal_repay_rs = deal_repay_money($deal);
			
    		$v['interest_amount'] = $deal_repay_rs['month_repay_money'];
				
    		$list[$k] = $v;
    	}
    	$count = $result['count'];
    	
    	$GLOBALS['tmpl']->assign("list",$list);
    	
    	$page = new Page($count,app_conf("PAGE_SIZE"));   //初始化分页对象
    	$p  =  $page->show();
    	$GLOBALS['tmpl']->assign('pages',$p);
    	
		$GLOBALS['tmpl']->assign('user_id', $GLOBALS['user_info']['id']);
		
    	$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_INVEST']);
    		
    	$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_invest.html");
    	$GLOBALS['tmpl']->display("page/uc.html");
    }
    
	public function refdetail(){
		//277
    	$user_id = $GLOBALS['user_info']['id'];
		$id = intval($_REQUEST['id']);
		$load_id = intval($_REQUEST['load_id']);
		require APP_ROOT_PATH."app/Lib/deal.php";
		$deal = get_deal($id);
		if(!$deal || $deal['deal_status']<4){
			showErr("无法查看，可能有以下原因！<br>1。借款不存在<br>2。借款被删除<br>3。借款未成功");
		}
		$GLOBALS['tmpl']->assign('deal',$deal);
				
		//获取本期的投标记录
		$temp_user_load = $GLOBALS['db']->getRow("SELECT dl.id,dl.deal_id,dl.user_id,dl.money,dlt.t_user_id FROM ".DB_PREFIX."deal_load dl left join ".DB_PREFIX."deal_load_transfer dlt on dlt.load_id = dl.id WHERE dl.deal_id=".$id." and dl.id=".$load_id);
		
		//print_r("SELECT id,deal_id,user_id,money FROM ".DB_PREFIX."deal_load  WHERE deal_id=".$id." and id=".$load_id);die;
		
		$user_load_ids = array();
		if($temp_user_load){
			$u_key = $GLOBALS['db']->getOne("SELECT u_key FROM ".DB_PREFIX."deal_load_repay WHERE load_id=".$load_id." and (user_id=".$user_id." or t_user_id = ".$user_id.")");
			if(($temp_user_load["user_id"] == $user_id && intval($temp_user_load['t_user_id']) == 0 )|| $temp_user_load['t_user_id'] == $user_id){
				$temp_user_load['repay_start_time'] = $deal['repay_start_time'];
				$temp_user_load['repay_time'] = $deal['repay_time'];
				$temp_user_load['rate'] = $deal['rate'];
				$temp_user_load['u_key'] = $u_key;
				$temp_user_load['load'] = get_deal_user_load_list($deal, $user_id, -1 ,$u_key);
				$temp_user_load['impose_money'] =0;
				$temp_user_load['manage_fee'] = 0;
				$temp_user_load['repay_money'] = 0;
				$temp_user_load['manage_interest_money'] = 0;
				foreach($temp_user_load['load'] as $kk=>$vv){
					$temp_user_load['impose_money'] += $vv['impose_money'];
					$temp_user_load['manage_fee'] += $vv['manage_money'];
					$temp_user_load['repay_money'] += $vv['month_has_repay_money'];
					$temp_user_load['manage_interest_money'] += floatval($vv['manage_interest_money']);
					
					//预期收益
					$temp_user_load['load'][$kk]['yuqi_money']=format_price($vv['month_repay_money']-$vv['self_money'] - $vv['manage_money'] - $vv['manage_interest_money']);
					//实际收益
					if($vv['has_repay']==1){
						$temp_user_load['load'][$kk]['real_money']=format_price($vv['month_repay_money']- $vv['self_money']+$vv['impose_money'] - $vv['manage_money']- $vv['manage_interest_money']);
					}
				}
				$user_load_ids[] = $temp_user_load;
			}
		}
		
		$GLOBALS['tmpl']->assign('user_load_ids',$user_load_ids);
		
		$inrepay_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id");
		$GLOBALS['tmpl']->assign("inrepay_info",$inrepay_info);
		
		$GLOBALS['tmpl']->assign("load_id",$load_id);
		$GLOBALS['tmpl']->assign("page_title","我的回款");
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_invest_refdetail.html");
		$GLOBALS['tmpl']->display("page/uc.html");	
    }
    
    public function mrefdetail(){
    	$user_id = $GLOBALS['user_info']['id'];
    	$id = intval($_REQUEST['id']);
    	$load_id = intval($_REQUEST['load_id']);
    	require APP_ROOT_PATH."app/Lib/deal.php";
    	$deal = get_deal($id);
    	if(!$deal || $deal['deal_status']<4){
    		showErr("无法查看，可能有以下原因！<br>1。借款不存在<br>2。借款被删除<br>3。借款未成功");
    	}
    	$GLOBALS['tmpl']->assign('deal',$deal);
    
    	$deal_load_list = get_deal_load_list($deal);
    
    	//获取本期的投标记录
		$temp_user_load = $GLOBALS['db']->getRow("SELECT id,deal_id,user_id,money FROM ".DB_PREFIX."deal_load WHERE deal_id=".$id." and id=".$load_id." and user_id=".$user_id);
		
		$user_load_ids = array();
		if($temp_user_load){
			$u_key = $GLOBALS['db']->getOne("SELECT u_key FROM ".DB_PREFIX."deal_load_repay WHERE load_id=".$load_id." and user_id=".$user_id);
			if($temp_user_load['user_id'] == $user_id){
				$temp_user_load['repay_start_time'] = $deal['repay_start_time'];
				$temp_user_load['repay_time'] = $deal['repay_time'];
				$temp_user_load['rate'] = $deal['rate'];
				$temp_user_load['u_key'] = $u_key;
				$temp_user_load['load'] = get_deal_user_load_list($deal, $user_id, -1 ,$u_key);
				$temp_user_load['impose_money'] =0;
				$temp_user_load['manage_fee'] = 0;
				$temp_user_load['repay_money'] = 0;
				$temp_user_load['manage_interest_money'] = 0;
				foreach($temp_user_load['load'] as $kk=>$vv){
					$temp_user_load['impose_money'] += $vv['impose_money'];
					$temp_user_load['manage_fee'] += $vv['manage_money'];
					$temp_user_load['repay_money'] += $vv['month_has_repay_money'];
					$temp_user_load['manage_interest_money'] += $vv['manage_interest_money'];
					
					//预期收益
					$temp_user_load['load'][$kk]['yuqi_money']=format_price($vv['month_repay_money']-$vv['self_money'] - $vv['manage_money'] - $vv['manage_interest_money']);
					//实际收益
					if($vv['has_repay']==1){
						$temp_user_load['load'][$kk]['real_money']=format_price($vv['month_repay_money']- $vv['self_money']+$vv['impose_money'] - $vv['manage_money']- $vv['manage_interest_money']);
						
					}
				}
				$user_load_ids[] = $temp_user_load;
			}
		}
    
    	$GLOBALS['tmpl']->assign('user_load_ids',$user_load_ids);
    
    	$inrepay_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id");
    	$GLOBALS['tmpl']->assign("inrepay_info",$inrepay_info);
    	
    	$GLOBALS['tmpl']->assign("load_id",$load_id);
    	$GLOBALS['tmpl']->assign("page_title","我的回款");
    	$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_invest_refdetail.html");
    	$GLOBALS['tmpl']->display("uc_invest_mrefdetail.html");
    }
    
    public function export_csv($page = 1)
    {
    	set_time_limit(0);
    	$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
    
    	$ac= strim($_REQUEST['ac']);  //"index".我的投资 "invite".招标的借款 "flow".流标的借款 "ing".回收中借款 "over".已回收借款 "bad".我的坏账
    	if(!in_array($ac,array("index","invite","flow","ing","over","bad"))){
    		showErr("无导出信息");
    	}
    	$result = getInvestList($ac,intval($GLOBALS['user_info']['id']),intval($_REQUEST['p']));
    	
    	$list = $result['list'];
    	//定义条件
    	if(!$list)
    	{
    		showErr("无导出信息");
    	}
    	foreach($list as $k=>$v){
    		
			$deal['borrow_amount'] = $v['u_load_money'];
			$deal['rate'] = $v['rate'];
			$deal['loantype'] = $v['loantype'];
			$deal['repay_time'] = $v['repay_time'];
	    	$deal['repay_time_type'] = $v['repay_time_type'];
	    	$deal['repay_start_time'] = $v['repay_start_time'];
			
			$deal_repay_rs = deal_repay_money($deal);
			
    		$v['interest_amount'] = $deal_repay_rs['month_repay_money'];
				
    		$list[$k] = $v;
    	}
    	
    	if($list)
    	{
    		register_shutdown_function(array(&$this, 'export_csv_1'), $page+1);
    		$repay_value = array('name'=>'""','user_name'=>'""','u_load_money'=>'""','repay_time'=>'""','rate'=>'""','rebate_money'=>'""','point_level'=>'""','progress_point'=>'""','deal_stauts'=>'""','transfer'=>'""');
    	
    		$content = "";
    		$contentss = iconv("utf-8","gbk","标题,借款人,投标金额,期限,利率,奖励,信用等级,进度,状态,是否转让");
    		$content  .= $contentss . "\n";
    		foreach($list as $k=>$v)
    		{
    			$deal_status = array("1"=>"筹标中","2"=>"已满标","3"=>"已流标","4"=>"回收中","5"=>"已回收");
    			$repay_value = array();
    			$repay_value['name'] = iconv('utf-8','gbk','" 第' . $v['name'] . '期"');
    			$repay_value['user_name'] = iconv('utf-8','gbk','"' . $v['user_name'] . '"');
    			$repay_value['u_load_money'] = iconv('utf-8','gbk','"' . format_price($v['u_load_money']) . '"');
    			$repay_value['repay_time'] = iconv('utf-8','gbk','"' . $v['repay_time'].($v['repay_time_type'] == 0? "天" : "个月") . '"');
    			$repay_value['rate'] = iconv('utf-8','gbk','"' . $v['rate']."%" . '"');
    			$repay_value['rebate_money'] = iconv('utf-8','gbk','"' . format_price($v['rebate_money']) . '"');
    			$repay_value['point_level'] = iconv('utf-8','gbk','"' . $v['point_level'] . '"');
    			$repay_value['progress_point'] = iconv('utf-8','gbk','"' . $v['progress_point']."%" . '"');
    			$repay_value['deal_stauts'] = iconv('utf-8','gbk','"' . $deal_status[$v['deal_status']]. '"');
    			if($v['has_transfer'] > 0 && $v['t_user_id'] <> $GLOBALS['user_info']['id']){
    				$repay_value['transfer'] = iconv('utf-8','gbk','"是"');
    			}
    			else{
    				$repay_value['transfer'] = iconv('utf-8','gbk','"否"');
    			}
    			$content .= implode(",", $repay_value) . "\n";
    		}
    			
    		header("Content-Disposition: attachment; filename=repay_list.csv");
    		echo $content;
    	}
    	else
    	{
    		if($page==1)
    			$this->error(L("NO_RESULT"));
    	}
    
    }
    
    function export_detail_csv(){
    	//277
    	$user_id = $GLOBALS['user_info']['id'];
		$id = intval($_REQUEST['id']);
		$load_id = intval($_REQUEST['load_id']);
		require APP_ROOT_PATH."app/Lib/deal.php";
		$deal = get_deal($id);
		if(!$deal || $deal['deal_status']<4){
			showErr("无法查看，可能有以下原因！<br>1。借款不存在<br>2。借款被删除<br>3。借款未成功");
		}
		$GLOBALS['tmpl']->assign('deal',$deal);
				
		//获取本期的投标记录
		$temp_user_load = $GLOBALS['db']->getRow("SELECT dl.id,dl.deal_id,dl.user_id,dl.money,dlt.t_user_id FROM ".DB_PREFIX."deal_load dl left join ".DB_PREFIX."deal_load_transfer dlt on dlt.load_id = dl.id WHERE dl.deal_id=".$id." and dl.id=".$load_id);
		
		//print_r("SELECT id,deal_id,user_id,money FROM ".DB_PREFIX."deal_load  WHERE deal_id=".$id." and id=".$load_id);die;
		
		$content = iconv("utf-8","gbk",'"'.$deal['name']." 投标的回款记录!".'"')."\n";
		$content .= iconv("utf-8","gbk","借款金额,年利率,期限,已还本息,管理费,利息管理费,逾期/违约,还款方式")."\n";
		$user_load_ids = array();
		if($temp_user_load){
			$u_key = $GLOBALS['db']->getOne("SELECT u_key FROM ".DB_PREFIX."deal_load_repay WHERE load_id=".$load_id." and (user_id=".$user_id." or t_user_id = ".$user_id.")");
			if(($temp_user_load["user_id"] == $user_id && intval($temp_user_load['t_user_id']) == 0 )|| $temp_user_load['t_user_id'] == $user_id){
				$temp_user_load['repay_start_time'] = $deal['repay_start_time'];
				$temp_user_load['repay_time'] = $deal['repay_time'];
				$temp_user_load['rate'] = $deal['rate'];
				$temp_user_load['u_key'] = $u_key;
				$temp_user_load['load'] = get_deal_user_load_list($deal, $user_id, -1 ,$u_key);
				$temp_user_load['impose_money'] =0;
				$temp_user_load['manage_fee'] = 0;
				$temp_user_load['repay_money'] = 0;
				$temp_user_load['manage_interest_money'] = 0;
				
				$list_content = "";
				foreach($temp_user_load['load'] as $kk=>$vv){
					$temp_user_load['impose_money'] += $vv['impose_money'];
					$temp_user_load['manage_fee'] += $vv['manage_money'];
					$temp_user_load['repay_money'] += $vv['month_has_repay_money'];
					$temp_user_load['manage_interest_money'] += floatval($vv['manage_interest_money']);
					
					//预期收益
					$temp_user_load['load'][$kk]['yuqi_money']=format_price($vv['month_repay_money']-$vv['self_money'] - $vv['manage_money'] - $vv['manage_interest_money']);
					//实际收益
					if($vv['has_repay']==1){
						$temp_user_load['load'][$kk]['real_money']=format_price($vv['month_repay_money']- $vv['self_money']+$vv['impose_money'] - $vv['manage_money']- $vv['manage_interest_money']);
						
					}
					$repay_value = array();
					$repay_value['repay_day'] = iconv("utf-8","gbk",'"'.to_date($vv['repay_day'],"Y-m-d").'"');
					$repay_value['true_repay_day'] = iconv("utf-8","gbk",'"'.to_date($vv['true_repay_time'],"Y-m-d").'"');
					$repay_value['month_has_repay_money'] = iconv("utf-8","gbk",'"'.format_price($vv['month_has_repay_money']).'"');
					$repay_value['manage_money'] = iconv("utf-8","gbk",'"'.format_price($vv['manage_money']).'"');
					$repay_value['manage_interest_money'] = iconv("utf-8","gbk",'"'.format_price($vv['manage_interest_money']).'"');
					$repay_value['impose_money'] = iconv("utf-8","gbk",'"'.format_price($vv['impose_money']).'"');
					$repay_value['yuqi_money'] = iconv("utf-8","gbk",'"'.format_price($temp_user_load['load'][$kk]['yuqi_money']).'"');
					$repay_value['real_money'] = iconv("utf-8","gbk",'"'.format_price($temp_user_load['load'][$kk]['real_money']).'"');
					$repay_value['status_format'] = iconv("utf-8","gbk",'"'.$vv['status_format'].'"');
					
					$list_content  .= implode(",", $repay_value) . "\n";
				}
				
				
				$content .=iconv("utf-8","gbk",'"'.format_price($temp_user_load['money']).'"').",";//借款金额
				$content .=iconv("utf-8","gbk",'"'.number_format($temp_user_load['rate'],2).'%"').",";//年利率
				$content .=iconv("utf-8","gbk",'"'.$deal['repay_time'].($deal['repay_time_type']==0 ? "天" :"个月").'"').",";//期限
				$content .=iconv("utf-8","gbk",'"'.format_price($temp_user_load['repay_money']).'"').",";//已还本息
				$content .=iconv("utf-8","gbk",'"'.format_price($temp_user_load['manage_fee']).'"').",";//管理费
				$content .=iconv("utf-8","gbk",'"'.format_price($temp_user_load['manage_interest_money']).'"').",";//利息管理费
				$content .=iconv("utf-8","gbk",'"'.format_price($temp_user_load['impose_money']).'"').",";//逾期/违约
				$content .=iconv("utf-8","gbk",'"'.loantypename($deal['loantype']).'"')."\n";//还款方式
				
				$content .="\n";
				$content .= iconv("utf-8","gbk","还款日,实际还款日,已回收本息,管理费,利息管理费,逾期/违约金,预期收益,实际收益,状态")."\n";
				
				$content .=$list_content;
			}
		}
		
		$GLOBALS['tmpl']->assign('user_load_ids',$user_load_ids);
		
		$inrepay_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal_inrepay_repay WHERE deal_id=$id");
		
		if($inrepay_info){
			$content .="\n";
			$content .= iconv("utf-8","gbk",'"因借款者在'.to_date($inrepay_info['true_repay_time'],"Y-m-d").'提前还款，故计算方式改变。"')."\n";
		}
		
		header("Content-Disposition: attachment; filename=repay_detail.csv");
    	echo $content;
		
    }
    
}
?>