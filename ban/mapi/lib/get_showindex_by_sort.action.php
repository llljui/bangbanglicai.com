<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class get_showindex_by_sort
{
	public function index(){
		
		$root = array();

		$by_sort = intval($GLOBALS['request']["by_sort"]);
		
		$by_time = intval($GLOBALS['request']["by_time"]);
		
		$order = "";
		$where = "";
		if($by_sort == 0)
		{
			$order = " order by a.money desc ";
		}
		elseif($by_sort == 1)
		{
			$order = " order by a.rate desc ";
		}
		
		if ($by_time > 0){
			$where = " where a.peizi_conf_id = ".$by_time;
		}
			
		
		//$show_sql = "select * from ".DB_PREFIX."peizi_indexshow ".$where.$order;
			
		$show_sql = "select a.*,b.type,b.name as conf_name from ".DB_PREFIX."peizi_indexshow a left join ".DB_PREFIX."peizi_conf b on b.id = a.peizi_conf_id ".$where.$order;
			
			$show_list = $GLOBALS['db']->getAll($show_sql);
			foreach($show_list as $k => $v)
			{
				if($v["type"] == 0)
				{
					$show_list[$k]["url"] = url("index","peizi#everwin",array('id'=>$v['peizi_conf_id']));
 				}
				elseif($v["type"] == 1)
				{
					$show_list[$k]["url"] = url("index","peizi#weekwin",array('id'=>$v['peizi_conf_id']));
				}
				else
				{
					$show_list[$k]["url"] = url("index","peizi#scheme",array('id'=>$v['peizi_conf_id']));
				}							
			}
		
		//print_r($peizi_list);exit;
//		$GLOBALS['tmpl']->assign("indexshow_list",$show_list);
//		$GLOBALS['tmpl']->assign("by_sort",$by_sort);
//		$GLOBALS['tmpl']->assign("by_time",$by_time);
//		$GLOBALS['tmpl']->display("peizi/indexshow_inc.html");
		
		$root['indexshow_list'] = $show_list;
		$root['by_sort'] = $by_sort;
		$root['by_time'] = $by_time;	
			
		$root['program_title'] = "股票配资排序";
		output($root);		
	}
}
?>
