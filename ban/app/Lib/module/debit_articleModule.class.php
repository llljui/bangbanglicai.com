<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
class debit_articleModule extends SiteBaseModule
{
	public function index()
	{			
		$ajax = intval($_REQUEST["is_ajax"]);
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim($_REQUEST['title']).$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('page/article_index.html', $cache_id))	
		{
			$article_name = urldecode($_REQUEST['title']);
			$article = get_article_buy_uname($article_name);
			if($article)
			{
				$article_list = get_article_list(12,$article["cate_id"],""," sort asc ");
			}
			if($article["title"] =="")
			{
				$article["title"] = $article_name;
			}
			$cate_info = $GLOBALS["db"]->getRow("select id,title from ".DB_PREFIX."article_cate where id = '".$article["cate_id"]."'");
			$GLOBALS['tmpl']->assign("article",$article);
			$GLOBALS['tmpl']->assign("cate_name",$cate_info["title"]);
			$GLOBALS['tmpl']->assign("article_list",$article_list["list"]);
			$seo_title = $article['seo_title']!=''?$article['seo_title']:$article['title'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $article['seo_keyword']!=''?$article['seo_keyword']:$article['title'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $article['seo_description']!=''?$article['seo_description']:$article['title'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
		}
		$GLOBALS['tmpl']->assign("ajax",$ajax);
		$GLOBALS['tmpl']->display("debit/debit_article.html",$cache_id);
	}
	// 	http://localhost/daikuang/debit.php?ctl=debit_article&act=help_center
	public function help_center()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.trim("debit_help_center").$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('page/article_index.html', $cache_id))	
		{
			$cate_info = $GLOBALS["db"]->getRow("select id,title from ".DB_PREFIX."article_cate where title like'%".strim($_REQUEST["title"])."%'");

			$art_list = get_article_list(12,$cate_info["id"],""," sort asc ");

			$article = $art_list["list"][0];
			
			$GLOBALS['tmpl']->assign("article",$article);
			$GLOBALS['tmpl']->assign("cate_name",$cate_info["title"]);
			$GLOBALS['tmpl']->assign("article_list",$art_list["list"]);
			$seo_title = $article['seo_title']!=''?$article['seo_title']:$article['title'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $article['seo_keyword']!=''?$article['seo_keyword']:$article['title'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $article['seo_description']!=''?$article['seo_description']:$article['title'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
		}
		$GLOBALS['tmpl']->display("debit/debit_article.html",$cache_id);
	}
}
?>