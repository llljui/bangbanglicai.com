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

<div class="main">
<div class="main_title"><?php echo ($vo["name"]); ?> <a href="<?php echo u("Payment/index");?>" class="back_list"><?php echo L("BACK_LIST");?></a></div>
<div class="blank5"></div>
<form name="edit" action="__APP__" method="post" enctype="multipart/form-data">
<table class="form" cellpadding=0 cellspacing=0>
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("PAYMENT_NAME");?>:</td>
		<td class="item_input">
			<input type="text" value="<?php echo ($vo["name"]); ?>" name="name" />
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("CLASS_NAME");?>:</td>
		<td class="item_input">
			<?php echo ($vo["class_name"]); ?>
			<input type="hidden" value="<?php echo ($vo["class_name"]); ?>" name="class_name" />
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("IS_EFFECT");?>:</td>
		<td class="item_input">
			<lable><?php echo L("IS_EFFECT_1");?><input type="radio" name="is_effect" value="1" <?php if($vo['is_effect'] == 1): ?>checked="checked"<?php endif; ?> /></lable>
			<lable><?php echo L("IS_EFFECT_0");?><input type="radio" name="is_effect" value="0" <?php if($vo['is_effect'] == 0): ?>checked="checked"<?php endif; ?> /></lable>
		</td>
	</tr>
	<?php if($vo['class_name'] != 'Account' and $vo['class_name'] != 'Voucher'): ?><tr>
		<td class="item_title"><?php echo L("FEE_AMOUNT");?>:</td>
		<td class="item_input">
			<select name="fee_type">
				<option value="0" <?php if($vo['fee_type'] == 0): ?>selected="selected"<?php endif; ?>><?php echo L("FEE_TYPE_0");?></option>
				<option value="1" <?php if($vo['fee_type'] == 1): ?>selected="selected"<?php endif; ?>><?php echo L("FEE_TYPE_1");?></option>
			</select>
			<input type="text" class="textbox" name="fee_amount" value="<?php echo ($vo["fee_amount"]); ?>" />
			<span class="tip_span"><?php echo L("FEE_TYPE_TIP");?></span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">支付公司收费:</td>
		<td class="item_input">
			<select name="pay_fee_type">
				<option value="0" <?php if($vo['pay_fee_type'] == 0): ?>selected="selected"<?php endif; ?>><?php echo L("FEE_TYPE_0");?></option>
				<option value="1" <?php if($vo['pay_fee_type'] == 1): ?>selected="selected"<?php endif; ?>><?php echo L("FEE_TYPE_1");?></option>
			</select>
			<input type="text" class="textbox" name="pay_fee_amount" value="<?php echo ($vo["pay_fee_amount"]); ?>" />
			<span class="tip_span"><?php echo L("FEE_TYPE_TIP");?>(平台给支付公司的费用,用于统计公司成本)</span>
		</td>
	</tr>	
	
	<?php if($vo['class_name'] != 'TenpayBank'): ?><tr>
		<td class="item_title"><?php echo L("PAYMENT_LOGO");?>:</td>
		<td class="item_input">
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='<?php echo ($vo["logo"]); ?>' name='logo' id='keimg_h_logo_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='logo'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='<?php if($vo["logo"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["logo"]); ?><?php endif; ?>' target='_blank' id='keimg_a_logo' ><img src='<?php if($vo["logo"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["logo"]); ?><?php endif; ?>' id='keimg_m_logo' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($vo["logo"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='logo' title='删除'>
				</div>
			</div>
		</div>
		</span>
		</td>
	</tr><?php endif; ?><?php endif; ?>
	<tr>
		<td class="item_title"><?php echo L("SORT");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="sort" value="<?php echo ($vo["sort"]); ?>" />
		</td>
	</tr>
	<?php if($vo['class_name'] != 'Account' and $vo['class_name'] != 'Voucher' and $vo['class_name'] != 'TenpayBank'): ?><tr>
		<td class="item_title"><?php echo L("DESCRIPTION");?>:</td>
		<td class="item_input">
			<textarea class="textarea" name="description" ><?php echo ($vo["description"]); ?></textarea>
		</td>
	</tr><?php endif; ?>
	<?php if($data['config']): ?><tr>
		<td class="item_title"><?php echo L("PAYMENT_CONFIG");?>
		<?php if($vo['class_name'] == 'Otherpay'): ?><a href="javascript:void(0);" onclick="AddOtherpayCfg();">增加</a><?php endif; ?>:
		</td>
		<td class="item_input" id="J_OtherPayCfg">
			<?php if($vo['class_name'] == 'Otherpay'){ ?>
				<?php $j_idx = count($vo['config']['pay_name']);
					for($kkk=0;$kkk<$j_idx;$kkk++ ){ ?>
				<div class="OtherPayCfgBox">
				<?php if($kkk>0){ echo "<hr />" ;} ?>
				<?php if(is_array($data["config"])): foreach($data["config"] as $key=>$config): ?><?php $config_name = $key; ?>
					<span class="cfg_title"><?php echo trim($data['lang'][$key]);?>:</span>
					<span class="cfg_content">
					<input type="text" class="textbox" name="config[<?php echo ($key); ?>][]" value="<?php echo ($vo['config'][$config_name][$kkk]); ?>" />
					</span>
					<div class="blank5"></div><?php endforeach; endif; ?>
				<div class="clearfix">
					<a href="javascript:void(0);" onclick="removeOtherpayCfg(this);"> 去掉 </a>
				</div>
				</div>
				<?php } ?>
			<?php } else { ?>
			<?php if(is_array($data["config"])): foreach($data["config"] as $key=>$config): ?><?php $config_name = $key; ?>
				<span class="cfg_title"><?php echo trim($data['lang'][$key]);?>:</span>
				<span class="cfg_content">
				<?php if($config['INPUT_TYPE'] == 0): ?><input type="text" class="textbox" name="config[<?php echo ($key); ?>]" value="<?php echo ($vo['config'][$key]); ?>" />
				<?php elseif($config['INPUT_TYPE'] == 2): ?>
				<input type="password" class="textbox" name="config[<?php echo ($key); ?>]" value="<?php echo ($vo['config'][$key]); ?>" />
				<?php elseif($config['INPUT_TYPE'] == 3): ?>
					<?php if(is_array($config["VALUES"])): foreach($config["VALUES"] as $key=>$val): ?><label><input type="checkbox" name="config[<?php echo ($config_name); ?>][<?php echo ($val); ?>]" value="1" <?php if($vo['config'][$config_name][$val] == 1): ?>checked="checked"<?php endif; ?> ><?php echo trim($data['lang'][$config_name."_".$val]);?></label>
						<br /><?php endforeach; endif; ?>
				<?php elseif($config['INPUT_TYPE'] == 4): ?>	
				
				<textarea name="config[<?php echo ($key); ?>]" class="textarea"><?php echo ($vo['config'][$key]); ?></textarea>	
				<?php else: ?>
				<select name="config[<?php echo ($key); ?>]" >
					<?php if(is_array($config["VALUES"])): foreach($config["VALUES"] as $key=>$val): ?><option value="<?php echo ($val); ?>" <?php if($vo['config'][$config_name] == $val): ?>selected="selected"<?php endif; ?>><?php echo trim($data['lang'][$config_name."_".$val]);?></option><?php endforeach; endif; ?>
				</select><?php endif; ?>
				</span>
				<div class="blank5"></div><?php endforeach; endif; ?>
			<?php } ?>
		</td>
	</tr><?php endif; ?>
	<tr>
		<td class="item_title"></td>
		<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" value="<?php echo ($vo["id"]); ?>" name="id" />
			<input type="hidden" value="<?php echo ($vo["online_pay"]); ?>" name="online_pay" />
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="Payment" />
			<input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="update" />
			<!--隐藏元素-->
			<input type="submit" class="button" value="<?php echo L("EDIT");?>" />
			<input type="reset" class="button" value="<?php echo L("RESET");?>" />
		</td>
	</tr>
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>	 
</form>
</div>
<?php if($vo['class_name'] == 'Otherpay'): ?><div id="J_J_OtherPayCfgTemp" style="display:none;">
		<div class="OtherPayCfgBox">
			<hr />
			<?php if(is_array($data["config"])): foreach($data["config"] as $key=>$config): ?><?php $config_name = $key; ?>
				<span class="cfg_title"><?php echo trim($data['lang'][$key]);?>:</span>
				<span class="cfg_content">
				<input type="text" class="textbox" name="config[<?php echo ($key); ?>][]" value="" />
				</span>
				<div class="blank5"></div><?php endforeach; endif; ?>
			<div class="clearfix">
				<a href="javascript:void(0);" onclick="removeOtherpayCfg(this);"> 去掉 </a>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		function AddOtherpayCfg(){
			var tempHtml = $("#J_J_OtherPayCfgTemp").html();
			$("#J_OtherPayCfg").append(tempHtml);
		}
		function removeOtherpayCfg(o){
			if(confirm("要删除删除？")){
				$(o).parent().parent().remove();
			}
		}
	</script><?php endif; ?>
</body>
</html>