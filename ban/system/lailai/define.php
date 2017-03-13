<?php 
define("IS_DEBUG",1);
define("SHOW_DEBUG",0);
define("SHOW_LOG",0);
define("LAI_LAI",0); 
define("MAX_DYNAMIC_CACHE_SIZE",1000);  //动态缓存最数量
define("SMS_TIMESPAN",60);  //短信验证码发送的时间间隔
define("SMS_EXPIRESPAN",300);  //短信验证码失效时间
define("TIME_UTC",get_gmtime());   //当前UTC时间戳
define("CLIENT_IP",get_client_ip());  //当前客户端IP
define("SITE_DOMAIN",get_domain());   //站点域名
define("MAX_LOGIN_TIME",1200);  //登录的过期时间
define("SESSION_TIME",3600); //session超时时间
define("AES_DECRYPT_KEY","__FANWEP2P__");//数据库加密
define("ACCOUNT_PAGE_SIZE",10); //一页显示条数
define("SEND_VERIFYSMS_LIMIT",3); //注册短信发送条数限制
?>