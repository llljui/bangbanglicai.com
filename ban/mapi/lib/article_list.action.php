<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class article_list
{
	public function index(){
		
		//分页
		$page = intval($GLOBALS['request']['page']);
		if($page==0)
			$page = 1;
		
		$root = get_baseroot();
		
		$cate_id =intval($GLOBALS['m_config']['article_cate_id']);
		
		//分页
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$sql = "select id,title from ".DB_PREFIX."article where is_effect = 1 and is_delete = 0 and cate_id = ".$cate_id." order by sort DESC ";
		$sql.=" limit ".$limit;
		
		$sql_count = "select count(*) from ".DB_PREFIX."article where is_effect = 1 and is_delete = 0 and cate_id = ".$cate_id;
		

		$count = $GLOBALS['db']->getOne($sql_count);
		$list = $GLOBALS['db']->getAll($sql);
	
	
						
		$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));	
		
		$root['response_code'] = 1;
		$root['list'] = $list;
		$root['program_title'] = "文章列表";
		output($root);		
	}
}
?>
