<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class licai
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		require_once APP_ROOT_PATH.'system/libs/licai.php';
		
		//本周畅销
		$week_bestseller_list = get_licai_list("re_type=3 and status=1","sort DESC",1);
		$root['week_bestseller_list'] = $week_bestseller_list['list'];

		//限时抢购
		$limit_rush_list = get_licai_list("re_type=4 and status=1","sort DESC",1);
		$root['limit_rush_list'] = $limit_rush_list['list'];

		//新品上架
		$news_list = get_licai_list("re_type=1 and status=1","sort DESC",1);
		$root['news_list'] = $news_list['list'];

		//获取普通推荐
		$rectype_list = get_licai_list("re_type > 0 and status=1 and is_recommend = 1 and status = 1","sort DESC,id DESC",3);
		$root['rectype_list'] = $rectype_list['list'];
		
		//获取特殊推荐
		$special_list = get_special_licais(" s.status=1 and a.status=1 ","s.sort DESC,s.id DESC",3);
		$root['special_list'] = $special_list;
		
		//余额宝
		$balance_list = get_licai_list("type = 0 and status=1 ","sort DESC,id DESC",5);
		$root['balance_list'] = $balance_list['list'];
		
		//定存宝
		$deposit_list = get_licai_list("type = 1 and status=1 ","sort DESC,id DESC",3);
		$root['deposit_list'] = $deposit_list['list'];
		
		//浮动定存
		$float_list = get_licai_list("type = 2 and status=1 ","sort DESC,id DESC",3);
		$GLOBALS['tmpl']->assign("float_list",$float_list['list']);
		$root['float_list'] = $float_list['list'];
 		
		//获取最近购买的
		$licai_dealshow =  get_licai_dealshow(10);
		$root['licai_dealshow'] = $licai_dealshow;
		
		
		$licai['url']=url("collocation#RegisterCat",array('user_id'=>$user_id));
		
		$root['licai'] = $licai;
		 
		$root['program_title'] = "理财";
		output($root);		
	}
}
?>
