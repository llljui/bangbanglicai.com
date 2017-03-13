<?php

require '../../system/common.php';

$payment_notice_id = intval($_REQUEST['payment_notice_id']);
require_once APP_ROOT_PATH."system/payment/Bfwap_payment.php";
$payment_object = new Bfwap_payment();
$post_url = $payment_object->get_payment($payment_notice_id);
					
//app_redirect($post_url);

$html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>
		<form name="form1" id="form1" method="post" action="'.$post_url.'" target="_self">		
				<input type="submit" value="提交"></input>
		</form>
		</body></html>
		<script language="javascript">document.form1.submit();</script>';
		
echo $html;			
?>