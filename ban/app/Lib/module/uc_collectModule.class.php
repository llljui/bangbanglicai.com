<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_collectModule extends SiteBaseModule
{
	public function index()
	{
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = get_collect_list($limit,$GLOBALS['user_info']['id']);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_COLLECT']);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_collect_index.html");
	
				
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function del()
	{
		$id = intval($_REQUEST['id']);
		$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_collect where id = ".$id." and user_id = ".intval($GLOBALS['user_info']['id']));
		if($GLOBALS['db']->affected_rows())
		{
			showSuccess($GLOBALS['lang']['DELETE_SUCCESS']);
		}
		else
		{
			showErr($GLOBALS['lang']['INVALID_COLLECT']);
		}
	}
	
	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		$result = get_collect_list($limit,$GLOBALS['user_info']['id']);
	
		$list = $result['list'];
		//定义条件
		if(!$list)
		{
			showErr("无导出信息");
		}
	
		foreach($list as $k=>$v){
			if($list[$k]['repay_time_type'] == 0){
				$list[$k]['repay_time'] = $list[$k]['repay_time']."天";
			}else{
				$list[$k]['repay_time'] = $list[$k]['repay_time']."个月";
			}
		}
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv_1'), $page+1);
			$repay_value = array('name'=>'""','user_name'=>'""','type_match_row'=>'""','borrow_amount_format'=>'""','repay_time'=>'""','rate'=>'""','point_level'=>'""','progress_point'=>'""');
	
			$content = "";
			$contentss = iconv("utf-8","gbk","标题,借款人,借款用途,金额,期限,利率,信用等级,进度");
			$content  .= $contentss . "\n";
			foreach($list as $k=>$v)
			{
				$repay_value = array();
				$repay_value['name'] = iconv('utf-8','gbk','"' . $v['name'] . '"');
				$repay_value['user_name'] = iconv('utf-8','gbk','"' . $v['user_name'] . '"');
				$repay_value['type_match_row'] = iconv('utf-8','gbk','"' . $v['type_match_row'] . '"');
				$repay_value['borrow_amount_format'] = iconv('utf-8','gbk','"' . $list[$k]['borrow_amount_format'] . '"');
				$repay_value['repay_time'] = iconv('utf-8','gbk','"' . $list[$k]['repay_time'] . '"');
				$repay_value['rate'] = iconv('utf-8','gbk','"' . $v['rate']."%" . '"');
				$repay_value['point_level'] = iconv('utf-8','gbk','"' . $list[$k]['point_level'] . '"');
				$repay_value['progress_point'] = iconv('utf-8','gbk','"' . (($v['deal_status'] >4 ? 100 : $v['progress_point'])."%") . '"');
				$content .= implode(",", $repay_value) . "\n";
			}
	
			header("Content-Disposition: attachment; filename=guangzhu.csv");
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