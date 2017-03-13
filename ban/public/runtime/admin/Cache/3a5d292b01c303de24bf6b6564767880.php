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

<script type="text/javascript" src="__TMPL__Common/js/conf.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/deal.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/colorpicker.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/colorpicker.css" />
<script type="text/javascript">
	window.onload = function()
	{
		init_dealform();
	}
	
	
	
</script>


<div class="main">
<div class="main_title"><?php echo ($vo["name"]); ?><?php echo L("EDIT");?> <a href="<?php if($vo['publish_wait'] == 2): ?><?php echo u("Deal/true_publish");?><?php else: ?><?php echo u("Deal/publish");?><?php endif; ?>" class="back_list"><?php echo L("BACK_LIST");?></a></div>
<div class="blank5"></div>
<form name="edit" action="__APP__" method="post" enctype="multipart/form-data">
<div class="button_row">
	<input type="button" class="button conf_btn" rel="1" value="<?php echo L("DEAL_BASE_INFO");?>" />&nbsp;
	<!--input type="button" class="button conf_btn" rel="2" value="相关参数" />&nbsp;
	<input type="button" class="button conf_btn" rel="5" value="抵押物" />&nbsp;		
    -->
	<input type="button" class="button conf_btn" rel="6" value="相关资料" />&nbsp;		
	<input type="button" class="button conf_btn" rel="3" value="<?php echo L("SEO_CONFIG");?>" />&nbsp;	
</div>
<div class="blank5"></div>
<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="1">
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title">颜色:</td>
		<td class="item_input">
			<input type="text" <?php if($vo['titlecolor'] != ''): ?>style="background:#<?php echo ($vo["titlecolor"]); ?>"<?php endif; ?> name="titlecolor" class="textbox" maxlength="6" size="6" id="colorpickerField" value="<?php echo ($vo["titlecolor"]); ?>" />
			<span class="tip_span">不填即为默认颜色</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">借款编号:</td>
		<td class="item_input">
			<input type="text" name="deal_sn" class="textbox <?php if($deal_sn == ''): ?><?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?><?php endif; ?>" value="<?php if($deal_sn != ''): ?><?php echo ($deal_sn); ?><?php else: ?><?php echo ($vo["deal_sn"]); ?><?php endif; ?>" <?php if($deal_sn != ''): ?>style="color:red"<?php else: ?><?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?><?php endif; ?> />
			<span class="tip_span">此处编号用于合同处，不得重复，<?php if($deal_sn != ''): ?>红色表示未失效编号<?php endif; ?></span>
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_NAME");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox require" name="name" style="width:500px;" value="<?php echo ($vo["name"]); ?>" />
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_SUB_NAME");?>:</td>
		<td class="item_input"><input type="text" class="textbox require" name="sub_name" value="<?php echo ($vo["sub_name"]); ?>" /> <span class='tip_span'>[<?php echo L("DEAL_SUB_NAME_TIP");?>]</span></td>
	</tr>
	<tr>
		<td class="item_title">会员:</td>
		<td class="item_input">
			<?php echo get_user_name_real($vo['user_id']);?> <a href="__APP__?m=User&a=passed&id=<?php echo ($vo["user_id"]); ?>&loantype=<?php echo ($vo["loantype"]); ?>" target="_blank">资料认证</a>
			<input type="hidden" name="user_id" value="<?php echo ($vo["user_id"]); ?>"/>            
		</td>
		
	</tr>


	<tr>
		<td class="item_title">所在城市:</td>
		<td class="item_input" id="citys_box">
			<?php if(is_array($citys)): foreach($citys as $key=>$city): ?><?php if($city['pid'] == 0): ?><div class="item">
						<div class="bcity f_l">
							<input name="city_id[]"  type="checkbox" value="<?php echo ($city["id"]); ?>" <?php if($city['is_selected'] == 1): ?>checked="checked"<?php endif; ?>>
							<?php echo ($city["name"]); ?>
						 	：
					 	</div>
					 	<div class="scity f_l">
					 	<?php if(is_array($citys)): foreach($citys as $key=>$citya): ?><?php if($city['id'] == $citya['pid']): ?><input  type="checkbox" name="city_id[]" value="<?php echo ($citya["id"]); ?>" <?php if($citya['is_selected'] == 1): ?>checked="checked"<?php endif; ?> >
								 <?php echo ($citya["name"]); ?><?php endif; ?><?php endforeach; endif; ?>
						</div>
					</div>
					<div class="blank5"></div><?php endif; ?><?php endforeach; endif; ?>
		</td>
	</tr>
	
	<tr>
		<td class="item_title"><?php echo L("CATE_TREE");?>:</td>
		<td class="item_input">
		<select name="cate_id" class="require">
			<option value="0">==<?php echo L("NO_SELECT_CATE");?>==</option>
			<?php if(is_array($deal_cate_tree)): foreach($deal_cate_tree as $key=>$cate_item): ?><option value="<?php echo ($cate_item["id"]); ?>" <?php if($vo['cate_id'] == $cate_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($cate_item["title_show"]); ?></option><?php endforeach; endif; ?>
		</select>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">担保机构:</td>
		<td class="item_input">
			<?php if($vo['ips_guarantor_bill_no'] != ''): ?><select name="agency_id">
					<?php if($vo['agency_id'] >0){ ?>
					<?php if(is_array($deal_agency)): foreach($deal_agency as $key=>$agency_item): ?><?php if($vo['agency_id'] == $agency_item['id']): ?><option value="<?php echo ($agency_item["id"]); ?>"><?php if($agency_item['short_name'] != ''): ?><?php echo ($agency_item["short_name"]); ?><?php else: ?><?php echo ($agency_item["user_name"]); ?><?php endif; ?></option><?php endif; ?><?php endforeach; endif; ?>
					<?php }
						else
						{ ?>
						<option value="0">==<?php echo L("NO_SELECT_AGENCY");?>==</option>
					<?php } ?>
				</select>
			<?php else: ?>
				<select name="agency_id">
					<option value="0">==<?php echo L("NO_SELECT_AGENCY");?>==</option>
					<?php if(is_array($deal_agency)): foreach($deal_agency as $key=>$agency_item): ?><option value="<?php echo ($agency_item["id"]); ?>" <?php if($vo['agency_id'] == $agency_item['id']): ?>selected="selected"<?php endif; ?>><?php if($agency_item['short_name'] != ''): ?><?php echo ($agency_item["short_name"]); ?><?php else: ?><?php echo ($agency_item["user_name"]); ?><?php endif; ?></option><?php endforeach; endif; ?>
				</select><?php endif; ?>
			<?php if($vo['agency_id'] > 0): ?><?php if($vo['agency_status'] == 0): ?><span class="tip_span">已应邀</span>
				<?php elseif($vo['agency_status'] == 1): ?>
					<span class="tip_span">邀约中</span>
				<?php elseif($vo['agency_status'] == 2): ?>
					<span class="tip_span">拒绝应邀</span><?php endif; ?><?php endif; ?>
		</td>
	</tr>
	<tr>
		<td class="item_title">担保范围:</td>
		<td class="item_input">
			<?php if($vo['ips_guarantor_bill_no'] != ''): ?><select name="warrant">
					<?php if($vo['warrant'] == 0): ?><option value="0" >无</option><?php endif; ?>
					<?php if($vo['warrant'] == 1): ?><option value="1">本金</option><?php endif; ?>
					<?php if($vo['warrant'] == 2): ?><option value="2">本金及利息</option><?php endif; ?>
				</select>
			<?php else: ?>
				<select name="warrant">
					<option value="0" <?php if($vo['warrant'] == 0): ?>selected="selected"<?php endif; ?>>无</option>
					<option value="1" <?php if($vo['warrant'] == 1): ?>selected="selected"<?php endif; ?>>本金</option>
					<option value="2" <?php if($vo['warrant'] == 2): ?>selected="selected"<?php endif; ?>>本金及利息</option>
				</select><?php endif; ?>
		</td>
	</tr>
	<tr id="guarantor_margin_amt_box" <?php if($vo['warrant'] == 0): ?>style="display:none"<?php endif; ?>>
		<td class="item_title">担保保证金[第三方托管]:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['ips_guarantor_bill_no'] != ''): ?>readonly<?php endif; ?>"  <?php if($vo['ips_guarantor_bill_no'] != ''): ?>readonly="readonly"<?php endif; ?> name="guarantor_margin_amt" value="<?php echo ($vo["guarantor_margin_amt"]); ?>" />
			<?php if($vo['mer_guarantor_bill_no'] != ''): ?>已解冻：<?php echo (format_price($vo["un_guarantor_real_freezen_amt"])); ?>&nbsp;<?php endif; ?><span class="tip_span">冻结担保人的金额，需要提前存钱</span>
		</td>
	</tr>
	<tr id="guarantor_amt_box" <?php if($vo['warrant'] == 0): ?>style="display:none"<?php endif; ?>>
		<td class="item_title">担保金额[第三方托管]:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['ips_guarantor_bill_no'] != ''): ?>readonly<?php endif; ?>"  <?php if($vo['ips_guarantor_bill_no'] != ''): ?>readonly="readonly"<?php endif; ?> name="guarantor_amt" value="<?php echo ($vo["guarantor_amt"]); ?>" />
			<a href="__ROOT__/index.php?ctl=tool" target="_blank">理财计算器</a>
		</td>
	</tr>
	<tr id="guarantor_pro_fit_amt_box" <?php if($vo['warrant'] == 0): ?>style="display:none"<?php endif; ?>>
		<td class="item_title">担保收益[第三方托管]:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['ips_guarantor_bill_no'] != ''): ?>readonly<?php endif; ?>"  <?php if($vo['ips_guarantor_bill_no'] != ''): ?>readonly="readonly"<?php endif; ?> name="guarantor_pro_fit_amt" value="<?php echo ($vo["guarantor_pro_fit_amt"]); ?>" />
			<?php if($vo['mer_guarantor_bill_no'] != ''): ?>实际担保收益：<?php echo (format_price($vo["guarantor_real_fit_amt"])); ?><?php endif; ?>
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_ICON");?>:</td>
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
		<td class="item_title"><?php echo L("TYPE_TREE");?>:</td>
		<td class="item_input">
		<select name="type_id" class="require">
			<?php if(is_array($deal_type_tree)): foreach($deal_type_tree as $key=>$type_item): ?><option value="<?php echo ($type_item["id"]); ?>" <?php if($type_item['id'] == $vo['type_id']): ?>selected="selected"<?php endif; ?>><?php echo ($type_item["name"]); ?></option><?php endforeach; endif; ?>
		</select>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">还款方式:</td>
		<td class="item_input">
		 	<?php if($vo['deal_status'] >= 1): ?><select name="loantype" >
				<?php if(is_array($loantype_list)): foreach($loantype_list as $key=>$loantype): ?><?php if($vo['loantype'] == $loantype['key']): ?><option value="<?php echo ($loantype["key"]); ?>"  rel="<?php echo ($loantype["repay_time_type_str"]); ?>"><?php echo ($loantype["sub_name"]); ?></option><?php endif; ?><?php endforeach; endif; ?>
				</select>
			<?php else: ?>
			<select name="loantype" >
				<?php if(is_array($loantype_list)): foreach($loantype_list as $key=>$loantype): ?><option value="<?php echo ($loantype["key"]); ?>"  rel="<?php echo ($loantype["repay_time_type_str"]); ?>" <?php if($vo['loantype'] == $loantype['key']): ?>selected="selected"<?php endif; ?>><?php echo ($loantype["sub_name"]); ?></option><?php endforeach; endif; ?>
			</select><?php endif; ?>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">借款合同范本:</td>
		<td class="item_input">
			<select name="contract_id" class="require">
				<option value="0">==选择合同范本==</option>
				<?php if(is_array($contract_list)): foreach($contract_list as $key=>$contract): ?><option value="<?php echo ($contract["id"]); ?>" <?php if($vo['contract_id'] == $contract['id']): ?>selected="selected"<?php endif; ?>><?php echo ($contract["title"]); ?></option><?php endforeach; endif; ?>
			</select>
		</td>
	</tr>
    <!--
	<tr>
		<td class="item_title">转让合同范本:</td>
		<td class="item_input">
			<select name="tcontract_id" class="require">
				<option value="0">==选择合同范本==</option>
				<?php if(is_array($contract_list)): foreach($contract_list as $key=>$contract): ?><option value="<?php echo ($contract["id"]); ?>" <?php if($vo['tcontract_id'] == $contract['id']): ?>selected="selected"<?php endif; ?>><?php echo ($contract["title"]); ?></option><?php endforeach; endif; ?>
			</select>
		</td>
	</tr>
    -->
	
	<tr>
		<td class="item_title"><?php echo L("BORROW_AMOUNT");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox require <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>"  <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> name="borrow_amount"  value="<?php echo ($vo["borrow_amount"]); ?>"  />
		</td>
	</tr>
	
	<tr>
		<td class="item_title">借款保证金[第三方托管]:</td>
		<td class="item_input">
			<input type="text" class="textbox require <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>"  <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> name="guarantees_amt"  value="<?php echo ($vo["guarantees_amt"]); ?>"  />
			<?php if($vo['mer_bill_no'] != ''): ?>已解冻：<?php echo (format_price($vo["un_real_freezen_amt"])); ?>&nbsp;<?php endif; ?><span class="tip_span">冻结借款人的金额，需要提前存钱</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">投标类型:</td>
		<td class="item_input">
			<select name="uloadtype">
				<option value=0 <?php if($vo['uloadtype'] == 0): ?>selected="selected"<?php endif; ?>>按金额</option>
				<option value=1 <?php if($vo['uloadtype'] == 1): ?>selected="selected"<?php endif; ?>>按份额</option>
			</select>
		</td>
	</tr>
	
	<tr class="uloadtype_0" <?php if($vo['uloadtype'] == 1): ?>style="display:none"<?php endif; ?>>
		<td class="item_title"><?php echo L("MIN_LOAN_MONEY");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox require" name="min_loan_money"  value="<?php echo ($vo["min_loan_money"]); ?>" />
		</td>
	</tr>
	
	<tr class="uloadtype_0" <?php if($vo['uloadtype'] == 1): ?>style="display:none"<?php endif; ?>>
		<td class="item_title"><?php echo L("MAX_LOAN_MONEY");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="max_loan_money"  value="<?php echo ($vo["max_loan_money"]); ?>" />
			<span class="tip_span">0为不限制</span>
		</td>
	</tr>
	
	<tr class="uloadtype_1" <?php if($vo['uloadtype'] == 0): ?>style="display:none"<?php endif; ?>>
		<td class="item_title">分成多少份:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="portion" value="<?php echo ($vo["portion"]); ?>"/>
		</td>
	</tr>
	
	<tr class="uloadtype_1" <?php if($vo['uloadtype'] == 0): ?>style="display:none"<?php endif; ?>>
		<td class="item_title">最高买多少份:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="max_portion"  value="<?php echo ($vo["max_portion"]); ?>" />
			<span class="tip_span">0为不限制</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title"><?php echo L("REPAY_TIME");?>:</td>
		<td class="item_input">
			<input type="text" id="repay_time" class="textbox require <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>"  <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5"  name="repay_time" value="<?php echo ($vo["repay_time"]); ?>" />
			<?php if($vo['deal_status'] >= 1): ?><select id="repay_time_type" name="repay_time_type">
					<?php if($vo['repay_time_type'] == 0): ?><option value="0" >天</option><?php endif; ?>
					<?php if($vo['repay_time_type'] == 1): ?><option value="1">月</option><?php endif; ?>
				</select>
			<?php else: ?>
				<select id="repay_time_type" name="repay_time_type">
					<option value="0" <?php if($vo['repay_time_type'] == 0): ?>selected="selected"<?php endif; ?>>天</option>
					<option value="1" <?php if($vo['repay_time_type'] == 1): ?>selected="selected"<?php endif; ?>>月</option>
				</select><?php endif; ?>
		</td>
	</tr>
	
	<tr>
		<td class="item_title"><?php echo L("RATE");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox require <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?>  SIZE="5" name="rate" value="<?php echo ($vo["rate"]); ?>"  />%
		</td>
	</tr>
	<tr>
		<td class="item_title">筹标期限:</td>
		<td class="item_input">
			<input type="text" class="textbox require" SIZE="5" name="enddate" value="<?php echo ($vo["enddate"]); ?>"  /> 天
		</td>
	</tr>
	<tr>
		<td class="item_title">可否使用红包:</td>
		<td class="item_input">
			<select name="use_ecv">
				<option value="0" <?php if($vo['use_ecv'] == 0): ?>selected="selected"<?php endif; ?>>否</option>
				<option value="1" <?php if($vo['use_ecv'] == 1): ?>selected="selected"<?php endif; ?>>是</option>
			</select>
			<span class="tip_span">选“是”请将“最低投标金额”设置大于最大红包额度</span>
		</td>
	</tr>
	
    <tr>
		<td class="item_title">可否使用加息券:</td>
		<td class="item_input">
			<select name="use_interestrate">
				<option value="0" <?php if($vo['use_interestrate'] == 0): ?>selected="selected"<?php endif; ?>>否</option>
				<option value="1" <?php if($vo['use_interestrate'] == 1): ?>selected="selected"<?php endif; ?>>是</option>
			</select>
		</td>
	</tr>
    
	<tr class="uloadtype_0">
        <td class="item_title">可否使用体验金:</td>
        <td class="item_input">
            <select name="use_learn">
                <option value="0" <?php if($vo['use_learn'] == 0): ?>selected="selected"<?php endif; ?>>否</option>
                <option value="1" <?php if($vo['use_learn'] == 1): ?>selected="selected"<?php endif; ?>>是</option>
            </select>
        </td>
    </tr>
	
	<tr>
		<td class="item_title"><?php echo L("DEAL_DESCRIPTION");?>:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='description' name='description' class='ketext' style='width:500px;height:200px' rel="true"><?php echo ($vo["description"]); ?></textarea> </div>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">风险等级:</td>
		<td class="item_input">
			<select name="risk_rank">
				<option value="0" <?php if($vo['risk_rank'] == 0): ?>selected="selected"<?php endif; ?>>低</option>
				<option value="1" <?php if($vo['risk_rank'] == 1): ?>selected="selected"<?php endif; ?>>中</option>
				<option value="2" <?php if($vo['risk_rank'] == 2): ?>selected="selected"<?php endif; ?>>高</option>
			</select>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">风险控制:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='risk_security' name='risk_security' class='ketext' style='width:500px;height:200px' rel="true"><?php echo ($vo["risk_security"]); ?></textarea> </div>
		</td>
	</tr>
		
	<tr>
		<td class="item_title"><?php echo L("SORT");?>:</td>
		<td class="item_input"><input type="text" class="textbox" name="sort" value="<?php echo ($vo["sort"]); ?>" /></td>
	</tr>
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>
<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="2">
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title">成交服务费:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?>  SIZE="5" name="services_fee" value="<?php echo ($vo["services_fee"]); ?>"  />%
			<span class="tip_span">按发布时的会员等级</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">借款者获得积分:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="score" value="<?php echo ($vo["score"]); ?>"  />
			【非信用积分】
		</td>
	</tr>
	
	<tr>
		<td class="item_title">借款者管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="manage_fee" value="<?php echo ($vo["manage_fee"]); ?>"  />%
			<span class="tip_span">管理费 = 本金总额×管理费率 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">投资者管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="user_loan_manage_fee" value="<?php echo ($vo["user_loan_manage_fee"]); ?>"  />%
			<span class="tip_span">管理费 = 投资总额×管理费率 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">投资者利息管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="user_loan_interest_manage_fee" value="<?php echo ($vo["user_loan_interest_manage_fee"]); ?>"  />%
			<span class="tip_span">管理费 = 实际得到的利息×管理费率 0即不收取</span>(如果是VIP会员将从VIP会员配置里读取)
		</td>
	</tr>
	<tr>
		<td class="item_title">普通逾期管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="manage_impose_fee_day1" value="<?php echo ($vo["manage_impose_fee_day1"]); ?>"  />%
			<span class="tip_span">逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">严重逾期管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="manage_impose_fee_day2" value="<?php echo ($vo["manage_impose_fee_day2"]); ?>"  />%
			<span class="tip_span">逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">普通逾期罚息:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="impose_fee_day1" value="<?php echo ($vo["impose_fee_day1"]); ?>"  />%
			<span class="tip_span">罚息总额 = 逾期本息总额×对应逾期管理费率×逾期天数 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">严重逾期罚息:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="impose_fee_day2" value="<?php echo ($vo["impose_fee_day2"]); ?>"  />%
			<span class="tip_span">逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">债权转让管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="user_load_transfer_fee" value="<?php echo ($vo["user_load_transfer_fee"]); ?>"  />%
			<span class="tip_span">管理费 = 转让金额×管理费率 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">债权转让期限:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="transfer_day" value="<?php echo ($vo["transfer_day"]); ?>"  />
			<span class="tip_span">满标放款多少天后才可以进行转让 0代表不限制</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">提前还款补偿:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="compensate_fee" value="<?php echo ($vo["compensate_fee"]); ?>"  />%
			<span class="tip_span">补偿金额 = 剩余本金×补偿利率 0即不收取</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">投资人返利:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="user_bid_rebate" value="<?php echo ($vo["user_bid_rebate"]); ?>"  />%
			<span class="tip_span">返利金额=投标金额×返利百分比【需满标】</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">投资返还积分比率:</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> SIZE="5" name="user_bid_score_fee" value="<?php echo ($vo["user_bid_score_fee"]); ?>"  />%
			<span class="tip_span">投标返还积分 = 投标金额 ×返还比率【需满标】</span>(如果是VIP会员将从VIP会员配置里读取)【非信用积分】
		</td>
	</tr>
	
	<tr>
		<td class="item_title">申请延期:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="generation_position" value="<?php echo ($vo["generation_position"]); ?>"  />%
			<span class="tip_span">当还款金额大于或等于设置的额度，借款人如果资金不够，可申请延期还款，延期还款就是平台代其还此借款。借款人未还部分由平台跟借款人协商。</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">合同附件:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='attachment' name='attachment' class='ketext' style='width:500px;height:200px' rel="true"><?php echo ($vo["attachment"]); ?></textarea> </div>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">转让合同附件:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='tattachment' name='tattachment' class='ketext' style='width:500px;height:200px' rel="true"><?php echo ($vo["tattachment"]); ?></textarea> </div>
		</td>
	</tr>
	
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>
<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="3">
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_SEO_TITLE");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_title" ><?php echo ($vo["seo_title"]); ?></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_SEO_KEYWORD");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_keyword" ><?php echo ($vo["seo_keyword"]); ?></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_SEO_DESCRIPTION");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_description" ><?php echo ($vo["seo_description"]); ?></textarea></td>
	</tr>
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>
<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="5">
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title">是否有抵押物</td>
		<td class="item_input">
			<select name="is_mortgage">
				<option value="0" <?php if($vo['is_mortgage'] == 0): ?>selected="selected"<?php endif; ?>>无</option>
				<option value="1" <?php if($vo['is_mortgage'] == 1): ?>selected="selected"<?php endif; ?>>有</option>
			</select>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">抵押物管理费</td>
		<td class="item_input">
			<input type="text" class="textbox <?php if($vo['deal_status'] >= 1): ?>readonly<?php endif; ?>" <?php if($vo['deal_status'] >= 1): ?>readonly="readonly"<?php endif; ?> size="5" name="mortgage_fee" value="<?php echo ($vo["mortgage_fee"]); ?>"> 元/月
		</td>
	</tr>
	
	
	<tr>
		<td class="item_title">抵押物说明</td>
		<td class="item_input">
			<textarea name="mortgage_desc" class="textarea" ><?php echo ($vo["mortgage_desc"]); ?></textarea>
		</td>
	</tr>
	
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>
<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="6">
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	
	<tr>
		<td class="item_title">认证资料显示:</td>
		<td>
			<?php if($old_imgdata_str): ?><?php if(is_array($old_imgdata_str)): foreach($old_imgdata_str as $key=>$img_item): ?><p style="float:left">
					<input style=" margin-top: 12px;"  type="checkbox" name="key[]" <?php if(isset($vo['view_info'][$img_item['key']])): ?>checked="checked"<?php endif; ?> value="<?php echo ($img_item["key"]); ?>">
					<a href='<?php echo ($img_item["img"]); ?>' target="_blank" title="<?php echo ($img_item["name"]); ?>"><img width="35" height="35" style="float:left; border:#ccc solid 1px; margin-left:5px;" id="<?php echo ($img_item["name"]); ?>" src="<?php echo ($img_item["img"]); ?>"></a>
					</p><?php endforeach; endif; ?><?php endif; ?>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">借款签约合同[ <a href="javascript:void(0);" onclick="add_mortgage_img('contract');">+</a> ]</td>
		<td class="item_input" id="mortgage_contract_box">
			
		</td>
	</tr>

	
	<tr>
		<td class="item_title">抵押物图片[ <a href="javascript:void(0);" onclick="add_mortgage_img('infos');">+</a> ]</td>
		<td class="item_input" id="mortgage_infos_box">
			
		</td>
	</tr>
	
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>
<div class="blank5"></div>
	<table class="form" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<?php if($vo['publish_wait'] == 2): ?><tr>
			<td class="item_title">借款状态:</td>
			<td class="item_input">
				<?php if($vo['deal_status'] == 1): ?>进行中	
					<div class="blank10"></div>
					开始日期 :<?php echo ($vo["start_time"]); ?><?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="item_title">复审状态:</td>
			<td class="item_input require_radio">
				<label><input type="radio" name="publish" value="0"> 审核通过</label>
				<label><input type="radio" name="publish" value="3"> 回退初审</label>
			</td>
		</tr>
		<tr id="publish_msg_box" style="display:none">
			<td class="item_title">失败原因:</td>
			<td class="item_input">
				<textarea name="publish_msg" rows="5" cols="80"><?php echo ($vo["publish_memo"]); ?></textarea>
			</td>
		</tr>
	<?php else: ?>
		<tr>
			<td class="item_title">审核状态:</td>
			<td class="item_input require_radio">
				<?php if($vo['publish_wait'] == 1 || $vo['publish_wait'] == 3): ?><label>审核失败<input type="radio" name="is_delete" value="3" /></label><?php endif; ?>
				<?php if($vo['deal_status'] == 1): ?><label>审核成功<input type="radio" name="deal_status" value="1" <?php if($vo['deal_status'] == 1): ?>checked="checked"<?php endif; ?> /></label>
				<?php elseif($vo['deal_status'] == 2): ?>
					已满标	<a href="<?php echo u("Deal/show_detail",array("id"=>$vo['id']));?>">投标详情和操作</a>
				<?php elseif($vo['deal_status'] == 3): ?>
					已流标	<a href="<?php echo u("Deal/show_detail",array("id"=>$vo['id']));?>">投标详情和操作</a>
					<div class="blank10"></div>
					流标原因 :
					<div class="blank10"></div>
					<?php echo ($vo["bad_msg"]); ?>
				<?php elseif($vo['deal_status'] == 4): ?>
					还款中	<a href="<?php echo u("Deal/show_detail",array("id"=>$vo['id']));?>">投标详情和操作</a>
					<div class="blank10"></div>
					确定日期 :<?php echo (to_date($vo["repay_start_time"],"Y-m-d")); ?>
				<?php elseif($vo['deal_status'] == 5): ?>
					已还清	<a href="<?php echo u("Deal/show_detail",array("id"=>$vo['id']));?>">投标详情和操作</a>
				<?php else: ?>
					<label>审核成功<input type="radio" name="deal_status" value="1" <?php if($vo['deal_status'] == 1): ?>checked="checked"<?php endif; ?> /></label><?php endif; ?>
			</td>
		</tr>
		<?php if($vo['publish_wait'] == 1): ?><tr id="delele_msg_box" style="display:none">
				<td class="item_title">失败原因:</td>
				<td class="item_input">
					<textarea name="delete_msg" rows="5" cols="80"><?php echo ($vo["delete_msg"]); ?></textarea>
				</td>
			</tr><?php endif; ?>
		<tr id="start_time_box" <?php if($vo['deal_status'] != 1): ?>style="display:none"<?php endif; ?>>
			<td class="item_title">开始时间:</td>
			<td class="item_input">
				<input type="text" class="textbox <?php if($vo['deal_status'] == 1): ?>require<?php endif; ?>" name="start_time" value="<?php echo ($vo["start_time"]); ?>" id="start_time"  onfocus="this.blur(); return showCalendar('start_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_start_time');" />
				<input type="button" class="button" id="btn_start_time" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('start_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_start_time');" />
				<input type="button" class="button" value="<?php echo L("CLEAR_TIME");?>" onclick="$('#start_time').val('');" />		
				如有同步：时间只能是当天或者前一天 
			</td>
		</tr><?php endif; ?>
	<?php if($vo['publish_wait'] == 3): ?><tr>
			<td class="item_title">复审失败原因:</td>
			<td class="item_input">
				<?php echo ($vo["publish_memo"]); ?>
			</td>
		</tr><?php endif; ?>
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
			<input type="hidden" name="old_next_repay_time" value="<?php echo ($vo["next_repay_time"]); ?>" />
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="Deal" />
			<?php if($vo['publish_wait'] == 2): ?><input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="true_publish_update" />
			<?php else: ?>
				<input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="publish_update" /><?php endif; ?>
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
<?php if($vo['publish_wait'] == 2): ?><script type="text/javascript">
		jQuery(function(){
			$(".conf_tab input,.conf_tab select,.conf_tab textarea").addClass("readonly");
			$(".conf_tab input,.conf_tab select,.conf_tab textarea").attr("readonly","readonly");
			$("input[name='publish']").click(function(){
				if($(this).val()=="3"){
					$("#publish_msg_box").show();
					$("#publish_msg_box textarea").addClass("require");
				}
				else{
					$("#publish_msg_box").hide();
					$("#publish_msg_box textarea").removeClass("require");
				}
			});
		});
		bindKdedior(true,true,true);
	</script><?php endif; ?>
<div style="display:none" id="J_tmp_ke_box">
	<div class="f_l">名称：<input type="text" class="textbox" size="10" name="mortgage_%k_name_%s" id="mortgage_%k_name_%s" value="" />&nbsp;</div>
	<div class="f_l">图片：</div>
	<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='mortgage_%k_img_%s' id='keimg_h_mortgage_%k_img_%s_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='mortgage_%k_img_%s'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_mortgage_%k_img_%s' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_mortgage_%k_img_%s' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='mortgage_%k_img_%s' title='删除'>
				</div>
			</div>
		</div>
		</span>
	<div class="blank5"></div>
</div>
<script type="text/javascript" defer="defer">
var c_imgs= <?php echo ($mortgage_contract_json); ?>; load_img_items(c_imgs,"contract");
var imgs= <?php echo ($mortgage_infos_json); ?>; load_img_items(imgs,"infos");
</script>
</body>
</html>