<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class mobileModule extends SiteBaseModule
{
	public function index()
	{
	    if(isWeixin())
        {
             echo '
                <!DOCTYPE html>
                <html>
                <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=0,minimum-scale=0.5">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-status-bar-style" content="black">
                <meta name="format-detection" content="telephone=no">
                </head>
                </body>
                ';
            if ($this->isios()){
                $str = '请使用浏览器打开下载：<br>';
                $str = $str.'1.点击右上角的按钮<br>';
                $str = $str.'2.选择 在Safari中打开 即可下载app';
                header("Content-Type:text/html; charset=utf-8");
               
                echo $str;
                
            }else{
                $str = '请使用浏览器打开下载：<br>';
                $str = $str.'1.点击右上角的按钮<br>';
                $str = $str.'2.选择 在浏览器中打开 即可下载app';
                header("Content-Type:text/html; charset=utf-8"); 
                echo $str;
               
            }
            
            echo "</body></html>";
        }
        else
        {
    		if($this->isios()){
    			app_redirect(app_conf("APPLE_DOWLOAD_URL"));
    		}else{
    		  app_redirect(app_conf("ANDROID_DOWLOAD_URL"));
    		}
        }
	}
	
	public function isios() {
		//判断手机发送的客户端标志,兼容性有待提高
		if (isset ($_SERVER['HTTP_USER_AGENT'])) {
			$clientkeywords = array (
					'iphone',
					'ipod',
					'mac',
			);
			// 从HTTP_USER_AGENT中查找手机浏览器的关键字
			if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
				return true;
			}
		}
	}
	
	
}
?>