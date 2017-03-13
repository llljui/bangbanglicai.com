<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class do_user_wx_register
{	
	
	public function wx_register(){
		if($GLOBALS['user_info']){
			app_redirect(url_wap("index#index"));
		}
		 
//		$GLOBALS['tmpl']->assign('wx_info',$GLOBALS['wx_info']);
//		$GLOBALS['tmpl']->display("user_wx_register.html");
		$root['wx_info'] = $GLOBALS['wx_info'];
	}
	//手机验证修改密码=====================================================================================
	public function phone_update_password()
	{
		$mobile = strim($_REQUEST['mobile']);
		$user_pwd = strim($_REQUEST['user_pwd']);
		$confirm_user_pwd=strim($_POST['confirm_user_pwd']);
		$settings_mobile_code1=strim($_POST['code']);
	
		if(!$mobile)
		{
			$data['status'] = 0;
			$data['info'] = "手机号码为空";
			ajax_return($data);
		}
	
		if($settings_mobile_code1==""){
			$data['status'] = 0;
			$data['info'] = "手机验证码为空";
			ajax_return($data);
		}
	
		if($user_pwd==""){
			$data['status'] = 0;
			$data['info'] = "密码为空";
			ajax_return($data);
		}
	
		if($user_pwd!==$confirm_user_pwd){
			$data['status'] = 0;
			$data['info'] = "两次密码不一致";
			ajax_return($data);
		}
	
		//判断验证码是否正确=============================
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile=".$mobile." AND verify_code='".$settings_mobile_code1."'")==0){
	
			$data['status'] = 0;
			$data['info'] = "手机验证码错误";
			ajax_return($data);
		}
	
	
		if($user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile =".$mobile))
		{
				
			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user SET user_pwd='".md5($user_pwd.$user_info['code'])."' where mobile=".$mobile);
			$result = 1;  //初始为1
			$data['status'] = 1;
			$data['info'] = "密码修改成功";
			ajax_return($data);//密码修改成功
		}
		else{
			$data['status'] = 0;
			$data['info'] = "没有该手机账户";
			ajax_return($data);//密码修改成功
		}
	}
	public function send_mobile_verify_code()
	{
		$mobile = addslashes(htmlspecialchars(trim($GLOBALS['request']['mobile'])));
		
		$root = array();
	
		if(app_conf("SMS_ON")==0)
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['SMS_OFF'];
			output($root);
		}
	
	
		if($mobile == '')
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['MOBILE_EMPTY_TIP'];
			output($root);
		}
	
		if(!check_mobile($mobile))
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['FILL_CORRECT_MOBILE_PHONE'];
			output($root);
		}
	

		if(!check_ipop_limit(CLIENT_IP,"mobile_verify",60,0))
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['MOBILE_SMS_SEND_FAST'];
			output($root);
		}

		
		//$sql = "select id,bind_verify from ".DB_PREFIX."user where mobile = '".$mobile."' and is_delete = 0";
		$sql = "select id,bind_verify from ".DB_PREFIX."user where mobile_encrypt = AES_ENCRYPT('".$mobile."','".AES_DECRYPT_KEY."') and is_delete = 0";
		$user_info = $GLOBALS['db']->getRow($sql);
		$user_id = intval($user_info['id']);
		$code = intval($user_info['bind_verify']);
	
		if($user_id == 0)
		{
			//$field_show_name = $GLOBALS['lang']['USER_TITLE_mobile'];
			$root['response_code'] = 0;
			$root['show_err'] = '手机号码不存在或被禁用';
			output($root);
		}
	

		//开始生成手机验证
		if ($code == 0){
			//已经生成过了，则使用旧的验证码；反之生成一个新的
			$code = rand(1111,9999);
			$GLOBALS['db']->query("update ".DB_PREFIX."user set bind_verify = '".$code."',verify_create_time = '".TIME_UTC."' where id = ".$user_id);
		}
	
		//使用立即发送方式
		$result = send_verify_sms($mobile,$code,null,true);//
		$root['response_code'] = $result['status'];
		
		if ($root['response_code'] == 1){
			$root['show_err'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		}else{
			$root['show_err'] = $result['msg'];
			if ($root['show_err'] == null || $root['show_err'] == ''){
				$root['show_err'] = "验证码发送失败";
			}
		}
		$root['post_type']=trim($GLOBALS['request']['post_type']);
		output($root);
		
//		$mobile = addslashes(htmlspecialchars(trim($_REQUEST['mobile'])));
//		//is_only 为1的话，表示不允许手机号重复
//		$is_only=intval($_REQUEST['is_only']);
//		if($mobile == '')
//		{
//			$data['status'] = 0;
//			$data['info'] = "请输入你的手机号";
//			//ajax_return($data);
//			$root['info'] = 1111111;
//			output($root);	
//		}
//		
//		if(!check_mobile($mobile))
//		{
//			$data['status'] = 0;
//			$data['info'] = "请填写正确的手机号码";
//			//ajax_return($data);
//			$root['info'] = $data;
//		}
//		
//		if($is_only==1){
//			$condition_1=" and mobile='".$mobile."' ";
//			if($GLOBALS['user_info']['id']){
//				$condition_1.=" and id!=".$GLOBALS['user_info']['id'];
//			}
//			if($GLOBALS['db']->getOne("select count(*) from  ".DB_PREFIX."user where 1=1 $condition_1 ")>0){
//				$data['status'] = 0;
//				$data['info'] = "该手机号已经存在";
//				ajax_return($data);
//			}
//		}
//			
// 		$field_name = addslashes(trim($_REQUEST['mobile']));
//		$field_data = $mobile;
//		require_once APP_ROOT_PATH."system/libs/user.php";
//		$res = check_user($field_name,$field_data);
//		
//		$result = array("status"=>1,"info"=>'');
//		if(!$res['status'])
//		{
//			$error = $res['data'];		
//			if(!$error['field_show_name'])
//			{
//				$error['field_show_name'] = "手机号码";
//			}
//			if($error['error']==EMPTY_ERROR)
//			{
//				$error_msg = sprintf("手机号码不能为空",$error['field_show_name']);
//			}
//			if($error['error']==FORMAT_ERROR)
//			{
//				$error_msg = sprintf("格式错误，请重新输入",$error['field_show_name']);
//			}
//			if($error['error']==EXIST_ERROR)
//			{
//				$error_msg = sprintf("已存在，请重新输入",$error['field_show_name']);
//			}
//			$result['status'] = 0;
//			$result['info'] = $error_msg;
//			ajax_return($result);
//		}
//		
//		
//		if(!check_ipop_limit(get_client_ip(),"mobile_verify",60,0))
//		{
//			$data['status'] = 0;
//			$data['info'] = "发送速度太快了";
//			ajax_return($data);
//		}
//		
//		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."' and client_ip='".get_client_ip()."' and create_time>=".(get_gmtime()-60)." ORDER BY id DESC") > 0)
//		{
//			$data['status'] = 0;
//			$data['info'] = "发送速度太快了";
//			ajax_return($data);
//		}
//		$n_time=get_gmtime()-300;
//		//删除超过5分钟的验证码
//		$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."mobile_verify_code WHERE create_time <=".$n_time);
//		//开始生成手机验证
//		$code = rand(100000,999999);
//		$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",array("verify_code"=>$code,"mobile"=>$mobile,"create_time"=>get_gmtime(),"client_ip"=>get_client_ip()),"INSERT");
//	
//		send_verify_sms($mobile,$code);
//		$data['status'] = 1;
//		$data['info'] = "验证码发送成功";
//		ajax_return($data);
//		output($root);	
	}
	
	public function wx_do_register()
	{
		
		$user_info=array();
		$user_info['mobile'] = strim($_REQUEST['mobile']);
 		$user_info['verify_coder']=strim($_REQUEST['code']);
		$user_info['wx_openid']=strim($_REQUEST['wx_openid']);
		$user_info['user_name']=strim($_REQUEST['user_name']);
		$user_info['province']=strim($_REQUEST['province']);
		$user_info['email']=strim($_REQUEST['email']);
		$user_info['city']=strim($_REQUEST['city']);
		$user_info['sex']=strim($_REQUEST['sex']);
		if(!$user_info['mobile'])
		{
			$data['status'] = 0;
			$data['info'] = "手机号码为空";
			ajax_return($data);
			
		}
		
	
		if($user_info['verify_coder']==""){
			$data['status'] = 0;
			$data['info'] = "手机验证码为空";
			ajax_return($data);
		}
		//判断验证码是否正确=============================
		if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile=".$user_info['mobile']." AND verify_code='".$user_info['verify_coder']."'")==0){
 			$data['status'] = 0;
			$data['info'] = "手机验证码错误";
			ajax_return($data);
		}
		$user=get_user_has('mobile',$user_info['mobile']);
		require_once APP_ROOT_PATH."system/libs/user.php";
		if($user){
			$GLOBALS['db']->query("update ".DB_PREFIX."user set wx_openid='".$user_info['wx_openid']."' where id=".$user['id']);
 			$user_id = $user['id'];	
 		}else{
 			if(!$user_info['email'])
			{
				$data['status'] = 0;
				$data['info'] = "邮箱为空";
				ajax_return($data);
			}
			if(!check_email($user_info['email'])){
				$data['status'] = 0;
				$data['info'] = "邮箱格式错误";
				ajax_return($data);
			}
			$has_email=get_user_has('email',$user_info['email']);
			if($has_email){
				$data['status'] = 0;
				$data['info'] = "邮箱已存在，请重新填写";
				ajax_return($data);
			}
			$has_user_name=get_user_has('user_name',$user_info['user_name']);
			if($has_user_name){
				$user_info['user_name']=$user_info['user_name'].rand(10000,99999);
			}
			
 			
 			if($user_info['sex']==0){
 				$user_info['sex']=-1;
 			}elseif($user_info['sex']==1){
 				$user_info['sex']=1;
 			}else{
 				$user_info['sex']=0;
 			}
 			//开启邮箱验证
            if(app_conf("USER_VERIFY")==0||app_conf("USER_VERIFY")==2){
                 $user_info['is_effect'] = 1;
            }else{
            	$user_info['is_effect'] = 0;
            }
 			
 			$user_info['create_time'] = get_gmtime();
			$user_info['update_time'] = get_gmtime();
			//新建用户 使用验证码作为密码
			$user_info['user_pwd']=$user_info['verify_coder'];
			//$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info,"INSERT");
 			$res = save_user($user_info);
 			 
			$user_id = intval($res['data']);	
 		}
 		 
  			$user_info_new = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
 			if($user_info_new['is_effect']==1)
			{
 				$result = do_login_user($user_info_new['mobile'],$user_info_new['user_pwd']);
  				ajax_return(array("status"=>1,"info"=>$result['msg'],"jump"=>url_wap("index")));
			}
			else
			{
                if(app_conf("USER_VERIFY")==1){
                    ajax_return(array("status"=>1,"jump"=>url_wap("user#mail_check",array('uid'=>$user_id))));
                }else if(app_conf("USER_VERIFY")==3){
                	ajax_return(array("status"=>0,"info"=>"请等待管理员审核"));
                }
					
			}                     
 	}
 	
 	
	
}
?>
