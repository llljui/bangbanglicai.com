<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/crowd_func.php';
class crowdModule extends SiteBaseModule
{
	
	public function show(){
		
		 
		$GLOBALS['tmpl']->display("page/crowd.html");
	}
 	
}
?>
