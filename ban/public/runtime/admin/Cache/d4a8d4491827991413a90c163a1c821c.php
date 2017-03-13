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

<?php function get_deal_edit($id,$deal)
	{
		return "<a href='".u("Deal/publish_edit",array("id"=>$id))."'>审核操作</a>";
	}
	
	function publish_status($status){
		if($status==1){
			return "等待初审";
		}
		elseif($status==2){
			return "初审通过";
		}
		elseif($status==3){
			return "复审失败";
		}
	}
	
	function get_deal_editname($id,$deal){
		return "<a href='".u("Deal/publish_edit",array("id"=>$id))."'>".msubstr($deal['name'])."</a>";
	} ?>
<div class="main">
<div class="main_title">未审核贷款</div>
<div class="blank5"></div>
<div class="button_row">
	<input type="button" class="button" value="<?php echo L("FOREVERDEL");?>" onclick="foreverdel();" />
</div>
<div class="blank5"></div>

<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="13" class="topTd" >&nbsp; </td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="50px   "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','Deal','publish')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','Deal','publish')" title="按照<?php echo L("DEAL_NAME");?>   <?php echo ($sortType); ?> "><?php echo L("DEAL_NAME");?>   <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','Deal','publish')" title="按照借款人   <?php echo ($sortType); ?> ">借款人   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('borrow_amount','<?php echo ($sort); ?>','Deal','publish')" title="按照贷款金额   <?php echo ($sortType); ?> ">贷款金额   <?php if(($order)  ==  "borrow_amount"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('rate','<?php echo ($sort); ?>','Deal','publish')" title="按照利率(%)   <?php echo ($sortType); ?> ">利率(%)   <?php if(($order)  ==  "rate"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('repay_time','<?php echo ($sort); ?>','Deal','publish')" title="按照期数   <?php echo ($sortType); ?> ">期数   <?php if(($order)  ==  "repay_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('type_id','<?php echo ($sort); ?>','Deal','publish')" title="按照借款用途   <?php echo ($sortType); ?> ">借款用途   <?php if(($order)  ==  "type_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('loantype','<?php echo ($sort); ?>','Deal','publish')" title="按照还款方式   <?php echo ($sortType); ?> ">还款方式   <?php if(($order)  ==  "loantype"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','Deal','publish')" title="按照发布时间   <?php echo ($sortType); ?> ">发布时间   <?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('publish_wait','<?php echo ($sort); ?>','Deal','publish')" title="按照审核状态   <?php echo ($sortType); ?> ">审核状态   <?php if(($order)  ==  "publish_wait"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','Deal','publish')" title="按照审核操作<?php echo ($sortType); ?> ">审核操作<?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th class="op_action"><a href="javascript:void(0)" class="A_opration">操作</a></th></tr><?php if(is_array($list)): $key = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$deal): ++$key;$mod = ($key % 2 )?><tr class="row <?php if($key % 2 == 0): ?>row1<?php endif; ?>" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($deal["id"]); ?>"></td><td>&nbsp;<?php echo ($deal["id"]); ?></td><td>&nbsp;<?php echo (get_deal_editname($deal["id"],$deal)); ?></td><td>&nbsp;<?php echo (get_user_name_real($deal["user_id"])); ?></td><td>&nbsp;<?php echo (format_price($deal["borrow_amount"])); ?></td><td>&nbsp;<?php echo ($deal["rate"]); ?></td><td>&nbsp;<?php echo (get_time_type($deal["repay_time"],$deal)); ?></td><td>&nbsp;<?php echo (get_loan_type_name($deal["type_id"])); ?></td><td>&nbsp;<?php echo (loantypename($deal["loantype"],0)); ?></td><td>&nbsp;<?php echo (to_date($deal["create_time"],'Y-m-d')); ?></td><td>&nbsp;<?php echo (publish_status($deal["publish_wait"])); ?></td><td>&nbsp;<?php echo (get_deal_edit($deal["id"],$deal)); ?></td><td class="op_action"><a href="javascript:foreverdel('<?php echo ($deal["id"]); ?>')"><?php echo L("FOREVERDEL");?></a>&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="13" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->
 

<div class="blank5"></div>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>