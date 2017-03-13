<?php
require '../system/common.php';
require 'rrl/Rrleeapi.php';
$act =  strim($_REQUEST['act']);

switch($act){
	case 'register':
		$newapi = new Rrleeapi();
		$newapi->register();
		break;
	case 'capture':
		$newapi = new Rrleeapi();
		$newapi->data_capture();
		break;
	case 'login':
		$newapi = new Rrleeapi();
		$newapi->checklogin();
		break;
}