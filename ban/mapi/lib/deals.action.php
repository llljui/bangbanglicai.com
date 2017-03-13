<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class deals
{
	public function index(){
		//require APP_ROOT_PATH.'app/Lib/page.php';
		$page = intval($GLOBALS['request']['page']);
		if($page==0)
			$page = 1;
		
		$keywords = trim(htmlspecialchars($GLOBALS['request']['keywords']));
		$level = intval($GLOBALS['request']['level']);		
		$interest = intval($GLOBALS['request']['interest']);		
		$months = intval($GLOBALS['request']['months']);			
		$lefttime = intval($GLOBALS['request']['lefttime']);
		$deal_status = intval($GLOBALS['request']['deal_status']);
		
		$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");
		$level_list = load_auto_cache("level");
		$cate_id = intval($GLOBALS['request']['cid']);
		
		
		$n_cate_id = 0;
		$condition = " publish_wait = 0  and is_hidden = 0 ";
		$orderby = "";
		if($cate_id > 0){
			$n_cate_id = $cate_id;
			//$condition .= "AND deal_status in(0,1)";		
			$orderby = "update_time DESC ,sort DESC,id DESC";			
		}else{
			$n_cate_id = 0;
			$orderby = "update_time DESC , sort DESC , id DESC";
		}
		
		if($keywords){
			$kw_unicode = str_to_unicode_string($keywords);
			$condition .=" and (match(name_match,deal_cate_match,tag_match,type_match) against('".$kw_unicode."' IN BOOLEAN MODE))";			
		}
		
		if($level > 0){
			$point  = $level_list['point'][$level];
			$condition .= " AND user_id in(SELECT u.id FROM ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user_level ul ON ul.id=u.level_id WHERE ul.point >= $point)";
		}
		
		if($interest > 0){
			$condition .= " AND rate >= ".$interest;
		}
		
		if($months > 0){
			if($months==12)
				$condition .= " AND repay_time <= ".$months;
			elseif($months==18)
				$condition .= " AND repay_time >= ".$months;
		}
		
		if($lefttime > 0){
			$condition .= " AND (start_time + enddate*24*3600 - ".TIME_UTC.") <= ".$lefttime*24*3600;
		}
		
		if ($deal_status > 0){
			$condition .= " AND deal_status = ".$deal_status;
		}
		
		if((int)app_conf("SHOW_EXPRIE_DEAL") == 0){
			$condition .= " AND (if(deal_status = 1, start_time + enddate*24*3600 > ".TIME_UTC .",1=1)) ";
		}
				
		$result = get_deal_list($limit,$n_cate_id,$condition,$orderby);														
		
		foreach ( $result ['list'] as $k => $v )
		{
			$cate_info_icon = get_abs_wap_url_root(get_abs_img_root($result ['list'][$k]['cate_info']['icon']));
			$result ['list'][$k]['cate_info']['icon'] = $cate_info_icon;

		}
		
		$root = get_baseroot();
		$root['response_code'] = 1;
		$root['item'] = $result['list'];
		//$root['DEAL_PAGE_SIZE'] = app_conf("DEAL_PAGE_SIZE");
		$root['rs_count'] = $result['count'];
		$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
		$root['program_title'] = "投资列表";
		output($root);		
	}
}
?>
