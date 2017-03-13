<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class IndexAction extends AuthAction{
	//首页
    public function index(){
		$this->display();
    }
    

    //框架头
	public function top()
	{
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$this->assign("adm_session",$adm_session);
		
		$navs = require_once APP_ROOT_PATH."system/admnav_cfg.php";		
        foreach($navs as $k=>$v){
           
            if((int)app_conf("NAV_LICAI_OPEN")==0 && strpos(strtolower($v['key']),"licai")!==false){
                unset($navs[$k]);
            }
        }

        //对于管理员没有的权限，选择隐藏
        //added by wuzeguo
        $this->check_priv($navs);

		$this->assign("navs",$navs);
		$this->display();
	}

    //检测模块是否有权限
    private function check_priv(&$navs) 
    {
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_id = intval($adm_session['adm_id']);
        $sql = "select role.role_id, role.node, role.module from ".DB_PREFIX."role_access as role left join ".
            DB_PREFIX."admin as admin on admin.role_id = role.role_id  ".
            "where admin.id = ".$adm_id;
        $have_priv_list = $GLOBALS['db']->getAll($sql);

        foreach ($navs AS $k=>$v) {
            $count2 = 0;
            foreach ($v['groups'] AS $k1 => $v1) {
                $count1 = 0;
                foreach ($v1['nodes'] AS $k2=>$v2) {
                    $count3 = 0;
                    foreach ($have_priv_list AS $k3=>$v3) {
                        if ($v2['module'] == $v3['module'] && $v2['action'] == $v3['node']) {
                            ++ $count3;
                            break;
                        }
                    }

                    if (!$count3)  {
                        unset($navs[$k]['groups'][$k1]['nodes'][$k2]);
                    } else {
                        ++ $count1;
                    }
                }
                if ($count1 == 0) {
                    unset($navs[$k]['groups'][$k1]);
                } else {
                    ++ $count2;
                }
            }

            if ($count2 == 0) unset($navs[$k]);
        }

        return $navs;
    }

	//框架左侧
	public function left()
	{
		$navs = require_once APP_ROOT_PATH."system/admnav_cfg.php";
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$adm_id = intval($adm_session['adm_id']);

        //对于管理员没有的权限，选择隐藏
        //added by wuzeguo
        $this->check_priv($navs);
		
		$nav_key = strim($_REQUEST['key']);
		$nav_group = $navs[$nav_key]['groups'];
		
        foreach($nav_group as $k=>$v){
            if((int)app_conf("NAV_LICAI_OPEN")==0 && strpos(strtolower($v['key']),"licai")!==false){
                unset($nav_group[$k]);
            }
        }
		$this->assign("menus",$nav_group);
		$this->display();
	}
	//默认框架主区域
	public function main()
	{
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$this->assign("adm_session",$adm_session);
		$adm_id = intval($adm_session['adm_id']);
		$login_time = $GLOBALS['db']->getOne("SELECT login_time FROM ".DB_PREFIX."admin where id = $adm_id ");
		$login_time = to_date($login_time);
		$this->assign("login_time",$login_time);
		$h=to_date(TIME_UTC,"H");
		if($h<12){
			$greet ="上午好";
		}elseif($h<18){
			$greet ="下午好";
		}else{
			$greet ="晚上好";
		}
		$this->assign("greet",$greet);
		
		$navs = require_once APP_ROOT_PATH."system/admnav_cfg.php";		
        foreach($navs as $k=>$v){
            
            if((int)app_conf("NAV_LICAI_OPEN")==0 && strpos(strtolower($v['key']),"licai")!==false){
                unset($navs[$k]);
            }
        }      
		$this->assign("navs",$navs);
		
		//会员数
		$total_user = M("User")->where("user_type in (0,1) ")->count();
		$total_verify_user = M("User")->where("is_effect=1 AND user_type in (0,1) ")->count();
		$this->assign("total_user",$total_user);
		$this->assign("total_verify_user",$total_verify_user);
		
		//今日新个人会员
		$today=to_date(TIME_UTC,"Y-m-d");
		$total_today_user = M("User")->where("user_type = 0 AND create_date = '$today'  ")->count();
		$this->assign("total_today_user",$total_today_user);
		
		//今日新企业会员
		$total_today_company_user = M("User")->where("user_type = 1 AND create_date = '$today'  ")->count();
		$this->assign("total_today_company_user",$total_today_company_user);
		
		//筹款中中的标 
		$fundraising_deal_count = M("Deal")->where("is_effect=1 AND publish_wait = 0 AND is_delete = 0 AND deal_status = 1")->count();
		
		//债权转让中的标
		$transfer_deal_count = M("DealLoadTransfer")->where("status = 1 ")->count();
		//今日债权转让
		$today_transfer_amount = M("DealLoadTransfer")->where("create_date = '$today' ")->sum("load_money");
		
		//待审核留言标
		$msgboard_count = M("DealMsgboard")->where("status = 0 ")->count();
		
		
		//满标的借款
		$suc_deal_count = M("Deal")->where("is_effect=1 AND publish_wait = 0 AND is_delete = 0 AND deal_status = 2")->count();
		//待审核的借款
		$wait_deal_count = M("Deal")->where("publish_wait in (1,3) AND is_delete = 0 ")->count();
		
		$wait_deal_review_count = M("Deal")->where("publish_wait = 2 AND is_delete = 0 ")->count();
		
		//网站垫付
		$website_pay_count =  M("GenerationRepay")->count();
		
		//留言待回复
		$noreply_message_count = M("Message")->where("update_time = 0")->count();
		
		//今日投资金额
		$today_invest_amount = M("DealLoad")->where("create_date = '$today'")->sum("money");
		
		//今日借款金额
		$today_borrow_amount = M("Deal")->where("start_date = '$today'")->sum("borrow_amount");
		
		//等待材料的借款
		$info_deal_count = M("Deal")->where("is_effect=1 AND publish_wait = 0 AND is_delete = 0 AND deal_status=0")->count();
		//等待审核的申请额度
		$quota_count = M("QuotaSubmit")->where("status=0")->count();
		
		//等待审核的授信额度
		$deal_quota_count = M("DealQuotaSubmit")->where("status=0")->count();
		
		//提现申请
		$carry_count = D("UserCarry")->where("status = 0")->count();
		//提现待付款
		$waitpay_carry_count = D("UserCarry")->where("status = 3")->count();
		
		//三日要还款的借款
		$threeday_repay_count = M("DealRepay")->where("((repay_time - ".TIME_UTC." +  24*3600 -1)/24/3600 between 0 AND 3) and has_repay=0 ")->count();
		
		//逾期未还款的
		$yq_repay_count = M("DealRepay")->where(" (".TIME_UTC." - repay_time  -  24*3600 +1 )/24/3600 > 0 and has_repay=0 ")->count();
		
		//未处理举报
		$reportguy_count = M("Reportguy")->where("status = 0")->count();
		
		//注册待审核
		$register_count = M("User")->where("login_time = 0 and login_ip='' and is_effect=0 and is_delete=0 and user_type=0 and is_black=0")->count();
		$company_register_count = M("User")->where("login_time = 0 and login_ip='' and is_effect=0 and is_delete=0 and user_type=1 and is_black=0")->count();
		
		//线下充值单
		$payment_id = M("Payment")->where("class_name = 'Otherpay'")->getField("id");
		$condition['payment_id'] = $payment_id;
		$offline_pay_count = floatval($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."payment_notice where is_paid = 1 and payment_id in (SELECT id from ".DB_PREFIX."payment where class_name='Otherpay') "));
		$this->assign("offline_pay_count",$offline_pay_count);
		
		if(app_conf("NAV_LICAI_OPEN") == 1)
		{
			
			$licai_verify_count = M("Licai")->where('verify in (0,2)')->count();
			$licai_count = M("Licai")->where('verify = 1')->count();			
			$licai_order_count = M("LicaiOrder")->count();			
			$licai_advance_count = M("LicaiAdvance")->count();
			$licai_redempte_count = M("LicaiRedempte")->where("type = 1 and status in (0,1,2)")->count();
			$licai_wait_redempte_count = M("LicaiRedempte")->where("type = 0 and status in (0,1,2)")->count();
			$licai_near_count = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."licai_order lco
		 left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id where lco.status in (1,2) and lco.end_interest_date >='".to_date(TIME_UTC-15*24*3600,"Y-m-d")."' and lco.end_interest_date <='".to_date(TIME_UTC,"Y-m-d")."'");
		 	$licai_send_count = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."licai_order lco
		 left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id where lco.status =3");
			
			$this->assign("licai_verify_count",$licai_verify_count);			
			$this->assign("licai_count",$licai_count);			
			$this->assign("licai_order_count",$licai_order_count);
			$this->assign("licai_advance_count",$licai_advance_count);
			$this->assign("licai_redempte_count",$licai_redempte_count);
			$this->assign("licai_wait_redempte_count",$licai_wait_redempte_count);
			$this->assign("licai_near_count",$licai_near_count);	
			$this->assign("licai_send_count",$licai_send_count);	
		}

		//未处理续约申请
		$generation_repay_submit = M("GenerationRepaySubmit")->where("status = 0")->count();
		
		$this->assign("register_count",$register_count);
		$this->assign("company_register_count",$company_register_count);
		$this->assign("suc_deal_count",$suc_deal_count);
		$this->assign("wait_deal_count",$wait_deal_count);	
		$this->assign("wait_deal_review_count",$wait_deal_review_count);	
		$this->assign("info_deal_count",$info_deal_count);
		$this->assign("deal_quota_count",$deal_quota_count);
		$this->assign("quota_count",$quota_count);
		$this->assign("carry_count",$carry_count);
		$this->assign("threeday_repay_count",$threeday_repay_count);
		$this->assign("yq_repay_count",$yq_repay_count);
		$this->assign("reportguy_count",$reportguy_count);
		$this->assign("generation_repay_submit",$generation_repay_submit);
		$this->assign("waitpay_carry_count",$waitpay_carry_count);
		$this->assign("website_pay_count",$website_pay_count);
		$this->assign("noreply_message_count",$noreply_message_count);
		$this->assign("fundraising_deal_count",$fundraising_deal_count);
		$this->assign("today_invest_amount",$today_invest_amount);
		$this->assign("today_borrow_amount",$today_borrow_amount);
		$this->assign("transfer_deal_count",$transfer_deal_count);
		$this->assign("today_transfer_amount",$today_transfer_amount);
		$this->assign("msgboard_count",$msgboard_count);
		
		
		$topic_count = M("Topic")->where("is_effect = 1 and fav_id = 0")->count();		
		$msg_count = M("Message")->where("is_buy = 0")->count();
		$buy_msg_count = M("Message")->count();
		
		
		
		$this->assign("topic_count",$topic_count);
		$this->assign("msg_count",$msg_count);
		$this->assign("buy_msg_count",$buy_msg_count);
		
		
		//充值单数
		$incharge_order_buy_count = M("PaymentNotice")->where("is_paid=1")->count();
		$this->assign("incharge_order_buy_count",$incharge_order_buy_count);
		
		
		$reminder = M("RemindCount")->find();
		$reminder['topic_count'] = intval(M("Topic")->where("is_effect = 1 and relay_id = 0 and fav_id = 0 and create_time >".$reminder['topic_count_time'])->count());
		$reminder['msg_count'] = intval(M("Message")->where("create_time >".$reminder['msg_count_time'])->count());
		/*$reminder['buy_msg_count'] = intval(M("Message")->where("create_time >".$reminder['buy_msg_count_time'])->count());
		$reminder['order_count'] = intval(M("DealOrder")->where("is_delete = 0 and type = 0 and pay_status = 2 and create_time >".$reminder['order_count_time'])->count());
		$reminder['refund_count'] = intval(M("DealOrder")->where("is_delete = 0 and refund_status = 1 and create_time >".$reminder['refund_count_time'])->count());
		$reminder['retake_count'] = intval(M("DealOrder")->where("is_delete = 0 and retake_status = 1 and create_time >".$reminder['retake_count_time'])->count());*/
		$reminder['incharge_count'] = intval(M("PaymentNotice")->where("is_paid = 1 and pay_date=".to_date(TIME_UTC,"Y-m-d")." ")->count());
		
		M("RemindCount")->save($reminder);
		$this->assign("reminder",$reminder);
		
		//待审核认证资料
		$auth_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_credit_file where status = 0 ");
		$this->assign("auth_count",$auth_count);
		
		//待补还项目
		$after_repay_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal as d where publish_wait=0 and is_delete =0 AND deal_status in(4,5) AND (repay_money > round((SELECT sum(repay_money) FROM ".DB_PREFIX."deal_load_repay WHERE has_repay=1 and deal_id = d.id),2) + 1 or (repay_money >0  and (SELECT count(*) FROM ".DB_PREFIX."deal_load_repay WHERE has_repay =1 and deal_id = d.id) = 0))");
		$this->assign("after_repay_count",$after_repay_count);
		$this->display();
	}	
	
	public function map(){
		$navs = require_once APP_ROOT_PATH."system/admnav_cfg.php";	
        foreach($navs as $k=>$v){
            
            if((int)app_conf("NAV_LICAI_OPEN")==0 && strpos(strtolower($v['key']),"licai")!==false){
                unset($navs[$k]);
            }
        }	
		$this->assign("navs",$navs);
		$this->display();
	}
	
	//底部
	public function footer()
	{
		$this->display();
	}
	
	//修改管理员密码
	public function change_password()
	{
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$this->assign("adm_data",$adm_session);
		$this->display();
	}
	public function do_change_password()
	{
		$adm_id = intval($_REQUEST['adm_id']);
		if(!check_empty($_REQUEST['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_EMPTY_TIP"));
		}
		if(!check_empty($_REQUEST['adm_new_password']))
		{
			$this->error(L("ADM_NEW_PASSWORD_EMPTY_TIP"));
		}
		if($_REQUEST['adm_confirm_password']!=$_REQUEST['adm_new_password'])
		{
			$this->error(L("ADM_NEW_PASSWORD_NOT_MATCH_TIP"));
		}		
		if(M("Admin")->where("id=".$adm_id)->getField("adm_password")!=md5($_REQUEST['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_ERROR"));
		}
		M("Admin")->where("id=".$adm_id)->setField("adm_password",md5($_REQUEST['adm_new_password']));
		save_log(M("Admin")->where("id=".$adm_id)->getField("adm_name").L("CHANGE_SUCCESS"),1);
		$this->success(L("CHANGE_SUCCESS"));
		
		
	}
	
	public function reset_sending()
	{
		$field = trim($_REQUEST['field']);
		if($field=='DEAL_MSG_LOCK'||$field=='PROMOTE_MSG_LOCK'||$field=='APNS_MSG_LOCK')
		{
			M("Conf")->where("name='".$field."'")->setField("value",'0');
			$this->success(L("RESET_SUCCESS"),1);
		}
		else
		{
			$this->error(L("INVALID_OPERATION"),1);
		}
	}
	
	
	function manage_carry(){
		
		$id = intval($_REQUEST['id']);
		$manage_carry_list = $GLOBALS['db']->getAll( "select * from ".DB_PREFIX."admin_carry");
		$this->assign("manage_carry_list",$manage_carry_list);
		$this->display();
	}
	public function de_manage_carry()
	{
		$id = intval($_REQUEST['id']);
		
		$list = M("AdminCarry")->where('id='.$id)->delete(); // 删除
	
		if(!$list){
			$this->error("删除失败");
		}else{
			$this->success("删除成功");
		}
	}
	public function add_manage_carry()
	{	
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$this->assign("adm_session",$adm_session);
		$this->display();
	}
	public function insert_carry()
	{
		$admin_id = intval($_REQUEST['admin_id']);
		$admin_name = $_REQUEST['admin_name'];
		$money = floatval($_REQUEST['money']);
		$memo = $_REQUEST['memo'];
		
		//$creat_time = to_date($_REQUEST['creat_time']);
		$creat_time = TIME_UTC;
		$admin_carry = array();
		$admin_carry['admin_id'] = $admin_id;
		$admin_carry['admin_name'] = $admin_name;
		$admin_carry['money'] = $money;
		$admin_carry['memo'] = $memo;
		$admin_carry['create_time'] = $creat_time;
		 
		M("AdminCarry")->add($admin_carry);
		
		$this->assign("jumpUrl",u(MODULE_NAME."/manage_carry"));
		$this->success(L("INSERT_SUCCESS"));
	}
	
	//统计信息
	function statistics(){
		//总的用户
		$user_count =  M("User")->where("user_type in(0,1)")->count();
		$this->assign("user_count",$user_count);
		//有效的未删除的
		$effect_user = $GLOBALS['db']->getAll("SELECT is_effect,count(*) as total_user FROM ".DB_PREFIX."user where is_delete = 0 and user_type in(0,1) group by is_effect ORDER BY is_effect DESC");
		$this->assign("effect_user",$effect_user);
		//回收站用户
		$trash_user_count = M("User")->where("is_delete=1 and user_type in(0,1)")->count();
		$this->assign("trash_user_count",$trash_user_count);
		
		//认证
		$credit_types = load_auto_cache("credit_type");
		$tcredit_files = $GLOBALS['db']->getAll("SELECT `type`,count(*) as total_user FROM ".DB_PREFIX."user_credit_file where status = 1 and passed=1 group by `type` ");
		$credit_files = array();
		foreach($tcredit_files as $k=>$v){
			$credit_files[$v['type']] = $v['total_user'];
		}
		unset($tcredit_files);
		$credit_types = $credit_types['list'];
		foreach($credit_types as $k=>$v){
			
			if($credit_files[$v['type']] > 0){
				$credit_types[$k]['user_count'] = $credit_files[$v['type']];
			}
			else{
				unset($credit_types[$k]);
			}
		}
		unset($credit_files);
		$this->assign("credit_types",$credit_types);
		
		//线上充值
		$online_pay = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where is_paid = 1 and payment_id not in (SELECT id from ".DB_PREFIX."payment where class_name='Otherpay') "));
		$this->assign("online_pay",$online_pay);
		//线下充值金额
		$below_pay = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where is_paid = 1 and payment_id in (SELECT id from ".DB_PREFIX."payment where class_name='Otherpay') "));
		$this->assign("below_pay",$below_pay);
		
		//线下充值ID
		$below_pay_id = $GLOBALS['db']->getOne("SELECT id from ".DB_PREFIX."payment where class_name='Otherpay'");
		$this->assign("below_pay_id",$below_pay_id);
		
		//管理员充值  （user_log管理员编辑账户）
		$manage_recharge = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_money_log where `type`='13'"));
		$this->assign("manage_recharge",$manage_recharge);
		
		//管理员提现
		$manage_carry = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."admin_carry "));
		$this->assign("manage_carry",$manage_carry);
		
		
		//成功提现
		$carry_amount = M("UserCarry")->where("status=1")->sum("money");
		$this->assign("carry_amount",$carry_amount);
		
		//第三方
		$className = getCollName();
		
		//环迅
		if(strtolower($className) == "ips")
		{
			//第三方充值
			$secend_pay = M("IpsDoDpTrade")->where("pErrCode='MG00000F'")->sum("pTrdAmt");
			//第三方提现
			$secend_carry_amount = M("IpsDoDwTrade")->where("pErrCode='MG00000F'")->sum("pTrdAmt");
		}
		elseif(strtolower($className) == "yeepay")
		{
			//第三方充值
			$secend_pay = M("YeepayRecharge")->where("code=1")->sum("amount");
			//第三方提现
			$secend_carry_amount = M("YeepayWithdraw")->where("code=1")->sum("amount");
		}
		elseif(strtolower($className) == "baofoo")
		{
			//第三方充值
			$secend_pay = M("BaofooRecharge")->where("code='CSD000'")->sum("amount");
			//第三方提现
			$secend_carry_amount = M("baofoo_fo_charge")->where("code='CSD000'")->sum("amount");
		}
		
		$this->assign("secend_pay",$secend_pay);
		
		
		$this->assign("secend_carry_amount",$secend_carry_amount);
		
		//总计
		$total_usre_money = $online_pay + $below_pay + $manage_recharge - $carry_amount;
		$this->assign("total_usre_money",$total_usre_money);
		
		/**
		 * 成功借出总额 total_borrow_amount
		 * 冻结中的保证金 借款人 freezen_amt
		 * 冻结中的保证金 担保人 grt_freezen_amt
		 * 成功借款投标奖励总额 rebate_amount
		 */
		$total_borrow_amount = $GLOBALS['db']->getOne("SELECT sum(borrow_amount) as total_borrow_amount FROM ".DB_PREFIX."deal where publish_wait = 0 and is_effect = 1 and is_delete = 0 and deal_status >=4 ");
		$this->assign("borrow_amount",$total_borrow_amount);
		
		//已还款总额
		$has_repay_amount = floatval($GLOBALS['db']->getOne("SELECT sum(self_money) FROM ".DB_PREFIX."deal_load_repay where has_repay = 1 "));
		$this->assign("has_repay_amount",$has_repay_amount);
		
		//未还总额
		$need_repay_amount = floatval($GLOBALS['db']->getOne("SELECT sum(self_money) FROM ".DB_PREFIX."deal_load_repay where has_repay = 0 "));
		$this->assign("need_repay_amount",$need_repay_amount);
		
		//冻结中的保证金 借款人
		$freezen_amt = $GLOBALS['db']->getOne("SELECT (sum(real_freezen_amt)-sum(un_real_freezen_amt) ) as freezen_amt FROM ".DB_PREFIX."deal where publish_wait = 0 and is_effect = 1 and is_delete = 0 and deal_status >=4 AND ips_bill_no<>'' ");
		$this->assign("freezen_amt",$freezen_amt);
		//冻结中的保证金 担保人
		$grt_freezen_amt = $GLOBALS['db']->getOne("SELECT (guarantor_real_freezen_amt - un_guarantor_real_freezen_amt) as grt_freezen_amt FROM ".DB_PREFIX."deal where publish_wait = 0 and is_effect = 1 and is_delete = 0 and deal_status >=4 AND ips_bill_no<>'' ");
		$this->assign("grt_freezen_amt",$grt_freezen_amt);
		
		//成功借款利息总额
		$load_rate_amount = floatval($GLOBALS['db']->getOne("SELECT sum(repay_money - self_money) FROM ".DB_PREFIX."deal_load_repay where has_repay = 1 "));
		$this->assign("load_rate_amount",$load_rate_amount);
		
		//成功借款投标奖励总额
		$rebate_amount = $GLOBALS['db']->getOne("SELECT sum(borrow_amount*CONVERT(user_bid_rebate,DECIMAL)*0.01) as rebate_amount FROM ".DB_PREFIX."deal where publish_wait = 0 and is_effect = 1 and is_delete = 0 and deal_status >=4 ");
		$this->assign("rebate_amount",$rebate_amount);
		
		//注册奖励冻结资金
		$register_lock_money = floatval($GLOBALS['db']->getOne("SELECT sum(lock_money) FROM ".DB_PREFIX."user_lock_money_log where `type` = 18 "));
		$this->assign("register_lock_money",$register_lock_money);
		
		//逾期还款总额
		$yq_repay_amount = floatval($GLOBALS['db']->getOne("SELECT sum(repay_manage_impose_money + impose_money) FROM ".DB_PREFIX."deal_load_repay where has_repay = 1 and status in(2,3)"));
		$this->assign("yq_repay_amount",$yq_repay_amount);
		//逾期未还款总额
		$yq_norepay_amount = floatval($GLOBALS['db']->getOne("SELECT sum(repay_manage_impose_money + impose_money) FROM ".DB_PREFIX."deal_load_repay where has_repay = 0 and status in(2,3)"));
		$this->assign("yq_norepay_amount",$yq_norepay_amount);
		
		//逾期罚息总额
		$yq_all_amount = floatval($GLOBALS['db']->getOne("SELECT sum(impose_money) FROM ".DB_PREFIX."deal_load_repay where status in(2,3)"));
		$this->assign("yq_all_amount",$yq_all_amount);
		
		//借款者成交服务费
		$success_service_fee = $GLOBALS['db']->getOne("SELECT sum(borrow_amount*CONVERT(services_fee,DECIMAL)*0.01)  FROM ".DB_PREFIX."deal where publish_wait = 0 and is_effect = 1 and is_delete = 0 and deal_status >=4 ");
		$this->assign("success_service_fee",$success_service_fee);
		
		//借款者成交管理费
		$success_manage_fee = $GLOBALS['db']->getOne("SELECT sum(true_manage_money) FROM ".DB_PREFIX."deal_repay where has_repay=1 ");
		$this->assign("success_manage_fee",$success_manage_fee);
		
		//投资者成交管理费
		$load_success_manage_fee = $GLOBALS['db']->getOne("SELECT sum(true_manage_money) FROM ".DB_PREFIX."deal_load_repay where has_repay=1 ");
		$this->assign("load_success_manage_fee",$load_success_manage_fee);
		
		//提现手续费
		$carry_manage_fee = $GLOBALS['db']->getOne("SELECT sum(fee) FROM ".DB_PREFIX."user_carry where status = 1 ");
		$this->assign("carry_manage_fee",$carry_manage_fee);
		
		//理财总计
		$licai["verify_count"] = M("Licai")->where('verify in (0,2)')->count();
		$licai["count"] = M("Licai")->where('verify = 1')->count();			
		$licai["order_count"] = M("LicaiOrder")->count();			
		$licai["advance_count"] = M("LicaiAdvance")->count();
		$licai["redempte_count"] = M("LicaiRedempte")->where("type = 1 and status in (0,1,2)")->count();
		$licai["wait_redempte_count"] = M("LicaiRedempte")->where("type = 0 and status in (0,1,2)")->count();
		$licai["near_count"] = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."licai_order lco
	 left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id where lco.status in (1,2) and lco.end_interest_date >='".to_date(TIME_UTC-15*24*3600,"Y-m-d")."' and lco.end_interest_date <='".to_date(TIME_UTC,"Y-m-d")."'");
		$licai["send_count"] = $GLOBALS["db"]->getOne("select count(*) from ".DB_PREFIX."licai_order lco
	 left join ".DB_PREFIX."licai lc on lco.licai_id = lc.id where lco.status =3");
		
		$this->assign("licai",$licai);		
		$this->display();
	}
	
	
}
?>
