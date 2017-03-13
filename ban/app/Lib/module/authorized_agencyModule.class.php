<?php

class agencyModule extends SiteBaseModule {

    function index() {
    	$id = intval($_REQUEST['id']);
    	
    	$authorizedagency = get_user_info("*","id=".$id);
    	if(!$authorizedagency|| $authorizedagency['is_effect']==0 || $authorizedagency['is_effect']!=3)
			app_redirect(url("index")); 
    	
    	$seo_title = $authorizedagency['short_name']!=''?$authorizedagency['short_name']: $authorizedagency['name'];
			
		$GLOBALS['tmpl']->assign("page_title",$seo_title);
		
		$seo_keyword = $seo_title;
		$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
		
		$seo_description = $authorizedagency['brief'];
		$GLOBALS['tmpl']->assign("seo_description",$seo_description.",");
		
		$GLOBALS['tmpl']->assign("agency",$authorizedagency);
		$GLOBALS['tmpl']->display("page/agency.html");
    }
}
?>