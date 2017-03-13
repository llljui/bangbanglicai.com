<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_interestrate_do_snexchange
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			
			$root['user_login_status'] = 1;		
			$root['response_code'] = 1;
			
			$sn = strim($GLOBALS['request']['sn']);
			$ecv_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."interestrate_type where exchange_sn = '".$sn."' and use_type in (1,2) and (end_time = 0 or end_time >= ".to_timespan(to_date(TIME_UTC,"Y-m-d")).")");
			if(!$ecv_type)
			{
				$root['status']=0;
				$root['response_code'] = 0;
				$root['show_err'] =$GLOBALS['lang']['INVALID_VOUCHER'];
			}
			else
			{
				$exchange_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."interestrate where ecv_type_id = ".$ecv_type['id']." and user_id = ".intval($GLOBALS['user_info']['id']));
				if($ecv_type['exchange_limit']>0&&$exchange_count>=$ecv_type['exchange_limit'])
				{
					$msg = sprintf($GLOBALS['lang']['EXCHANGE_VOUCHER_LIMIT'],$ecv_type['exchange_limit']);
					$root['status']=0;
					$root['response_code'] = 0;
					$root['show_err'] =$msg;
				}
				else
				{
					require_once APP_ROOT_PATH."system/libs/voucher.php";
					$rs = send_interestrate($ecv_type['id'],$GLOBALS['user_info']['id'],1);
					if($rs)
					{
						$root['status']=1;
						$root['response_code'] = 1;
						$root['show_err'] =$GLOBALS['lang']['EXCHANGE_SUCCESS'];
					}
					else
					{
						$root['status']=0;
						$root['response_code'] = 0;
						$root['show_err'] =$GLOBALS['lang']['EXCHANGE_FAILED'];
					}
				}
			}
			
		}else{
			$root['status']=0;
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "我的加息券";
		output($root);		
	}
}
?>
