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
<script type="text/javascript" src="__TMPL__Common/js/deal_add.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/colorpicker.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/colorpicker.css" />

<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/jquery.autocomplete.css" />
<script type="text/javascript" src="__TMPL__Common/js/jquery.autocomplete.min.js"></script>
<script type="text/javascript">
	window.onload = function()
	{
		init_dealform();
	}
</script>
<div class="main">
<div class="main_title"><a href="<?php if($reback_url): ?><?php echo ($reback_url); ?><?php else: ?><?php echo u("Deal/index");?><?php endif; ?>" class="back_list"><?php echo L("BACK_LIST");?></a></div>
<div class="blank5"></div>
<form name="edit" action="__APP__" method="post" enctype="multipart/form-data">
<div class="button_row">
	<input type="button" class="button conf_btn" rel="1" value="<?php echo L("DEAL_BASE_INFO");?>" />&nbsp;
	<input type="button" class="button conf_btn" rel="2" value="相关参数" />&nbsp;	
	<!--<input type="button" class="button conf_btn" rel="5" value="抵押物" />&nbsp;	
	<input type="button" class="button conf_btn" rel="6" value="相关资料" />&nbsp;	
    -->
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
			<input type="text" name="titlecolor" class="textbox" maxlength="6" size="6" id="colorpickerField" value="" />
			<span class="tip_span">不填即为默认颜色</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">借款编号:</td>
		<td class="item_input">
			<input type="text" name="deal_sn" class="textbox" value="<?php echo ($deal_sn); ?>" />
			<span class="tip_span">此处编号用于合同处</span>
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_NAME");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox require" name="name" style="width:500px;" />
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_SUB_NAME");?>:</td>
		<td class="item_input"><input type="text" class="textbox require" name="sub_name" /> <span class='tip_span'>[<?php echo L("DEAL_SUB_NAME_TIP");?>]</span></td>
	</tr>
	<tr>
		<td class="item_title">会员名称:</td>
		<td class="item_input">
			<?php if($vo['user_id'] > 0): ?><?php echo get_user_name_real($vo['user_id']);?> <a href="__APP__?m=User&a=passed&id=<?php echo ($vo["user_id"]); ?>&loantype=<?php echo ($vo["loantype"]); ?>" target="_blank">资料认证</a>
			<input type="hidden" name="user_id" value="<?php echo ($vo["user_id"]); ?>"/>
			<?php else: ?>
			<!--<input type="text" class="textbox require J_autoUserName" name="user_name" />
       <input type="hidden" class="textbox require J_autoUserId" name="user_id" />-->
           <select name="user_id" id="user_list">
               <option value="0">平台发布</option>
               <?php if(is_array($user_list)): foreach($user_list as $key=>$user): ?><option value="<?php echo ($user["id"]); ?>"><?php echo ($user["user_name"]); ?>-<?php echo ($user["real_name"]); ?>-<?php echo ($user["mobile"]); ?></option><?php endforeach; endif; ?>
           </select>
           <input type="text" class="textbox" placeholder="昵称/真实姓名/手机号查找用户" id="search_user"/>
           <a href="javascript:get_user_list();">查找</a><?php endif; ?>
		</td>
	</tr>

	<tr>
		<td class="item_title">所在城市:</td>
		<td class="item_input" id="citys_box">
			
			<?php if(is_array($citys)): foreach($citys as $key=>$city): ?><?php if($city['pid'] == 0): ?><div class="item">
						<div class="bcity f_l">
							<input name="city_id[]"  type="checkbox" value="<?php echo ($city["id"]); ?>" >
							<?php echo ($city["name"]); ?>
						 	：
					 	</div>
					 	<div class="scity f_l">
					 	<?php if(is_array($citys)): foreach($citys as $key=>$citya): ?><?php if($city['id'] == $citya['pid']): ?><input  type="checkbox" name="city_id[]" value="<?php echo ($citya["id"]); ?>" >
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
			<?php if(is_array($deal_cate_tree)): foreach($deal_cate_tree as $key=>$cate_item): ?><option value="<?php echo ($cate_item["id"]); ?>"><?php echo ($cate_item["title_show"]); ?></option><?php endforeach; endif; ?>
		</select>
		</td>
	</tr>
	
	
	<tr>
		<td class="item_title">担保机构:</td>
		<td class="item_input">
		<select name="agency_id">
			<option value="0">==<?php echo L("NO_SELECT_AGENCY");?>==</option>
			<?php if(is_array($deal_agency)): foreach($deal_agency as $key=>$agency_item): ?><option value="<?php echo ($agency_item["id"]); ?>"><?php if($agency_item['short_name'] != ''): ?><?php echo ($agency_item["short_name"]); ?><?php else: ?><?php echo ($agency_item["user_name"]); ?><?php endif; ?></option><?php endforeach; endif; ?>
		</select>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">担保范围:</td>
		<td class="item_input">
		<select name="warrant">
			<option value="0">无</option>
			<option value="1">本金</option>
			<option value="2">本金及利息</option>
		</select>
		</td>
	</tr>
	
	<tr id="guarantor_margin_amt_box" style="display:none">
		<td class="item_title">担保保证金[第三方托管]:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="guarantor_margin_amt" value="0.00" />
			<span class="tip_span">冻结担保人的金额，需要提前存钱</span>
		</td>
	</tr>
	
	<tr id="guarantor_amt_box" style="display:none">
		<td class="item_title">担保金额[第三方托管]:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="guarantor_amt" value="0.00" />
			<a href="__ROOT__/index.php?ctl=tool" target="_blank">理财计算器</a>
		</td>
	</tr>
	
	<tr id="guarantor_pro_fit_amt_box" style="display:none">
		<td class="item_title">担保收益[第三方托管]:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="guarantor_pro_fit_amt" value="0.00" />
		</td>
	</tr>
	
	<tr>
		<td class="item_title"><?php echo L("DEAL_ICON");?>:</td>
		<td class="item_input">
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='icon' id='keimg_h_icon_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='icon'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_icon' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_icon' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='icon' title='删除'>
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
			<option value="0">==<?php echo L("NO_SELECT_TYPE");?>==</option>
			<?php if(is_array($deal_type_tree)): foreach($deal_type_tree as $key=>$type_item): ?><option value="<?php echo ($type_item["id"]); ?>"><?php echo ($type_item["name"]); ?></option><?php endforeach; endif; ?>
		</select>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">还款方式:</td>
		<td class="item_input">
			<select name="loantype" >
				<?php if(is_array($loantype_list)): foreach($loantype_list as $key=>$loantype): ?><option value="<?php echo ($loantype["key"]); ?>" rel="<?php echo ($loantype["repay_time_type_str"]); ?>"><?php echo ($loantype["sub_name"]); ?></option><?php endforeach; endif; ?>
			</select>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">借款合同范本:</td>
		<td class="item_input">
			<select name="contract_id" class="require">
				<option value="0">==选择合同范本==</option>
				<?php if(is_array($contract_list)): foreach($contract_list as $key=>$contract): ?><option value="<?php echo ($contract["id"]); ?>"><?php echo ($contract["title"]); ?></option><?php endforeach; endif; ?>
			</select>
		</td>
	</tr>
    <!--
	<tr>
		<td class="item_title">转让合同范本:</td>
		<td class="item_input">
			<select name="tcontract_id" class="require">
				<option value="0">==选择合同范本==</option>
				<?php if(is_array($contract_list)): foreach($contract_list as $key=>$contract): ?><option value="<?php echo ($contract["id"]); ?>"><?php echo ($contract["title"]); ?></option><?php endforeach; endif; ?>
			</select>
		</td>
	</tr>
    -->
	
	<tr>
		<td class="item_title"><?php echo L("BORROW_AMOUNT");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox require" name="borrow_amount" value="<?php if($vo['borrow_amount']): ?><?php echo ($vo["borrow_amount"]); ?><?php else: ?>0.00<?php endif; ?>" />
		</td>
	</tr>
	
	<tr>
		<td class="item_title">借款保证金[第三方托管]:</td>
		<td class="item_input">
			<input type="text" class="textbox require " name="guarantees_amt"  value="0.00"  />
			<span class="tip_span">冻结借款人的金额，需要提前存钱</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">投标类型:</td>
		<td class="item_input">
			<select name="uloadtype">
				<option value=0>按金额</option>
				<option value=1>按份额</option>
			</select>
		</td>
	</tr>
	
	<tr class="uloadtype_0">
		<td class="item_title"><?php echo L("MIN_LOAN_MONEY");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox require" name="min_loan_money" value="0"/>
		</td>
	</tr>
	
	<tr class="uloadtype_0">
		<td class="item_title"><?php echo L("MAX_LOAN_MONEY");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="max_loan_money"  value="0" />
			<span class="tip_span">0为不限制</span>
		</td>
	</tr>
	
	<tr class="uloadtype_1" style="display:none">
		<td class="item_title">分成多少份:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="portion" value="0"/>
		</td>
	</tr>
	
	<tr class="uloadtype_1" style="display:none">
		<td class="item_title">最高买多少份:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="max_portion"  value="0" />
			<span class="tip_span">0为不限制</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title"><?php echo L("REPAY_TIME");?>:</td>
		<td class="item_input">
			<input type="text" id="repay_time" class="textbox require" SIZE="5"  name="repay_time" value="3" />
			<select id="repay_time_type" name="repay_time_type">
				<option value="0">天</option>
				<option value="1" selected="selected">月</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("RATE");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox require" SIZE="5" name="rate"  value="<?php echo ($vo["rate"]); ?>"  />%
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
				<option value="0">否</option>
				<option value="1">是</option>
			</select>
			<span class="tip_span">选“是”请将“最低投标金额”设置大于最大红包额度</span>
		</td>
	</tr>
    
    <tr>
		<td class="item_title">可否使用加息券:</td>
		<td class="item_input">
			<select name="use_interestrate">
				<option value="0">否</option>
				<option value="1">是</option>
			</select>
		</td>
	</tr>
	
	<tr class="uloadtype_0">
        <td class="item_title">可否使用体验金:</td>
        <td class="item_input">
            <select name="use_learn">
                <option value="0">否</option>
                <option value="1">是</option>
            </select>
        </td>
    </tr>
	
	<tr>
		<td class="item_title"><?php echo L("DEAL_DESCRIPTION");?>:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='description' name='description' class='ketext' style='width:500px;height:200px' rel="true"></textarea> </div>
		</td>
	</tr>
	<tr>
		<td class="item_title">风险等级:</td>
		<td class="item_input">
			<select name="risk_rank">
				<option value="0">低</option>
				<option value="1">中</option>
				<option value="2">高</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="item_title">风险控制:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='risk_security' name='risk_security' class='ketext' style='width:500px;height:200px' rel="true"></textarea> </div>
		</td>
	</tr>
	<?php if(ACTION_NAME != 'makedeals'): ?><tr>
		<td class="item_title">借款状态:</td>
		<td class="item_input">
			<label><?php echo L("DEAL_STATUS_1");?><input type="radio" name="deal_status" value="1" /></label>
			<label><?php echo L("DEAL_STATUS_3");?><input type="radio" name="deal_status" value="3" /></label>
		</td>
	</tr><?php endif; ?>
	<tr id="start_time_box" style="display:none">
		<td class="item_title">开始时间:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="start_time" value="" id="start_time"  onfocus="this.blur(); return showCalendar('start_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_start_time');" />
			<input type="button" class="button" id="btn_start_time" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('start_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_start_time');" />
			<input type="button" class="button" value="<?php echo L("CLEAR_TIME");?>" onclick="$('#start_time').val('');" />		
			如有同步：时间只能是当天或者前一天 
		</td>
	</tr>
	<tr id="bad_time_box" style="display:none">
		<td class="item_title"><?php echo L("DEAL_STATUS_3");?>时间:</td>
		<td class="item_input">
			<input type="text" class="textbox" name="bad_time" id="bad_time" value="" onfocus="this.blur(); return showCalendar('bad_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_bad_time');" />
			<input type="button" class="button" id="btn_bad_time" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('bad_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_bad_time');" />
			<input type="button" class="button" value="<?php echo L("CLEAR_TIME");?>" onclick="$('#bad_time').val('');" />	
		</td>
	</tr>
	<tr id="bad_info_box" style="display:none">
		<td class="item_title"><?php echo L("DEAL_STATUS_3");?>原因:</td>
		<td class="item_input">
			<textarea type="text" class="textbox" name="bad_msg" id="bad_msg" value="" rows="3" cols="50"></textarea>
		</td>
	</tr>
	<!--tr>
		<td class="item_title"><?php echo L("IS_EFFECT");?>:</td>
		<td class="item_input">
			<lable><?php echo L("IS_EFFECT_1");?><input type="radio" name="is_effect" value="1" checked="checked" /></lable>
			<lable><?php echo L("IS_EFFECT_0");?><input type="radio" name="is_effect" value="0" /></lable>
		</td>
	</tr-->
	<tr>
		<td class="item_title"><?php echo L("SORT");?>:</td>
		<td class="item_input"><input type="text" class="textbox" name="sort" value="<?php echo ($new_sort); ?>" /></td>
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
			<input type="text" class="textbox" SIZE="5" name="services_fee" value="<?php if($vo['services_fee'] != ''): ?><?php echo ($vo["services_fee"]); ?><?php endif; ?>"  />%
			<span class="tip_span">按发布时的会员等级</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">借款者获得积分:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="score" value="0"  />
			【非信用积分】
		</td>
	</tr>
	<tr>
		<td class="item_title">借款者管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="manage_fee" value="<?php if($vo['manage_fee'] != ''): ?><?php echo ($vo["manage_fee"]); ?><?php else: ?><?php echo app_conf("MANAGE_FEE");?><?php endif; ?>"  />%
			<span class="tip_span">管理费 = 本金总额×管理费率 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">投资者管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="user_loan_manage_fee" value="<?php echo app_conf("USER_LOAN_MANAGE_FEE");?>"  />%
			<span class="tip_span">管理费 = 投资总额×管理费率 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">投资者利息管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="user_loan_interest_manage_fee" value="<?php echo app_conf("USER_LOAN_INTEREST_MANAGE_FEE");?>"  />%
			<span class="tip_span">管理费 = 实际得到的利息×管理费率 0即不收取</span>(如果是VIP会员将从VIP会员配置里读取)
		</td>
	</tr>
	<tr>
		<td class="item_title">普通逾期管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="manage_impose_fee_day1" value="<?php echo app_conf("MANAGE_IMPOSE_FEE_DAY1");?>"  />%
			<span class="tip_span">逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">严重逾期管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="manage_impose_fee_day2" value="<?php echo app_conf("MANAGE_IMPOSE_FEE_DAY2");?>"  />%
			<span class="tip_span">逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">普通逾期罚息:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="impose_fee_day1" value="<?php echo app_conf("IMPOSE_FEE_DAY1");?>"  />%
			<span class="tip_span">罚息总额 = 逾期本息总额×对应罚息利率×逾期天数 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">严重逾期罚息:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="impose_fee_day2" value="<?php echo app_conf("IMPOSE_FEE_DAY2");?>"  />%
			<span class="tip_span">罚息总额 = 逾期本息总额×对应罚息利率×逾期天数 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">债权转让管理费:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="user_load_transfer_fee" value="<?php echo app_conf("USER_LOAD_TRANSFER_FEE");?>"  />%
			<span class="tip_span">管理费 = 转让金额×管理费率 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">债权转让期限:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="transfer_day" value="0"  />
			<span class="tip_span">满标放款多少天后才可以进行转让 0代表不限制</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">提前还款补偿:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="compensate_fee" value="<?php echo app_conf("COMPENSATE_FEE");?>"  />%
			<span class="tip_span">补偿金额 = 剩余本金×补偿利率 0即不收取</span>
		</td>
	</tr>
	<tr>
		<td class="item_title">投资人返利:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="user_bid_rebate" value="<?php echo app_conf("USER_BID_REBATE");?>"  />%
			<span class="tip_span">返利金额=投标金额×返利百分比【需满标】</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">投资返还积分比率:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="user_bid_score_fee" value="<?php echo app_conf("USER_BID_SCORE_FEE");?>"  />%
			<span class="tip_span">投标返还积分 = 投标金额 ×返还比率【需满标】</span>(如果是VIP会员将从VIP会员配置里读取)【非信用积分】
		</td>
	</tr>
	
	<tr>
		<td class="item_title">申请延期:</td>
		<td class="item_input">
			<input type="text" class="textbox" SIZE="5" name="generation_position" value="100"  />%
			<span class="tip_span">当还款金额大于或等于设置的额度，借款人如果资金不够，可申请延期还款，延期还款就是平台代其还此借款。借款人未还部分由平台跟借款人协商。</span>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">合同附件:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='attachment' name='attachment' class='ketext' style='width:500px;height:200px' rel="true"></textarea> </div>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">转让合同附件:</td>
		<td class="item_input">
			 <div  style='margin-bottom:5px; '><textarea id='tattachment' name='tattachment' class='ketext' style='width:500px;height:200px' rel="true"></textarea> </div>
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
		<td class="item_input"><textarea class="textarea" name="seo_title" ></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_SEO_KEYWORD");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_keyword" ></textarea></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DEAL_SEO_DESCRIPTION");?>:</td>
		<td class="item_input"><textarea class="textarea" name="seo_description" ></textarea></td>
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
				<option value="0">无</option>
				<option value="1">有</option>
			</select>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">抵押物管理费</td>
		<td class="item_input">
			<input type="text" class="textbox " size="5" name="mortgage_fee" value="0"> 元/月
		</td>
	</tr>
	
	<tr>
		<td class="item_title">抵押物说明</td>
		<td class="item_input">
			<textarea name="mortgage_desc" class="textarea" ></textarea>
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
		<td id="view_user_img_box">
			<?php if($old_imgdata_str): ?><?php if(is_array($old_imgdata_str)): foreach($old_imgdata_str as $key=>$img_item): ?><p style="float:left">
					<input style=" margin-top: 12px;"  type="checkbox" name="key[]" <?php if(isset($vo['view_info'][$img_item['key']])): ?>checked="checked"<?php endif; ?> value="<?php echo ($img_item["key"]); ?>">
					<a href='<?php echo ($img_item["img"]); ?>' target="_blank" title="<?php echo ($img_item["name"]); ?>"><img width="35" height="35" style="float:left; border:#ccc solid 1px; margin-left:5px;" id="<?php echo ($img_item["name"]); ?>" src="<?php echo ($img_item["img"]); ?>"></a>
					</p><?php endforeach; endif; ?><?php endif; ?>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">借款签约合同[ <a href="javascript:void(0);" onclick="add_mortgage_img('contract');">+</a> ]</td>
		<td class="item_input" id="mortgage_contract_box">
			<div class="f_l">名称：<input type="text" class="textbox" size="10" name="mortgage_contract_name_1" id="mortgage_contract_name_1" />&nbsp;</div>
			<div class="f_l">图片：</div>
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='mortgage_contract_img_1' id='keimg_h_mortgage_contract_img_1_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='mortgage_contract_img_1'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_mortgage_contract_img_1' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_mortgage_contract_img_1' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='mortgage_contract_img_1' title='删除'>
				</div>
			</div>
		</div>
		</span>
			<div class="blank5"></div>
			<div class="f_l">名称：<input type="text" class="textbox" size="10" name="mortgage_contract_name_2" id="mortgage_contract_name_2" />&nbsp;</div>
			<div class="f_l">图片：</div>
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='mortgage_contract_img_2' id='keimg_h_mortgage_contract_img_2_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='mortgage_contract_img_2'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_mortgage_contract_img_2' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_mortgage_contract_img_2' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='mortgage_contract_img_2' title='删除'>
				</div>
			</div>
		</div>
		</span>
			<div class="blank5"></div>
			<div class="f_l">名称：<input type="text" class="textbox" size="10" name="mortgage_contract_name_3" id="mortgage_contract_name_3" />&nbsp;</div>
			<div class="f_l">图片：</div>
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='mortgage_contract_img_3' id='keimg_h_mortgage_contract_img_3_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='mortgage_contract_img_3'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_mortgage_contract_img_3' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_mortgage_contract_img_3' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='mortgage_contract_img_3' title='删除'>
				</div>
			</div>
		</div>
		</span>
			<div class="blank5"></div>
			<div class="f_l">名称：<input type="text" class="textbox" size="10" name="mortgage_contract_name_4" id="mortgage_contract_name_4" />&nbsp;</div>
			<div class="f_l">图片：</div>
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='mortgage_contract_img_4' id='keimg_h_mortgage_contract_img_4_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='mortgage_contract_img_4'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_mortgage_contract_img_4' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_mortgage_contract_img_4' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='mortgage_contract_img_4' title='删除'>
				</div>
			</div>
		</div>
		</span>
			<div class="blank5"></div>
		</td>
	</tr>
	
	<tr>
		<td class="item_title">抵押物图片[ <a href="javascript:void(0);" onclick="add_mortgage_img('infos');">+</a> ]</td>
		<td class="item_input" id="mortgage_infos_box">
			<div class="f_l">名称：<input type="text" class="textbox" size="10" name="mortgage_infos_name_1" id="mortgage_infos_name_1" />&nbsp;</div>
			<div class="f_l">图片：</div>
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='mortgage_infos_img_1' id='keimg_h_mortgage_infos_img_1_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='mortgage_infos_img_1'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_mortgage_infos_img_1' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_mortgage_infos_img_1' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='mortgage_infos_img_1' title='删除'>
				</div>
			</div>
		</div>
		</span>
			<div class="blank5"></div>
			<div class="f_l">名称：<input type="text" class="textbox" size="10" name="mortgage_infos_name_2" id="mortgage_infos_name_2" />&nbsp;</div>
			<div class="f_l">图片：</div>
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='mortgage_infos_img_2' id='keimg_h_mortgage_infos_img_2_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='mortgage_infos_img_2'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_mortgage_infos_img_2' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_mortgage_infos_img_2' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='mortgage_infos_img_2' title='删除'>
				</div>
			</div>
		</div>
		</span>
			<div class="blank5"></div>
			<div class="f_l">名称：<input type="text" class="textbox" size="10" name="mortgage_infos_name_3" id="mortgage_infos_name_3" />&nbsp;</div>
			<div class="f_l">图片：</div>
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='mortgage_infos_img_3' id='keimg_h_mortgage_infos_img_3_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='mortgage_infos_img_3'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_mortgage_infos_img_3' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_mortgage_infos_img_3' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='mortgage_infos_img_3' title='删除'>
				</div>
			</div>
		</div>
		</span>
			<div class="blank5"></div>
			<div class="f_l">名称：<input type="text" class="textbox" size="10" name="mortgage_infos_name_4" id="mortgage_infos_name_4" />&nbsp;</div>
			<div class="f_l">图片：</div>
			<span>
        <div style='float:left; height:35px; padding-top:1px;'>
			<input type='hidden' value='' name='mortgage_infos_img_4' id='keimg_h_mortgage_infos_img_4_i' />
			<div class='buttonActive' style='margin-right:5px;'>
				<div class='buttonContent'>
					<button type='button' class='keimg ke-icon-upload_image' rel='mortgage_infos_img_4'>选择图片</button>
				</div>
			</div>
		</div>
		 <a href='./admin/Tpl/default/Common/images/no_pic.gif' target='_blank' id='keimg_a_mortgage_infos_img_4' ><img src='./admin/Tpl/default/Common/images/no_pic.gif' id='keimg_m_mortgage_infos_img_4' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
		 <div style='float:left; height:35px; padding-top:1px;'>
			 <div class='buttonActive'>
				<div class='buttonContent'>
					<img src='/admin/Tpl/default/Common/images/del.gif' style='display:none; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='mortgage_infos_img_4' title='删除'>
				</div>
			</div>
		</div>
		</span>
			<div class="blank5"></div>
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
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
			<!--隐藏元素-->
			
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="Deal" />
			<input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="insert" />
			<!--隐藏元素-->
			<input type="submit" class="button" value="<?php echo L("ADD");?>" />
			<input type="reset" class="button" value="<?php echo L("RESET");?>" />
			</td>
		</tr>
		<tr>
			<td colspan=2 class="bottomTd"></td>
		</tr>
	</table> 	 
</form>
</div>
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
</body>
</html>