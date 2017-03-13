<?php
if(!defined('ROOT_PATH'))
define('ROOT_PATH', str_replace('licai.php', '', str_replace('\\', '/', __FILE__)));

global $pay_req;
$pay_req['ctl'] = "licai";
$pay_req['act'] = "index";
include ROOT_PATH."index.php";
?>