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

<script type="text/javascript">
    function trash()
    {
        location.href = ROOT+"?m=Departments&a=trash";
    }
</script>
<div class="main">
<div class="main_title"><?php echo ($main_title); ?></div>
<div class="blank5"></div>
<?php if($is_department != 1): ?><div class="button_row">
	<input type="button" class="button" value="<?php echo L("ADD");?>" onclick="add();" />
	<input type="button" class="button" value="<?php echo L("DEL");?>" onclick="del();" />
	<input type="button" class="button" value="回收站" onclick="trash();" />
</div>
<div class="blank5"></div>
<form name="search" action="__APP__" method="get">	
<div class="search_row">
	部门：
	<input type="text" class="textbox" name="adm_name"  value="<?php echo ($adm_name); ?>" />
	
	<input type="hidden" value="Departments" name="m" />
	<input type="hidden" value="index" name="a" />
	<input type="submit" class="button" value="<?php echo L("SEARCH");?>" />
	<input type="button" class="button" onclick="export_csv()" value="导出" />

</div>
</form>
<div class="blank5"></div><?php endif; ?>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="13" class="topTd" >&nbsp; </td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="50px   "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','Departments','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('adm_name','<?php echo ($sort); ?>','Departments','index')" title="按照部门   <?php echo ($sortType); ?> ">部门   <?php if(($order)  ==  "adm_name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('real_name','<?php echo ($sort); ?>','Departments','index')" title="按照姓名   <?php echo ($sortType); ?> ">姓名   <?php if(($order)  ==  "real_name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('mobile','<?php echo ($sort); ?>','Departments','index')" title="按照手机   <?php echo ($sortType); ?> ">手机   <?php if(($order)  ==  "mobile"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('role_id_format','<?php echo ($sort); ?>','Departments','index')" title="按照部门角色   <?php echo ($sortType); ?> ">部门角色   <?php if(($order)  ==  "role_id_format"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('referrals_rate','<?php echo ($sort); ?>','Departments','index')" title="按照提成系数(%)   <?php echo ($sortType); ?> ">提成系数(%)   <?php if(($order)  ==  "referrals_rate"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('referrals_count','<?php echo ($sort); ?>','Departments','index')" title="按照成员数   <?php echo ($sortType); ?> ">成员数   <?php if(($order)  ==  "referrals_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('referrals_money','<?php echo ($sort); ?>','Departments','index')" title="按照提成   <?php echo ($sortType); ?> ">提成   <?php if(($order)  ==  "referrals_money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="120px   "><a href="javascript:sortBy('is_effect','<?php echo ($sort); ?>','Departments','index')" title="按照<?php echo L("IS_EFFECT");?><?php echo ($sortType); ?> "><?php echo L("IS_EFFECT");?><?php if(($order)  ==  "is_effect"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="150px   "><a href="javascript:sortBy('login_time','<?php echo ($sort); ?>','Departments','index')" title="按照最后登录时间<?php echo ($sortType); ?> ">最后登录时间<?php if(($order)  ==  "login_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="120px"><a href="javascript:sortBy('login_ip','<?php echo ($sort); ?>','Departments','index')" title="按照最后登录IP<?php echo ($sortType); ?> ">最后登录IP<?php if(($order)  ==  "login_ip"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th class="op_action"><a href="javascript:void(0)" class="A_opration">操作</a></th></tr><?php if(is_array($list)): $key = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$department): ++$key;$mod = ($key % 2 )?><tr class="row <?php if($key % 2 == 0): ?>row1<?php endif; ?>" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($department["id"]); ?>"></td><td>&nbsp;<?php echo ($department["id"]); ?></td><td>&nbsp;<?php echo ($department["adm_name"]); ?></td><td>&nbsp;<?php echo ($department["real_name"]); ?></td><td>&nbsp;<?php echo ($department["mobile"]); ?></td><td>&nbsp;<?php echo ($department["role_id_format"]); ?></td><td>&nbsp;<?php echo ($department["referrals_rate"]); ?></td><td>&nbsp;<?php echo ($department["referrals_count"]); ?></td><td>&nbsp;<?php echo ($department["referrals_money"]); ?></td><td>&nbsp;<?php echo (get_is_effect($department["is_effect"],$department['id'])); ?></td><td>&nbsp;<?php echo (to_date($department["login_time"])); ?></td><td>&nbsp;<?php echo ($department["login_ip"]); ?></td><td class="op_action"><div class="viewOpBox"><a href="javascript:edit('<?php echo ($department["id"]); ?>')"><?php echo L("EDIT");?></a>&nbsp;<a href="javascript: del('<?php echo ($department["id"]); ?>')"><?php echo L("DEL");?></a>&nbsp;</div><a href="javascript:void(0);" class="opration">操作+</a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="13" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->


<div class="blank5"></div>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>