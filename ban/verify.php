<?php 
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少(273017814@qq.com)
// +----------------------------------------------------------------------\
define("FILE_PATH",""); //文件目录，空为根目录
require_once './system/common.php';
        logger::write(es_session::get('verify'));
es_session::start();
require APP_ROOT_PATH."system/utils/es_image.php";
//require APP_ROOT_PATH."system/utils/Verify.class.php";
$vname = isset($_REQUEST['vname']) ? !empty($_REQUEST['vname']) ? strim($_REQUEST['vname']) : 'verify' : 'verify';
$w = isset($_REQUEST['w']) ? intval($_REQUEST['w']) : 50;
$h = isset($_REQUEST['h']) ? intval($_REQUEST['h']) : 22;
$code = 1;
if($vname=="smsVerify"){
	$code = 10;
}
es_image::buildImageVerify(4,$code,'gif',$w,$h,$vname);
//$sconfig['imageW'] = $w;
//$sconfig['imageH'] = $h;
//$sconfig['fontSize'] = ($w==50 ? 12 : 18);
//$sconfig['useCurve'] = ($w==50 ? false : true);
//$sconfig['useNoise'] = ($w==50 ? false : true);
//$sconfig['length'] = 4;
//$verify = new Verify ($sconfig);
//$verify->entry($vname);
?>
