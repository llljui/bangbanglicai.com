<?php
if(!defined('ROOT_PATH'))
define('ROOT_PATH', str_replace('callback/pay/bfwap_response.php', '', str_replace('\\', '/', __FILE__)));
$_root = $_SERVER["PHP_SELF"];
$_root = (($_root=='/' || $_root=='\\')?'':$_root);
$_root = str_replace("/callback/pay/bfwap_response.php","",$_root);
define("APP_ROOT",$_root);
global $pay_req;
$pay_req['ctl'] = "payment";
$pay_req['act'] = "response";
$pay_req['class_name'] = "Bfwap";
$pay_req['is_sj'] = 1;

include ROOT_PATH."index.php";


?>