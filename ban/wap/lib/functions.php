<?php



//显示错误
function showErr($msg,$ajax=0,$jump='',$stay=0)
{	
	if($jump == '')
	{
		echo "<script>alert('".$msg."'); history.go(-1);</script>";
	}
	else
		echo "<script>alert('".$msg."');location.href='".$jump."';</script>";exit;	
}

//显示成功
function showSuccess($msg,$ajax=0,$jump='',$stay=0)
{
	echo "<script>alert('".$msg."');location.href='".$jump."';</script>";exit;
}


//编译生成css文件
function parse_css($urls)
{
	static $color_cfg;
	if(empty($color_cfg))
		$color_cfg = include_once APP_ROOT_PATH."wap/tpl/".TMPL_NAME."/color_cfg.php";
	
	
	$url = md5(implode(',',$urls));
	$css_url = 'public/runtime/wap/statics/'.$url.'.css';
	$url_path = APP_ROOT_PATH.$css_url;
	if(!file_exists($url_path)||IS_DEBUG)
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/statics/')){
			mkdir(APP_ROOT_PATH.'public/runtime/wap/statics/');
			@chmod(APP_ROOT_PATH.'public/runtime/wap/statics/', 0777);
		}
		$tmpl_path = $GLOBALS['tmpl']->_var['TMPL'];

		$css_content = '';
		foreach($urls as $url)
		{
			$css_content .= @file_get_contents($url);
		}
		$css_content = preg_replace("/[\r\n]/",'',$css_content);
		$css_content = str_replace("../images/",$tmpl_path."/images/",$css_content);
		if (is_array($color_cfg)){
			foreach($color_cfg as $k=>$v)
			{
				$css_content = str_replace($k,$v,$css_content);
			}
		}
		//		@file_put_contents($url_path, unicode_encode($css_content));
		@file_put_contents($url_path, $css_content);
	}
	return SITE_DOMAIN."/".APP_ROOT.'/../'.$css_url;
}


/**
 * 
 * @param $urls 载入的脚本
 * @param $encode_url 需加密的脚本
 */
function parse_wap_script($urls,$encode_url=array())
{	
	$url = md5(implode(',',$urls));
	$js_url = 'public/runtime/wap/statics/'.$url.'.js';
	$url_path = APP_ROOT_PATH.$js_url;
	if(!file_exists($url_path)||IS_DEBUG)
	{
		if(!file_exists(APP_ROOT_PATH.'public/runtime/wap/statics/')){
			mkdir(APP_ROOT_PATH.'public/runtime/wap/statics/');
			@chmod(APP_ROOT_PATH.'public/runtime/wap/statics/', 0777);
		}
	
		if(count($encode_url)>0)
		{
			require_once APP_ROOT_PATH."system/libs/javascriptpacker.php";
		}
		
		$js_content = '';
		foreach($urls as $url)
		{
			$append_content = @file_get_contents($url)."\r\n";
			if(in_array($url,$encode_url))
			{
				$packer = new JavaScriptPacker($append_content);
				$append_content = $packer->pack();
			}			
			$js_content .= $append_content;
		}		
		@file_put_contents($url_path,$js_content);
	}
	return SITE_DOMAIN.APP_ROOT."/../".$js_url;
}


//解析URL标签
// $str = u:shop|acate#index|id=10&name=abc
function parse_wap_url_tag($str)
{
	$key = md5("WAP_URL_TAG_".$str);
	if(isset($GLOBALS[$key]))
	{
		return $GLOBALS[$key];
	}

	$url = load_dynamic_cache($key);
	$url=false;
	if($url!==false)
	{
		$GLOBALS[$key] = $url;
		return $url;
	}
	$str = substr($str,2);
	$str_array = explode("|",$str);
	$app_index = $str_array[0];
	$route = $str_array[1];
	$param_tmp = explode("&",$str_array[2]);
	$param = array();

	foreach($param_tmp as $item)
	{
		if($item!='')
			$item_arr = explode("=",$item);
		if($item_arr[0]&&$item_arr[1])
			$param[$item_arr[0]] = $item_arr[1];
	}
	if(isset($_REQUEST['app'])){
		$param['app'] = intval($_REQUEST['app']);
	}
	$GLOBALS[$key]= wap_url($app_index,$route,$param);
	set_dynamic_cache($key,$GLOBALS[$key]);
	return $GLOBALS[$key];
}

function getMConfig(){

	$m_config = $GLOBALS['cache']->get("m_config");
	if(true || $m_config===false)
	{
		$m_config = array();
		$sql = "select code,val from ".DB_PREFIX."m_config";
		$list = $GLOBALS['db']->getAll($sql);
		foreach($list as $item){
			$m_config[$item['code']] = $item['val'];
		}

		$GLOBALS['cache']->set("m_config",$m_config);
	}
	return $m_config;
}
/**
 * 获取当前的url地址，包含分页
 * @return string
 */
function get_current_url()
{
	$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?");
	$parse = parse_url($url);
	if(isset($parse['query'])) {
		parse_str($parse['query'],$params);
		$url   =  $parse['path'].'?'.http_build_query($params);
	}
	return $url;
}
//以下微信支付有调用getMConfig and get_user_has
function get_user_has($key,$value){
	$row=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."user where $key='".$value."'");
	
	if($row){
		return $row;
	}else{
		return false;
	}
}

function user_login(){
	//会员自动登录及输出
	
	if($GLOBALS['wx_info'])
	{
		$userinfo = get_user_has("wx_openid",$GLOBALS['wx_info']['openid']);
// 		print_r($userinfo);
		$cookie_uname = $userinfo['user_name'];
		$cookie_upwd = $userinfo['user_pwd'];
		
		if($cookie_uname!=''&&$cookie_upwd!='')
		{
			$cookie_uname = addslashes(trim(htmlspecialchars($cookie_uname)));
			$cookie_upwd = addslashes(trim(htmlspecialchars($cookie_upwd)));
			require_once APP_ROOT_PATH."system/libs/user.php";
			
			auto_do_login_user($cookie_uname,$cookie_upwd,false);
		
		}
	}
	else 
	{
		$cookie_uname = es_cookie::get("user_name")?es_cookie::get("user_name"):'';
		$cookie_upwd = es_cookie::get("user_pwd")?es_cookie::get("user_pwd"):'';
		
		if($cookie_uname!=''&&$cookie_upwd!=''&&!es_session::get("user_info"))
		{
			//logUtils::log_str("=======1=======");
			$cookie_uname = addslashes(trim(htmlspecialchars($cookie_uname)));
			$cookie_upwd = addslashes(trim(htmlspecialchars($cookie_upwd)));
			require_once APP_ROOT_PATH."system/libs/user.php";
			//require_once APP_ROOT_PATH."app/Lib/common.php";
			auto_do_login_user($cookie_uname,$cookie_upwd);
		
		}
	}


function send_register_code()
	{
		$mobile = addslashes(htmlspecialchars(trim($GLOBALS['request']['mobile'])));
		$root = array();
		
		//if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".$mobile."'")>0)
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile_encrypt = AES_ENCRYPT('".$mobile."','".AES_DECRYPT_KEY."') ")>0)
		{ 
			$field_show_name = $GLOBALS['lang']['USER_TITLE_mobile'];//手机号码
			$root['response_code'] = 0;
			$root['show_err'] = sprintf($GLOBALS['lang']['EXIST_ERROR_TIP'],$field_show_name); //已存在，请重新输入
			return $root;
		}
		
		
		if(!check_ipop_limit(CLIENT_IP,"mobile_verify",60,0))
		{
			$root['response_code'] = 0;
			$root['show_err'] = $GLOBALS['lang']['MOBILE_SMS_SEND_FAST']; //短信发送太快
			//output($root);
			return $root;
		}
	
		//删除超过5分钟的验证码
		$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."mobile_verify_code WHERE create_time <=".TIME_UTC-300);
	
		$verify_code = $GLOBALS['db']->getOne("select verify_code from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."' and create_time>=".(TIME_UTC-180)." ORDER BY id DESC");
		if(intval($verify_code) == 0)
		{
			//如果数据库中存在验证码，则取数据库中的（上次的 ）；确保连接发送时，前后2条的验证码是一至的.==为了防止延时
			//开始生成手机验证
			$verify_code = rand(1111,9999);
			$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",array("verify_code"=>$verify_code,"mobile"=>$mobile,"create_time"=>TIME_UTC,"client_ip"=>CLIENT_IP),"INSERT");
		}
	
		//使用立即发送方式
		$result = send_verify_sms($mobile,$verify_code,null,true);//
		//$root['response_code'] = $result['status'];
		$root['response_code'] = 1;
		
		if ($root['response_code'] == 1){
			$root['show_err'] = $GLOBALS['lang']['MOBILE_VERIFY_SEND_OK'];
		}else{
			$root['show_err'] = $result['msg'];
			if ($root['show_err'] == null || $root['show_err'] == ''){
				$root['show_err'] = "验证码发送失败";
			}
		}
		//../system/sms/FW_sms.php  提示账户或密码错误地址
		
		return $root;
		//output($root);
	}
		
	
}

function show_ke_image($id,$cnt="")
{
	if($cnt)
	{
		$image_path = $cnt;
		$is_show="display:inline-block;";
	}
	else{
		
		$image_path =APP_ROOT."/admin/Tpl/default/Common/images/no_pic.gif";
		$image_path = str_replace("/wap", "", $image_path);
		$is_show="display:none;";
	}
	
	$del_image_path =APP_ROOT."/admin/Tpl/default/Common/images/del.gif";
	$del_image_path = str_replace("/wap", "", $del_image_path);
	
	$parseStr= "<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='".(empty($cnt) ? "" : $image_path)."' name='$id' id='keimg_h_$id' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='$id'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='$image_path' target='_blank' id='keimg_a_$id' ><img src='$image_path' id='keimg_m_$id' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;margin-right:10px'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='".APP_ROOT."/admin/Tpl/default/Common/images/del.gif' style='$is_show; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='$id' title='删除'>
				</div>
			</div>
		</div>
		</span>";
	return $parseStr;				
}



function replacemoney($moeny){
	return round($moeny/10000,2);
}

?>