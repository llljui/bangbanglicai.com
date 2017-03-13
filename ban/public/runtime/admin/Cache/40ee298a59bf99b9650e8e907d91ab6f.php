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

<script type="text/javascript" src="__TMPL__Common/js/article.js"></script>
<div class="main">
<div class="main_title"><?php echo ($vo["title"]); ?><?php echo L("EDIT");?> <a href="<?php echo u("Article/index");?>" class="back_list"><?php echo L("BACK_LIST");?></a></div>
<div class="blank5"></div>
<form name="edit" action="__APP__" method="post" enctype="multipart/form-data">
<table class="form" cellpadding=0 cellspacing=0>
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("ARTICLE_TITLE");?>:</td>
		<td class="item_input"><input type="text" class="textbox require" name="title" value="<?php echo ($vo["title"]); ?>" /></td>
	</tr>
	<tr>
		<td class="item_title">图标:</td>
		<td class="item_input">
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='<?php echo ($vo["icon"]); ?>' name='icon' id='keimg_h_icon_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='icon'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='<?php if($vo["icon"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["icon"]); ?><?php endif; ?>' target='_blank' id='keimg_a_icon' ><img src='<?php if($vo["icon"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["icon"]); ?><?php endif; ?>' id='keimg_m_icon' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($vo["icon"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='icon' title='删除'>
				</div>
			</div>
		</div>
		</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">附标题:</td>
		<td class="item_input"><input type="text" class="textbox" name="sub_title" value="<?php echo ($vo["sub_title"]); ?>" /> 
			<span class="tip_span">[仅用于公告的推荐显示]</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">简介:</td>
		<td class="item_input"><textarea class="textarea" name="brief" ><?php echo ($vo["brief"]); ?></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("URL_UNAME");?>:</td>
		<td class="item_input"><input type="text" class="textbox word-only" name="uname" value="<?php echo ($vo["uname"]); ?>" /> </td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("REL_URL");?>:</td>
		<td class="item_input"><input type="text" class="textbox" name="rel_url" value="<?php echo ($vo["rel_url"]); ?>" /></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("SORT");?>:</td>
		<td class="item_input"><input type="text" class="textbox" name="sort" value="<?php echo ($vo["sort"]); ?>" /></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("CATE_TREE");?>:</td>
		<td class="item_input">
		<select name="cate_id" class="require">
			<option value="0">==<?php echo L("NO_SELECT_CATE");?>==</option>
			<?php if(is_array($cate_tree)): foreach($cate_tree as $key=>$cate_item): ?><option value="<?php echo ($cate_item["id"]); ?>" <?php if($vo['cate_id'] == $cate_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($cate_item["title_show"]); ?></option><?php endforeach; endif; ?>
		</select>
		</td>
	</tr>
	
	<tr>
		<td class="item_title"><?php echo L("IS_EFFECT");?>:</td>
		<td class="item_input">
			<lable><?php echo L("IS_EFFECT_1");?><input type="radio" name="is_effect" value="1" <?php if($vo['is_effect'] == 1): ?>checked="checked"<?php endif; ?> /></lable>
			<lable><?php echo L("IS_EFFECT_0");?><input type="radio" name="is_effect" value="0" <?php if($vo['is_effect'] == 0): ?>checked="checked"<?php endif; ?> /></lable>
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("ARTICLE_CONTENT");?>:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='editor' name='content' class='ketext' style='width:750px; height:350px;' rel="true"><?php echo ($vo["content"]); ?></textarea> </div>
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("ARTICLE_SEO_TITLE");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_title" ><?php echo ($vo["seo_title"]); ?></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("ARTICLE_SEO_KEYWORD");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_keyword" ><?php echo ($vo["seo_keyword"]); ?></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("ARTICLE_SEO_DESCRIPTION");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_description" ><?php echo ($vo["seo_description"]); ?></textarea></td>
	</tr>
	<tr>
		<td class="item_title"></td>
		<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="Article" />
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
</body>
</html>