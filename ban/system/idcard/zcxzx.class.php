<?php
$config_lang = array(
    'account'		=>	'账户',
    'privateKey'		=>	'私钥',
);

$config = array(
    'account'	=>	array(
        'INPUT_TYPE'	=>	'0'
    ), //账户
	'privateKey'	=>	array(
        'INPUT_TYPE'	=>	'0'
    ), //私钥
);

/**
 * 身份证查询认证接口
 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'zcxzx';
    //借口名称
    $module['name']    = "中诚信征信";
    //短名称
    $module['sub_name']    = "中诚信征信";
	
	$module['reg_url']    = "http://www.ccxcredit.com.cn/";
    
    $module['config']    = $config;
    
    $module['lang']    = $config_lang;
    
    return $module;
}

require_once APP_ROOT_PATH."system/libs/idcard.php";
class zcxzx implements idcard{
	/**
	 * 身份证
	 */
	function getinfo($info){
	    
	    $config =  $GLOBALS['db']->getOne("select `config` from ".DB_PREFIX."idcard Where `class_name`= 'zcxzx' ");
	    $config =  unserialize($config);
	    
	    $url =  "https://122.112.76.24/data-service/identity/score";
		
		$parms['account'] = $config['account'];
		$parms['cid'] = $info['card'];
		$parms['name'] = $info['realname'];
		$sign_str = "";
		foreach ($parms as $key => $value) {
			$sign_str .=$key.$value;
		}
		
		$parms['sign'] = strtoupper(md5($sign_str.$config['privateKey']));
		
	    $content  = $this->getCURL($url."?".http_build_query($parms));
		
	    if($content){
	    	$result = json_decode($content,1);
		
		    if(trim($result['resCode'])=="0000" && (int)$result['score'] >= 60){
		    	$return['status'] = 1;
		    	$return['isok'] = true;
		    }
		    else{
		        $return['status'] = 0;
		        $return['info'] = $result['resMsg'];
		    }
		}
		else{
			$return['status'] = 2;
			$return['info'] = "通信失败";
		}
		return $return;
	}
	
	function getbankinfo($info){
		$config =  $GLOBALS['db']->getOne("select `config` from ".DB_PREFIX."idcard Where `class_name`= 'zcxzx' ");
	    $config =  unserialize($config);
	    
	    $url =  "https://122.112.76.24/data-service/auth/valid";
		
		$parms['account'] = $config['account'];
		$parms['cid'] = $info['card'];
		$parms['name'] = $info['realname'];
		//$parms['mobile'] = $info['mobile'];
		$parms['card'] = $info['bankcard'];//银行卡号
		/*
		 *  1：卡号、姓名
			2：卡号、姓名、身份证号、手机号
			3：卡号、手机号
			4：卡号、手机号、姓名
			5：卡号、姓名、身份证号
		 */
		$parms['type'] = 5;
		$sign_str = "";
		foreach ($parms as $key => $value) {
			$sign_str .=$key.$value;
		}
		
		//$parms['sign'] = strtoupper(md5($sign_str.$config['privateKey']));
		$parms['sign'] = strtoupper(md5("account".$config['account'].$config['privateKey']));
		
	    $content  = $this->getCURL($url."?".http_build_query($parms));
		
		if($content){
	    	$result = json_decode($content,1);
		
		    if((int)$result['resCode']==2030){
		    	$return['status'] = 1;
		    	$return['isok'] = true;
		    }
		    else{
		        $return['status'] = 0;
		        $return['info'] = $result['resMsg'];
		    }
		}
		else{
			$return['status'] = 2;
			$return['info'] = "通信失败";
		}
		return $return;
	}

	private function getCURL($url,$referer = ""){
		if($url=="")
			return false;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_TIMEOUT,60);
		if(!empty($referer))
			curl_setopt ($ch, CURLOPT_REFERER,$referer);
		curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.168 Safari/535.19");
		curl_setopt($ch, CURLOPT_MAXREDIRS,11);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}
}
?>
