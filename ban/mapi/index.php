<?php 
// +----------------------------------------------------------------------
// | 邦邦理财 mapi 插件
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------

if(!defined("MAGIC_QUOTES_GPC")){
    @set_magic_quotes_runtime (0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
}

//过滤请求
function mapi_filter_request(&$request)
{
    if(MAGIC_QUOTES_GPC)
    {
        foreach($request as $k=>$v)
        {
            if(is_array($v))
            {
                mapi_filter_request($request[$k]);
            }
            else
            {
                $request[$k] = stripslashes(trim($v));
            }
        }
    }

}

$_REQUEST = array_merge($_GET,$_POST);
mapi_filter_request($_REQUEST);

if (isset($_REQUEST['i_type']))
{
    $i_type = intval($_REQUEST['i_type']);
}

if ($i_type == 1)
{
    $request = $_REQUEST;
}
else
{
    if (isset($_REQUEST['requestData']))
    {
        if ($i_type == 2)
        {
            $request = json_decode(trim($_REQUEST['requestData']), 1);
        }else if($i_type == 4){
			
        	$dir = str_replace('mapi/index.php', '', str_replace('\\', '/', __FILE__));
        	
			require_once $dir.'/system/libs/crypt_aes.php';
			$aes = new CryptAES();
			$aes->set_key('FANWE5LMUQC43P2P');
			$aes->require_pkcs5();
			//echo FANWE_AES_KEY; exit;
			//XqcrhQzAmFVYFLM1w80gU5ehUS19vtrZcLR26z8HtVzPMj082BfaMIaAlxrEINTaXr2ANG69bDqjQu80MeGfYMux7AA1Fqx8ofIeMtFfcg1IP2OfwS+meIM+VB4HBusuqkCRkT+eKv5UD/s1HZ5y9NCMFosa7qm84BOWHJTVsXc=
			//print_r($_REQUEST['requestData']);exit;
			$decString = $aes->decrypt(trim($_REQUEST['requestData']));

			//print_r($decString);exit;
			$request = json_decode($decString, 1);
		}else
        {
            //$_REQUEST['requestData'] ="eyJ1c2VyX2luZm8iOiJ7XCJpZFwiOjE3Mjk0OTAxNjAsXCJpZHN0clwiOlwiMTcyOTQ5MDE2MFwiLFwic2NyZWVuX25hbWVcIjpcIkNoaWdhb1wiLFwibmFtZVwiOlwiQ2hpZ2FvXCIsXCJwcm92aW5jZVwiOlwiMzVcIixcImNpdHlcIjpcIjFcIixcImxvY2F0aW9uXCI6XCLnpo/lu7og56aP5beeXCIsXCJkZXNjcmlwdGlvblwiOlwiXCIsXCJ1cmxcIjpcImh0dHA6XC9cL3d3dy5teXhpbGkuY29tXCIsXCJwcm9maWxlX2ltYWdlX3VybFwiOlwiaHR0cDpcL1wvdHAxLnNpbmFpbWcuY25cLzE3Mjk0OTAxNjBcLzUwXC8wXC8xXCIsXCJwcm9maWxlX3VybFwiOlwiY2hpZ2FvXCIsXCJkb21haW5cIjpcImNoaWdhb1wiLFwid2VpaGFvXCI6XCJcIixcImdlbmRlclwiOlwibVwiLFwiZm9sbG93ZXJzX2NvdW50XCI6OTgsXCJmcmllbmRzX2NvdW50XCI6NDMwLFwic3RhdHVzZXNfY291bnRcIjo1NjUsXCJmYXZvdXJpdGVzX2NvdW50XCI6MCxcImNyZWF0ZWRfYXRcIjpcIlR1ZSBBcHIgMTMgMTc6MTc6MzMgKzA4MDAgMjAxMFwiLFwiZm9sbG93aW5nXCI6ZmFsc2UsXCJhbGxvd19hbGxfYWN0X21zZ1wiOmZhbHNlLFwiZ2VvX2VuYWJsZWRcIjp0cnVlLFwidmVyaWZpZWRcIjpmYWxzZSxcInZlcmlmaWVkX3R5cGVcIjotMSxcInJlbWFya1wiOlwiXCIsXCJzdGF0dXNcIjp7XCJjcmVhdGVkX2F0XCI6XCJTYXQgTWFyIDIzIDE2OjIwOjA3ICswODAwIDIwMTNcIixcImlkXCI6MzU1OTA0ODc0ODY1Mjk5NCxcIm1pZFwiOlwiMzU1OTA0ODc0ODY1Mjk5NFwiLFwiaWRzdHJcIjpcIjM1NTkwNDg3NDg2NTI5OTRcIixcInRleHRcIjpcIiPoiIzlsJbkuIrnmoTpn6nlm70jIOWFs+mUrueci+aYr+S4jeaYr+e7v+iJsuaXoOaxoeafk++8jOacieayoeaciea3u+WKoOWJgu+8gSDor6bmg4U6aHR0cDpcL1wvdC5jblwvellreXB0eFwiLFwic291cmNlXCI6XCI8YSBocmVmPVxcXCJodHRwOlwvXC9hcHAud2VpYm8uY29tXC90XC9mZWVkXC80QWJBRlZcXFwiIHJlbD1cXFwibm9mb2xsb3dcXFwiPuW+ruivnemimDxcL2E+XCIsXCJmYXZvcml0ZWRcIjpmYWxzZSxcInRydW5jYXRlZFwiOmZhbHNlLFwiaW5fcmVwbHlfdG9fc3RhdHVzX2lkXCI6XCJcIixcImluX3JlcGx5X3RvX3VzZXJfaWRcIjpcIlwiLFwiaW5fcmVwbHlfdG9fc2NyZWVuX25hbWVcIjpcIlwiLFwicGljX3VybHNcIjpbXSxcImdlb1wiOm51bGwsXCJhbm5vdGF0aW9uc1wiOlt7XCJzb3VyY2VcIjp7XCJuYW1lXCI6XCLoiIzlsJbkuIrnmoTpn6nlm71cIixcImFwcGlkXCI6XCI0MzhcIixcInVybFwiOlwiaHR0cDpcL1wvaHVhdGkud2VpYm8uY29tXC8yOTU4MlwifX0se1wiaHVhdGlcIjp7XCJmcm9tXCI6XCJ0b3BpY19wdWJsaXNoXCJ9fV0sXCJyZXBvc3RzX2NvdW50XCI6MCxcImNvbW1lbnRzX2NvdW50XCI6MCxcImF0dGl0dWRlc19jb3VudFwiOjAsXCJtbGV2ZWxcIjowLFwidmlzaWJsZVwiOntcInR5cGVcIjowLFwibGlzdF9pZFwiOjB9fSxcImFsbG93X2FsbF9jb21tZW50XCI6dHJ1ZSxcImF2YXRhcl9sYXJnZVwiOlwiaHR0cDpcL1wvdHAxLnNpbmFpbWcuY25cLzE3Mjk0OTAxNjBcLzE4MFwvMFwvMVwiLFwidmVyaWZpZWRfcmVhc29uXCI6XCJcIixcImZvbGxvd19tZVwiOmZhbHNlLFwib25saW5lX3N0YXR1c1wiOjAsXCJiaV9mb2xsb3dlcnNfY291bnRcIjoxLFwibGFuZ1wiOlwiemgtY25cIixcInN0YXJcIjowLFwibWJ0eXBlXCI6MCxcIm1icmFua1wiOjAsXCJibG9ja193b3JkXCI6MH0iLCJyZWZyZXNoX3RpbWUiOiIxMzcxNDA5MjE2IiwiYWNjZXNzX3NlY3JldCI6IjVkY2RjMTlmMDhjZmU3ZThkM2MxNTM2YzAxZGYwYTk0IiwidHlwZSI6ImFuZHJvaWQiLCJhY3QiOiJzeW5jbG9naW4iLCJzaW5hX2lkIjoiMTcyOTQ5MDE2MCIsImFjY2Vzc190b2tlbiI6IjIuMDBhNWxDdEJpOHV4X0JjNThiYTA2OGRiQ3BKdjlCIiwibG9naW5fdHlwZSI6IlVTU2luYSJ9";
            $request = base64_decode((trim($_REQUEST['requestData'])));
            $request = json_decode($request, 1);
        }
    }else
    {
        $request = $_REQUEST;
    }
}



require '../system/common.php';
require '../app/Lib/common.php';
require './lib/functions.php';
require '../system/libs/user.php';
if(!defined("APP_INDEX"))
	define("APP_INDEX","index");
	

if (!isset($request['from']) || empty($request['from'])){
	$request['from'] = 'app';
}


$sessid = $request['session_id'];

if (empty($sessid)){
	$sessid = es_session::id();
	$request['session_id'] = $sessid;
}

es_session::set_sessid($sessid);



$m_config = getMConfig();//初始化手机端配置

define('VERSION',1); //接口版本号,float 类型
define("CACHE_TIME",60); //动态数据缓存时间，300秒
if (intval($m_config['page_size']) == 0){
	define('PAGE_SIZE',20); //分页的常量
}else{
	define('PAGE_SIZE',intval($m_config['page_size'])); //分页的常量
}

$class = strtolower(strim($request['act']));
$act2 = strtolower(strim($request['act_2']))?strtolower(strim($request['act_2'])):"";
$city_id = intval($request['city_id']);
define('ACT',$class); //act常量
define('ACT_2',$act2);


$GLOBALS['user_info'] = es_session::get("user_info");

if(empty($GLOBALS['user_info']) && $class != 'login')
{
	$cookie_uname = strim($request['email']);//用户名或邮箱
	$cookie_upwd = strim($request['pwd']);//密码
	
	if($cookie_uname!=''&&$cookie_upwd!='')
	{
		$cookie_uname = strim($cookie_uname);
		if (strlen($cookie_upwd) != 32)
			$cookie_upwd = md5($cookie_upwd);
		$cookie_upwd = md5($cookie_upwd."_EASE_COOKIE");
		auto_do_login_user($cookie_uname,$cookie_upwd);
		$GLOBALS['user_info'] = es_session::get('user_info');
	}
}
else{
    $GLOBALS['user_info'] = get_user_info("*","id=".$GLOBALS['user_info']['id']);
    es_session::set('user_info',$GLOBALS['user_info']);
}

if(true) 
{
	$url = SITE_DOMAIN.APP_ROOT."/index.php?requestData=".$_REQUEST['requestData']."&r_type=2";
	$api_log = array();
	$api_log['api'] = $url;
	$api_log['act'] = $class;
	$GLOBALS['db']->autoExecute(DB_PREFIX."api_log", $api_log, 'INSERT');
}



//公共初始化
if(file_exists("./lib/".$class.".action.php"))
{	
	require_once "./lib/".$class.".action.php";			
	if(class_exists($class))
	{
		$obj = new $class;		
		if(method_exists($obj,"index"))
		{
			$obj->index();
		}
		else
		{
			header("Content-Type:text/html; charset=utf-8");
			exit("Hack attemp!");
		}
	}
	else
	{
		header("Content-Type:text/html; charset=utf-8");
		exit("Hack attemp!");
	}
}
else
{
	header("Content-Type:text/html; charset=utf-8");
	exit("Hack attemp!".ACT);
}

?>