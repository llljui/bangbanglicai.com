<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
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

<div class="main">
<div class="main_title">微信第三方平台配置</div>
<div class="blank5"></div>
	<form method='post' id="form" name="form" action="__APP__">
	<table cellpadding="4" cellspacing="0" border="0" class="form">
		<tr>
			<td colspan="2" class="topTd"></td>
		</tr>
		<?php if(is_array($config)): foreach($config as $key=>$cfg): ?><tr>		
			<td class="item_title" style=" width:200px;"><?php echo ($cfg["title"]); ?></th>
			<td class="item_input">
				<?php if($cfg['type'] == 1): ?><div  style='margin-bottom:5px; '><textarea id='<?php echo ($cfg["name"]); ?>' name='<?php echo ($cfg["name"]); ?>' class='ketext' style=' height:150px;width:750px;' rel="true"><?php echo ($cfg["value"]); ?></textarea> </div><?php endif; ?>
				<?php if($cfg['type'] == 2): ?><span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='<?php echo ($cfg["value"]); ?>' name='<?php echo ($cfg["name"]); ?>' id='keimg_h_<?php echo ($cfg["name"]); ?>_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='<?php echo ($cfg["name"]); ?>'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='<?php if($cfg["value"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($cfg["value"]); ?><?php endif; ?>' target='_blank' id='keimg_a_<?php echo ($cfg["name"]); ?>' ><img src='<?php if($cfg["value"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($cfg["value"]); ?><?php endif; ?>' id='keimg_m_<?php echo ($cfg["name"]); ?>' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($cfg["value"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='<?php echo ($cfg["name"]); ?>' title='删除'>
				</div>
			</div>
		</div>
		</span><?php endif; ?>
				<?php if($cfg['type'] == 0): ?><input type="text" class="textbox <?php if($cfg['is_require'] == 1): ?>require<?php endif; ?>" name="<?php echo ($cfg["name"]); ?>" value="<?php echo ($cfg["value"]); ?>"   /><?php endif; ?>
				<?php if($cfg['type'] == 3): ?><textarea class="textbox " name="<?php echo ($cfg["name"]); ?>"  style="height:100px;width:250px;"><?php echo ($cfg["value"]); ?></textarea><?php endif; ?>
				<?php if($cfg['type'] == 4): ?><select name="<?php echo ($cfg["name"]); ?>">
	  					<?php if(is_array($cfg["value_scope"])): foreach($cfg["value_scope"] as $key=>$preset_value): ?><option value="<?php echo ($preset_value); ?>" <?php if($cfg['value'] == $preset_value): ?>selected="selected"<?php endif; ?>>
								<?php echo l("mconf_".$cfg['name']."_".$preset_value);?>
							</option><?php endforeach; endif; ?>
					</select><?php endif; ?>
			</td>
		</tr><?php endforeach; endif; ?>
		<tr>		
		
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
				<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="WeixinConf" />
			
				<input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="update" />
				<input type="submit" class="button" value="<?php echo L("EDIT");?>" />
				<input type="reset" class="button" value="<?php echo L("RESET");?>" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="bottomTd"></td>
		</tr>
	</table>
	</form>
</div>
</body>
</html>