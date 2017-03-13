<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/style.css" />
<script type="text/javascript" src="__TMPL__Common/js/check_dog.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/IA300ClientJavascript.js"></script>
<script type="text/javascript">
 	var VAR_MODULE = "<?php echo conf("VAR_MODULE");?>";
	var VAR_ACTION = "<?php echo conf("VAR_ACTION");?>";
	var MODULE_NAME	=	'<?php echo MODULE_NAME; ?>';
	var ACTION_NAME	=	'<?php echo ACTION_NAME; ?>';
	var ROOT = '__APP__';
	var ROOT_PATH = '<?php echo APP_ROOT; ?>';
	var CURRENT_URL = '<?php echo trim($_SERVER['REQUEST_URI']);?>';
	var INPUT_KEY_PLEASE = "<?php echo L("INPUT_KEY_PLEASE");?>";
	var TMPL = '__TMPL__';
	var APP_ROOT = '<?php echo APP_ROOT; ?>';
	var FILE_UPLOAD_URL = ROOT   + "?m=file&a=do_upload";
	var EMOT_URL = '<?php echo APP_ROOT; ?>/public/emoticons/';
	var MAX_FILE_SIZE = "<?php echo (app_conf("MAX_IMAGE_SIZE")/1000000)."MB"; ?>";
	var LOGINOUT_URL = '<?php echo u("Public/do_loginout");?>';
	var WEB_SESSION_ID = '<?php echo es_session::id(); ?>';
	CHECK_DOG_HASH = '<?php $adm_session = es_session::get(md5(conf("AUTH_KEY"))); echo $adm_session["adm_dog_key"]; ?>';
	var IS_WATER_MARK = <?php echo app_conf("IS_WATER_MARK");?>;
	function check_dog_sender_fun()
	{
		window.clearInterval(check_dog_sender);
		check_dog2();
	}
	var check_dog_sender = window.setInterval("check_dog_sender_fun()",5000);
</script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.timer.js"></script>
<script type="text/javascript" src="__ROOT__/public/runtime/admin/lang.js"></script>
<script type='text/javascript'  src='__ROOT__/admin/public/kindeditor/kindeditor.js'></script>
<script type='text/javascript'  src='__ROOT__/admin/public/kindeditor/lang/zh_CN.js'></script>
<script type="text/javascript" src="__TMPL__Common/js/script.js"></script>
</head>
<body onLoad="javascript:DogPageLoad();">
<div id="info"></div>

<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>

<?php function get_is_paid($status)
	{
		if($status == 0)
		return l("NO");
		else
		return l("YES");
	} ?>
<div class="main">
<div class="main_title"><?php echo ($main_title); ?></div>
<div class="blank5"></div>
<div class="search_row">
	<form name="search" action="__APP__" method="get">	
		创建时间 ：
		<input type="text" class="textbox" name="start_time" id="start_time" value="<?php echo trim($_REQUEST['start_time']);?>" onfocus="return showCalendar('start_time', '%Y-%m-%d', false, false, 'start_time');" />
		-
		<input type="text" class="textbox" name="end_time" id="end_time" value="<?php echo trim($_REQUEST['end_time']);?>" onfocus="return showCalendar('end_time', '%Y-%m-%d', false, false, 'end_time');" />
		<div class="blank5"></div>
		<?php echo L("PAYMENT_NOTICE_SN");?>：<input type="text" class="textbox" name="notice_sn" value="<?php echo trim($_REQUEST['notice_sn']);?>" />
		<?php echo L("PAYMENT_METHOD");?>：
		<select name="payment_id">
			<option value="0" <?php if(intval($_REQUEST['payment_id']) == 0): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
			<?php if(is_array($payment_list)): foreach($payment_list as $key=>$payment_item): ?><option value="<?php echo ($payment_item["id"]); ?>" <?php if(intval($_REQUEST['payment_id']) == $payment_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($payment_item["name"]); ?></option><?php endforeach; endif; ?>
		</select>
		支付状态
		<select name="is_paid">
			<option value="-1" <?php if(intval($_REQUEST['is_paid']) == -1 || !isset($_REQUEST['is_paid'])): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
			<option value="0" <?php if(intval($_REQUEST['is_paid']) == 0 && isset($_REQUEST['is_paid'])): ?>selected="selected"<?php endif; ?>>未支付</option>
			<option value="1" <?php if(intval($_REQUEST['is_paid']) == 1): ?>selected="selected"<?php endif; ?>>已支付</option>
		</select>			
		<input type="hidden" value="PaymentNotice" name="m" />
		<input type="hidden" value="index" name="a" />
		<input type="submit" class="button" value="<?php echo L("SEARCH");?>" />
	</form>
</div>
<div class="blank5"></div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="12" class="topTd" >&nbsp; </td></tr><tr class="row" ><th width="50px"><a href="javascript:sortBy('id','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('notice_sn','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("PAYMENT_NOTICE_SN");?><?php echo ($sortType); ?> "><?php echo L("PAYMENT_NOTICE_SN");?><?php if(($order)  ==  "notice_sn"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("CREATE_TIME");?>  <?php echo ($sortType); ?> "><?php echo L("CREATE_TIME");?>  <?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('pay_time','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("PAY_TIME");?>  <?php echo ($sortType); ?> "><?php echo L("PAY_TIME");?>  <?php if(($order)  ==  "pay_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_paid','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("IS_PAID");?>  <?php echo ($sortType); ?> "><?php echo L("IS_PAID");?>  <?php if(($order)  ==  "is_paid"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("USER_NAME");?>  <?php echo ($sortType); ?> "><?php echo L("USER_NAME");?>  <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('payment_id','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("PAYMENT_METHOD");?>  <?php echo ($sortType); ?> "><?php echo L("PAYMENT_METHOD");?>  <?php if(($order)  ==  "payment_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('money','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("PAYMENT_MONEY");?>  <?php echo ($sortType); ?> "><?php echo L("PAYMENT_MONEY");?>  <?php if(($order)  ==  "money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('fee_amount','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照收手续费  <?php echo ($sortType); ?> ">收手续费  <?php if(($order)  ==  "fee_amount"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('pay_fee_amount','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照支出手续费  <?php echo ($sortType); ?> ">支出手续费  <?php if(($order)  ==  "pay_fee_amount"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('outer_notice_sn','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("OUTER_NOTICE_SN");?>  <?php echo ($sortType); ?> "><?php echo L("OUTER_NOTICE_SN");?>  <?php if(($order)  ==  "outer_notice_sn"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('memo','<?php echo ($sort); ?>','PaymentNotice','index')" title="按照<?php echo L("PAYMENT_MEMO");?><?php echo ($sortType); ?> "><?php echo L("PAYMENT_MEMO");?><?php if(($order)  ==  "memo"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th></tr><?php if(is_array($list)): $key = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$payment_notice): ++$key;$mod = ($key % 2 )?><tr class="row <?php if($key % 2 == 0): ?>row1<?php endif; ?>" ><td>&nbsp;<?php echo ($payment_notice["id"]); ?></td><td>&nbsp;<?php echo ($payment_notice["notice_sn"]); ?></td><td>&nbsp;<?php echo (to_date($payment_notice["create_time"])); ?></td><td>&nbsp;<?php echo (to_date($payment_notice["pay_time"])); ?></td><td>&nbsp;<?php echo (get_is_paid($payment_notice["is_paid"])); ?></td><td>&nbsp;<?php echo (get_user_name_real($payment_notice["user_id"])); ?></td><td>&nbsp;<?php echo (get_payment_name($payment_notice["payment_id"])); ?></td><td>&nbsp;<?php echo (format_price($payment_notice["money"])); ?></td><td>&nbsp;<?php echo (format_price($payment_notice["fee_amount"])); ?></td><td>&nbsp;<?php echo (format_price($payment_notice["pay_fee_amount"])); ?></td><td>&nbsp;<?php echo ($payment_notice["outer_notice_sn"]); ?></td><td>&nbsp;<?php echo ($payment_notice["memo"]); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="12" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->
 

<div class="blank5"></div>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>