<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少(273017814@qq.com)
// +----------------------------------------------------------------------

// 前后台加载的系统配置文件

$config = array();
// 加载数据库中的配置与数据库配置
if(file_exists(APP_ROOT_PATH.'public/db_config.php'))
{
	$db_config	=	require APP_ROOT_PATH.'public/db_config.php';
	$config = array_merge($config,$db_config);
}

//加载系统配置信息
if(file_exists(APP_ROOT_PATH.'public/sys_config.php'))
{
	$db_conf	=	require APP_ROOT_PATH.'public/sys_config.php';
	$config = array_merge($config,$db_conf);
}

//加载系统信息
if(file_exists(APP_ROOT_PATH.'public/version.php'))
{
	$version	=	require APP_ROOT_PATH.'public/version.php';
	$config = array_merge($config,$version);
}

//加载时区信息
if(file_exists(APP_ROOT_PATH.'public/timezone_config.php'))
{
	$timezone	=	require APP_ROOT_PATH.'public/timezone_config.php';
	$config = array_merge($config,$timezone);
}


return $config;
?>