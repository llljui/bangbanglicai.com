<?php
if(!defined('ROOT_PATH'))
define('ROOT_PATH', str_replace('callback/pay/baofoo_callback.php', '', str_replace('\\', '/', __FILE__)));
$_root = $_SERVER["PHP_SELF"];
$_root = (($_root=='/' || $_root=='\\')?'':$_root);
$_root = str_replace("/callback/pay/baofoo_callback.php","",$_root);
define("APP_ROOT",$_root);
global $pay_req;
$pay_req['ctl'] = "payment";
$pay_req['act'] = isset($_REQUEST['act']) ? $_REQUEST['act'] : "response";
if($pay_req['act'] ==""){
	$pay_req['act'] = "response";
}
$pay_req['class_name'] = "Baofoo";
include ROOT_PATH."index.php";
?>