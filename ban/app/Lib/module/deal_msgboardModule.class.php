<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
require APP_ROOT_PATH.'app/Lib/uc.php';
class deal_msgboardModule extends SiteBaseModule
{
	public function index()
	{	
		$loan_type_list = load_auto_cache("deal_loan_type_list");
    	foreach($loan_type_list as $k=>$v){
    		if($v['credits']!=""){
    			$loan_type_list[$k]['credits'] = unserialize($v['credits']);
    			if(!is_array($loan_type_list[$k]['credits'])){
					$loan_type_list[$k]['credits'] = array();
				}
    		}
    		else
    			$loan_type_list[$k]['credits'] = array();
    	}
    	
    	$GLOBALS['tmpl']->assign('usefulness_type_list',$loan_type_list);
		
		$GLOBALS['tmpl']->display("page/deal_msgboard_index.html",$cache_id);
	}
	
	public function savedeal()
	{
		$is_ajax = intval($_REQUEST['is_ajax']);

    	$verify_code = strim($_REQUEST['verify_code']);
		
		$data = array();
		$data["user_name"] = strim($_REQUEST["user_name"]);
		$data["ID_NO"] = strim($_REQUEST["ID_NO"]);
		$data["mobile"] = strim($_REQUEST["mobile"]);
		$data["money"] = floatval($_REQUEST["money"]);
		$data["time_limit"] = strim($_REQUEST["time_limit"]);
		$data["usefulness"] = $GLOBALS["db"]->getOne("select name from ".DB_PREFIX."deal_loan_type where id = ".intval($_REQUEST["usefulness"]));
		
		$data["create_time"] = to_date(TIME_UTC);
		$data["status"] = 0;
		$data["unit"] = intval($_REQUEST["unit"]);
		
		if($verify_code==""){
			showErr("请输入手机验证码");
		}
		//判断验证码是否正确
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".strim($data['mobile'])."' AND verify_code='".strim($verify_code)."' AND create_time + ".SMS_EXPIRESPAN." > ".TIME_UTC." ")==0){
			showErr("手机验证码出错,或已过期");
		}		
		
		if($data["user_name"]=="")
		{
			showErr("请填写您的真实姓名",$ajax,"");
		}
		if($data["ID_NO"]=="")
		{
			showErr("请填写您的身份证号码",$ajax,"");
		}
		if($data["mobile"]=="")
		{
			showErr("请填写您的手机号",$ajax,"");
		}
		if($data["money"] <= 0)
		{
			showErr("请填写贷款金额",$ajax,"");
		}
		if($data["time_limit"]=="")
		{
			showErr("请填写贷款期限",$ajax,"");
		}
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msgboard",$data,'INSERT','','SILENT');
		
		showSuccess("提交成功,等待管理员审核",0,url("index","deal_msgboard#index"));
	}


	public function get_verify_code()
	{
		if(app_conf("SMS_ON")==0)
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['SMS_OFF'];
			ajax_return($data);
		}
		$user_mobile = strim($_REQUEST['user_mobile']);
			
		if($user_mobile == '')
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_MOBILE_EMPTY'];
			ajax_return($data);
		}
	
		if(!check_mobile($user_mobile))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			ajax_return($data);
		}
	
		if(!check_ipop_limit(CLIENT_IP,"register_mobile_verify",60,0))
		{
			$data['status'] = 0;
			$data['info'] = $GLOBALS['lang']['VERIFY_CODE_SEND_FAST'];
			ajax_return($data);
		}
	
	
		$begin_time = to_timespan(to_date(TIME_UTC,"Y-m-d"));
		
		if($GLOBALS['db']->getOne("SELECT send_count FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'  AND  create_time between ".$begin_time." and  ".($begin_time+24*3600)."") >= SEND_VERIFYSMS_LIMIT){
			$data['status'] = 0;
			$data['info'] = "你今天已经不能再发验证码了";
			ajax_return($data);
		}
		
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE client_ip='".CLIENT_IP."'  AND  create_time >=".(TIME_UTC - 60)."  ") > 0){
			$data['status'] = 0;
			$data['info'] = "请稍后再试";
			ajax_return($data);
		}
	
	
		//开始生成手机验证
		$verify_data['verify_code'] = rand(111111,999999);
		$verify_data['mobile'] = $user_mobile;
		$verify_data['create_time'] = TIME_UTC;
		$verify_data['client_ip'] = CLIENT_IP;
		$verify_data['send_count'] = 1;
		
		if($info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$user_mobile."'")){
			if($info['create_time'] < $begin_time){
				$verify_data['send_count'] = 1;
			}
			else{
				$verify_data['send_count'] = $info['send_count'] + 1;
			}
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"UPDATE","id=".$info['id']);	
		}
		else
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",$verify_data,"INSERT");
		
		send_verify_sms($user_mobile,$verify_data['verify_code'],$GLOBALS['user_info'],true);
		$data['status'] = 1;
		$data['info'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		ajax_return($data);
	}
}
?>