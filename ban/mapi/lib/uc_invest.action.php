<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/uc_func.php';
class uc_invest
{
	public function index(){
		
		$root = get_baseroot();
		
		$page = intval($GLOBALS['request']['page']);
		
		$mode = strim($GLOBALS['request']['mode']);//index,invite,ing,over,bad
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$status = intval($GLOBALS['request']['status']);
			$root['status'] = $status;
			/*
			 * $status 1 进行中,2还款中,3已还清,4满标,5流标,0或其他 默认为全部
			 */
			if(isset($status) && $status=="1"){
				$result = getInvestList($mode = "in",$user_id,$page,$user['user_name'],$user['user_pwd']);	//进行中
			}elseif(isset($status) && $status=="2"){
				$result = getInvestList($mode = "ing",$user_id,$page,$user['user_name'],$user['user_pwd']); //还款中
			}elseif(isset($status) && $status=="3"){
				$result = getInvestList($mode = "over",$user_id,$page,$user['user_name'],$user['user_pwd']); //已还清
			}elseif(isset($status) && $status=="4"){
				$result = getInvestList($mode = "full",$user_id,$page,$user['user_name'],$user['user_pwd']); //满标
			}elseif(isset($status) && $status=="5"){
				$result = getInvestList($mode = "flow",$user_id,$page,$user['user_name'],$user['user_pwd']); //流标
			}else{
				$result = getInvestList($mode,$user_id,$page,$user['user_name'],$user['user_pwd']);
			}
			
			 //修改描述字段
			$list_coyp=$result['list'];
			 foreach ($list_coyp  as $key => $value) {
			$list_coyp[$key]['remain_time']=$list_coyp[$key]['start_time'] + $list_coyp[$key]['enddate']*24*3600-TIME_UTC ;
			$list_coyp[$key]['description_ios']=$list_coyp[$key]['description'];
			unset($list_coyp[$key]['descreption']);
				//当为天的时候
				if($list_coyp[$key]['repay_time_type'] == 0){
					$true_repay_time = 1;
				}
				else{
					$true_repay_time = $list_coyp[$key]['repay_time'];
				}
				
				$deal['borrow_amount'] = $list_coyp[$key]['u_load_money'];
				$deal['rate'] = $list_coyp[$key]['rate'];
				$deal['loantype'] = $list_coyp[$key]['loantype'];
				$deal['repay_time'] = $list_coyp[$key]['repay_time'];
		    	$deal['repay_time_type'] = $list_coyp[$key]['repay_time_type'];
		    	$deal['repay_start_time'] = $list_coyp[$key]['repay_start_time'];
				
				$deal_repay_rs = deal_repay_money($deal);
				
	    		$v['interest_amount'] = $deal_repay_rs['month_repay_money'];
					
	    		$list[$key] = $list_coyp[$key];
			}
			$root['item'] = $list_coyp;
			$root['page'] = array("page"=>$page,"page_total"=>ceil($result['count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "我的投资";
		output($root);		
	}
}
?>
