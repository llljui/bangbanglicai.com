<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_op_list
{
	public function index(){
		
			$root = get_baseroot();
			$user =  $GLOBALS['user_info']; 
			$root['session_id'] = es_session::id();
			$user_id  = intval($user['id']);
			$id = intval($GLOBALS['request']['id']);
			$tp = intval($GLOBALS['request']['tp']);
			if ($user_id >0){
			
			$root['peizi_order_id'] = $id;
			$root['user_name'] = $user['user_name'];
			require_once APP_ROOT_PATH.'system/libs/peizi.php';	
			$page = intval($GLOBALS['request']['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			/*操盘列表*/
			if($tp == 1){
				$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order_op where  peizi_order_id=".$id." ");
				$op_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id=".$id." order by id desc ");		
				$root['tp'] = $tp;
			}else{
				$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order_op where user_id = ".$user_id." and peizi_order_id=".$id." ");
				$op_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_op where user_id = ".$user_id." and peizi_order_id=".$id." order by id desc ");		
			}
			
			foreach($op_list as $k => $v)
			{
				
				$op_list[$k]["op_type_format"] = get_peizi_op_type($v["op_type"]);
				$op_list[$k]["op_status_format"] = get_peizi_op_status($v["op_status"],$v["op_type"]);
				
				if($v["op_status"] == 3 || $v["op_status"] == 4)
				{
					$op_list[$k]["op_date"] = $v["op_date2"];
				}
				else
				{
					$op_list[$k]["op_date"] = $v["op_date1"];
				}
			}
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			$root['op_list'] = $op_list;
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
			
		$root['program_title'] = "操盘明细";
		
		output($root);		
		
	}
}
?>
