<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>支付宝即时到账交易接口接口</title>
</head>
<body>
<?php
require '../../system/common.php';

$payment_notice_id = intval($_REQUEST['payment_notice_id']);
require_once APP_ROOT_PATH."system/payment/Walipay_payment.php";
$payment_object = new Walipay_payment();
$html = $payment_object->get_payment($payment_notice_id);
echo $html;
?>
</body>
</html>