<?php

class agencyModule extends SiteBaseModule {

    function index() {
    	$id = intval($_REQUEST['id']);
    	
    	$agency = get_user_info("*","id=".$id);
    	if(!$agency || $agency['is_effect']==0 || $agency['is_effect']!=2)
			app_redirect(url("index")); 
    	
    	$seo_title = $agency['short_name']!=''?$agency['short_name']: $agency['name'];
			
		$GLOBALS['tmpl']->assign("page_title",$seo_title);
		
		$seo_keyword = $seo_title;
		$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
		
		$seo_description = $agency['brief'];
		$GLOBALS['tmpl']->assign("seo_description",$seo_description.",");
		
		$GLOBALS['tmpl']->assign("agency",$agency);
		$GLOBALS['tmpl']->display("page/agency.html");
    }
}
?>