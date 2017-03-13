<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class StatisticsAction extends CommonAction{

    public function index() {
    	if(trim($_REQUEST['search'])=="do"){
	    	$o_start_time = $start_time = trim($_REQUEST['start_time']);
	    	if($start_time==""){
	    		$this->error("请选择开始时间");	
	    		die();
	    	}
	    	$this->assign("start_time",$start_time);
	    	$o_end_time = $end_time = trim($_REQUEST['end_time']);
	    	
	    	if($end_time==""){
	    		$this->error("请选择结束时间");
	    		die();
	    	}
	    	
	    	$this->assign("end_time",$end_time);
	    	
	    	$start_time = to_timespan($start_time,"Y-m-d");
	    	$end_time = to_timespan($end_time,"Y-m-d");
	    	
	    	if($end_time < $start_time){
	    		$this->error("结束时间必须大于开始时间");
	    		die();
	    	}
	    	
	    	$now_time = to_timespan(to_date(TIME_UTC,"Y-m-d"),"Y-m-d");
	    	
	    	if($end_time > $now_time){
	    		$end_time = $now_time;
	    	}
	    	
	    	//开始时间跟结束时间差多少天
	    	$day = ($end_time - $start_time) /24 /3600;
	    	
	    	
	    	$list = array();
	    	$day_time = $start_time;
	    	
	    	//标分类
	    	$deal_cate = load_auto_cache("cache_deal_cate");
	    	
	    	/*foreach($deal_cate as $kk=>$vv){
	    		if(strpos($vv['name'],"智能")!==false){
	    			unset($deal_cate[$kk]);
	    		}
	    	}*/
	    	$this->assign("deal_cate",$deal_cate);
	    	
	    	//获取改时间段内所有的 还款中和 已还清的贷款
	    	//$deals = $GLOBALS['db']->getAll("SELECT id FROM ".DB_PREFIX."deal where deal_status in(4,5) and is_effect=1 and is_delete=0 and publish_wait=0 AND success_time >= $start_time and  ((loantype=1 and (success_time + repay_time*31*24*3600) >=$end_time) or (loantype=0 and (success_time + (repay_time+1)*24*3600)>=$end_time))");
	    	$deals = $GLOBALS['db']->getAll("SELECT id FROM ".DB_PREFIX."deal where deal_status in(4,5) and is_effect=1 and is_delete=0 and publish_wait=0 AND success_time >= $start_time");
	    	
	    	$temp_deals = array();
	    	
	    	require_once APP_ROOT_PATH."app/Lib/common.php";
    		require_once APP_ROOT_PATH."app/Lib/deal.php";
    		require_once APP_ROOT_PATH."system/libs/user.php";
            
             $temp_otherpay = $GLOBALS['db']->getAll("SELECT sum(money) as total_money,pay_date FROM ".DB_PREFIX."payment_notice where is_paid = 1 and pay_date between '".$o_start_time."' and '".$o_end_time."'  and payment_id not in (SELECT id from ".DB_PREFIX."payment where class_name='Otherpay') GROUP BY pay_date ");
	    	$otherpay =array();
			
	    	foreach($temp_otherpay as $k=>$v){
	    	    $otherpay[$v['pay_date']] = $v['total_money'];
	    	}
	    	$temp_onlinepay = $GLOBALS['db']->getAll("SELECT sum(money) as total_money,pay_date FROM ".DB_PREFIX."payment_notice where is_paid = 1 and pay_date between '".$o_start_time."' and '".$o_end_time."' and payment_id in (SELECT id from ".DB_PREFIX."payment where class_name='Otherpay') GROUP BY pay_date ");
	    	$onlinepay =array();
            foreach($temp_onlinepay as $k=>$v){
                $onlinepay[$v['pay_date']] = $v['total_money'];
            }

			$temp_borrow_amount = $GLOBALS['db']->getAll("SELECT sum(borrow_amount) as total_borrow_amount,concat(cate_id,'-',FROM_UNIXTIME(success_time+8*3600,'%Y-%m-%d')) as abcd FROM ".DB_PREFIX."deal where deal_status>=4 and is_delete = 0 and publish_wait = 0 and success_time between '".$start_time."' and '".($end_time+24*3600-1)."'  GROUP BY concat(cate_id,'-',FROM_UNIXTIME(success_time+8*3600,'%Y-%m-%d'))");
			
			$borrow_amount =array();
            foreach($temp_borrow_amount as $k=>$v){
                $borrow_amount[$v['abcd']] = $v['total_borrow_amount'];
            }

			$temp_load_amount = $GLOBALS['db']->getAll("SELECT sum(money) as total_self_money,create_date FROM ".DB_PREFIX."deal_load where create_date  between '".$o_start_time."' and '".$o_end_time."' and is_repay=0 GROUP BY create_date ");
			$load_amount = array();
			foreach($temp_load_amount as $k=>$v){
                $load_amount[$v['create_date']] = $v['total_self_money'];
            }

			$temp_load_lixi_amount = $GLOBALS['db']->getAll("SELECT sum(true_interest_money) as total_true_interest_money,repay_date FROM ".DB_PREFIX."deal_load_repay where repay_date between '".$o_start_time."' and '".$o_end_time."'  AND has_repay=1 GROUP BY repay_date ");
			$load_lixi_amount = array();
			foreach($temp_load_lixi_amount as $k=>$v){
                $load_lixi_amount[$v['repay_date']] = $v['total_true_interest_money'];
            }

			$temp_ss_rs = $GLOBALS['db']->getAll("SELECT sum(impose_money) as total_impose_money,sum(self_money) as total_self_money,sum(interest_money) as total_interest_money,repay_date FROM ".DB_PREFIX."deal_load_repay where repay_date between '".$o_start_time."' and '".$o_end_time."' GROUP BY repay_date ");
			$all_ss_rs =array();
			foreach($temp_ss_rs as $k=>$v){
                $all_ss_rs[$v['repay_date']] = $v;
            }
			

			$temp_has_repay_benjin_amount = $GLOBALS['db']->getAll("SELECT sum(self_money) as total_self_money,repay_date FROM ".DB_PREFIX."deal_load_repay where repay_date between '".$o_start_time."' and '".$o_end_time."'  AND has_repay=1 GROUP BY repay_date ");
			$has_repay_benjin_amount = array();
			foreach($temp_has_repay_benjin_amount as $k=>$v){
                $has_repay_benjin_amount[$v['repay_date']] = $v['total_self_money'];
            }
			
			$temp_has_repay_lxi_amount = $GLOBALS['db']->getAll("SELECT sum(true_interest_money) as total_true_interest_money,repay_date FROM ".DB_PREFIX."deal_load_repay where repay_date between '".$o_start_time."' and '".$o_end_time."' AND has_repay=1 GROUP BY repay_date");
			$has_repay_lxi_amount = array();
			foreach($temp_has_repay_lxi_amount as $k=>$v){
                $has_repay_lxi_amount[$v['repay_date']] = $v['total_true_interest_money'];
            }

			$temp_has_repay_impose_amount = $GLOBALS['db']->getAll("SELECT sum(impose_money) as total_impose_money,repay_date FROM ".DB_PREFIX."deal_load_repay where repay_date between '".$o_start_time."' and '".$o_end_time."' AND has_repay=1 GROUP BY repay_date");
			$has_repay_impose_amount = array();
			foreach($temp_has_repay_impose_amount as $k=>$v){
                $has_repay_impose_amount[$v['repay_date']] = $v['total_impose_money'];
            }

			$temp_wait_repay_benjin_amount = $GLOBALS['db']->getAll("SELECT sum(self_money) as total_self_money,repay_date FROM ".DB_PREFIX."deal_load_repay where repay_date between '".$o_start_time."' and '".$o_end_time."'  AND has_repay=0 GROUP BY repay_date");
			$wait_repay_benjin_amount = array();
			foreach($temp_wait_repay_benjin_amount as $k=>$v){
                $wait_repay_benjin_amount[$v['repay_date']] = $v['total_self_money'];
            }

			$temp_wait_repay_lxi_amount = $GLOBALS['db']->getAll("SELECT sum(interest_money) as total_interest_money, repay_date FROM ".DB_PREFIX."deal_load_repay where repay_date between '".$o_start_time."' and '".$o_end_time."'  AND has_repay=0 GROUP BY repay_date");
			$wait_repay_lxi_amount = array();
			foreach($temp_wait_repay_lxi_amount as $k=>$v){
                $wait_repay_lxi_amount[$v['repay_date']] = $v['total_interest_money'];
            }

			$temp_wait_repay_impose_amount = $GLOBALS['db']->getAll("SELECT sum(impose_money) as total_impose_money ,repay_date FROM ".DB_PREFIX."deal_load_repay where repay_date between '".$o_start_time."' and '".$o_end_time."'  AND has_repay=0 GROUP BY repay_date");
			$wait_repay_impose_amount = array();
			foreach($temp_wait_repay_impose_amount as $k=>$v){
                $wait_repay_impose_amount[$v['repay_date']] = $v['total_impose_money'];
            }

			$temp_carry = $GLOBALS['db']->getAll("SELECT sum(money) as total_money ,create_date  FROM ".DB_PREFIX."user_carry where create_date between '".$o_start_time."' and '".$o_end_time."' GROUP BY create_date");
			$carry = array();
			foreach($temp_carry as $k=>$v){
                $carry[$v['create_date']] = $v['total_money'];
            }

			$temp_suc_carry = $GLOBALS['db']->getAll("SELECT sum(money) as total_money ,create_date FROM ".DB_PREFIX."user_carry where status=1 and create_date between '".$o_start_time."' and '".$o_end_time."' GROUP BY create_date");
			$suc_carry = array();
			foreach($temp_suc_carry as $k=>$v){
                $suc_carry[$v['create_date']] = $v['total_money'];
            }

			$temp_user_amount = $GLOBALS['db']->getAll("SELECT sum(account_money) as total_account_money,create_time_ymd FROM (SELECT account_money,create_time_ymd FROM ".DB_PREFIX."user_money_log where create_time_ymd between '".$o_start_time."' and '".$o_end_time."' GROUP BY user_id ORDER by id DESC) as b GROUP BY create_time_ymd");
			$user_amount = array();
			foreach($temp_user_amount as $k=>$v){
                $user_amount[$v['create_time_ymd']] = $v['total_account_money'];
            }

			/*$sql_ua = "SELECT sum(account_money) as total_account_money,create_time_ymd  FROM (SELECT * FROM " .
	    					"(SELECT * FROM ".DB_PREFIX."user_money_log WHERE id in" .
			    				" (SELECT id FROM ".DB_PREFIX."user_money_log WHERE user_id not in" .
			    						"(SELECT user_id FROM ".DB_PREFIX."user_money_log  where create_time_ymd between '".$o_start_time."' and '".$o_end_time."')" .
			    				") AND create_time<".$day_time." ORDER BY id DESC) A " .
	    				" GROUP BY user_id) B ";
			$teno_ua = $GLOBALS['db']->getAll($sql_ua);
			$ua = array();
			foreach($teno_ua as $k=>$v){
                $ua[$v['create_time_ymd']] = $v['total_account_money'];
            }*/
				
	    	for($i = 0 ; $i<=$day; $i++){
	    		$day_date = to_date($day_time,"Y-m-d");
	    		$list[$i]['day'] = $day_time;
	    		//线上充值金额
	    		$list[$i]['online_pay'] = floatval($otherpay[$day_date]);
	    		//线下充值金额
	    		$list[$i]['below_pay'] = floatval($onlinepay[$day_date]);
	    		
	    		foreach($deal_cate as $kk=>$vv){
	    			//if(strpos($vv['name'],"智能")===false)
	    				$list[$i][$vv['id']]['borrow_amount'] = floatval($borrow_amount[$vv['id']."-".$day_date]);
	    		}
	    		
	    		//投资总额[投标者]
	    		$list[$i]['load_amount'] = floatval($load_amount[$day_date]);
	    		
	    		//已获利息总额[投标者]
	    		$list[$i]['load_lixi_amount'] = floatval($load_lixi_amount[$day_date]);
	    		
	    		
	    		$ss_rs = $all_ss_rs[$day_date];
	    		
	    		//应付本金
	    		$list[$i]['benjin_amount'] = $ss_rs['total_self_money'];
	    		//应付利息 
	    		$list[$i]['pay_lxi_amount'] = $ss_rs['total_interest_money'];
	    		//应付罚息
	    		$list[$i]['impose_amount'] = $ss_rs['total_impose_money'];
	    		
	    		//已付本金
	    		$list[$i]['has_repay_benjin_amount']=floatval($has_repay_benjin_amount[$day_date]);
	    		//已付利息
	    		$list[$i]['has_repay_lxi_amount']= floatval($has_repay_lxi_amount[$day_date]);
	    		//已付罚息
	    		$list[$i]['has_repay_impose_amount'] = floatval($temp_has_repay_impose_amount[$day_date]);
	    		
	    		
	    		//待还本金
	    		$list[$i]['wait_repay_benjin_amount'] = floatval($wait_repay_benjin_amount[$day_date]);
	    		//待还利息
	    		$list[$i]['wait_repay_lxi_amount'] = floatval($wait_repay_lxi_amount[$day_date]);
	    		//待还罚息
	    		$list[$i]['wait_repay_impose_amount'] = floatval($wait_repay_impose_amount['day_date']);
	    		
	    		
	    		//申请提现总额
	    		$list[$i]['carry'] = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry where create_date = '".$day_date."' "));
	    		
	    		//成功提现金额
	    		$list[$i]['suc_carry'] = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_carry where status=1 and create_date ='".$day_date."' "));
	    		
	    		//待投资资金
	    		$list[$i]['user_amount'] = floatval($user_amount[$day_date]);
	    		
	    		
	    			
	    		//echo $sql_ua;die();
	    		//$list[$i]['user_amount'] += floatval($ua[$day_date]);
	    		
	    		$day_time +=24 * 3600;
	    	}
	    	
	    	
			
	    	
	    	$this->assign("list",$list);
    	}
    	$this->display();
    }
}
?>