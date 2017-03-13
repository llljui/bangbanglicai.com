<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

define(MODULE_NAME,"index");
require APP_ROOT_PATH.'app/Lib/deal.php';
class indexModule extends SiteBaseModule
{
	public function index()
	{
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 180;  //首页缓存3分钟
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);	
		if (!$GLOBALS['tmpl']->is_cached("page/index.html", $cache_id))
		{	
			change_deal_status();
			
			if((int)app_conf("SHOW_EXPRIE_DEAL") == 0){
				$extW = " AND (if(deal_status = 1, start_time + enddate*24*3600 > ".TIME_UTC .",1=1)) ";
			}
			
			//借款预告列表
			$advance_deal_list =  get_deal_list(5,0,"publish_wait =0 AND deal_status =1 AND is_advance=1 AND start_time >".TIME_UTC." and is_hidden = 0 ".$extW," deal_status ASC, is_recommend DESC,sort DESC,id DESC");
			$GLOBALS['tmpl']->assign("advance_deal_list",$advance_deal_list['list']);
			
			//最新借款列表
			$deal_list =  get_deal_list(5,0,"publish_wait =0 AND deal_status in(1,2,4) AND is_new=1 AND start_time <=".TIME_UTC." and is_hidden = 0 ".$extW," deal_status ASC, is_recommend DESC,sort DESC,id DESC");
			$GLOBALS['tmpl']->assign("new_deal_list",$deal_list['list']);
			
			
			//最新借款列表
			$deal_list =  get_deal_list(11,0,"publish_wait =0 AND deal_status in(1,2,4) AND start_time <=".TIME_UTC." and is_hidden = 0 ".$extW," deal_status ASC, is_recommend DESC,sort DESC,id DESC");
			$GLOBALS['tmpl']->assign("deal_list",$deal_list['list']);
			
			//输出最新转让
			$transfer_list =  get_transfer_list(11," and d.deal_status >= 4  AND dlt.status=1  ",'',''," d.create_time DESC , dlt.id DESC ");
			$GLOBALS['tmpl']->assign('transfer_list',$transfer_list['list']);
						
			//输出公告
			$notice_list = get_notice(0);
			$GLOBALS['tmpl']->assign("notice_list",$notice_list);	
			
			//输出公司动态
			$art_id =  $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."article_cate where title='公司动态'");
		
			if($art_id > 0){
				$compnay_active_list  = get_article_list(5,$art_id);
				$GLOBALS['tmpl']->assign("art_id",$art_id);
				$GLOBALS['tmpl']->assign("compnay_active_list",$compnay_active_list['list']);	
			}
			
			//输出媒体报道
			$mtbd_id =  $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."article_cate where is_delete=0 and title='媒体报道'");
				
			if($mtbd_id > 0){
				$mtbd_list  = get_article_list(5,$mtbd_id);
				foreach($mtbd_list['list'] as $k => $v) {
					$mtbd_list['list'][$k]['contents'] = msubstr($mtbd_list['list'][$k]['content'],0,25) ;
				}
				
				$GLOBALS['tmpl']->assign("mtbd_id",$mtbd_id);
				$GLOBALS['tmpl']->assign("mtbd_list",$mtbd_list['list']);
			}
			
			//投资排行
			//天
			$now_time = to_date(TIME_UTC,"Y-m-d");
			$day_load_top_list =  $GLOBALS['db']->getAll("SELECT * FROM (SELECT user_name,sum(money) as total_money FROM ".DB_PREFIX."deal_load where create_date = '".$now_time."' and is_repay= 0   group by user_id ORDER BY total_money DESC) as tmp LIMIT 10");
			
			//周
			$week_time_start =  to_date(TIME_UTC - to_date(TIME_UTC,"w") * 24*3600 ,"Y-m-d");
			$week_load_top_list =  $GLOBALS['db']->getAll("SELECT * FROM (SELECT user_name,sum(money) as total_money FROM ".DB_PREFIX."deal_load where create_date in (".date_in($week_time_start,to_date(TIME_UTC,"Y-m-d")).") and is_repay= 0   group by user_id ORDER BY total_money DESC) as tmp LIMIT 10 ");
			//月
			$month_time_start = to_date(TIME_UTC,"Y-m")."-01";
			$month_load_top_list =  $GLOBALS['db']->getAll("SELECT * FROM (SELECT user_name,sum(money) as total_money FROM ".DB_PREFIX."deal_load where create_date in (".date_in($month_time_start,to_date(TIME_UTC,"Y-m-d")).") and is_repay= 0   group by user_id ORDER BY total_money DESC) as tmp LIMIT 10");
			//总
			$all_load_top_list =  $GLOBALS['db']->getAll("SELECT * FROM (SELECT user_name,sum(money) as total_money FROM ".DB_PREFIX."deal_load where  is_repay= 0  group by user_id ORDER BY total_money DESC) as tmp LIMIT 10");
			
			
			$GLOBALS['tmpl']->assign("day_load_top_list",$day_load_top_list);	
			$GLOBALS['tmpl']->assign("week_load_top_list",$week_load_top_list);	
			$GLOBALS['tmpl']->assign("month_load_top_list",$month_load_top_list);	
			$GLOBALS['tmpl']->assign("all_load_top_list",$all_load_top_list);	
			
			//收益排名
			$load_repay_list = $GLOBALS['db']->getAll("SELECT us.*,u.user_name FROM ".DB_PREFIX."user_sta us LEFT JOIN ".DB_PREFIX."user u ON us.user_id=u.id WHERE u.is_effect =1 and u.is_delete=0 and us.load_earnings > 0  ORDER BY us.load_earnings DESC LIMIT 5");
			$GLOBALS['tmpl']->assign("load_repay_list",$load_repay_list);	
			
			//使用技巧
			$use_tech_list  = get_article_list(12,6);
			$GLOBALS['tmpl']->assign("use_tech_list",$use_tech_list);	
			
			$now = TIME_UTC;
			$vote = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vote where is_effect = 1 and begin_time < ".$now." and (end_time = 0 or end_time > ".$now.") order by sort desc limit 1");
			$GLOBALS['tmpl']->assign("vote",$vote);
			
			$stats = site_statics();
			$GLOBALS['tmpl']->assign("stats",$stats);
			
			$near_deal_loads = get_near_deal_loads("0,8");
			$GLOBALS['tmpl']->assign("near_deal_loads",$near_deal_loads);
			
			//格式化统计代码
			$VIRTUAL_MONEY_1_FORMAT =  format_conf_count($stats['total_load']);
			$VIRTUAL_MONEY_2_FORMAT =  format_conf_count($stats['total_rate']);
			$VIRTUAL_MONEY_3_FORMAT =  format_conf_count($stats['total_bzh']);
			$GLOBALS['tmpl']->assign("VIRTUAL_MONEY_1_FORMAT",$VIRTUAL_MONEY_1_FORMAT);
			$GLOBALS['tmpl']->assign("VIRTUAL_MONEY_2_FORMAT",$VIRTUAL_MONEY_2_FORMAT);
			$GLOBALS['tmpl']->assign("VIRTUAL_MONEY_3_FORMAT",$VIRTUAL_MONEY_3_FORMAT);
			
			$GLOBALS['tmpl']->assign("show_site_titile",1);
		}
		
		$GLOBALS['tmpl']->display("page/index.html",$cache_id);
	}
}	
?>