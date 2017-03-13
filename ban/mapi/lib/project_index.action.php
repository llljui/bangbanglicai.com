<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require_once APP_ROOT_PATH.'app/Lib/project_func.php';
class project_index
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new*/				
		/*虚拟的累计项目总个数，支持总人数，项目支持总金额*/ 
	 	$virtual_effect = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project where is_effect = 1 and is_delete=0");
	 	$virtual_person =  $GLOBALS['db']->getOne("select sum((support_count+virtual_person)) from ".DB_PREFIX."project_item");
	 	$virtual_money =  $GLOBALS['db']->getOne("select sum((support_count+virtual_person)*price) from ".DB_PREFIX."project_item");

	 	$root['virtual_effect'] = $virtual_effect;//项目总个数
		$root['virtual_person'] = $virtual_person;//累计支持人
		$root['virtual_money'] =number_format($virtual_money,2);//筹资总金额
		
		/*项目显示以及权限控制*/
		//===============首页项目列表START===================
		$page_size = app_conf("PAGE_SIZE");

		$limit="  0,".$page_size." ";
			
		$condition = " d.is_recommend=1 ";
 		$now_time = TIME_UTC;
		$deal_result= get_project_list($limit,$condition," d.sort asc ",'project',0);
		
 		$deal_list = $deal_result['list'];
		$deal_count =  $deal_result['rs_count'];
		
		//获取当前项目列表下的所有子项目
		$root["count"] = $deal_count;
		$root["list"] = $deal_list;

		$cate_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_cate where pid =0 order by sort asc");
	
		$root["cate_list"] = $cate_list;
		/*end new*/
		 
		$root['user_income'] = $user_income;
		$root['program_title'] = "众筹列表";
		
		output($root);		
	}
}
?>
