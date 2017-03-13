<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

define(MODULE_NAME,"debit");
require APP_ROOT_PATH.'app/Lib/deal.php';
require APP_ROOT_PATH.'app/Lib/uc.php';
class debit_dealModule extends SiteBaseModule
{
	public function index()
	{		
		if(!$GLOBALS['user_info']){
    		showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$is_ajax);
    	}
		$view["type"] = intval($_REQUEST["type"]);
		$view["debit_money"] = strim($_REQUEST["debit_money"]);
		$view["repay_time"] = intval($_REQUEST["repaytime"]);
		
		if($view["type"] == 0 && $view["debit_money"] == "" && $view["repay_time"] == 0 )
		{
			app_redirect(url("debit","debit")); 
			die;
		}
		/*读取白条配置*/
		$debit_conf = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."debit_conf");
		
		/*读取标类别作为商品名*/
		$view["deal_loan_type"] = $GLOBALS["db"]->getOne("select name from ".DB_PREFIX."deal_loan_type"." where id =".$view["type"]);
				
		/*计算标的各种金额*/
		
		//月还款本金
		$view["stages_money"] = floatval($view["debit_money"])/floatval($view["repay_time"]);
		//利率
		
		$level_list = load_auto_cache("level");
			
		$level_list_info = reset($level_list["repaytime_list"]);
		
		foreach($level_list_info as $k=>$v) 
		{
			if($v[1] != 0 && $v[0] == $view["repay_time"])
			{
				$min_rate = $v[2];
				$max_rate = $v[3];
				break;
			}
		}
		if($debit_conf["rate_cfg"]==0)
		{
			$view["rate"] = $min_rate;
		}
		elseif($debit_conf["rate_cfg"]==1)
		{
			$view["rate"] = floatval(($min_rate+$max_rate)/2);
		}
		elseif($debit_conf["rate_cfg"]==2)
		{
			$view["rate"] = $max_rate;
		}
		
		//月供
		//*************
    	//等额本息
		if($debit_conf['loantype']==0)
			$view['guarantor_amt'] = pl_it_formula($view["debit_money"],$view['rate']/12/100,$view["repay_time"]);
		if($debit_conf['loantype']==3)
			$view['fee'] = av_it_formula($view["debit_money"],$view['rate']/12/100);
		
		
		$view["first_relief"] = $debit_conf["first_relief"];
		//$view["first_pay"] = format_price($view['guarantor_amt'] - $debit_conf["first_relief"],2);
		
		//付息还本
		/*elseif($debit_conf['loantype']==1)
			$view['guarantor_amt'] = av_it_formula($view["debit_money"],$view['rate']/12/100) * $view["repay_time"]  +  $view["debit_money"];
		//到期本息
		elseif($debit_conf['loantype']==2)
			$view['guarantor_amt'] = $view["debit_money"] * $view['rate']/12/100 * $view["repay_time"] +  $view["debit_money"];
		*/
		//*************
		//月付利息
		$view['guarantor_amt'] = number_format($view["fee"]+$view["stages_money"],2);
		$view["fee"] = number_format($view["fee"],2);
		
		$GLOBALS['tmpl']->assign("view",$view);
		$GLOBALS['tmpl']->assign("u_info",$GLOBALS["user_info"]);
		$GLOBALS['tmpl']->display("debit/debit_deal.html");
	}
	function savedebit(){

		$view["type"] = intval($_REQUEST["type"]);
		$view["debit_money"] = strim($_REQUEST["debit_money"]);
		$view["repay_time"] = intval($_REQUEST["repaytime"]);
		
		$view["university"] = strim($_REQUEST["university"]);
		$view["address"] = strim($_REQUEST["address"]);
		$view["mobile"] = strim($_REQUEST["mobile"]);
		$view["real_name"] = strim($_REQUEST["real_name"]);
		$view["u_alipay"] = strim($_REQUEST["u_alipay"]);
		
		$is_ajax = intval($_REQUEST["is_ajax"]);
		
		if(strim($GLOBALS["user_info"]["u_alipay"])=="" &&strim($_REQUEST["u_alipay"]) == "")
		{
			showErr("请先绑定支付宝",$is_ajax);
		}
		if($view["university"] == "")
		{
			showErr("请填写学校信息",$is_ajax);
		}
		
		if($view["address"] == "")
		{
			showErr("请填写地址",$is_ajax);
		}
		
		if($view["mobile"] == "")
		{
			showErr("请填写联系电话",$is_ajax);
		}
		
		if($view["type"] == "" || $view["debit_money"] == "" || $view["repay_time"] =="")
		{
			showErr("请返回重新提交",$is_ajax);
		}
		
    	$is_ajax = intval($_REQUEST['is_ajax']);
    	
    	if(!$GLOBALS['user_info']){
    		showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$is_ajax);
    	}
		
    	$t = trim($_REQUEST['t']);
    	
    	if(!in_array($t,array("save","publish"))){
    		showErr($GLOBALS['lang']['ERROR_TITLE'],$is_ajax);
    	}

		/*读取白条配置*/
		$debit_conf = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."debit_conf");

		/*读取标类别作为商品名*/
		$view["deal_loan_type"] = $GLOBALS["db"]->getOne("select name from ".DB_PREFIX."deal_loan_type"." where id =".$view["type"]);
		
		
    	if($t=="save")
    		$data['is_delete'] = 2;
    	else
    		$data['is_delete'] = 0;
    	
    	$data['name'] = $view["deal_loan_type"].$view["debit_money"]."元";
    	if(empty($data['name'])){
    		showErr("请输入借款标题",$is_ajax);
    	}
		
    	$data['publish_wait'] = 1;
    	$icon_type = "systemImg";
    	if($icon_type==""){
    		showErr("请选择借款图片类型",$is_ajax);
    	}
		
    	$icon_type_arr = array(
    		'upload' =>1,
    		'userImg' =>2,
    		'systemImg' =>3,
    	);
    	$data['icon_type'] = $icon_type_arr[$icon_type];
    	
    	if(intval($data['icon_type'])==0)
    	{
    		showErr("请选择借款图片类型",$is_ajax);
    	}
    	$_REQUEST['systemimgpath'] = $view["type"];
    	switch($data['icon_type']){
    		case 1 :
    			if(strim($_REQUEST['icon'])==''){
    				showErr("请上传图片",$is_ajax);
    			}
    			else{
    				$data['icon'] = replace_public(strim($_REQUEST['icon']));
    			}
    			break;
    		case 2 :
    			$data['icon'] = replace_public(get_user_avatar($GLOBALS['user_info']['id'],'big'));
    			break;
    		case 3 :
    			if(intval($_REQUEST['systemimgpath'])==0){
    				showErr("请选择系统图片",$is_ajax);
    			}
    			else{
    				$data['icon'] = $GLOBALS['db']->getOne("SELECT icon FROM ".DB_PREFIX."deal_loan_type WHERE id=".intval($_REQUEST['systemimgpath']));
    			}
    			break;
    	}
    	
    	//$data['cate_id'] = $data['type_id'] = intval($view["type"]);
		$data['type_id'] = intval($view["type"]);
		
    	if($data['type_id']==0){
    		showErr("请选择借款用途",$is_ajax);
    	}
    	
    	$data['borrow_amount'] = floatval($view["debit_money"]);
    	
    	/*if($data['borrow_amount'] < (int)trim(app_conf('MIN_BORROW_QUOTA')) || $data['borrow_amount'] > (int)trim(app_conf('MAX_BORROW_QUOTA')) || $data['borrow_amount'] %50 != 0){
    		showErr("请正确选择借款金额",$is_ajax);
    	}*/
		
    	//判断是否需要额度
    	if($GLOBALS['db']->getOne("SELECT is_quota FROM ".DB_PREFIX."deal_loan_type WHERE id=".$data['type_id']) == 1){
	    	if(intval($GLOBALS['user_info']['quota']) != 0){
	    		$can_use_quota = get_can_use_quota($GLOBALS['user_info']['id']);
	    		if($data['borrow_amount'] > intval($can_use_quota)){
	    			showErr("您申请的白条总额已超过您的白条额度,请返回修改订单",$is_ajax);
	    		}
	    	}
    	}
    	
    	$data['repay_time'] = intval($_REQUEST['repaytime']);
    	if($data['repay_time']==0){
    		showErr("借款期限",$is_ajax);
    	}
    	$data['rate'] = floatval($view["rate"]); //
    	$data['repay_time_type'] = 1;  //按月
    	$min_rate = 0;
    	$max_rate = 0;
    	$is_rate_lock = false;
		
    	$level_list = load_auto_cache("level");
			
		$level_list_info = reset($level_list["repaytime_list"]);
		
		foreach($level_list_info as $k=>$v) 
		{
			if($v[1] != 0 && $v[0] == $view["repay_time"])
			{
				$min_rate = $v[2];
				$max_rate = $v[3];
				break;
			}
		}
		if($debit_conf["rate_cfg"]==0)
		{
			$data["rate"] = $min_rate;
		}
		elseif($debit_conf["rate_cfg"]==1)
		{
			$data["rate"] = floatval(($min_rate+$max_rate)/2);
		}
		elseif($debit_conf["rate_cfg"]==2)
		{
			$data["rate"] = $max_rate;
		}

    	if(floatval($data['rate']) <= 0 || floatval($data['rate']) > $max_rate || floatval($data['rate']) < $min_rate){
    		showErr("请正确输入借款利率",$is_ajax);
    	}
    	
    	$data['enddate'] = intval($debit_conf['enddate']); //筹标期限 
    	
    	$data['description'] = $GLOBALS["user_info"]["user_name"]."的白条：".$view["deal_loan_type"].$view["debit_money"]."元";
    	
    	if(trim($data['description'])==''){
    		showErr("请输入项目描述",$is_ajax);
    	}
    	
		$datas["university"] = $view["university"];
		$datas["address"] = $view["address"];
		$datas["mobile"] = $view["mobile"];
		$datas["real_name"] = $view["real_name"];
		$datas["u_alipay"] = $view["u_alipay"];
    		
    	//$datas['view_info'] = serialize($user_view_info);
    	
    	$GLOBALS['db']->autoExecute(DB_PREFIX."user",$datas,"UPDATE","id=".$GLOBALS['user_info']['id']);

		$data["services_fee"]=$debit_conf["services_fee"];
		$data["manage_fee"]=$debit_conf["manage_fee"];
		$data["manage_impose_fee_day1"]=$debit_conf["manage_impose_fee_day1"];
		$data["manage_impose_fee_day2"]=$debit_conf["manage_impose_fee_day2"];
		$data["impose_fee_day1"]=$debit_conf["impose_fee_day1"];
		$data["impose_fee_day2"]=$debit_conf["impose_fee_day2"];
		
    	$data['voffice'] = 1;
    	$data['vposition'] = 1;
    	
    	$data['is_effect'] = 1;
    	$data['deal_status'] = 0;
    	
    	$data['agency_id'] = 0; //担保机构
    	$data['agency_status'] = 1;
    	$data['warrant'] = 0; //担保类型
    	
    	
    	$data['guarantor_margin_amt'] = 0; //担保保证金
    	$data['guarantor_pro_fit_amt'] = 0; //担保收益
    	
    	$data['user_id'] = intval($GLOBALS['user_info']['id']);
    	
    	$data['loantype'] = intval($debit_conf['loantype']);
    	if($data['repay_time_type'] == 0){
    		$data['loantype'] = 2;
    	}
    	
    	//当为天的时候
		if($data['repay_time_type'] == 0){
			$true_repay_time = 1;
		}
		else{
			$true_repay_time = $data['repay_time'];
		}
    	
    	//本金担保
    	if($data['warrant'] == 1){
    		$data['guarantor_amt'] = $data['borrow_amount'];
    	}
    		
    	//本息担保
    	elseif($data['warrant'] == 2){
    		//等额本息
    		if($data['loantype']==0)
    			$data['guarantor_amt'] = pl_it_formula($data['borrow_amount'],$data['rate']/12/100,$true_repay_time) * $true_repay_time;
			}
    	
		$data['is_hidden'] = 1;
		
    	$data['create_time'] = TIME_UTC;
    	
		//推荐人
		$work_id = strim($_REQUEST["work_id"]);
		if($work_id)
		{
			$data["admin_id"] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."admin where work_id = '".$work_id."'");
		}
		
    	$module = "INSERT";
    	$jumpurl = url("debit","debit_uc_center#order");
    	$condition = "";
    	
    	/*$deal_id = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."deal WHERE ((is_delete=2 or is_delete=3) or (is_delete=0 and publish_wait=1)) AND user_id=".$GLOBALS['user_info']['id']);
    	if($deal_id > 0){
    		$module = "UPDATE";
    		if($t=="save")
    			$jumpurl = url("debit","debit_uc_center#order");
    		$condition = "id = $deal_id";
    	}
    	else{
    		if($t=="save"){
    			$jumpurl = url("debit","debit_uc_center#order");
    		}
    	}*/
		
    	$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$data,$module,$condition);
    	if($module == "INSERT"){
    		$deal_id = $GLOBALS['db']->insert_id();
    	}
    	
		require_once APP_ROOT_PATH.'app/Lib/deal.php';
		$deal = get_deal($deal_id);
    	//发送验证通知
    	if(
    		$t!="save" &&
    		trim(app_conf('CUSTOM_SERVICE'))!=''&&
    		($GLOBALS['user_info']['idcardpassed']==0 || 
    		 $GLOBALS['user_info']['incomepassed']==0 || 
    		 $GLOBALS['user_info']['creditpassed']==0 || 
    		 $GLOBALS['user_info']['workpassed']==0
    		)
    		
    	){
    		$ulist = explode(",",trim(app_conf('CUSTOM_SERVICE')));
			$ulist = array_filter($ulist);
			
			if($ulist){
				$uuid = $ulist[array_rand($ulist)];
				if($uuid > 0){
		    		$content = app_conf("SHOP_TITLE")."用户您好，请尽快上传必要信用认证材料（包括身份证认证、工作认证、收入认证、信用报告认证）。另外，多上传一些可选信用认证，有助于您提高借款额度，也有利于出借人更多的了解您的情况，以便让您更快的筹集到所需的资金。请您点击'我要贷款'，之后点击相应的审核项目，进入后，可先阅读该项信用认证所需材料及要求，然后按要求上传资料即可。 如果您有任何问题请您拨打客服电话 ".app_conf('SHOP_TEL')." 或给客服邮箱发邮件 ".app_conf("REPLY_ADDRESS")." 我们会及时给您回复。";
		    		require_once APP_ROOT_PATH.'app/Lib/message.php';
		    		
		    		//添加留言
					$message['title'] = $content;
					$message['content'] = htmlspecialchars(addslashes(valid_str($content)));
					$message['title'] = valid_str($message['title']);
						
					$message['create_time'] = TIME_UTC;
					$message['rel_table'] = "deal";
					$message['rel_id'] = $deal_id;
					$message['user_id'] = $uuid;
					
					$message['is_effect'] = 1;		
					$GLOBALS['db']->autoExecute(DB_PREFIX."message",$message);
					
					//添加到动态
					insert_topic("message",$message['rel_id'],$message['user_id'],get_user_name($message['user_id'],false),$GLOBALS['user_info']['id']);
					
					//自己给自己留言不执行操作
					if($deal['user_id']!=$message['user_id']){
						$msg_conf = get_user_msg_conf($deal['user_id']);
						//站内信
						if($msg_conf['sms_asked']==1){
							$notices['shop_title'] = app_conf("SHOP_TITLE");
							$notices['shop_tel'] = app_conf('SHOP_TEL');
							$notices['shop_address'] = app_conf("REPLY_ADDRESS");
							
							/*{$notice.shop_title}用户您好，请尽快上传必要信用认证材料（包括身份证认证、工作认证、收入认证、信用报告认证）。另外，多上传一些可选信用认证，有助于您提高借款额度，也有利于出借人更多的了解您的情况，以便让您更快的筹集到所需的资金。请您点击'我要贷款'，之后点击相应的审核项目，进入后，可先阅读该项信用认证所需材料及要求，然后按要求上传资料即可。 如果您有任何问题请您拨打客服电话{$notice.shop_tel}或给客服邮箱发邮件{$notice.shop_address}我们会及时给您回复。*/
							$notices['url'] = "“<a href=\"".$deal_info['url']."\">".$deal_info['name']."</a>”";
							$notices['user_name'] = get_user_name($message['user_id']);
							$notices['money'] = ($user_load_data['true_repay_money']+$user_load_data['impose_money']);
							
							$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_WORDS_MSG'",false);
							$GLOBALS['tmpl']->assign("notice",$notices);
							$contents = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
							
							send_user_msg("",$contents,0,$deal['user_id'],TIME_UTC,0,true,13,$message['rel_id']);
						}
						//邮件
						if($msg_conf['mail_asked']==1 && app_conf('MAIL_ON')==1){
							$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_DEAL_MSG'");
							$tmpl_content = $tmpl['content'];
							
							$notice['user_name'] = $GLOBALS['user_info']['user_name'];
							$notice['msg_user_name'] = get_user_name($message['user_id'],false);
							$notice['deal_name'] = $deal['name'];
							$notice['deal_url'] = SITE_DOMAIN.url("index","deal",array("id"=>$deal['id']));
							$notice['message'] = $message['content'];
							$notice['site_name'] = app_conf("SHOP_TITLE");
							$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
							$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
							
							
							$GLOBALS['tmpl']->assign("notice",$notice);
							
							$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
							$msg_data['dest'] = $GLOBALS['user_info']['email'];
							$msg_data['send_type'] = 1;
							$msg_data['title'] = get_user_name($message['user_id'],false)."给您的标留言！";
							$msg_data['content'] = addslashes($msg);
							$msg_data['send_time'] = 0;
							$msg_data['is_send'] = 0;
							$msg_data['create_time'] = TIME_UTC;
							$msg_data['user_id'] = $GLOBALS['user_info']['id'];
							$msg_data['is_html'] = $tmpl['is_html'];
							$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
						}
					}
				}
			}
    	}
    	
    	if($is_ajax==1){
    		showSuccess("提交成功，请等待管理员审核",$is_ajax,$jumpurl);
    	}
    	else{
	    	app_redirect($jumpurl);
    	}
    }
	//调用支付宝接口充值
	///$money   还款金额
	///$order_id 还款编号
	///$repay_type 0正常还款  1提前还款
	
	function debit_incharge($money,$order_id,$debit_type)
	{
		$payment_id = $GLOBALS["db"]->getOne("select id from ".DB_PREFIX."payment where class_name ='Baofoo'");
		$bank_id = 0;//addslashes(htmlspecialchars(trim($_REQUEST['bank_id'])));
		$memo = "";//addslashes(htmlspecialchars(trim($_REQUEST['memo'])));
		$pingzheng = "";//replace_public(trim($_REQUEST['pingzheng']));
		
		$status = getInchargeDone($payment_id,$money,$bank_id,$memo,$pingzheng,$order_id,$debit_type);
		if($status['status'] == 0){			
			showErr($status['show_err']);
		}
		else{
			if($status['pay_status'])
			{
				return url("debit","payment#incharge_done",array("id"=>$status['order_id'],"from"=>"debit")); //充值支付成功
			}
			else
			{
				return url("debit","payment#pay",array("id"=>$status['order_id'],"from"=>"debit")); //充值支付成功
			}
		}
	}
	//正常还款执行界面
	public function repay_borrow_money(){
		$id = intval($_REQUEST['id']);
		$ids = intval($_REQUEST['ids']); //只有单个还款,多条暂不考虑
		if(!$id)
		{
			showSuccess("订单错误，请重试",1);
		}
		$deal_repay = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."deal_repay where deal_id = ".$id." and l_key=".$ids);
		
		if(!$deal_repay)
		{
			showSuccess("订单错误，请重试",1);
		}
		$first_relief = 0;
		if($ids == 0)
		{
			//首单 减钱
			$deal_admin = $GLOBALS["db"]->getOne("select admin_id from ".DB_PREFIX."deal where id = ".$id);
			if($deal_admin)
			{
				$first_relief = $GLOBALS["db"]->getOne("select first_relief from ".DB_PREFIX."debit_conf");
			}
		}
		
		$status["jump"] = $this->debit_incharge($deal_repay["repay_money"] - $first_relief,$deal_repay["id"],1);
		
		$status["status"] = 2;
		
		ajax_return($status);
		
		/*
		$status = getUcRepayBorrowMoney($id,$ids);
		if ($status['status'] == 2){
			ajax_return($status);
			die();
		}
		elseif ($status['status'] == 0){
			showErr($status['show_err'],1);
		}else{
			showSuccess($status['show_err'],1);
		}
		*/
				
	}
	//提前还款操作界面
	public function inrepay_refund(){
		
		$id = intval($_REQUEST['id']);		
		
		$status = getUcInrepayRefund($id);
		if ($status['status'] == 1){		
			//$deal = $status['deal'];
			$GLOBALS['tmpl']->assign("deal",$status['deal']);
			$GLOBALS['tmpl']->assign("true_all_manage_money",$status['true_all_manage_money']);
			
			$GLOBALS['tmpl']->assign("impose_money",$status['impose_money']);
			$GLOBALS['tmpl']->assign("total_repay_money",$status['total_repay_money']);
						
			$GLOBALS['tmpl']->assign("true_total_repay_money",$status['true_total_repay_money']);
			
			$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_DEAL_REFUND']);
			$GLOBALS['tmpl']->assign("inc_file","debit/debit_inrepay_refund.html");
			$GLOBALS['tmpl']->display("debit/debit_uc.html");	
		}else{
			showErr($status['show_err']);
		}
	}

	//提前还款执行程序
	public function inrepay_repay_borrow_money(){
		$id = intval($_REQUEST['id']);
		
		if(!$id)
		{
			showSuccess("订单错误，请重试",1);
		}
		
		$status = getUcInrepayRefund($id);
		if ($status['status'] == 1){
			$first_relief = 0;
			$ids = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."deal_repay where deal_id = ".$id." and has_repay = 1");
			if($ids==0)
			{
				//首单 减钱
				$deal_admin = $GLOBALS["db"]->getOne("select admin_id from ".DB_PREFIX."deal where id = ".$id);

				if($deal_admin)
				{
					$first_relief = $GLOBALS["db"]->getOne("select first_relief from ".DB_PREFIX."debit_conf");
				}
			}
			$status["jump"] = $this->debit_incharge($status['total_repay_money']-$first_relief,$id,2);
		
			$status["status"] = 2;
			
			ajax_return($status);
			
		}else{
			showErr($status['show_err']);
		}
		/*
		$this->debit_incharge($money,$order_id,1);
		*/
		/*
		$status = getUCInrepayRepayBorrowMoney($id);
		if ($status['status'] == 0){
			showErr($status['show_err'],1);
		}else{
			showSuccess($status['show_err'],1);
		}
		*/		
	}
}	
?>