<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_carry_revoke_apply
{
	public function index(){
		
		$root = get_baseroot();
		
		
		$page = intval($GLOBALS['request']['page']);
		$dltid = intval($GLOBALS['request']['dltid']);	
		$status = intval($GLOBALS['request']['status']);	
			
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require_once APP_ROOT_PATH.'app/Lib/uc_func.php';
			require_once APP_ROOT_PATH.'system/libs/user.php';
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			$nmc_amount = $GLOBALS['db']->getOne("SELECT nmc_amount FROM ".DB_PREFIX."user where id = ".$user_id);
			
			if($status == 0){
				$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user_carry SET status=4 where id=".$dltid." and status=0  and user_id = ".$user_id);
				if($GLOBALS['db']->affected_rows()){
					$data = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_carry where id=".$dltid." and status=4 and user_id = ".$user_id);
					modify_account(array('money'=>$data['money'],'lock_money'=>-$data['money']),$data['user_id'],"撤销提现,提现金额",8);
					modify_account(array('money'=>$data['fee'],'lock_money'=>-$data['fee']),$data['user_id'],"撤销提现，提现手续费",9);
					$root['show_err'] = "撤销操作成功";
				}
				else{
					$root['show_err'] = "撤销操作失败";
				}
				output($root);
			}elseif($status == 4){
				$data = $GLOBALS['db']->getRow("SELECT user_id,money,fee FROM ".DB_PREFIX."user_carry where id=".$dltid." and status=4 and user_id = ".$user_id);
				if(((float)$data['money'] + (float)$data['fee'] + (float)$user['nmc_amount']) > (float)$user['money']){
					$root['show_err'] = "继续申请提现失败,金额不足";
				}
				
				$sql = "UPDATE ".DB_PREFIX."user_carry SET status=0 where id=".$dltid." and (money + fee + $nmc_amount) <= ".(float)$user['money']." and status=4 and user_id = ".$user_id."  ";
				$root['sql'] = $sql;
				$GLOBALS['db']->query($sql);
				if($GLOBALS['db']->affected_rows()){
					modify_account(array('money'=>-$data['money'],'lock_money'=>$data['money']),$data['user_id'],"提现申请",8);
					modify_account(array('money'=>-$data['fee'],'lock_money'=>$data['fee']),$data['user_id'],"提现手续费",9);
					$root['show_err'] = "继续申请提现成功";
				}
				else{
					$root['show_err'] = "继续申请提现失败";
				}
				output($root);
			}else{
				$root['show_err'] = "操作失败";
				output($root);
			}
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "提现操作";
		output($root);		
	}
}
?>
