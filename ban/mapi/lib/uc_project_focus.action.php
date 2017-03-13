<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_project_focus
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new*/
		$root["program_title"]="关注的项目";
		
		if(!$user)
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		
		$page_size = intval(app_conf("DEAL_PAGE_SIZE"));;
		$page = intval($GLOBALS["request"]['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;
		
		$s = intval($GLOBALS["request"]['s']);
		if($s==3)
		$sort_field = " d.support_amount desc ";
		if($s==1)
		$sort_field = " d.support_count desc ";
		if($s==2)
		$sort_field = " d.support_amount - d.limit_price desc ";
		if($s==0)
		$sort_field = " d.end_time asc ";
		
		$root["s"]=$s;
		
		$f = intval($GLOBALS["request"]['f']);
		if($f==0)
		$cond = " 1=1 ";
		if($f==1)
		$cond = " d.begin_time < ".TIME_UTC." and (d.end_time = 0 or d.end_time > ".TIME_UTC.") ";
		if($f==2)
		$cond = " d.end_time <> 0 and d.end_time < ".TIME_UTC." and d.is_success = 1 "; //过期成功
		if($f==3)
		$cond = " d.end_time <> 0 and d.end_time < ".TIME_UTC." and d.is_success = 0 "; //过期失败
		$root["f"]=$f;
		
		
		
		$app_sql = " ".DB_PREFIX."project_focus_log as dfl left join ".DB_PREFIX."project as d on d.id = dfl.deal_id where dfl.user_id = ".$user_id." and d.is_effect = 1 and d.is_delete = 0 and ".$cond." ";
				
		
		$deal_list = $GLOBALS['db']->getAll("select d.*,dfl.id as fid from ".$app_sql." and d.type =0 order by ".$sort_field." limit ".$limit);
		$deal_count = $GLOBALS['db']->getOne("select count(*) from ".$app_sql." and d.type =0");
		
		foreach($deal_list as $k=>$v)
		{
			$deal_list[$k]['remain_days'] = ceil(($v['end_time'] - TIME_UTC)/(24*3600));
			$deal_list[$k]['percent'] = round($v['support_amount']/$v['limit_price']*100);
			if($v['type']== 0){
				
				$deal_list[$k]['support_amount']= $deal_list[$k]['support_amount']+ $deal_list[$k]['virtual_price'];
				$deal_list[$k]['percent'] = round($deal_list[$k]['support_amount']/$v['limit_price']*100,2);
				$deal_list[$k]['support_count']= $deal_list[$k]['support_count']+ $deal_list[$k]['virtual_num'];
			}
			if($v['type']== 1){
				$deal_list[$k]['percent']= round($v['invote_money']/$v['limit_price']*100,2);
			}
			$deal_list[$k]['image'] = get_abs_img_root($v['image']);
		}
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($deal_count/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));


		$root['deal_list']=$deal_list;
		
		
		output($root);		
	}
}
?>
