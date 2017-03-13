<?php
class init{
	public function index()
	{		
		$root = get_baseroot();
		$root['response_code'] = 1;
		
		//print_r($GLOBALS['db_conf']); exit;

		$root['kf_phone'] = $GLOBALS['m_config']['kf_phone'];//客服电话
		$root['kf_email'] = $GLOBALS['m_config']['kf_email'];//客服邮箱
		
		//$pattern = "/<img([^>]*)\/>/i";
		//$replacement = "<img width=300 $1 />";
		//$goods['goods_desc'] = preg_replace($pattern, $replacement, get_abs_img_root($goods['goods_desc']));
		//关于我们(填文章ID)
		$root['about_info'] = intval($GLOBALS['m_config']['about_info']);
		
		$root['db_version'] = app_conf("DB_VERSION"); //数据库版本号
		$root['version'] = VERSION; //接口版本号int
		$root['page_size'] = PAGE_SIZE;//默认分页大小
		$root['program_title'] = $GLOBALS['m_config']['program_title'];
		$root['site_domain'] = str_replace("/mapi", "", SITE_DOMAIN.APP_ROOT);//站点域名;
		$root['site_domain'] = str_replace("http://", "", $root['site_domain']);//站点域名;
		$root['site_domain'] = str_replace("https://", "", $root['site_domain']);//站点域名;
		//$root['newslist'] = $GLOBALS['m_config']['newslist'];
		
		$stats = site_statics();
		$root['stats'] = $stats;
		$root['virtual_money_1'] = strip_tags(number_format(floatval($stats['total_load'])/10000,3));//虚拟的累计成交额;
		$root['virtual_money_2'] = strip_tags(number_format(floatval($stats['total_rate'])/10000,3));//虚拟的累计创造收益;
		$root['virtual_money_3'] = strip_tags(number_format(floatval($stats['total_bzh'])/10000,3));//虚拟的本息保障金;
		
		$total_vs = $stats['total_load'];
		if($total_vs < $stats['total_rate'])
			$total_vs = $stats['total_rate'];
		if($total_vs < $stats['total_bzh'])
			$total_vs = $stats['total_bzh'];	
		$root['virtual_money_1_pos']  = intval($stats['total_load']*100/$total_vs);
		$root['virtual_money_2_pos']  = intval($stats['total_rate']*100/$total_vs);
		$root['virtual_money_3_pos']  = intval($stats['total_bzh']*100/$total_vs);
		
		
		$index_list = $GLOBALS['cache']->get("MOBILE_INDEX_ADVS");
		if($index_list===false)
		{
			$advs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_adv where status = 1 and `page`='top' order by sort desc ");
			$adv_list = array();
			foreach($advs as $k=>$v)
			{
				if ($v['img'] != ''){
					$v['img'] = get_abs_img_root(get_spec_image($v['img'],640,240,1));
					$adv_list[] = $v;	
				}
			}
			
			$index_list['adv_list'] = $adv_list;

			//===========特别推荐================
			//publish_wait 0:已审核 1:等待审核;deal_status 0待等材料，1进行中，2满标，3流标，4还款中，5已还清
			$condition = " publish_wait = 0 AND deal_status in (1,2,4,5) and is_new = 1 OR(is_advance=1 AND start_time > ".TIME_UTC.") and is_hidden = 0 ";
			
			if((int)app_conf("SHOW_EXPRIE_DEAL") == 0){
				$condition .= " AND (if(deal_status = 1, start_time + enddate*24*3600 > ".TIME_UTC .",1=1)) ";
			}
			
			require APP_ROOT_PATH.'app/Lib/deal.php';
			$limit = "0,5";
			$orderby = "deal_status ASC,sort DESC,id DESC";
			
			$result = get_deal_list($limit,0,$condition,$orderby);			
			foreach ( $result ['list'] as $m => $v )
			{
				$cate_info_icon = get_abs_wap_url_root(get_abs_img_root($result['list'][$m]['cate_info']['icon']));
				$result ['list'][$m]['cate_info']['icon'] = $cate_info_icon;
				
			}
			
			$index_list['rec_deal_list'] = $result['list'];
			//===========特别推荐END================
			
			//===========普通推荐================
			//publish_wait 0:已审核 1:等待审核;deal_status 0待等材料，1进行中，2满标，3流标，4还款中，5已还清
			$condition = " publish_wait = 0 AND deal_status in (1,2,4,5) and is_new = 0 and is_advance=0 AND start_time < ".TIME_UTC." and is_hidden = 0 ";
			
			if((int)app_conf("SHOW_EXPRIE_DEAL") == 0){
				$condition .= " AND (if(deal_status = 1, start_time + enddate*24*3600 > ".TIME_UTC .",1=1)) ";
			}
			
			require APP_ROOT_PATH.'app/Lib/deal.php';
			$limit = "0,5";
			$orderby = "deal_status ASC,sort DESC,id DESC";
			
			$result = get_deal_list($limit,0,$condition,$orderby);			
			foreach ( $result ['list'] as $m => $v )
			{
				$cate_info_icon = get_abs_wap_url_root(get_abs_img_root($result['list'][$m]['cate_info']['icon']));
				$result ['list'][$m]['cate_info']['icon'] = $cate_info_icon;
				
			}
			
			$index_list['deal_list'] = $result['list'];
			//===========普通推荐END================
			
			
			$GLOBALS['cache']->set("MOBILE_INDEX_ADVS",$index_list);
		}
		$root['index_list'] = $index_list;
		
		$root['deal_cate_list'] = getDealCateArray();//分类
		
		if(strim($GLOBALS['m_config']['sina_app_key'])!=""&&strim($GLOBALS['m_config']['sina_app_secret'])!="")
		{
			$root['api_sina'] = 1;
			$root['sina_app_key'] = $GLOBALS['m_config']['sina_app_key'];
			$root['sina_app_secret'] = $GLOBALS['m_config']['sina_app_secret'];
			$root['sina_bind_url'] = $GLOBALS['m_config']['sina_bind_url'];
		}
		if(strim($GLOBALS['m_config']['tencent_app_key'])!=""&&strim($GLOBALS['m_config']['tencent_app_secret'])!="")
		{
			$root['api_tencent'] = 1;
			$root['tencent_app_key'] = $GLOBALS['m_config']['tencent_app_key'];
			$root['tencent_app_secret'] = $GLOBALS['m_config']['tencent_app_secret'];
			$root['tencent_bind_url'] = $GLOBALS['m_config']['tencent_bind_url'];
		}

		output($root);
	}
}

function getDealCateArray(){
	//$land_list = FanweService::instance()->cache->loadCache("land_list");
		
		$sql = "select id, pid, name, icon from ".DB_PREFIX."deal_cate where pid = 0 and is_effect = 1 and is_delete = 0 order by sort desc ";
		//echo $sql; exit;
		$list = $GLOBALS['db']->getAll($sql);

	return $list;
}
?>