<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_scores_log
{
	public function index(){
		
		$root = get_baseroot();
		
		$page = intval($GLOBALS['request']['page']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/uc_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			/*$title_arrays = array(
					"100" => "全部",
					"0" => "结存",
					"1" => "充值",
					"2" => "投标成功",
					"3" => "招标成功",
					"8" => "申请提现",
					"9" => "提现手续费",
					"13" => "人工操作",
					"18" => "开户奖励",
					"22" => "兑换",
					"25" => "签到成功",
					"28"=>"投资奖励 ",
					"29"=>"红包奖励 "
				);*/
			$title_arrays = load_auto_cache("cache_money_type",array("class"=>"score"));
			
			$result = get_user_score_log($limit,$GLOBALS['user_info']['id'],-1); //会员积分日志
			
			foreach($result['list'] as $k=>$v){
				$result['list'][$k]['title'] = $title_arrays[$v['type']];
			}
			
			$list = $result['list'];

			$root['item'] = $list;
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "我的积分";
		output($root);		
	}
}
?>
