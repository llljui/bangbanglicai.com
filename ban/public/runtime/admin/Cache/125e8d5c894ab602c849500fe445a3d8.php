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
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>

<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>

<script type="text/javascript">
	function show_detail(id)
	{
		window.location.href=ROOT+'?m=Deal&a=show_detail&id='+id;
	}
	function preview(id)
	{
		window.open("__ROOT__/index.php?ctl=deal&id="+id+"&preview=1");
	}
	function repay_plan(id)
	{
		window.location.href=ROOT+'?m=Deal&a=repay_plan&id='+id;
	}
	function do_apart(id){
		$.ajax({
			url:ROOT+'?m=Deal&a=apart&id='+id,
			dataType:"json",
			success:function(result){
				if(result.status==1){
					$.weeboxs.open(result.info, {contentType:'text',showButton:false,title:'拆标',width:600});
				}
				else{
					alert(result.info);
				}
			}
			
		});
		
	}
	
	
	function set_advance(id,domobj)
	{
			$.ajax({ 
					url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=set_advance&id="+id, 
					data: "ajax=1",
					dataType: "json",
					success: function(obj){
	
						if(obj.data=='1')
						{
							$(domobj).html("是");
						}
						else if(obj.data=='0')
						{
							$(domobj).html("否");
						}
						else if(obj.data=='')
						{
							
						}
						$("#info").html(obj.info);
					}
			});
	}

	function set_hidden(id,domobj)
	{
			$.ajax({ 
					url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=set_hidden&id="+id, 
					data: "ajax=1",
					dataType: "json",
					success: function(obj){
	
						if(obj.data=='1')
						{
							$(domobj).html("是");
						}
						else if(obj.data=='0')
						{
							$(domobj).html("否");
						}
						else if(obj.data=='')
						{
							
						}
						$("#info").html(obj.info);
					}
			});
	}
	function open_contract(id){
		$.weeboxs.open(ROOT+'?m=Deal&a=q_contract&id='+id, {contentType:'ajax',showButton:false,title:"合同",width:760,height:500});
	}
	function closeWeeboxs(){
		$.weeboxs.close();
	}
	function interestrate_send(id)
	{
		$.ajax({ 
				url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=interestrate_send&id="+id, 
				data: "ajax=1",
				dataType: "json",
				success: function(obj){

					if(obj.data=='1')
					{
						alert(obj.info);
					}
					else if(obj.data=='0')
					{
						alert(obj.info)
					}
					else if(obj.data=='')
					{
						
					}
					$("#info").html(obj.info);
				}
		});
	}
	function send_interestrate(){
		
		var inputs = $(".search_row").find("input");
		var selects = $(".search_row").find("select");
		var param = '';
		for(i=0;i<inputs.length;i++)
		{
			if(inputs[i].name!='m'&&inputs[i].name!='a')
			param += "&"+inputs[i].name+"="+$(inputs[i]).val();
		}
		for(i=0;i<selects.length;i++)
		{
			param += "&"+selects[i].name+"="+$(selects[i]).val();
		}
		
		$.ajax({
			url:ROOT+'?m=Deal&a=send_interestrate',
			data:param,
			dataType:"json",
			success:function(result){
				if(result && result.status==1){
					$.weeboxs.open(result.info, {contentType:'text',showButton:false,title:'发放结果',width:500});
				}
				else{
					alert(result.info);
				}
			}
			
		});
	}
</script>
<?php function a_get_deal_type($type,$id)
	{
		$deal = M("Deal")->getById($id);
		if($deal['is_coupon'])
		return l("COUNT_TYPE_".$deal['deal_type']);
		else
		return l("NO_DEAL_COUPON_GEN");
		
	}
	
	function a_get_buy_status($buy_status,$deal)
	{
		if($deal['is_effect'] == 0){
			return l("IS_EFFECT_0");
		}
		if($buy_status==2){
			return "<span style='color:red'>".l("DEAL_STATUS_".$buy_status)."</span>";
		}
		else{
			if($deal['deal_status'] == 1 && ($deal['start_time'] + $deal['enddate'] *24*3600 - 1) < TIME_UTC){
				return "已过期";
			}
			elseif($deal['deal_status'] == 1 && $deal['start_time'] > TIME_UTC)
				return "<span style='color:red'>未开始</span>";
			else
				return l("DEAL_STATUS_".$buy_status);
		}
	}
	function get_buy_type_title($buy_type)
	{
		return l("DEAL_BUY_TYPE_".$buy_type);
	}
	
	function get_is_has_loans($is_has_loans,$deal){
		if($deal['deal_status'] >= 4 || $deal['deal_status'] == 2){
			if($is_has_loans==0){
				return '<a href="javascript:show_detail('.$deal['id'].')" style="color:red">否</a>';
			}
			else{
				return "<span style='color:red'>是</span>";
			}
		}
		else
		{
			return "否";
		}
	}
	function get_is_has_received($is_has_received,$deal){
		if($deal['deal_status']==3 || $deal['deal_status'] == 2  || ((($deal['start_time'] + $deal['end_date'] *24*3600 - 1) < TIME_UTC) && $deal['deal_status'] == 1) || $deal['deal_status'] == 1){
			if($is_has_received==0 && ($deal['deal_status'] == 2 || ((($deal['start_time'] + $deal['end_date'] *24*3600 - 1) < TIME_UTC) && $deal['deal_status'] == 1))){
				if($deal['deal_status'] == 1){
					if((($deal['start_time'] + $deal['end_date'] *24*3600 - 1) < TIME_UTC) && $deal['deal_status'] == 1)
						return "未返还";
					else
						return '<a href="javascript:show_detail('.$deal['id'].')" style="color:red">未满标</a>';
				}
				else
					return '<a href="javascript:show_detail('.$deal['id'].')" style="color:red">'.($deal['buy_count'] > 0 ? "未返还" : "未返还").'</a>';
			}
			else{
				if($deal['buy_count'] > 0){
					if($deal['is_has_received']==0){
						return '<a href="javascript:show_detail('.$deal['id'].')" style="color:red">未返还</a>';
					}
					else{
						return "<span style='color:red'>已返还</span>";
					}
				}
				else
					return "未返还";
			}
		}
		else{
			return "未返还";
		}
	}
	
	function get_ips_status($s,$deal){
		if(app_conf("OPEN_IPS") == 0){
			return "未开启功能";
		}
		$msg = "";
		if($deal['mer_bill_no'] == ""){
			if($deal['deal_status']>=3 || $deal['deal_status']==2 || $deal['buy_count'] >0){
				$msg .="无法同步<br>";
			}
			else{
				$msg .='<a href="__ROOT__/index.php?ctl=collocation&act=RegisterSubject&pOperationType=1&deal_id='.$deal['id'].'" target="_blank">同步</a><br>';
			}
		}
		else{
			$msg .="已同步<br>";
		}
		/*if($deal['mer_bill_no'] != "" && $deal['agency_id'] > 0){
			if($deal['ips_guarantor_bill_no'] == ""){
				if($deal['deal_status']>=3 || $deal['deal_status']==2 || $deal['buy_count'] >0){
					$msg .="&nbsp;无法同步[担]";
				}
				else{
					$msg .='&nbsp;<a href="__ROOT__/index.php?ctl=collocation&act=RegisterGuarantor&deal_id='.$deal['id'].'" target="_blank">同步[担]</a>';
				}
			}
			else{
				$msg .="&nbsp;已同步[担]<br>";
			}
		}*/
		
		if($deal['deal_status'] == 5  && $deal['ips_over'] == 0 && $deal['ips_bill_no'] != ""){
			$msg .='&nbsp;<a href="__ROOT__/index.php?ctl=collocation&act=RegisterSubject&pOperationType=2&status=1&deal_id='.$deal['id'].'" target="_blank">还款完成</a>';
		}
		
		return $msg;
	}
	
	function get_repay_plan($id,$deal){
		$str = "";
		if($deal['deal_status']>=4)
			$str .= '<a href="javascript:repay_plan('.$id.');">还款计划</a>&nbsp;';
		
		if(((($deal['start_time'] + $deal['end_date'] *24*3600 - 1) > TIME_UTC) && $deal['deal_status'] == 1) || $deal['deal_status']==2 || ($deal['deal_status']==1 && $deal['buy_count'] > 0) || $deal['deal_status']>=4){
			$str .= '<a href="javascript:show_detail('.$id.');">投标详情和操作</a>&nbsp;';
		}
		
		return $str;
	}
	
	function check_del($id,$deal){
		if($deal['deal_status'] ==0)
			return '<a href="javascript:del('.$id.');">删除</a>&nbsp;';
	}
	
	/*拆标*/
	function do_apart($id,$deal){
		if($deal['deal_status'] == 1 && $deal['load_money'] >0 && $deal['ips_bill_no'] ==""){
			return '<a href="javascript:do_apart('.$id.');">拆标</a>&nbsp;';
		}
	}
	
	function get_is_advance($tag,$id){
		if($tag)
		{
			return "<span class='is_advance' onclick='set_advance(".$id.",this);'>是</span>";
		}
		else
		{
			return "<span class='is_advance' onclick='set_advance(".$id.",this);'>否</span>";
		}
	}
    function get_is_hidden($is_hidden,$id)
    {
    	if($is_hidden)
		{
			return "<span class='is_advance' onclick='set_hidden(".$id.",this);'>是</span>";
		}
		else
		{
			return "<span class='is_advance' onclick='set_hidden(".$id.",this);'>否</span>";
		}
    }
    function get_adm_name($admin_id)
    {
    	$admin_info = M("admin")->getById($admin_id);
        return $admin_info["adm_name"];
    }
	function get_deal_contract($id)
    {
    	return "<a href='javascript:void(0);' onclick='open_contract(".$id.")'>合同</a>";
    	//return "x";
    }
	function interestrate_send($id,$deal)
    {
    	if($deal["deal_status"] >= 4 && $deal["use_interestrate"] == 1)
        {
        	return "<a href='javascript:void(0);' onclick='interestrate_send(".$id.")'>发放加息券红包</a>";
        }
    	
    } ?>
<div class="main">
<div class="main_title"><?php echo L(MODULE_NAME."_".ACTION_NAME);?></div>
<div class="blank5"></div>
<div class="button_row">
	<?php if(ACTION_NAME == 'index' || ACTION_NAME == 'ing'): ?><input type="button" class="button" value="<?php echo L("ADD");?>" onclick="add();" /><?php endif; ?>
	<input type="button" class="button" value="<?php echo L("DEL");?>" onclick="del();" />
</div>
<div class="blank5"></div>
<div class="search_row">
	<form name="search" id="form1" action="__APP__" method="get">	
		<?php echo L("DEAL_NAME");?>：<input type="text" class="textbox" name="name" value="<?php echo trim($_REQUEST['name']);?>" />
		
		贷款人：
		<input type="text" class="textbox" name="user_name" value="<?php echo trim($_REQUEST['user_name']);?>" size="10" />
		贷款金额：
		<input type="text" class="textbox" name="borrow_amount" value="<?php echo trim($_REQUEST['borrow_amount']);?>" size="10" />
		利率：
		<input type="text" class="textbox" name="rate" value="<?php echo trim($_REQUEST['rate']);?>" size="5" />
		
		贷款期数：
		<input type="text" class="textbox" name="repay_time" value="<?php echo trim($_REQUEST['repay_time']);?>" size="3" />
		<select name="repay_time_type">
			<option value="0" <?php if(intval($_REQUEST['repay_time_type']) == 0): ?>selected="selected"<?php endif; ?>>全部</option>
			<option value="1" <?php if(intval($_REQUEST['repay_time_type']) == 1): ?>selected="selected"<?php endif; ?>>天</option>
			<option value="2" <?php if(intval($_REQUEST['repay_time_type']) == 2): ?>selected="selected"<?php endif; ?>>月</option>
		</select>
        推荐码
        <input type="text" class="textbox" name="work_id" value="<?php echo trim($_REQUEST['work_id']);?>" size="10" />
		<div class="blank10"></div>
		还款方式：
		<select name="loantype">
			<option value="-1" <?php if(intval($_REQUEST['loantype']) == -1): ?>selected="selected"<?php endif; ?>>全部</option>
			<?php if(is_array($loantype_list)): foreach($loantype_list as $key=>$loantype): ?><option value="<?php echo ($loantype["key"]); ?>" <?php if(intval($_REQUEST['loantype']) == $loantype['key']): ?>selected="selected"<?php endif; ?>><?php echo ($loantype["sub_name"]); ?></option><?php endforeach; endif; ?>
		</select>
		<?php echo L("CATE_TREE");?>：
		<select name="cate_id">
			<option value="0" <?php if(intval($_REQUEST['cate_id']) == 0): ?>selected="selected"<?php endif; ?>><?php echo L("NO_SELECT_CATE");?></option>
			<?php if(is_array($cate_tree)): foreach($cate_tree as $key=>$cate_item): ?><option value="<?php echo ($cate_item["id"]); ?>" <?php if(intval($_REQUEST['cate_id']) == $cate_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($cate_item["title_show"]); ?></option><?php endforeach; endif; ?>
		</select>
		用途：
		<select name="type_id">
			<option value="0" <?php if(intval($_REQUEST['type_id']) == 0): ?>selected="selected"<?php endif; ?>><?php echo L("NO_SELECT_CATE");?></option>
			<?php if(is_array($type_tree)): foreach($type_tree as $key=>$type_item): ?><option value="<?php echo ($type_item["id"]); ?>" <?php if(intval($_REQUEST['type_id']) == $type_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($type_item["title_show"]); ?></option><?php endforeach; endif; ?>
		</select>
		区域：
		<select name="city_id">
			<option value="0" <?php if(intval($_REQUEST['city_id']) == 0): ?>selected="selected"<?php endif; ?>><?php echo L("NO_SELECT_CATE");?></option>
			<?php if(is_array($citys_tree)): foreach($citys_tree as $key=>$city_item): ?><option value="<?php echo ($city_item["id"]); ?>" <?php if(intval($_REQUEST['city_id']) == $city_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($city_item["title_show"]); ?></option><?php endforeach; endif; ?>
		</select>
		
		<?php if(ACTION_NAME == 'index'): ?>贷款状态
		<select name="deal_status">
			<option value="all" <?php if($_REQUEST['deal_status'] == 'all' || trim($_REQUEST['deal_status']) == ''): ?>selected="selected"<?php endif; ?>>所有状态</option>
			<option value="0" <?php if($_REQUEST['deal_status'] != 'all' && trim($_REQUEST['deal_status']) != '' && intval($_REQUEST['deal_status']) == 0): ?>selected="selected"<?php endif; ?>><?php echo L("DEAL_STATUS_0");?></option>
			<option value="1" <?php if(intval($_REQUEST['deal_status']) == 1): ?>selected="selected"<?php endif; ?>><?php echo L("DEAL_STATUS_1");?></option>
			<option value="2" <?php if(intval($_REQUEST['deal_status']) == 2): ?>selected="selected"<?php endif; ?>><?php echo L("DEAL_STATUS_2");?></option>
			<option value="6" <?php if(intval($_REQUEST['deal_status']) == 6): ?>selected="selected"<?php endif; ?>>已过期</option>
			<option value="3" <?php if(intval($_REQUEST['deal_status']) == 3): ?>selected="selected"<?php endif; ?>><?php echo L("DEAL_STATUS_3");?></option>
			<option value="4" <?php if(intval($_REQUEST['deal_status']) == 4): ?>selected="selected"<?php endif; ?>><?php echo L("DEAL_STATUS_4");?></option>
			<option value="5" <?php if(intval($_REQUEST['deal_status']) == 5): ?>selected="selected"<?php endif; ?>><?php echo L("DEAL_STATUS_5");?></option>
		</select>
	
		
		流标返还
		<select name="is_has_received">
			<option value="all" <?php if($_REQUEST['is_has_received'] == 'all' || trim($_REQUEST['is_has_received']) == ''): ?>selected="selected"<?php endif; ?>>全部</option>
			<option value="0" <?php if($_REQUEST['is_has_received'] != 'all' && trim($_REQUEST['is_has_received']) != '' && intval($_REQUEST['is_has_received']) == 0): ?>selected="selected"<?php endif; ?>>未返还</option>
			<option value="1" <?php if(intval($_REQUEST['is_has_received']) == 1): ?>selected="selected"<?php endif; ?>>已返还</option>
		</select><?php endif; ?>
        
		<input type="hidden" value="Deal" name="m" />
		<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a" />
		<input type="submit" class="button" value="<?php echo L("SEARCH");?>" />
        <?php if($show_rate == 1): ?><input type="button" class="button" onclick="send_interestrate();" value="批量发放加息券红包" /><?php endif; ?>
	</form>
</div>
<div class="blank5"></div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="21" class="topTd" >&nbsp; </td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="50px   "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','Deal','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('name','<?php echo ($sort); ?>','Deal','index')" title="按照<?php echo L("DEAL_NAME");?><?php echo ($sortType); ?> "><?php echo L("DEAL_NAME");?><?php if(($order)  ==  "name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','Deal','index')" title="按照借款人   <?php echo ($sortType); ?> ">借款人   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('borrow_amount','<?php echo ($sort); ?>','Deal','index')" title="按照贷款金额   <?php echo ($sortType); ?> ">贷款金额   <?php if(($order)  ==  "borrow_amount"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('rate','<?php echo ($sort); ?>','Deal','index')" title="按照利率(%)   <?php echo ($sortType); ?> ">利率(%)   <?php if(($order)  ==  "rate"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('repay_time','<?php echo ($sort); ?>','Deal','index')" title="按照期数   <?php echo ($sortType); ?> ">期数   <?php if(($order)  ==  "repay_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('loantype','<?php echo ($sort); ?>','Deal','index')" title="按照还款方式   <?php echo ($sortType); ?> ">还款方式   <?php if(($order)  ==  "loantype"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('deal_status','<?php echo ($sort); ?>','Deal','index')" title="按照<?php echo L("DEAL_STATUS");?>   <?php echo ($sortType); ?> "><?php echo L("DEAL_STATUS");?>   <?php if(($order)  ==  "deal_status"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_has_loans','<?php echo ($sort); ?>','Deal','index')" title="按照放款   <?php echo ($sortType); ?> ">放款   <?php if(($order)  ==  "is_has_loans"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_has_received','<?php echo ($sort); ?>','Deal','index')" title="按照流标返回    <?php echo ($sortType); ?> ">流标返回    <?php if(($order)  ==  "is_has_received"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px   "><a href="javascript:sortBy('buy_count','<?php echo ($sort); ?>','Deal','index')" title="按照投标数<?php echo ($sortType); ?> ">投标数<?php if(($order)  ==  "buy_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_recommend','<?php echo ($sort); ?>','Deal','index')" title="按照<?php echo L("IS_RECOMMEND");?>   <?php echo ($sortType); ?> "><?php echo L("IS_RECOMMEND");?>   <?php if(($order)  ==  "is_recommend"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="100px   "><a href="javascript:sortBy('mer_bill_no','<?php echo ($sort); ?>','Deal','index')" title="按照同步到第三方<?php echo ($sortType); ?> ">同步到第三方<?php if(($order)  ==  "mer_bill_no"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_advance','<?php echo ($sort); ?>','Deal','index')" title="按照预告   <?php echo ($sortType); ?> ">预告   <?php if(($order)  ==  "is_advance"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_new','<?php echo ($sort); ?>','Deal','index')" title="按照<?php echo L("IS_NEW");?>   <?php echo ($sortType); ?> "><?php echo L("IS_NEW");?>   <?php if(($order)  ==  "is_new"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_effect','<?php echo ($sort); ?>','Deal','index')" title="按照<?php echo L("IS_EFFECT");?>         <?php echo ($sortType); ?> "><?php echo L("IS_EFFECT");?>         <?php if(($order)  ==  "is_effect"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_hidden','<?php echo ($sort); ?>','Deal','index')" title="按照隐藏         <?php echo ($sortType); ?> ">隐藏         <?php if(($order)  ==  "is_hidden"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('admin_id','<?php echo ($sort); ?>','Deal','index')" title="按照推荐管理员   <?php echo ($sortType); ?> ">推荐管理员   <?php if(($order)  ==  "admin_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('sort','<?php echo ($sort); ?>','Deal','index')" title="按照<?php echo L("SORT");?><?php echo ($sortType); ?> "><?php echo L("SORT");?><?php if(($order)  ==  "sort"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th class="op_action"><a href="javascript:void(0)" class="A_opration">操作</a></th></tr><?php if(is_array($list)): $key = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$deal): ++$key;$mod = ($key % 2 )?><tr class="row <?php if($key % 2 == 0): ?>row1<?php endif; ?>" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($deal["id"]); ?>"></td><td>&nbsp;<?php echo ($deal["id"]); ?></td><td>&nbsp;<a href="javascript:edit   ('<?php echo (addslashes($deal["id"])); ?>')"><?php echo (msubstr($deal["name"])); ?></a></td><td>&nbsp;<?php echo (get_user_name_real($deal["user_id"])); ?></td><td>&nbsp;<?php echo (format_price($deal["borrow_amount"])); ?></td><td>&nbsp;<?php echo ($deal["rate"]); ?></td><td>&nbsp;<?php echo (get_time_type($deal["repay_time"],$deal)); ?></td><td>&nbsp;<?php echo (loantypename($deal["loantype"],1)); ?></td><td>&nbsp;<?php echo (a_get_buy_status($deal["deal_status"],$deal)); ?></td><td>&nbsp;<?php echo (get_is_has_loans($deal["is_has_loans"],$deal)); ?></td><td>&nbsp;<?php echo (get_is_has_received($deal["is_has_received"],$deal)); ?></td><td>&nbsp;<?php echo ($deal["buy_count"]); ?></td><td>&nbsp;<?php echo (get_toogle_status($deal["is_recommend"],$deal['id'],is_recommend)); ?></td><td>&nbsp;<?php echo (get_ips_status($deal["mer_bill_no"],$deal)); ?></td><td>&nbsp;<?php echo (get_is_advance($deal["is_advance"],$deal['id'])); ?></td><td>&nbsp;<?php echo (get_is_new($deal["is_new"],$deal['id'])); ?></td><td>&nbsp;<?php echo (get_is_effect($deal["is_effect"],$deal['id'])); ?></td><td>&nbsp;<?php echo (get_is_hidden($deal["is_hidden"],$deal['id'])); ?></td><td>&nbsp;<?php echo (get_adm_name($deal["admin_id"])); ?></td><td>&nbsp;<?php echo (get_sort($deal["sort"],$deal['id'])); ?></td><td class="op_action"><div class="viewOpBox"><a href="javascript:edit('<?php echo ($deal["id"]); ?>')"><?php echo L("EDIT");?></a>&nbsp;<?php echo (check_del($deal["id"],$deal)); ?><?php echo (do_apart($deal["id"],$deal)); ?><?php echo (get_repay_plan($deal["id"],$deal)); ?><?php echo (get_deal_contract($deal["id"])); ?><a href="javascript:preview('<?php echo ($deal["id"]); ?>')"><?php echo L("PREVIEW");?></a>&nbsp;<?php echo (interestrate_send($deal["id"],$deal)); ?></div><a href="javascript:void(0);" class="opration">操作+</a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="21" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->
 

<div class="blank5"></div>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>