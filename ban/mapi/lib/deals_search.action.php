<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class deals_search
{
	function index(){
		$root = get_baseroot();
		$root['response_code'] = 1;
		$root['program_title'] = "投资搜索";
		
		$level_list = load_auto_cache("level");
		$root["level_list"] = $level_list['list'];
		output($root);		
	}
}
?>
