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
<?php function get_gift_status($status){
		if($status==1)
		return "已发放";
		else
		return "未发放";
	}
	
	function get_deal_url($deal_id){
		$name=M("Deal")->where(" id=".$deal_id)->getField("name");
		return '<a href="'.__ROOT__.'/index.php?ctl=deal&id='.$deal_id.'" target="_blank">'.$name.'</a>';
	}
	
	function get_gift_type($gift_type){
		if($gift_type==1){
			return "非提现金";
		}elseif($gift_type==2){
			return "收益率";
		}elseif($gift_type==3){
			return "积分";
		}elseif($gift_type==4){
			return "礼品";
		}
	}
	
	function get_gift_value($id,$gift){
		if($gift['gift_type']==1){
			return $gift['gift_value']." 元";
		}elseif($gift['gift_type']==2){
			if($gift['reward_money']!=0){
				return $gift['gift_value']."% (金额".$gift['reward_money']." 元)";
			}else{
				$reward_money=M("DealLoadRepay")->where(" load_id=".$gift['load_id'])->getField("reward_money");
				if($reward_money!=0){
					return $gift['gift_value']."% (金额".$reward_money." 元)";
				}else{
					return $gift['gift_value']."%";
				}
				
			}
			
		}elseif($gift['gift_type']==3){
			return $gift['gift_value']." 积分";
		}elseif($gift['gift_type']==4){
			$name=M("VipGift")->where(" id=".$gift['gift_value'])->getField("name");
			return $name;
		}
	} ?>
<div class="main">
<div class="main_title">奖励发放列表</div>
<div class="blank5"></div>

<div class="blank5"></div>
<div class="search_row">
	<form name="search" action="__APP__" method="get">
		<?php echo L("USER_NAME");?>：<input type="text" class="textbox" name="user_name" value="<?php echo trim($_REQUEST['user_name']);?>" style="width:100px;" />
		标题：<input type="text" class="textbox" name="name" value="<?php echo trim($_REQUEST['name']);?>" style="width:100px;" />
		奖励类别：
		<select name="gift_type" >
			<option value="-1" <?php if(intval($_REQUEST['gift_type']) == -1 || intval($_REQUEST['gift_type']) == '' ): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
			<option value="1" <?php if(intval($_REQUEST['gift_type']) == 1): ?>selected="selected"<?php endif; ?>>非提现金</option>
			<option value="2" <?php if(intval($_REQUEST['gift_type']) == 2): ?>selected="selected"<?php endif; ?>>收益率</option>
			<option value="3" <?php if(intval($_REQUEST['gift_type']) == 3): ?>selected="selected"<?php endif; ?>>积分</option>
			<option value="4" <?php if(intval($_REQUEST['gift_type']) == 4): ?>selected="selected"<?php endif; ?>>礼品</option>
		</select>
		发放时间：
		<input type="text" class="textbox" name="begin_time" id="begin_time" value="<?php echo trim($_REQUEST['begin_time']);?>" onfocus="return showCalendar('begin_time', '%Y-%m-%d', false, false, 'begin_time');" style="width:130px" />
		-
		<input type="text" class="textbox" name="end_time" id="end_time" value="<?php echo trim($_REQUEST['end_time']);?>" onfocus="return showCalendar('end_time', '%Y-%m-%d', false, false, 'end_time');" style="width:130px" />
		
		<input type="hidden" value="VipGift" name="m" />
		<input type="hidden" value="vip_gift_record" name="a" />
		<input type="submit" class="button" value="<?php echo L("SEARCH");?>" />
		<input type="button" class="button" value="<?php echo L("EXPORT");?>" onclick="export_csv();" />
	</form>
</div>
<div class="blank5"></div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="8" class="topTd" >&nbsp; </td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="50px  "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VipGift','vip_gift_record')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('deal_id','<?php echo ($sort); ?>','VipGift','vip_gift_record')" title="按照标题  <?php echo ($sortType); ?> ">标题  <?php if(($order)  ==  "deal_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','VipGift','vip_gift_record')" title="按照<?php echo L("USER_NAME");?>  <?php echo ($sortType); ?> "><?php echo L("USER_NAME");?>  <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('gift_type','<?php echo ($sort); ?>','VipGift','vip_gift_record')" title="按照收益类型  <?php echo ($sortType); ?> ">收益类型  <?php if(($order)  ==  "gift_type"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VipGift','vip_gift_record')" title="按照收益值  <?php echo ($sortType); ?> ">收益值  <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('status','<?php echo ($sort); ?>','VipGift','vip_gift_record')" title="按照发放状态  <?php echo ($sortType); ?> ">发放状态  <?php if(($order)  ==  "status"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('release_date','<?php echo ($sort); ?>','VipGift','vip_gift_record')" title="按照发放时间  <?php echo ($sortType); ?> ">发放时间  <?php if(($order)  ==  "release_date"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th></tr><?php if(is_array($list)): $key = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gift): ++$key;$mod = ($key % 2 )?><tr class="row <?php if($key % 2 == 0): ?>row1<?php endif; ?>" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($gift["id"]); ?>"></td><td>&nbsp;<?php echo ($gift["id"]); ?></td><td>&nbsp;<?php echo (get_deal_url($gift["deal_id"],$item['deal_id'])); ?></td><td>&nbsp;<?php echo (get_user_name_real($gift["user_id"])); ?></td><td>&nbsp;<?php echo (get_gift_type($gift["gift_type"])); ?></td><td>&nbsp;<?php echo (get_gift_value($gift["id"],$gift)); ?></td><td>&nbsp;<?php echo (get_gift_status($gift["status"])); ?></td><td>&nbsp;<?php echo ($gift["release_date"]); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="8" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->
 

<div class="blank5"></div>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>