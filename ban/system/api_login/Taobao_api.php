<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------

$api_lang = array(
	'name'	=>	'支付宝快捷登录',
	'app_key'	=>	'合作者身份ID',
	'app_secret'	=>	'安全检验码',
);

$config = array(
	'app_key'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //腾讯API应用的KEY值
	'app_secret'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //腾讯API应用的密码值
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
	if(ACTION_NAME=='install')
	{
		//更新字段
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `taobao_id`  varchar(255) NOT NULL",'SILENT');
	}
    $module['class_name']    = 'Taobao';

    /* 名称 */
    $module['name']    = $api_lang['name'];

	$module['config'] = $config;
	
	$module['lang'] = $api_lang;
    
    return $module;
}

// QQ的api登录接口
require_once(APP_ROOT_PATH.'system/libs/api_login.php');
class Taobao_api implements api_login {
	
	private $api;
	
	public function __construct($api)
	{
		$api['config'] = unserialize($api['config']);
		$this->api = $api;
	}
	
	public function get_api_url()
	{
		es_session::start();
		es_session::set("taobao_app_key",$this->api['config']['app_key']);
		es_session::set("taobao_app_secret",$this->api['config']['app_secret']);
		es_session::set("taobao_callback",SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Taobao");
		es_session::set("is_bind",0);
		$url = SITE_DOMAIN.APP_ROOT."/system/api_login/taobao/redirect.php";	
		$str = "<a href='".$url."' title='".$this->api['name']."'><img src='".$this->api['icon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}
	
	public function get_big_api_url()
	{
		es_session::start();
		es_session::set("taobao_app_key",$this->api['config']['app_key']);
		es_session::set("taobao_app_secret",$this->api['config']['app_secret']);
		es_session::set("taobao_callback",SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Taobao");
		es_session::set("is_bind",0);
		$url = SITE_DOMAIN.APP_ROOT."/system/api_login/taobao/redirect.php";	
		$str = "<a href='".$url."' title='".$this->api['name']."'><img src='".$this->api['bicon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}	
	
	public function get_bind_api_url()
	{
		es_session::start();
		es_session::set("taobao_app_key",$this->api['config']['app_key']);
		es_session::set("taobao_app_secret",$this->api['config']['app_secret']);
		es_session::set("taobao_callback",SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Taobao");
		es_session::set("is_bind",1);
		$url = SITE_DOMAIN.APP_ROOT."/system/api_login/taobao/redirect.php";	
		$str = "<a href='".$url."' title='".$this->api['name']."'><img src='".$this->api['bicon']."' alt='".$this->api['name']."' /></a>&nbsp;";
		return $str;
	}	
	
	public function callback()
	{
		es_session::start();	
		$aliapy_config['partner']		= $this->api['config']['app_key'];
		$aliapy_config['key']			=  $this->api['config']['app_secret'];
		$aliapy_config['return_url']   = SITE_DOMAIN.APP_ROOT."/api_callback.php?c=Taobao";
		$aliapy_config['sign_type']    = 'MD5';
		$aliapy_config['input_charset']      = 'utf-8';
		$aliapy_config['transport']    = 'http';
		require_once APP_ROOT_PATH."system/api_login/taobao/alipay_notify.class.php";
		
		unset($_GET['c']);
		$alipayNotify = new AlipayNotify($aliapy_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
		    $user_id	= $_GET['user_id'];	//支付宝用户id
		    $token		= $_GET['token'];	//授权令牌
			$real_name=$_GET['real_name'];
		
			//执行商户的业务程序
			$msg['id'] = $user_id;
			$msg['name'] = $real_name;			
			$msg['field'] = 'taobao_id';
			es_session::set("api_user_info",$msg);
			$is_bind = intval(es_session::get("is_bind"));
	
			$user_data = get_user_info("*","taobao_id = '".$msg['id']."' and taobao_id <> ''");	
			if($user_data)
			{
					$user_current_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".intval($user_data['group_id']));
					$user_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where score <=".intval($user_data['score'])." order by score desc");
					if($user_current_group['score']<$user_group['score'])
					{
						$user_data['group_id'] = intval($user_group['id']);
					}
					es_session::set("user_info",$user_data);
					//$GLOBALS['db']->query("update ".DB_PREFIX."user set login_ip = '".CLIENT_IP."',login_time= ".TIME_UTC.",group_id=".intval($user_data['group_id'])." where id =".$user_data['id']);				
					//$GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set user_id = ".intval($user_data['id'])." where session_id = '".es_session::id()."'");
					es_session::delete("api_user_info");
					
					if($is_bind)
					{
						if(intval($user_data['id'])!=intval($GLOBALS['user_info']['id']))
						{
							showErr("该帐号已经被别的会员绑定过，请直接用帐号登录",0,url("shop","uc_center#setweibo"));
						}
						else
						{
							es_session::set("user_info",$user_data);
							app_redirect(url("shop","uc_center#setweibo"));
						}
					}
					else
					{
						require_once APP_ROOT_PATH."system/libs/user.php";
						auto_do_login_user($user_data['user_name'],md5($user_data['user_pwd']."_EASE_COOKIE"),$from_cookie = false);
						app_recirect_preview();
					}
			}
			elseif($is_bind==1&&$GLOBALS['user_info'])
			{
				//当有用户身份且要求绑定时
				$GLOBALS['db']->query("update ".DB_PREFIX."user set taobao_id= '".$msg['id']."' where id =".$GLOBALS['user_info']['id']);						
				app_redirect(url("index","uc_center#setweibo"));
			}
			else{
				$this->create_user();
				//app_redirect(url("index","user#api_login"));
				app_recirect_preview();
			}
			
			
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
		    //验证失败
		    //如要调试，请看alipay_notify.php页面的return_verify函数，比对sign和mysign的值是否相等，或者检查$veryfy_result有没有返回true
		   echo "验证失败";
		}
		
	}
	
	public function get_title()
	{
		return '支付宝快捷登录';
	}
	public function create_user()
	{
		$s_api_user_info = es_session::get("api_user_info");
		$user_data['user_name'] = $s_api_user_info['name'];
		$user_data['user_pwd'] = md5(rand(100000,999999));
		$user_data['create_time'] = TIME_UTC;
		$user_data['update_time'] = TIME_UTC;
		$user_data['login_ip'] = CLIENT_IP;
		$user_data['group_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_group order by score asc limit 1");
		$user_data['is_effect'] = 1;
		$user_data['taobao_id'] = $s_api_user_info['id'];
		$origin_username = $user_data['user_name'];
		$count = 0;
		do{
			if($count>0)
			$user_data['user_name'] = $origin_username.TIME_UTC;
			if($user_data['taobao_id'])
			$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_data,"INSERT",'','SILENT');
			$rs = $GLOBALS['db']->insert_id();
			$count++;
		}while(intval($rs)==0&&$user_data['taobao_id']);
		
		$user_info = get_user_info("*","id = ".intval($rs));
        if($rs > 0)
        {
            $user_id = $rs; 
            $data_time = to_date(TIME_UTC);
            send_register_reward($user_id,$data_time,$user_info['pid']);
        }
		es_session::set("user_info",$user_info);
		es_session::delete("api_user_info");
	}	
}
?>