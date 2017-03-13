<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class belenderModule  extends SiteBaseModule{

    function index() {
		if(!$GLOBALS['user_info']){
			set_gopreview();
			app_redirect(url("index","user#login"));
			exit();
		}
		$info['title'] = "成为借出者";
		
		if($GLOBALS['user_info']['is_borrow_out']==0){
			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user SET is_borrow_out=1 WHERE id=".$GLOBALS['user_info']['id']);
		}
		
		$seo_title = $info['seo_title']!=''?$info['seo_title']:$info['title'];
		$GLOBALS['tmpl']->assign("page_title",$seo_title);
		$seo_keyword = $info['seo_keyword']!=''?$info['seo_keyword']:$info['title'];
		$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
		$seo_description = $info['seo_description']!=''?$info['seo_description']:$info['title'];
		$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
		
		$GLOBALS['tmpl']->display("page/belender.html");
    }
}
?>