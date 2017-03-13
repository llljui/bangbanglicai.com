<?php 
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------


/**
 非明确的操作时,禁止提交如下几个参数名(因为这个参数名，会被覆盖)
 $request_param['city_id']=$city_id;
 $request_param['city_name']=$city_name;

 $request_param['uid']=es_session::get("uid");
 $request_param['pwd']=es_session::get("user_pwd");
 $request_param['email']=es_session::get("user_name");

 $request_param['supplier_id']=es_session::get("supplier_id");
 $request_param['biz_email']=es_session::get("biz_email");
 $request_param['biz_pwd']=es_session::get("biz_pwd");

 $request_param['m_latitude']= es_session::get("m_latitude");
 $request_param['m_longitude']= es_session::get("m_longitude");
 */

define('TMPL_NAME','fanwe');

require '../system/common.php';
require './lib/page.php';
require './lib/functions.php';
require './lib/transport.php';
require './lib/template.php';

if(!defined("APP_INDEX"))
	define("APP_INDEX","index");
	
define('AS_LOG_DIR', APP_ROOT_PATH.'wap/log/');
define('AS_DEBUG', true);
require './lib/logUtils.php';

if (es_cookie::is_set("is_pc")){
	es_cookie::delete("is_pc");
}



$transport = new transport;
$transport->use_curl = true;
//调用模板引擎
//require_once  APP_ROOT_PATH.'system/template/template.php';
if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/',0777);

if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/tpl_caches/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/tpl_caches/',0777);

if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/tpl_compiled/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/tpl_compiled/',0777);

if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/statics/'))
	mkdir(APP_ROOT_PATH.'public/runtime/wap/statics/',0777);

$tmpl = new WapTemplate;
$tmpl->template_dir   = APP_ROOT_PATH . 'wap/tpl/'.TMPL_NAME;
$tmpl->cache_dir      = APP_ROOT_PATH . 'public/runtime/wap/tpl_caches';
$tmpl->compile_dir    = APP_ROOT_PATH . 'public/runtime/wap/tpl_compiled';
$tmpl->assign("TMPL_REAL", APP_ROOT_PATH . 'wap/tpl/'.TMPL_NAME);
//定义模板路径
$tmpl_path = SITE_DOMAIN.APP_ROOT.'/tpl/'.TMPL_NAME;
$tmpl->assign("TMPL",$tmpl_path);



$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
if($HTTP_REFERER == ""){
	$HTTP_REFERER = APP_ROOT;
}
$tmpl->assign("HTTP_REFERER", $HTTP_REFERER);

//访问wap时，去除使用pc端访问，标志
if (isset($_COOKIE["is_pc"])){
	setcookie ("is_pc", null);
	setcookie ("is_pc", time()-3600);
	unset($_COOKIE["is_pc"]);
}

if (isset($_REQUEST['i_type']))
{
	$i_type = intval($_REQUEST['i_type']);
}

//$_REQUEST = array_merge($_GET,$_POST);
$request_param = $_REQUEST;



if(isset($request_param['ctl'])){
	$class = strtolower(strim($request_param['ctl']));
		
}else{
	$class='init';
}

if(isset($request_param['act'])){
$act2 = strtolower(strim($request_param['act']))?strtolower(strim($request_param['act'])):"";
}else{
	$act2='index';
}

$sessid = $request_param['session_id'];

if (empty($sessid)){
	$sessid = es_session::id();
	//$request['session_id'] = $sessid;
	$request_param['session_id'] = $sessid;
}

es_session::set_sessid($sessid);


if(strim($_REQUEST['ctl']) =='login_out'){

	require_once APP_ROOT_PATH."system/libs/user.php";
	$result = loginout_user();
	if($result['status'])
	{
		es_cookie::delete("user_name");
		es_cookie::delete("user_pwd");
		es_session::delete("user_info");
		$result['data'] = $result['msg'];
	}
	
	$result['status'] = 1;
	$result['info'] = '退出成功!';
	$result['jump'] = wap_url('index','init');
	echo json_encode($result);die();
}


$is_weixin=isWeixin();
$m_config = getMConfig();//初始化手机端配置
//用户登陆处理;
user_login();
$user_info = es_session::get('user_info');


//存储邀请人的id
if($_REQUEST['r'])
{
    $rid = intval(base64_decode($_REQUEST['r']));
    if($rid > 0){
        $ref_uid = intval($GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where id = ".$rid));
        if($ref_uid > 0){
            es_cookie::set("REFERRAL_USER",intval($ref_uid));
            $request_param["r"] = $_REQUEST['r'];
        }
    }
}
else
{
    //获取存在的推荐人ID
    if(intval(es_cookie::get("REFERRAL_USER"))>0){
        $request_param["r"] = str_replace('+', '%2b',base64_encode($ref_uid));
    } 
}

$is_sdk_browser= 0;
if(strpos(strtolower($_SERVER['ALL_HTTP']), 'fanwe_app_sdk') !== false)   
	$is_sdk_browser++;   
if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'fanwe_app_sdk') !== false)   
	$is_sdk_browser++;

if($is_sdk_browser > 0){
	$request_param['is_sdk'] = 1;
}

if($user_info && $class == 'register'){
	app_redirect(wap_url("index","uc_center"));
}

if($user_info && $class == 'login'){
	app_redirect(wap_url("index","uc_center"));
}


// && ($class == 'login' || $class == 'register')
if ($is_weixin && !$user_info ){
	$GLOBALS['tmpl']->assign("is_weixin",$is_weixin);
	$request_param['is_weixin'] = 1;
	require_once APP_ROOT_PATH.'system/utils/weixin.php';
	
	//file_put_contents(APP_ROOT_PATH."wap/log/test_".strftime("%Y%m%d%H%M%S",time())."_".$class."_.txt",print_r($_REQUEST,true));
	
	if($_REQUEST['code']&&$_REQUEST['state']==1&&$m_config['wx_appid']&&$m_config['wx_secrit'] &&!$user_info){
		//require_once APP_ROOT_PATH.'system/model/user.php';
		require_once APP_ROOT_PATH.'system/libs/user.php';
		$weixin=new weixin($m_config['wx_appid'],$m_config['wx_secrit'],get_domain().APP_ROOT."/wap/index.php");
		global $wx_info;
		$wx_info=$weixin->scope_get_userinfo($_REQUEST['code']);
		
		//file_put_contents(APP_ROOT_PATH."wap/log/wx_info".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($wx_info,true));
		
		$GLOBALS['tmpl']->assign('wx_info',$wx_info);
		//用户未登陆
		if($wx_info['openid']){
			
			$wx_user_info=get_user_info('*',"wx_openid='".$wx_info['openid']."'");
	
			if($wx_user_info){
				//如果会员存在，直接登录
				//do_login_user($wx_user_info['mobile'],$wx_user_info['user_pwd']);
				$s_user = "";
				if($wx_user_info['user_name']!=""){
					$s_user =$wx_user_info['user_name'];
				}
				if($s_user=="" && $wx_user_info['email']!=""){
					$s_user =$wx_user_info['email'];
				}
				
				if($s_user==""){
					$s_user =$wx_user_info['mobile'];
				}
				
				wx_auto_do_login_user($s_user,$wx_user_info['user_pwd'],false);
				
				//file_put_contents(APP_ROOT_PATH."wap/log/REQUEST_URI".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($_SERVER["REQUEST_URI"],true));
			}else{
				//file_put_contents(APP_ROOT_PATH."wap/log/test2_".strftime("%Y%m%d%H%M%S",time())."_".$class."_.txt",print_r($_SERVER["REQUEST_URI"],true));
				
				if ($class == 'login' || $class == 'register' ||  strpos($_SERVER["REQUEST_URI"],'member.php') > 0 ||  strpos($class,'uc_') === 0){
		//			print_r($wx_info);
		//			die;
					//会员不存在进入登录流程
					$user_data = array();
					$user_data['user_name'] = $wx_info['nickname'];
					$user_data['wx_openid'] = $wx_info['openid'];				
					//$rs = auto_create($user_data);
					$user_data = $rs['user_data'];
					$class='user_wx_register';
					//wx_auto_do_login_user($wx_user_info['user_name'],$wx_user_info['user_pwd'],false);
					//app_redirect(wap_url('index','user_wx_register'));
				}
				 
			}
		}
	}else{
		//es_session::get('is_sq') != 1 || 
		//file_put_contents(APP_ROOT_PATH."wap/log/test3_".strftime("%Y%m%d%H%M%S",time())."_".$class."_.txt",print_r($_SERVER["REQUEST_URI"],true));
		
		if (($class == 'login' || $class == 'register' || strpos($_SERVER["REQUEST_URI"],'member.php') > 0) ||  strpos($class,'uc_') === 0){
			if($is_weixin&&!$user_info&&$m_config['wx_appid']&&$m_config['wx_secrit']&&$class!='user_wx_register'&&$class!='wx_do_register'&&$class!='send_wx_code'&&$class!='pay_wx_jspay'){
				
			//echo 'dd:'.get_domain().$_SERVER["REQUEST_URI"];exit;
				$weixin_2=new weixin($m_config['wx_appid'],$m_config['wx_secrit'],get_domain().$_SERVER["REQUEST_URI"]);
				
				$wx_url=$weixin_2->scope_get_code();
				
				//file_put_contents(APP_ROOT_PATH."wap/log/test4_".strftime("%Y%m%d%H%M%S",time())."_".$class."_.txt",print_r($wx_url,true));
				
				//es_session::set('is_sq',1);
				app_redirect($wx_url);
			}
		}
	}
}

if($is_weixin && $user_info && $m_config['wx_appid']&&$m_config['wx_secrit']&&$class!='pay_wx_jspay'){
	require_once APP_ROOT_PATH."system/utils/jssdk.php";
	$jssdk = new JSSDK($m_config['wx_appid'],$m_config['wx_secrit']);
	$signPackage = $jssdk->GetSignPackage();	
	$GLOBALS['tmpl']->assign("signPackage",$signPackage);
  	$wx_url=get_domain().$_SERVER["REQUEST_URI"];
  	if(strpos($wx_url,"?")  === false){
  	    if($user_info)
  	        $wx_url.="?r=".str_replace('+', '%2b',base64_encode($user_info['id']));
  	}
  	else{
  	    if($user_info)
  	        $wx_url.="&r=".str_replace('+', '%2b',base64_encode($user_info['id']));
  	}
  	$GLOBALS['tmpl']->assign("wx_url",$wx_url);
}


if (empty($act2)) $act2='index';




//获取模板文件的名称
$tmpl_dir=$class.'.html';
//$tmpl_dir=$class.'_'.$act2.'.html';

//=========================

//$request_url = 'http://127.0.0.1/'.str_replace('/wap', '', APP_ROOT).'/sjmapi/index.php';
$request_url = SITE_DOMAIN.str_replace('/wap', '', APP_ROOT).'/mapi/index.php';

//echo get_domain()."<br>;".APP_ROOT; exit;



//会员自动登录及输出
$cookie_uname = es_cookie::get("user_name")?es_cookie::get("user_name"):'';
$cookie_upwd = es_cookie::get("user_pwd")?es_cookie::get("user_pwd"):'';


//logUtils::log_str($cookie_uname);
//logUtils::log_str($cookie_upwd);

if($cookie_uname!=''&&$cookie_upwd!=''&&!es_session::get("user_info"))
{
	//logUtils::log_str("=======1=======");
	$cookie_uname = addslashes(trim(htmlspecialchars($cookie_uname)));
	$cookie_upwd = addslashes(trim(htmlspecialchars($cookie_upwd)));
	require_once APP_ROOT_PATH."system/libs/user.php";
	//require_once APP_ROOT_PATH."app/Lib/common.php";
	auto_do_login_user($cookie_uname,$cookie_upwd);
	
	//logUtils::log_str("========2=========");
}



$user_info = es_session::get('user_info');
//logUtils::log_obj($user_info);

if($user_info)
{
	$request_param['uid']= intval($user_info["id"]);
	$request_param['pwd']=$user_info["user_pwd"];
	$request_param['email']=$user_info["user_name"];
}

//如果用户已经登陆,再点：登陆按钮时,则直接转到会员中心界面
if($class =='login' && $request_param['uid'] > 0){
	//logUtils::log_obj($request_param);
	//file_put_contents(APP_ROOT_PATH."wap/log/a11.txt",url('index','uc_center'));
	app_redirect(wap_url('index','uc_center'));
}


if($class=='pay_wx_jspay' && isset($_GET['code'])){
	$request_param['code'] = $_GET['code'];
}

//logUtils::log_obj($request_param);



if($class=="uc_credit_save"){
	
	foreach($_FILES['file']['name'] as $k=>$v){
		if($v){
			$tmpfile = pathinfo($v);
								
			if($tmpfile['error'] == 0){
				$time = to_date(TIME_UTC,"Ym");
				if(!file_exists(APP_ROOT_PATH."/public/attachment/".$time))
					@mkdir(APP_ROOT_PATH."/public/attachment/".$time,0777);
				
				$d = to_date(TIME_UTC,"d");
				if(!file_exists(APP_ROOT_PATH."/public/attachment/".$time."/".$d))
					@mkdir(APP_ROOT_PATH."/public/attachment/".$time."/".$d,0777);
					
				$h = to_date(TIME_UTC,"h");
				if(!file_exists(APP_ROOT_PATH."/public/attachment/".$time."/".$d."/".$h))
					@mkdir(APP_ROOT_PATH."/public/attachment/".$time."/".$d."/".$h,0777);
			
				$file_name = md5(TIME_UTC.$v).".".$tmpfile['extension'];
				
				$allow_ext_arr =  FilterAllowExt();
				if(!in_array(strtolower($tmpfile['extension']),$allow_ext_arr)){
					showErr('图片格式错误!',0,wap_url('index','uc_credit'));
					
				}
				
				move_uploaded_file($_FILES['file']['tmp_name'][$k],APP_ROOT_PATH."/public/attachment/".$time."/".$d."/".$h."/".$file_name);
				
				if(file_exists(APP_ROOT_PATH."/public/attachment/".$time."/".$d."/".$h."/".$file_name)){
					$request_param['file'][] = "./public/attachment/".$time."/".$d."/".$h."/".$file_name;
				}
			
			}
		}
	}
	
	$request_param['is_wap'] = 1;
	$data = request_api($request_url,$class,$request_param);
	
	if($data['status']==0){
		showErr($data['show_err'],0,wap_url('index','uc_credit'));
	}
	else
		showSuccess('提交成功!',0,wap_url('index','uc_center'));
}

if($request_param['post_type']!='json'){
	/*
	//file_put_contents(APP_ROOT_PATH."wap/log/a1.txt",'');
	$request_param['act']=$class;
	$request_param['r_type']=0;
	$request_param['i_type']=1;
	$request_param['from']='wap';
	
	
	//echo $request_url."<br>";
	//print_r($request_param);
	$request_data=$GLOBALS['transport']->request($request_url,$request_param);
	
	request_api($request_url,$class,$request_param);

	$data=$request_data['body'];
	//print_r($data);exit;
	$data=json_decode(base64_decode($data),1);
	*/
	
	$data = request_api($request_url,$class,$request_param);
	
	//file_put_contents(APP_ROOT_PATH."wap/log/a2.txt",'');
	if ($request_param['is_debug'] == 1){
		//print_r($data);exit;
	}
	//echo "<br>=========================<br>";
	//print_r($data);exit;
	 //判断是否需要登陆
	if(isset($data['user_login_status']) && $data['user_login_status'] == 0 && $class != "pwd" && $class != "user_wx_register" && $class != "login" && $class !='register' && $class !='register_verify_code') {
	
		//接口需要求登陆,并且未登陆时,提示用户登陆;
		//es_session::delete("uid");
		//es_session::delete("user_email");
		//es_session::delete("user_pwd");

		if ($is_weixin){
			
		}else{
			
			showSuccess('请先登陆!',0,wap_url('index','login#index'));
		}
		
		/*
			es_cookie::delete("user_name");
			es_cookie::delete("user_pwd");
			es_session::delete("user_info");
			
			showSuccess('请先登陆2!',0,url('index','login#index'));
		*/
	
	}
	
	
	
	//$domain = app_conf("PUBLIC_DOMAIN_ROOT")==''?get_domain().APP_ROOT:app_conf("PUBLIC_DOMAIN_ROOT");
	//echo $domain;exit;
	
	if(isset($data['page']) && is_array($data['page']) && $data['page']['page_total'] > 1){
		//感觉这个分页有问题,查询条件处理;分页数10,需要与sjmpai同步,是否要将分页处理移到sjmapi中?或换成下拉加载的方式,这样就不要用到分页了		
		$page = new Page($data['page']['page_total'],$data['page']['page_size']);   //初始化分页对象 	
		//$page->parameter
		$p  =  $page->show();
		//print_r($p);exit;
		$GLOBALS['tmpl']->assign('pages',$p);
	}
	
	if($class=='pay_order'){
	
		//在支付界面时,清空购买车,但如果清空了,用户点：返回 后，再去购买时,会购买空商品，这个需要注意处理一下
		$session_cart_data=es_session::get("cart_data");
		unset($session_cart_data);
		es_session::set("cart_data",$session_cart_data);
		es_session::set("cart_data",array());
		es_session::delete("cart_data");
	
	}
	
	if($class=='pay_wx_jspay'){
		//微信v3版跳转
		//print_r($data['is_wap_url']); echo "<br>";echo $data['notify_url'];exit;
		//file_put_contents(APP_ROOT_PATH."wap/log/dd2ss".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($data,true));
		
		if($data['wap_notify_url'] && $data['is_wap_url']==1)
		{
			//file_put_contents(APP_ROOT_PATH."wap/log/ddss".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($data,true));
			Header("location:".$data['wap_notify_url']);
			exit;
		}
		
	
	}
	
		//file_put_contents(APP_ROOT_PATH."wap/log/a3.txt",'');
	//echo $tmpl_dir; exit;
	//print_r($request_param);exit;
	$GLOBALS['tmpl']->assign('request',$request_param);
	$GLOBALS['tmpl']->assign('is_ajax',intval($request_param['is_ajax']));
	$GLOBALS['tmpl']->assign('data',$data);
	$GLOBALS['tmpl']->assign('WAP_ROOT',APP_ROOT);
	$WAP_ROOT = str_replace('/wap', '', APP_ROOT);
	$GLOBALS['tmpl']->assign('APP_ROOT', $WAP_ROOT);
	
	if (es_session::get('user_info')){
		$GLOBALS['tmpl']->assign('is_login',1);//用户已登陆
	}else{
		$GLOBALS['tmpl']->assign('is_login',0);//用户未登陆
		
	}
	
//file_put_contents(APP_ROOT_PATH."wap/log/a4.txt",$tmpl_dir);
	//==============================
	//判断是否有缓存
	//echo $tmpl_dir; exit;
	//生成缓存的ID
	
	//$cache_id  = md5($class.$act2.trim($request_param['id']).$city_id);	
	//if (!$GLOBALS['tmpl']->is_cached($tmpl_dir, $cache_id)){}
	//echo $tmpl_dir; exit;
	//	print_r($wx_info);
	
	if((strpos($class,"project")>0 || strpos($class,"project")===0) && $data['status'] == 0 && $data['show_err'] != "")
	{
		if($data["jump"])
		{
			showSuccess($data['show_err'],0,$data['jump']);
		}
		else
		{
			showSuccess($data['show_err'],0,wap_url("index"));
		}
	}
	$GLOBALS['tmpl']->display($tmpl_dir);
}else{
	/*
	$request_param['from']='wap';
	$request_param['act']=$class;
	//$request_param['i_type']=2;
	//$request_param['r_type']=0;
	
	$postData = array();
	$postData['i_type']=0;
	$postData['r_type']=0;	 
	$postData['requestData'] = base64_encode(json_encode($request_param));
	
	$request_data=$GLOBALS['transport']->request($request_url,$postData);
	$data=$request_data['body'];
	
	//@eval("\$data = ".$data.';');
	$data=base64_decode($data);

	*/
	$data = request_api($request_url,$class,$request_param);
	
	
	if($class=='register' || $class=='register_verify_code'){
 		if($data['response_code']==1){
 			/*
			//将会员信息存在session中
			es_session::set('uid',$i->uid);			
			es_session::set('user_name',$i->user_name);			
			es_session::set('user_pwd',$i->user_pwd);
			*/
 			
 			//logUtils::log_obj($i);
 			
 			es_session::delete("user_info");
			es_cookie::set("user_name",$data['user_name'],3600*24*30);
			es_cookie::set("user_pwd",md5($data['user_pwd']."_EASE_COOKIE"),3600*24*30);
		}
	}
	if($class=='pwd'){
		if($data['response_code']==1){
			//es_session::set('user_pwd',$request_param['newpassword']);
			es_session::delete("user_info");
			es_cookie::set("user_pwd",md5($data['user_pwd']."_EASE_COOKIE"),3600*24*30);
		}
	}
	
	if($class=='wx_do_register'){
		if($data['status']==1){
			es_session::delete("user_info");
			es_cookie::set("user_name",$data->user_name,3600*24*30);
			es_cookie::set("user_pwd",md5($data['user_pwd']."_EASE_COOKIE"),3600*24*30);
		}
	}
	
	if($class=='login'){
 		if($data['response_code']==1){
 			es_session::delete("user_info");
			es_cookie::set("user_name",$data['user_name'],3600*24*30);
			es_cookie::set("user_pwd",md5($data['user_pwd']."_EASE_COOKIE"),3600*24*30);
		}
	}
	
	echo json_encode($data);
}

?>