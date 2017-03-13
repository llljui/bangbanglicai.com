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
<?php function get_given_type($given_type){
		if($given_type==1)
		return "现金红包";
		else
		return "积分";
	}
	function get_given_name_type($id,$given_name){
		if($given_name['given_name_type']==1){
			return "生日礼品";
		}
		elseif($given_name['given_name_type']==2){
			if($given_name['gift_id']>0){
				return "节日礼品"."(".$given_name['given_name'].")";
			}else{
				return "节日礼品";
			}
			
		}
		
	}
	function get_given_value($id,$given_name){
		if($given_name['given_type']==1){
			return $given_name['given_value']." 元现金红包";
		}else{
			return $given_name['given_value']." 积分";
		}
		
	}
	function get_send_state($send_state){
		if($send_state==1)
		return "已发放";
		else
		return "未发放";
	}
	
	function get_vip_id($vip_id)
    {
    	return M("VipType")->where(" id=".$vip_id)->getField("vip_grade");
    } ?>
<div class="main">
<div class="main_title">福利发放列表</div>
<div class="blank5"></div>
<div class="search_row">
	<form name="search" action="__APP__" method="get">
		VIP等级：
		<select name="vip_id">
			<option value="-1" <?php if(intval($_REQUEST['vip_id']) == -1 || intval($_REQUEST['vip_id']) == '' ): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
			<?php if(is_array($vip_list)): foreach($vip_list as $key=>$vip_item): ?><option value="<?php echo ($vip_item["id"]); ?>" <?php if($_REQUEST['vip_id'] == $vip_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($vip_item["vip_grade"]); ?></option><?php endforeach; endif; ?>
		</select>	
		<?php echo L("USER_NAME");?>：<input type="text" class="textbox" name="user_name" value="<?php echo trim($_REQUEST['user_name']);?>" style="width:100px;" />
		
		礼品名称：
		<select name="given_name_type">
			<option value="-1" <?php if(intval($_REQUEST['given_name_type']) == -1 || intval($_REQUEST['given_name_type']) == '' ): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
			<option value="1" <?php if($_REQUEST['given_name_type'] == 1): ?>selected="selected"<?php endif; ?>>生日礼品</option>
			<option value="2" <?php if($_REQUEST['given_name_type'] == 2): ?>selected="selected"<?php endif; ?>>节日礼品</option>
		</select>
		礼品类型：
		<select name="given_type">
			<option value="-1" <?php if(intval($_REQUEST['given_type']) == -1 || intval($_REQUEST['given_type']) == '' ): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
			<option value="1" <?php if($_REQUEST['given_type'] == 1): ?>selected="selected"<?php endif; ?>>现金红包</option>
			<option value="2" <?php if($_REQUEST['given_type'] == 2): ?>selected="selected"<?php endif; ?>>积分</option>
		</select>
		
		发放日期：
		<input type="text" class="textbox" name="begin_time" id="begin_time" value="<?php echo trim($_REQUEST['begin_time']);?>" onfocus="return showCalendar('begin_time', '%Y-%m-%d', false, false, 'begin_time');" style="width:130px" />
		-
		<input type="text" class="textbox" name="end_time" id="end_time" value="<?php echo trim($_REQUEST['end_time']);?>" onfocus="return showCalendar('end_time', '%Y-%m-%d', false, false, 'end_time');" style="width:130px" />
		<input type="hidden" value="VipWelfare" name="m" />
		<input type="hidden" value="given_record" name="a" />
		<input type="submit" class="button" value="<?php echo L("SEARCH");?>" />
		<input type="button" class="button" value="<?php echo L("EXPORT");?>" onclick="export_csv();" />
	</form>
</div>
<div class="blank5"></div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="9" class="topTd" >&nbsp; </td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="50px   "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VipWelfare','given_record')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','VipWelfare','given_record')" title="按照VIP会员   <?php echo ($sortType); ?> ">VIP会员   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('vip_id','<?php echo ($sort); ?>','VipWelfare','given_record')" title="按照VIP等级   <?php echo ($sortType); ?> ">VIP等级   <?php if(($order)  ==  "vip_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VipWelfare','given_record')" title="按照礼品名称   <?php echo ($sortType); ?> ">礼品名称   <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VipWelfare','given_record')" title="按照礼品值   <?php echo ($sortType); ?> ">礼品值   <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('given_num','<?php echo ($sort); ?>','VipWelfare','given_record')" title="按照数量   <?php echo ($sortType); ?> ">数量   <?php if(($order)  ==  "given_num"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('send_date','<?php echo ($sort); ?>','VipWelfare','given_record')" title="按照发放日期   <?php echo ($sortType); ?> ">发放日期   <?php if(($order)  ==  "send_date"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('send_state','<?php echo ($sort); ?>','VipWelfare','given_record')" title="按照发放状态<?php echo ($sortType); ?> ">发放状态<?php if(($order)  ==  "send_state"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th></tr><?php if(is_array($list)): $key = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$given_name): ++$key;$mod = ($key % 2 )?><tr class="row <?php if($key % 2 == 0): ?>row1<?php endif; ?>" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($given_name["id"]); ?>"></td><td>&nbsp;<?php echo ($given_name["id"]); ?></td><td>&nbsp;<?php echo (get_user_name_real($given_name["user_id"])); ?></td><td>&nbsp;<?php echo (get_vip_id($given_name["vip_id"])); ?></td><td>&nbsp;<?php echo (get_given_name_type($given_name["id"],$given_name)); ?></td><td>&nbsp;<?php echo (get_given_value($given_name["id"],$given_name)); ?></td><td>&nbsp;<?php echo ($given_name["given_num"]); ?></td><td>&nbsp;<?php echo ($given_name["send_date"]); ?></td><td>&nbsp;<?php echo (get_send_state($given_name["send_state"])); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="9" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->
 

<div class="blank5"></div>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>