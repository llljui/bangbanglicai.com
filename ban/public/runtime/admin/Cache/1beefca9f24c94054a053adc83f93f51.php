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
<script type="text/javascript">
	
	function total_info(){
		location.href = ROOT + '?m=StatisticsBorrow&a=tender_total_info';
	}
</script>
<script type="text/javascript">	
	function export_csv_total()
	{
		var query = $("#search_form").serialize();
		query = query.replace("&m=StatisticsBorrow","");
		query = query.replace("&a=tender_total","");
		var url= ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=export_csv_total"+"&"+query;
		location.href = url;
	}
	
</script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<div class="main">
<div class="main_title">借出总统计</div>
<div class="blank5"></div>
	
	
	<form name="search" id = "search_form"  action="__APP__" method="get">	
		<input type="button" class="button" value="查看所有投资人" onclick="total_info()" />	
		预计回款时间：
		<input type="text" class="textbox" name="begin_time" id="begin_time" value="<?php echo trim($_REQUEST['begin_time']);?>" onfocus="return showCalendar('begin_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_begin_time');" style="width:130px" />
		<input type="button" class="button" id="btn_begin_time" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('begin_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_begin_time');" />	
		-
		<input type="text" class="textbox" name="end_time" id="end_time" value="<?php echo trim($_REQUEST['end_time']);?>" onfocus="return showCalendar('end_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_end_time');" style="width:130px" />
		<input type="button" class="button" id="btn_end_time" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('end_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_end_time');" />
		
		<input type="hidden" value="StatisticsBorrow" name="m" />
		<input type="hidden" value="tender_total" name="a" />
		
		<input type="submit" class="button" value="搜索" />
		<input type="button" class="button" value="<?php echo L("EXPORT");?>" onclick="export_csv_total();" />
	</form>
	
	
<div class="blank5"></div>

<div class="blank5"></div>
	
	
	
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="11" class="topTd" >&nbsp; </td></tr><tr class="row" ><th><a href="javascript:sortBy('投资人数','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照投资人数   <?php echo ($sortType); ?> ">投资人数   <?php if(($order)  ==  "投资人数"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('成功投资金额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照成功投资金额   <?php echo ($sortType); ?> ">成功投资金额   <?php if(($order)  ==  "成功投资金额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('奖励总额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照奖励总额   <?php echo ($sortType); ?> ">奖励总额   <?php if(($order)  ==  "奖励总额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('待收总额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照待收总额   <?php echo ($sortType); ?> ">待收总额   <?php if(($order)  ==  "待收总额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('待收本金总额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照待收本金总额   <?php echo ($sortType); ?> ">待收本金总额   <?php if(($order)  ==  "待收本金总额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('待收利润总额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照待收利润总额   <?php echo ($sortType); ?> ">待收利润总额   <?php if(($order)  ==  "待收利润总额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('已收总额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照已收总额   <?php echo ($sortType); ?> ">已收总额   <?php if(($order)  ==  "已收总额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('已收本金总额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照已收本金总额   <?php echo ($sortType); ?> ">已收本金总额   <?php if(($order)  ==  "已收本金总额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('已收利润总额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照已收利润总额   <?php echo ($sortType); ?> ">已收利润总额   <?php if(($order)  ==  "已收利润总额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('提前还款罚息总额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照提前还款罚息总额   <?php echo ($sortType); ?> ">提前还款罚息总额   <?php if(($order)  ==  "提前还款罚息总额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('逾期还款罚金总额','<?php echo ($sort); ?>','StatisticsBorrow','tender_total')" title="按照逾期还款罚金总额   <?php echo ($sortType); ?> ">逾期还款罚金总额   <?php if(($order)  ==  "逾期还款罚金总额"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th></tr><?php if(is_array($list)): $key = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$deal): ++$key;$mod = ($key % 2 )?><tr class="row <?php if($key % 2 == 0): ?>row1<?php endif; ?>" ><td>&nbsp;<?php echo ($deal["投资人数"]); ?></td><td>&nbsp;<?php echo (format_price($deal["成功投资金额"])); ?></td><td>&nbsp;<?php echo (format_price($deal["奖励总额"])); ?></td><td>&nbsp;<?php echo (format_price($deal["待收总额"])); ?></td><td>&nbsp;<?php echo (format_price($deal["待收本金总额"])); ?></td><td>&nbsp;<?php echo (format_price($deal["待收利润总额"])); ?></td><td>&nbsp;<?php echo (format_price($deal["已收总额"])); ?></td><td>&nbsp;<?php echo (format_price($deal["已收本金总额"])); ?></td><td>&nbsp;<?php echo (format_price($deal["已收利润总额"])); ?></td><td>&nbsp;<?php echo (format_price($deal["提前还款罚息总额"])); ?></td><td>&nbsp;<?php echo (format_price($deal["逾期还款罚金总额"])); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="11" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->
 
					
	
<div class="blank5"></div>
<div class="page"><?php echo ($page); ?></div>

</div>

</body>
</html>