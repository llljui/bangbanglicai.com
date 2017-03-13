<?php
if(!defined('ROOT_PATH'))
define('ROOT_PATH', str_replace('callback/pay/walipay_notify.php', '', str_replace('\\', '/', __FILE__)));
$_root = $_SERVER["PHP_SELF"];
$_root = (($_root=='/' || $_root=='\\')?'':$_root);
$_root = str_replace("/callback/pay/walipay_notify.php","",$_root);
define("APP_ROOT",$_root);
global $pay_req;
$pay_req['ctl'] = "payment";
$pay_req['act'] = "notify";
$pay_req['class_name'] = "Walipay";
$pay_req['is_sj'] = 1;
include ROOT_PATH."index.php";
?>