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

<script type="text/javascript" src="__TMPL__Common/js/jquery.bgiframe.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.weebox.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/user.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<script type="text/javascript">
function address(user_id)
{
	location.href = ROOT + '?m=User&a=address&id='+user_id;
}
function company_trash()
{
    location.href = ROOT+"?m=User&a=company_trash";
}
function trash()
{
    location.href = ROOT+"?m=User&a=trash";
}
function black()
{
    location.href = ROOT+"?m=User&a=black";
}
function company_black()
{
    location.href = ROOT+"?m=User&a=company_black";
}

function deal_manage(user_name)
{
    location.href = ROOT+'?m=Deal&a=index&user_id='+user_name;
}

function vip_setting(user_id)
{
	$.weeboxs.open(ROOT+'?m=User&a=vip_setting&id='+user_id, {contentType:'ajax',showButton:false,title:"VIP变更",width:600,height:360});
	//location.href = ROOT + '?m=User&a=vip_setting&id='+user_id;
}
</script>
<?php function get_user_group($group_id)
	{
		$group_name = M("UserGroup")->where("id=".$group_id)->getField("name");
		if($group_name)
		{
			return $group_name;
		}
		else
		{
			return l("NO_GROUP");
		}
	}
	function get_user_level($id)
	{
		$level_name = M("UserLevel")->where("id=".$id)->getField("name");
		if($level_name)
		{
			return $level_name;
		}
		else
		{
			return "<span style='color:red'>无</span>";
		}
	}
	function get_referrals_name($user_id)
	{
		$user_name = M("User")->where("id=".$user_id)->getField("user_name");
		if($user_name)
		return $user_name;
		else
			return '无';
	}
	

	
	function ips_status($ips_acct_no){
		if($ips_acct_no==""){
			return "未同步";
		}
		else{
			return "已同步";
		}
	}
	function user_type_status($type){
		if($type==1){
			return "企业";
		}
		else{
			return "普通";
		}
	}
	function user_company($id,$user){
		if($user['user_type']==1){
			return "<a href='javascript:user_company(".$id.");'>公司</a>&nbsp;";
		}
	}
	function get_is_black($tag,$id){
		if($tag)
		{
			return "<span class='is_black' data='".$tag."' onclick='set_black(".$id.",this);'>是</span>";
		}
		else
		{
			return "<span class='is_black' data='".$tag."' onclick='set_black(".$id.",this);'>否</span>";
		}
	} ?>
<div class="main">
<div class="main_title"><?php echo L(MODULE_NAME."_".ACTION_NAME);?></div>
<div class="blank5"></div>
<div class="button_row">
	<?php if(ACTION_NAME == 'index' || ACTION_NAME == 'company_index'): ?><input type="button" class="button" value="<?php echo L("ADD");?>" onclick="add();" /><?php endif; ?>
	<input type="button" class="button" value="<?php echo L("DEL");?>" onclick="del();" />
	<?php if(ACTION_NAME == 'index' ): ?><input type="button" class="button" value="回收站"  onclick="trash();" /><?php endif; ?>
	<?php if(ACTION_NAME == 'company_index' ): ?><input type="button" class="button" value="回收站"  onclick="company_trash();" /><?php endif; ?>
	<?php if(ACTION_NAME == 'index' ): ?><input type="button" class="button" value="黑名单"  onclick="black();" /><?php endif; ?>
	<?php if(ACTION_NAME == 'company_index' ): ?><input type="button" class="button" value="黑名单"  onclick="company_black();" /><?php endif; ?>
</div>

<div class="blank5"></div>
<div class="search_row">
	<form name="search" action="__APP__" method="get">	
		<?php echo L("USER_NAME");?>：<input type="text" class="textbox" name="user_name" value="<?php echo trim($_REQUEST['user_name']);?>" style="width:100px;" />
		<?php echo L("USER_EMAIL");?>：<input type="text" class="textbox" name="email" value="<?php echo trim($_REQUEST['email']);?>" style="width:100px;" />
		<?php echo L("USER_MOBILE");?>：<input type="text" class="textbox" name="mobile" value="<?php echo trim($_REQUEST['mobile']);?>" style="width:100px;" />
		模糊查询:<input type="checkbox" name="is_mohu" value="1" <?php if(intval($_REQUEST['is_mohu']) == 1): ?>checked="checked"<?php endif; ?> />
		<?php echo L("REFERRALS_NAME");?>：<input type="text" class="textbox" name="pid_name" value="<?php echo trim($_REQUEST['pid_name']);?>" style="width:100px;" />
		<?php echo L("USER_GROUP");?>: 
		<select name="group_id">
				<option value="0" <?php if(intval($_REQUEST['group_id']) == 0): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
				<?php if(is_array($group_list)): foreach($group_list as $key=>$group_item): ?><option value="<?php echo ($group_item["id"]); ?>" <?php if(intval($_REQUEST['group_id']) == $group_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($group_item["name"]); ?></option><?php endforeach; endif; ?>
		</select>
		<?php if(ACTION_NAME == 'index' or ACTION_NAME == 'company_index'): ?><select name="is_effect">
				<option value="-1" <?php if($_REQUEST['is_effect'] == -1 || $_REQUEST['is_effect'] == ''): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
				<option value="1" <?php if(intval($_REQUEST['is_effect']) == 1): ?>selected="selected"<?php endif; ?>>有效</option>
				<option value="0" <?php if(intval($_REQUEST['is_effect']) == 0 && isset($_REQUEST['is_effect'])): ?>selected="selected"<?php endif; ?>>无效</option>
				
		</select><?php endif; ?>
		<div class="blank5"></div>
		注册时间：<input type="text" class="textbox" name="begin_time" id="begin_time" value="<?php echo trim($_REQUEST['begin_time']);?>" onfocus="return showCalendar('begin_time', '%Y-%m-%d %H:%M:%S', false, false, 'begin_time');" style="width:130px" />
		-
		<input type="text" class="textbox" name="end_time" id="end_time" value="<?php echo trim($_REQUEST['end_time']);?>" onfocus="return showCalendar('end_time', '%Y-%m-%d %H:%M:%S', false, false, 'end_time');" style="width:130px" />
		
		<input type="hidden" name="user_type" value="<?php if(ACTION_NAME == 'index' or ACTION_NAME == 'register'): ?>0<?php else: ?>1<?php endif; ?>" />
		<input type="hidden" value="User" name="m" />
		<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a" />
		<input type="submit" class="button" value="<?php echo L("SEARCH");?>" />
    	<input type="button" class="button" value="<?php echo L("EXPORT");?>" onclick="export_csv();" />
	</form>
</div>
<div class="blank5"></div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="16" class="topTd" >&nbsp; </td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="50px"><a href="javascript:sortBy('id','<?php echo ($sort); ?>','User','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_name','<?php echo ($sort); ?>','User','index')" title="按照会员<?php echo ($sortType); ?> ">会员<?php if(($order)  ==  "user_name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('email','<?php echo ($sort); ?>','User','index')" title="按照邮箱<?php echo ($sortType); ?> ">邮箱<?php if(($order)  ==  "email"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('mobile','<?php echo ($sort); ?>','User','index')" title="按照手机<?php echo ($sortType); ?> ">手机<?php if(($order)  ==  "mobile"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('money','<?php echo ($sort); ?>','User','index')" title="按照余额<?php echo ($sortType); ?> ">余额<?php if(($order)  ==  "money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('lock_money','<?php echo ($sort); ?>','User','index')" title="按照冻结<?php echo ($sortType); ?> ">冻结<?php if(($order)  ==  "lock_money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('point','<?php echo ($sort); ?>','User','index')" title="按照信用<?php echo ($sortType); ?> ">信用<?php if(($order)  ==  "point"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('quota','<?php echo ($sort); ?>','User','index')" title="按照额度<?php echo ($sortType); ?> ">额度<?php if(($order)  ==  "quota"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('u_alipay','<?php echo ($sort); ?>','User','index')" title="按照支付宝账号<?php echo ($sortType); ?> ">支付宝账号<?php if(($order)  ==  "u_alipay"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('level_id','<?php echo ($sort); ?>','User','index')" title="按照等级<?php echo ($sortType); ?> ">等级<?php if(($order)  ==  "level_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('pid','<?php echo ($sort); ?>','User','index')" title="按照<?php echo L("REFERRALS_USER");?><?php echo ($sortType); ?> "><?php echo L("REFERRALS_USER");?><?php if(($order)  ==  "pid"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_effect','<?php echo ($sort); ?>','User','index')" title="按照<?php echo L("IS_EFFECT");?><?php echo ($sortType); ?> "><?php echo L("IS_EFFECT");?><?php if(($order)  ==  "is_effect"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_black','<?php echo ($sort); ?>','User','index')" title="按照黑名单<?php echo ($sortType); ?> ">黑名单<?php if(($order)  ==  "is_black"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('ips_acct_no','<?php echo ($sort); ?>','User','index')" title="按照第三方<?php echo ($sortType); ?> ">第三方<?php if(($order)  ==  "ips_acct_no"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th class="op_action"><a href="javascript:void(0)" class="A_opration">操作</a></th></tr><?php if(is_array($list)): $key = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$user): ++$key;$mod = ($key % 2 )?><tr class="row <?php if($key % 2 == 0): ?>row1<?php endif; ?>" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($user["id"]); ?>"></td><td>&nbsp;<?php echo ($user["id"]); ?></td><td>&nbsp;<a href="javascript:edit('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["user_name"]); ?></a></td><td>&nbsp;<?php echo ($user["email"]); ?></td><td>&nbsp;<?php echo ($user["mobile"]); ?></td><td>&nbsp;<?php echo (format_price($user["money"])); ?></td><td>&nbsp;<?php echo ($user["lock_money"]); ?></td><td>&nbsp;<?php echo ($user["point"]); ?></td><td>&nbsp;<?php echo (format_price($user["quota"])); ?></td><td>&nbsp;<?php echo ($user["u_alipay"]); ?></td><td>&nbsp;<?php echo (get_user_level($user["level_id"])); ?></td><td>&nbsp;<?php echo (get_referrals_name($user["pid"])); ?></td><td>&nbsp;<?php echo (get_is_effect($user["is_effect"],$user['id'])); ?></td><td>&nbsp;<?php echo (get_is_black($user["is_black"],$user['id'])); ?></td><td>&nbsp;<?php echo (ips_status($user["ips_acct_no"])); ?></td><td class="op_action"><div class="viewOpBox"><a href="javascript:edit('<?php echo ($user["id"]); ?>')"><?php echo L("EDIT");?></a>&nbsp;<a href="javascript: del('<?php echo ($user["id"]); ?>')"><?php echo L("DEL");?></a>&nbsp;<a href="javascript:address('<?php echo ($user["id"]); ?>')">收货地址</a>&nbsp;<?php echo (user_company($user["id"],$user)); ?><a href="javascript:user_work('<?php echo ($user["id"]); ?>')"><?php echo L("USER_WORK_SHORT");?></a>&nbsp;<a href="javascript: user_passed('<?php echo ($user["id"]); ?>')"><?php echo L("USER_PASSED_SHORT");?></a>&nbsp;<a href="javascript:account_detail('<?php echo ($user["id"]); ?>')"><?php echo L("USER_ACCOUNT_DETAIL_SHORT");?></a>&nbsp;<a href="javascript:info_down('<?php echo ($user["id"]); ?>')">资料</a>&nbsp;<a href="javascript:view_info('<?php echo ($user["id"]); ?>')">资料展示</a>&nbsp;<a href="javascript:bank_manage('<?php echo ($user["id"]); ?>')">银行卡</a>&nbsp;<a href="javascript:load_manage('<?php echo ($user["id"]); ?>')">投标记录</a>&nbsp;<a href="javascript:vip_setting('<?php echo ($user["id"]); ?>')">VIP变更</a>&nbsp;<a href="javascript:deal_manage('<?php echo ($user["id"]); ?>')">贷款记录</a>&nbsp;</div><a href="javascript:void(0);" class="opration">操作+</a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="16" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->
 

<div class="blank5"></div>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>