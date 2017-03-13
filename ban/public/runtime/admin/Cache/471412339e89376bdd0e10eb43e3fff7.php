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

<?php function get_idcard_effect($effect)
	{
		return l("IS_EFFECT_".$effect);
	}
	function get_idcard_install($install)
	{
		return l("IS_INSTALL_".$install);
	} ?>
<script type="text/javascript">
	function uninstall(id)
	{
		if(confirm("<?php echo L("CONFIRM_DELETE");?>"))
		{
			location.href = ROOT + "?m=Idcard&a=uninstall&id="+id;
		}
	}
</script>
<div class="main">
<div class="main_title">身份证认证接口</div>
<div class="blank5"></div>

<table cellspacing="0" cellpadding="0" class="dataTable" id="dataTable">
	<tbody>
		<tr>
			<td class="topTd" colspan="7">&nbsp; </td>
			</tr>
			<tr class="row">
				<th>接口名称</th>
				<th><?php echo L("IS_EFFECT");?></th>
				<th><?php echo L("IS_INSTALL");?></th>
				<th><?php echo L("SORT");?></th>
				<th><?php echo L("TAG_LANG_OPERATE");?></th>
				</tr>
				<?php if(is_array($service_list)): foreach($service_list as $key=>$idcard_item): ?><tr class="row">
					<td><?php echo ($idcard_item["name"]); ?>
					<?php if($idcard_item['reg_url'] != ''): ?><a href="<?php echo ($idcard_item['reg_url']); ?>" target="_blank">
							<?php echo L("GO_TO_REG");?>
						</a><?php endif; ?>
					</td>
					<td><?php echo (get_idcard_effect($idcard_item["is_effect"])); ?></td>
					<td><?php echo (get_idcard_install($idcard_item["installed"])); ?></td>
					<td><?php echo ($idcard_item["sort"]); ?></td>
					<td>
						<?php if($idcard_item['installed'] == 0): ?><a href="<?php echo u("Idcard/install",array("class_name"=>$idcard_item['class_name']));?>"><?php echo L("INSTALL");?></a>
						<?php else: ?>
						<a href="<?php echo u("Idcard/edit",array("id"=>$idcard_item['id']));?>"><?php echo L("EDIT");?></a>
						<a href="javascript:uninstall(<?php echo ($idcard_item["id"]); ?>);" ><?php echo L("UNINSTALL");?></a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
				<tr><td class="bottomTd" colspan="7"> &nbsp;</td></tr>
			</tbody>
		</table>


</div>
</body>
</html>