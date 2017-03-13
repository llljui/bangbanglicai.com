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
<?php function f_to_date($date){
		return to_date($date,"Y-m-d H:i");
	}
	
	function get_vip_state($vip_state){
		if($vip_state==1)
		return "有效";
		else
		return "无效";
	}
	
	function get_vip_id($vip_id)
    {
    	return M("VipType")->where(" id=".$vip_id)->getField("vip_grade");
    }
	function show_bothday($id,$user)
	{
		return $user['byear']."-".$user['bmonth']."-".$user['bday'];
	}
	
	function get_customer($id){
		if($id > 0)
			return M("Customer")->where("id=".$id)->getField("name");
	}
	
	function get_customer_service($id,$user){
		$str = "";
		if($user['customer_id']==0){
			$str .= '<a href="javascript:add_customer_service('.$id.');">分配客服</a>&nbsp;';
		}else{
			$str .= '<a href="javascript:show_customer_service('.$id.');">查看/编辑客服</a>&nbsp;';
		}
		return $str;
	} ?>
<script type="text/javascript">
	
	function add_customer_service(id)
	{
		//window.location.href=ROOT+'?m=VipPrivilege&a=add_sc_service&id='+id;
		$.weeboxs.open(ROOT+'?m=VipPrivilege&a=add_sc_service&id='+id+"&status=<?php echo $_REQUEST['status']; ?>", {contentType:'ajax',showButton:false,title:"分配客服",width:600,height:245});
	}
	function show_customer_service(id)
	{
		//window.location.href=ROOT+'?m=VipPrivilege&a=sc_service&id='+id;
		$.weeboxs.open(ROOT+'?m=VipPrivilege&a=sc_service&id='+id+"&status=<?php echo $_REQUEST['status']; ?>", {contentType:'ajax',showButton:false,title:"查看/编辑客服",width:600,height:305});
	}
	
	function modify_vip_user(id){
		$.weeboxs.open(ROOT+'?m=VipPrivilege&a=send_gift&id='+id+"&status=<?php echo $_REQUEST['status']; ?>", {contentType:'ajax',showButton:false,title:"生日礼品发放",width:600,height:400});
	}
	
	function modify_sc_service(id){
		location.href = ROOT + '?m=VipPrivilege&a=add_sc_service&id='+id;
		//location.href = ROOT + '?m=VipPrivilege&a=edit&id='+id;
	}
	
	function send(id)
	{
		if(!id)
		{
			idBox = $(".key:checked");
			if(idBox.length == 0)
			{
				alert("请选择要发放的选项！");
				return;
			}
			idArray = new Array();
			$.each( idBox, function(i, n){
				idArray.push($(n).val());
			});
			id = idArray.join(",");
		}
	//	if(confirm(LANG['CONFIRM_DELETE']))
		$.ajax({ 
				url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=send_gift_all&id="+id, 
				data: "ajax=1",
				dataType: "json",
				success: function(obj){
					$("#info").html(obj.info);
					if(obj.status==1)
					location.href=location.href;
				}
		});
	}
	
</script>
<div class="main">
<div class="main_title">VIP会员表</div>
<div class="blank5"></div>
<div class="button_row">
	<input type="button" class="button" value="发放" onclick="send();" />
</div>
<div class="blank5"></div>
<div class="search_row">
	<form name="search" action="__APP__" method="get">
		VIP等级：
		<select name="vip_id">
			<option value="-1" <?php if(intval($_REQUEST['vip_id']) == -1 || intval($_REQUEST['vip_id']) == '' ): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
			<?php if(is_array($vip_list)): foreach($vip_list as $key=>$vip_item): ?><option value="<?php echo ($vip_item["id"]); ?>" <?php if($_REQUEST['vip_id'] == $vip_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($vip_item["vip_grade"]); ?></option><?php endforeach; endif; ?>
			</select>	
		<?php echo L("USER_NAME");?>：<input type="text" class="textbox" name="user_name" value="<?php echo trim($_REQUEST['user_name']);?>" style="width:100px;" />
		VIP状态：
		<select name="vip_state" >
			<option value="-1" <?php if(intval($_REQUEST['vip_state']) == -1 || intval($_REQUEST['vip_state']) == '' ): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
			<option value="1" <?php if(intval($_REQUEST['vip_state']) == 1): ?>selected="selected"<?php endif; ?>>有效</option>
			<option value="0" <?php if(intval($_REQUEST['vip_state']) == 0 && isset($_REQUEST['vip_state']) ): ?>selected="selected"<?php endif; ?>>无效</option>
		</select>
		所属客服：
		<select name="cnames">
			<option value="-3" <?php if(intval($_REQUEST['cnames']) == -3 || intval($_REQUEST['cnames']) == '' ): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
			<option value="-2" <?php if(intval($_REQUEST['cnames']) == -2): ?>selected="selected"<?php endif; ?>>未分配</option>
			<option value="-1" <?php if(intval($_REQUEST['cnames']) == -1): ?>selected="selected"<?php endif; ?>>已分配</option>
			<?php if(is_array($customer_cate)): foreach($customer_cate as $key=>$item): ?><option value="<?php echo ($item["id"]); ?>" <?php if(intval($_REQUEST['cnames']) == $item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($item["name"]); ?></option><?php endforeach; endif; ?>
		</select>
		生日时间：
		<input type="text" class="textbox" name="begin_time" id="begin_time" value="<?php echo trim($_REQUEST['begin_time']);?>" onfocus="return showCalendar('begin_time', '%Y-%m-%d', false, false, 'begin_time');" style="width:130px" />
		-
		<input type="text" class="textbox" name="end_time" id="end_time" value="<?php echo trim($_REQUEST['end_time']);?>" onfocus="return showCalendar('end_time', '%Y-%m-%d', false, false, 'end_time');" style="width:130px" />
		
		<input type="hidden" name="user_type" value="<?php if(ACTION_NAME == 'info'): ?>0<?php else: ?>1<?php endif; ?>" />
		<input type="hidden" value="VipPrivilege" name="m" />
		<input type="hidden" value="vip_user" name="a" />
		<input type="submit" class="button" value="<?php echo L("SEARCH");?>" />
		
	</form>
</div>
<div class="blank5"></div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="9" class="topTd" >&nbsp; </td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="50px  "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VipPrivilege','vip_user')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VipPrivilege','vip_user')" title="按照<?php echo L("USER_NAME");?>  <?php echo ($sortType); ?> "><?php echo L("USER_NAME");?>  <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('vip_id','<?php echo ($sort); ?>','VipPrivilege','vip_user')" title="按照VIP等级  <?php echo ($sortType); ?> ">VIP等级  <?php if(($order)  ==  "vip_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('vip_state','<?php echo ($sort); ?>','VipPrivilege','vip_user')" title="按照VIP状态  <?php echo ($sortType); ?> ">VIP状态  <?php if(($order)  ==  "vip_state"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('money','<?php echo ($sort); ?>','VipPrivilege','vip_user')" title="按照<?php echo L("USER_MONEY");?>  <?php echo ($sortType); ?> "><?php echo L("USER_MONEY");?>  <?php if(($order)  ==  "money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VipPrivilege','vip_user')" title="按照生日  <?php echo ($sortType); ?> ">生日  <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('customer_id','<?php echo ($sort); ?>','VipPrivilege','vip_user')" title="按照客服专员  <?php echo ($sortType); ?> ">客服专员  <?php if(($order)  ==  "customer_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th class="op_action"><a href="javascript:void(0)" class="A_opration">操作</a></th></tr><?php if(is_array($list)): $key = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$user): ++$key;$mod = ($key % 2 )?><tr class="row <?php if($key % 2 == 0): ?>row1<?php endif; ?>" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($user["id"]); ?>"></td><td>&nbsp;<?php echo ($user["id"]); ?></td><td>&nbsp;<?php echo (get_user_name_real($user["id"])); ?></td><td>&nbsp;<?php echo (get_vip_id($user["vip_id"])); ?></td><td>&nbsp;<?php echo (get_is_effect($user["vip_state"],$user['id'])); ?></td><td>&nbsp;<?php echo (format_price($user["money"])); ?></td><td>&nbsp;<?php echo (show_bothday($user["id"],$user)); ?></td><td>&nbsp;<?php echo (get_customer($user["customer_id"])); ?></td><td class="op_action"><div class="viewOpBox"><a href="javascript:modify_vip_user('<?php echo ($user["id"]); ?>')">查看/发放 </a>&nbsp;<?php echo (get_customer_service($user["id"],$user)); ?></div><a href="javascript:void(0);" class="opration">操作+</a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="9" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->
 

<div class="blank5"></div>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>