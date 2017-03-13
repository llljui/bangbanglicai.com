<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_project_support
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new*/
		$root["program_title"]="支持的项目";
		$page_size = intval(app_conf("DEAL_PAGE_SIZE"));
		$page = intval($GLOBALS["request"]['page']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;

		$order_list = $GLOBALS['db']->getAll("select po.*,pi.repaid_day from ".DB_PREFIX."project_order po  left join ".DB_PREFIX."project_item pi on po.deal_item_id = pi.id where po.user_id = ".$user_id." and (po.type=0 or po.type=2) order by po.create_time desc limit ".$limit);
		
		foreach($order_list as $k=>$v){
			if($v['repay_make_time']==0&&$v['repay_time']>0){
				
				$left_date=intval($v['repaid_day']) >0 ? intval($v['repaid_day']) :intval(app_conf("REPAY_MAKE"));
				
				$repay_make_date=$v['repay_time']+$left_date*24*3600;
				if($repay_make_date<=TIME_UTC){
 					$GLOBALS['db']->query("update ".DB_PREFIX."project_order set repay_make_time =  ".TIME_UTC." where id = ".$v['id'] );
					$order_list[$k]['repay_make_time']=TIME_UTC;
				}
			}
		}	
		
		$order_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_order where user_id = ".$user_id." and (type=0 or type=2)");
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($order_count/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
		
		$deal_ids=array();
		
		foreach($order_list as $k=>$v){
			$deal_ids[] =  $v['deal_id'];
		}
		
		if($deal_ids!=null){
			$deal_list_array=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."project where  is_effect = 1 and is_delete = 0 and id in (".implode(',',$deal_ids).")  and (type=0 or type=2) ");
		
			$deal_list=array();
			foreach($deal_list_array as $k=>$v){
				if($v['id']){
					$v["image"] = get_abs_url_root($v["image"]);
					$deal_list[$v['id']]=$v;
				}
			}
			
			foreach($order_list as $k=>$v)
			{
	 			$order_list[$k]['deal_info'] =$deal_list[$v['deal_id']];
			}
			
			$root['order_list']=$order_list;
		}
		
		$root['now']=TIME_UTC;
		
		output($root);		
	}
}
?>
