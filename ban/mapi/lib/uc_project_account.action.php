<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_project_account
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;

		/*new*/
		$root["page_title"]="我的项目列表";
		if(!$user)
		{
			app_redirect(url("user#login"));	
		}

		$page_size = app_conf("DEAL_PAGE_SIZE");
		$page = intval($GLOBALS["request"]['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*$page_size).",".$page_size;
		
  		$more_search=intval($GLOBALS["request"]['more_search']);
 		$root['more_search']=$more_search;
 		$parameter[]="more_search=".$more_search;
		
		$deal_name=strim($GLOBALS["request"]['deal_name']);
		
		$condition = " 1=1 ";
		
		if(!empty($deal_name)){
			$condition .= " and  name like '%$deal_name%' ";
			$parameter[]="deal_name=".$deal_name;
			$root['deal_name']=$deal_name;
		}
		
		$deal_status=intval($GLOBALS["request"]['deal_status']);
		if($deal_status>0){
			switch($deal_status){
				//待审核
				case 1:
				$condition .=" and  is_effect=0 and is_edit =0";
				break;
				//进行中
				case 2:
				$condition .=" and is_effect=1 and begin_time<".TIME_UTC." and  end_time>".TIME_UTC." ";
				break;
				//已成功
				case 3:
				$condition .= " and is_success=1 and is_effect=1 and  end_time<".TIME_UTC." ";
				break;
				//已失败
				case 4:
				$condition .= " and  is_success=0 and is_effect=1 and  end_time<".TIME_UTC." ";
				break;
				//未通过
				case 5:
				$condition .=" and  is_effect=2 ";
				break;
				//预热中
				case 6:
				$condition .=" and  is_effect=1 and is_success=0 and begin_time >".TIME_UTC."";
				break;
			}
			$parameter[]="deal_status=".$deal_status;
			$root['deal_status']=$deal_status;
		}
		
		$give_status=intval($GLOBALS["request"]['give_status']);
		if($give_status>0){
			if($deal_status==3){
				switch($give_status){
					//已发放
					case 1:
					$condition .=" and  left_money=0 ";
					break;
					//未发放
					case 2:
					$condition .=" and  left_money>0 ";
					break;
				}
			}
			else
			{
				switch($give_status){
					//已发放
					case 1:
					$condition .=" and  left_money=0  and  is_success= 1";
					break;
					//未发放
					case 2:
					$condition .=" and  left_money>0 and  is_success= 1";
					break;
				}
			}
			$parameter[]="give_status=".$give_status;
			$root['give_status']=$give_status;
		}
		
		$begin_time=strim($GLOBALS["request"]['begin_time']);
 		if($begin_time!=0){
 			$begin_time=to_timespan($begin_time,'Y-m-d');
 			$condition.=" and  create_time>=$begin_time ";
 			$root['begin_time']=to_date($begin_time,'Y-m-d');
 			$parameter[]="begin_time=".to_date($begin_time,'Y-m-d');
 		}
		
		$end_time=strim($GLOBALS["request"]['end_time']);
 		if($end_time!=0){
 			$end_time=to_timespan($end_time,'Y-m-d');
 			$condition.=" and create_time<=$end_time ";
 			$root['end_time']=to_date($end_time,'Y-m-d');
 			$parameter[]="end_time=".to_date($end_time,'Y-m-d');
 		}
		
		$deal_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project where $condition and user_id = ".$user_id." and is_delete = 0 order by id desc,create_time desc limit ".$limit);
		
		$deal_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project where $condition and user_id = ".$user_id." and is_delete = 0");
		
		$deal_cp_sum = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project where type=0 and user_id = ".$user_id." and is_delete = 0");
		$root['deal_cp_sum']=$deal_cp_sum;
		
		foreach($deal_list as $k=>$v)
		{
			$deal_list[$k]['remain_days'] = ceil(($v['end_time'] - TIME_UTC)/(24*3600));
			$deal_list[$k]['percent'] = round($v['support_amount']/$v['limit_price']*100,2);
			if($v['type']== 0){
				$deal_list[$k]['support_count']= $deal_list[$k]['support_count']+ $deal_list[$k]['virtual_num'];
			}
			$is_lottery=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_item where deal_id=".$v['id']." and type=2");
			$deal_list[$k]['is_lottery']=$is_lottery>0?1:0;
			
			$deal_list[$k]['image'] = get_abs_url_root($v['image']);

		}
		
		$parameter_str="&".implode("&",$parameter);
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($deal_count/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
		
		$root['deal_list']=$deal_list;
		
		$root['program_title'] = "回报项目(".($deal_cp_sum>0?$deal_cp_sum:"0").")";
		
		$root['now']=TIME_UTC;
		
		output($root);		
	}
}
?>
