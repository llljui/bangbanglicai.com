<?php 
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少(273017814@qq.com)
// +----------------------------------------------------------------------
error_reporting(0);
if((trim($_REQUEST['m'])=='File'&&trim($_REQUEST['a'])=='do_upload_img')||(trim($_REQUEST['m'])=='File'&&trim($_REQUEST['a'])=='do_upload'))
{
	define("ADMIN_ROOT",1);
	require "admin.php";
}
?>
