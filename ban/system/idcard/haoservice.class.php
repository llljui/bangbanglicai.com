<?php
$config_lang = array(
    'key'		=>	'MD5校验码',
);

$config = array(
    'key'	=>	array(
        'INPUT_TYPE'	=>	'0'
    ), //校验码

);

/**
 * 身份证查询认证接口
 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'haoservice';
    //借口名称
    $module['name']    = "haoservice";
    //短名称
    $module['sub_name']    = "haoservice";
	
	$module['reg_url']    = "http://www.haoservice.com/docs/65";
    
    $module['config']    = $config;
    
    $module['lang']    = $config_lang;
    
    return $module;
}

require_once APP_ROOT_PATH."system/libs/idcard.php";
class haoservice implements idcard{
	/**
	 * 身份证
	 */
	function getinfo($info){
	    
	    $config =  $GLOBALS['db']->getOne("select `config` from ".DB_PREFIX."idcard Where `class_name`= 'haoservice' ");
	    $config =  unserialize($config);
	    
	    $url =  "http://apis.haoservice.com/idcard/VerifyIdcard";
	    $str = "?cardNo=".$info['card']."&realName=".$info['realname']."&key=".$config['key'];
	   
	    $content  = @file_get_contents($url.$str);
	    $result = json_decode($content,1);
	    if($result['error_code']==0){
	    	$return['status'] = 1;
	    	$return['isok'] = $result['result']['isok'];
	        if($result['result']['isok']==false){
	            $return['info'] = "不匹配";
	        }
	        else{
    	        $return['realName'] = $result['result']['realname'];
    	        $return['cardNO'] = $result['result']['idcard'];
    	        $return['address'] = $result['result']['address'];
    	        $return['birthday'] = $result['result']['birthday'];
    	        $return['sex'] = $result['result']['sex'];
	        }
	    }
	    else{
	        $return['status'] = 0;
	        $return['info'] = $result['reason'];
	    }
		return $return;
	}
}
?>
