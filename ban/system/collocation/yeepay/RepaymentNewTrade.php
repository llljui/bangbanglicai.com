<?php

	function RepaymentNewTradeXml($data,$pDetails,$pWebUrl,$pS2SUrl){
		$strxml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"
				."<request platformNo='".$data['platformNo']."'>"
				."<requestNo>" .$data['requestNo'] ."</requestNo>"
				."<platformUserNo>" .$data['platformUserNo'] ."</platformUserNo>"
				."<bizType>REPAYMENT</bizType>"
				.'<userType>'.$data['userType'].'</userType>'
				.$pDetails
				."<callbackUrl><![CDATA[" .$pWebUrl ."]]></callbackUrl>"
				."<notifyUrl><![CDATA[" .$pS2SUrl ."]]></notifyUrl>"
				."</request>";
	
		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$strxml=str_replace('\\','',$strxml);//去除转义反斜杠\
		return $strxml;
	}	
	
	/**
	 * 还款
	 * @param deal $deal  标的数据
	 * @param array $repaylist  还款列表
	 * @param int $deal_repay_id  还款计划ID
	 * @param int $MerCode  商户ID
	 * @param string $cert_md5 
	 * @param string $post_url
	 * @return string
	 */
	function RepaymentNewTrade($deal, $repaylist, $deal_repay_id, $platformNo,$post_url){

		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Yeepay&class_act=RepaymentNewTrade&from=".$_REQUEST['from'];//web方式返回
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Yeepay&class_act=RepaymentNewTrade&from=".$_REQUEST['from'];//s2s方式返回		
		
		$fee = 0;
		
		//--记录日志
		$yeepay_log = array();
		$yeepay_log['code'] = 'toCpTransaction';
		$yeepay_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log);
		$requestNo = $GLOBALS['db']->insert_id();
		
		$cp_t = array();
		$cp_t['requestNo'] = $requestNo;
		$cp_t['platformNo'] = $platformNo;
		$cp_t['platformUserNo'] = $deal["user_id"];
		$u_info = get_user_info("*","id = ".$deal['user_id']);
		$cp_t['userType'] = $u_info;
		$cp_t['bizType'] = 'REPAYMENT';
		
		if ($u_info['user_type'] == 0){
			$cp_t['userType'] = 'MEMBER';
		}else{
			$cp_t['userType'] = 'MERCHANT';
		}
		$cp_t['tenderOrderNo'] = $deal["id"];
		$cp_t['tenderName'] = $deal["sub_name"];
		$cp_t['tenderAmount'] = $deal["borrow_amount"];
		$cp_t['deal_repay_id'] = $deal_repay_id;
		$cp_t['tenderDescription'] = $deal['name'];//项目描述信息
		$cp_t['borrowerPlatformUserNo'] = $deal['user_id'];//项目的借款人平台用户编
		$cp_t['create_time'] = TIME_UTC;
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_cp_transaction",$cp_t);
		$id = $GLOBALS['db']->insert_id();
		
		$fee = 0;
		
		foreach($repaylist as $k=>$v){
				
			//平台收取：借款者 的管理费 + 管理逾期罚息
			$fee = $fee + $v['repay_manage_money'] + $v['repay_manage_impose_money'];
				
			//==============================投资者获取的，费用===================================
			$detail = array();
			$detail['pid'] = $id;
			$detail['deal_load_repay_id'] = $v['id'];
			$detail['repay_manage_impose_money'] = $v['repay_manage_impose_money'];//平台收取 借款者 的管理费逾期罚息
			$detail['impose_money'] = $v['impose_money'];//投资者收取 借款者 的逾期罚息			
			$detail['repay_status'] = intval($v['status']) - 1;//还款状态
			$detail['true_repay_time'] = TIME_UTC;//还款时间
			
			//投资人会员编号
			if ($v['t_user_id']){
				//债权转让后,还款时，转给：承接者, 在债权转让后需要更新 fanwe_deal_load_repay.t_user_id 数据值
				$detail['user_id'] = $v['t_user_id'];
			}else{
				$detail['user_id'] = $v['user_id'];
			}
			$detail_user_info = get_user_info("*","id = ".$detail['user_id']);
			
			if ($detail_user_info['user_type'] == 0){
				$detail['userType'] = 'MEMBER';
			}else{
				$detail['userType'] = 'MERCHANT';
			}
			
			$detail['targetUserType'] = $detail_user_info["user_type"];
			$detail['targetPlatformUserNo'] = $detail_user_info["ips_acct_no"];
			
			$detail['bizType'] = 'REPAYMENT';
			
			//平台收取：投资者 的投资金额管理费 + 利息管理费
			$detail['fee'] = $v['manage_money']+$v['manage_interest_money'] + $v['repay_manage_money'] + $v['repay_manage_impose_money'] + $v['mortgage_fee'];
			
			//投资者获取的 $v['month_repay_money'] + $v['impose_money'] - $v['manage_money'] - $v['manage_interest_money']
			$detail['amount'] = $v['month_repay_money'] + $v['impose_money'] - $v['manage_money'] - $v['manage_interest_money'];
				
			$detail['amount'] = round($detail['amount'], 2);
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_cp_transaction_detail",$detail,'INSERT');
			
			$details[] = $detail;

		}
		
		$data_update = array();
		$data_update['requestNo'] = $requestNo;
		$data_update['fee'] = $fee; //手续费(涉及到满标、还款接口)
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_cp_transaction",$data_update,'UPDATE',' requestNo='.$requestNo);
		
		//$details = "<details></details>";
		
		$actions = "<details>";		
		foreach($details as $k=>$v){
			$actions .= "<detail><targetUserType>".$v['userType']."</targetUserType><targetPlatformUserNo>".intval($v['user_id'])."</targetPlatformUserNo><amount>".$v['amount']."</amount><bizType>".$v["bizType"]."</bizType></detail>"  
					  ."<detail><targetUserType>MERCHANT</targetUserType><targetPlatformUserNo>$platformNo</targetPlatformUserNo><amount>".$v['fee']."</amount><bizType>COMMISSION</bizType></detail>";
		}
		$actions .= "</details>";
			
		$extend = '<extend>'
				.'<property name="tenderOrderNo" value="'.$deal["id"].'" />'
				.'</extend>';	
		
		$strxml = RepaymentNewTradeXml($cp_t,$actions.$extend,$pWebUrl,$pS2SUrl);
		
		$pSign= cfca($strxml);
		
		$html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>
	<form style="display:none;" name="form1" id="form1" method="post" action="'.$post_url.'/bha/toCpTransaction" target="_self">
		<input type="hidden" name="sign" value="'.$pSign.'" />
		<textarea name="req" cols="100" rows="5" style="display:none">'.$strxml.'</textarea>
		<input type="submit" value="提交"></input>
	</form>
	</body></html>
	<script language="javascript">document.form1.submit();</script>';
		
		$cp_t = array();
		$cp_t['details'] = $actions;//项目描述信息
		$cp_t['extend'] = $deal['extend'];//项目的借款人平台用户编
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_cp_transaction",$cp_t," requestNo = ".$requestNo);
		
		$yeepay_log = array();
		$yeepay_log['strxml'] =$strxml;
		$yeepay_log['html'] = $html;
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log,'UPDATE','id='.$requestNo);

		return $html;

	}
	
	//还款回调
	function RepaymentNewTradeCallBack($str3Req,$platformNo,$post_url){
		
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Yeepay&class_act=RepaymentRepayCallBack&from=".$_REQUEST['from'];//s2s方式返回		

		$requestNo = $str3Req["requestNo"];

		$where = " requestNo = '".$requestNo."'";
		$sql = "update ".DB_PREFIX."yeepay_cp_transaction set is_callback = 1 where is_callback = 0 and ".$where;
		$GLOBALS['db']->query($sql);
		if ($str3Req["code"] == "1" && $GLOBALS['db']->affected_rows()){
			//操作成功
			
			$result = array();
			$result['code'] = $str3Req['code'];
			$result['description'] = strim($str3Req['code'])==""?$str3Req['message']:$str3Req['description'];
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_cp_transaction",$result,'UPDATE',$where);
			
			if ($GLOBALS['db']->affected_rows())
			{	
				$ipsdata = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."yeepay_cp_transaction where ".$where);
				$deal_id = intval($ipsdata['tenderOrderNo']);
				$deal_repay_id = intval($ipsdata['deal_repay_id']);
			
				//确认转账
				$data = array();
				$data['requestNo'] = $requestNo;//请求流水号
				$data['platformNo'] = $platformNo;// 商户编号
				$data['mode'] = "CONFIRM";
				
				/* 请求参数 */  
				$req = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"
				."<request platformNo=\"".$data['platformNo']."\">"
				."<requestNo>".$data['requestNo']."</requestNo>"
				."<mode>".$data['mode']."</mode>"
				."<notifyUrl><![CDATA[" .$pS2SUrl ."]]></notifyUrl>"
				."</request>";
	
				/* 签名数据 */
				$pSign= cfca($req);
				/* 调用账户查询服务 */
				$service = "COMPLETE_TRANSACTION";
				
				$yeepay_log = array();
				$yeepay_log['code'] = 'bhaController';
				$yeepay_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
				$yeepay_log['strxml'] = $req;
				$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log);
				
				$ch = curl_init($post_url."/bhaexter/bhaController");
				curl_setopt_array($ch, array(
				CURLOPT_POST => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_SSL_VERIFYPEER=>0,
				CURLOPT_SSL_VERIFYHOST=>0,
				CURLOPT_POSTFIELDS => 'service=' . $service . '&req=' . rawurlencode($req) . "&sign=" . rawurlencode($pSign)
				));
				$resultStr = curl_exec($ch);
				
				if (empty($resultStr)){
					$result = array();
					$result['pErrCode'] = 9999;
					$result['pErrMsg'] = '返回出错';
					$result['pIpsAcctNo'] = '';
					$result['pBalance'] = 0;
					$result['pLock'] = 0;
					$result['pNeedstl'] = 0;
				}else{
						require_once(APP_ROOT_PATH.'system/collocation/yeepay/xml.php');
						$str3ParaInfo = @XML_unserialize($resultStr);
						$str3Req = $str3ParaInfo['response'];
						
						$result = array();
						$result['pErrCode'] = $str3Req["code"];
						$result['pErrMsg'] = $str3Req["description"];
						
						$yeepay_log = array();
						$yeepay_log['code'] = 'response';
						$yeepay_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
						$yeepay_log['strxml'] = $resultStr;
						$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log);

						if($str3Req["code"] == '1')
						{
							$platformNo = $str3Req["platformNo"];
							$where = " requestNo = '".$requestNo."'";
							$sql = "update ".DB_PREFIX."yeepay_cp_transaction set is_complete_transaction = 1 where is_callback = 1 and ".$where;
							$GLOBALS['db']->query($sql);
							if($GLOBALS['db']->affected_rows())
							{
								$sql = "select * from ".DB_PREFIX."yeepay_cp_transaction_detail where deal_load_repay_id > 0 and pid = ".$ipsdata['id'];
								
								$list = $GLOBALS['db']->getAll($sql);
								
								require_once APP_ROOT_PATH."app/Lib/deal.php";
								require_once APP_ROOT_PATH."app/Lib/common.php";
								
								foreach($list as $k=>$v){
									$load_repay = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_load_repay where id = ".$v['deal_load_repay_id']);
		
									$detail = array();
									$detail['repay_manage_impose_money'] = $v["repay_manage_impose_money"];//平台收取 借款者 的管理费逾期罚息
									$detail['impose_money'] = $v["impose_money"];//投资者收取 借款者 的逾期罚息
									$detail['status'] = $v["repay_status"];//还款状态
									$detail['true_repay_time'] = $v["true_repay_time"];//还款时间
									$detail['true_repay_date'] = to_date($v["true_repay_time"]);
									
									$detail['has_repay'] = 1;//0未收到还款，1已收到还款
									$detail['true_manage_money'] = $load_repay['manage_money'];
									$detail['true_manage_interest_money'] = $load_repay["manage_interest_money"];
									$detail['true_repay_manage_money'] = $load_repay["repay_manage_money"];
									$detail['true_mortgage_fee'] = $load_repay["mortgage_fee"];
									$detail['true_repay_money'] =$load_repay["repay_money"];
									$detail['true_self_money'] = $load_repay['self_money'];
									$detail['true_interest_money'] =  $load_repay['interest_money'];
														
									$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_repay",$detail,'UPDATE'," has_repay = 0 and id = ".intval($v['deal_load_repay_id']));
									
									//普通会员邀请返利
									get_referrals($v['deal_load_repay_id']);
										
									//短信通知回款
									if($load_repay['t_user_id'] > 0)
										$loan_user_info = get_user("*",intval($load_repay['t_user_id']));
									else
										$loan_user_info  = get_user("*",intval($load_repay['user_id']));
										
										
									$deal = get_deal(intval($load_repay['deal_id']));
										
									$load_repay_rs = $GLOBALS['db']->getRow("SELECT (sum(true_interest_money) + sum(impose_money)) as shouyi,sum(impose_money) as total_impose_money FROM ".DB_PREFIX."deal_load_repay WHERE id=".$load_repay['id']);
									$all_shouyi_money= number_format($load_repay_rs['shouyi'],2);
									$all_impose_money = number_format($load_repay_rs['total_impose_money'],2);
										
									$unext_loan = $GLOBALS['db']->getRow("select *,repay_time as repay_day,repay_money as month_repay_money from ".DB_PREFIX."deal_load_repay where deal_id = ".$load_repay['deal_id']." AND l_key > ".$load_repay['l_key']);
										
									send_repay_reback_sms_mail($deal,$loan_user_info,$unext_loan,$detail,$all_shouyi_money,$all_impose_money);
								}	
								require_once APP_ROOT_PATH."app/Lib/deal_func.php";
								//更新用户回款计划
								$deal_id = $GLOBALS['db']->getOne("select tenderOrderNo from ".DB_PREFIX."yeepay_cp_transaction where id = ".$ipsdata['id']);
							
								syn_deal_repay_status($deal_id,$deal_repay_id);
							}
					 	}
				};
			}
			return 1;
		}
	}
	function RepaymentRepayCallBack($str3Req)
	{
		if($str3Req["code"] == '1')
		{
			$requestNo = $str3Req["requestNo"];
			$platformNo = $str3Req["platformNo"];
			$where = " requestNo = '".$requestNo."'";
			$sql = "update ".DB_PREFIX."yeepay_cp_transaction set is_complete_transaction = 1 where is_callback = 1 and ".$where;
			$GLOBALS['db']->query($sql);
			if($GLOBALS['db']->affected_rows()){
				$yee_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."yeepay_cp_transaction where requestNo = ".$requestNo);
				
				$sql = "select * from ".DB_PREFIX."yeepay_cp_transaction_detail where deal_load_repay_id > 0 and pid = ".$yee_data['id'];
				$list = $GLOBALS['db']->getAll($sql);
				
				require_once APP_ROOT_PATH."app/Lib/deal.php";
				require_once APP_ROOT_PATH."app/Lib/common.php";
				
				foreach($list as $k=>$v){
					$load_repay = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_load_repay where id = ".$v['deal_load_repay_id']);
									
					$detail = array();
					$detail['repay_manage_impose_money'] = $v["repay_manage_impose_money"];//平台收取 借款者 的管理费逾期罚息
					$detail['impose_money'] = $v["impose_money"];//投资者收取 借款者 的逾期罚息
					$detail['status'] = $v["repay_status"];//还款状态
					$detail['true_repay_time'] = $v["true_repay_time"];//还款时间
					$detail['true_repay_date'] = to_date($v["true_repay_time"]);
					
					
					$detail['has_repay'] = 1;//0未收到还款，1已收到还款
					$detail['true_manage_money'] = $load_repay['manage_money'];
	                $detail['true_mortgage_fee'] = $load_repay['mortgage_fee'];
					$detail['true_manage_interest_money'] = $load_repay["manage_interest_money"];
					$detail['true_repay_manage_money'] = $load_repay["repay_manage_money"];
					$detail['true_repay_money'] =$load_repay["repay_money"];
					$detail['true_self_money'] = $load_repay['self_money'];
					$detail['true_interest_money'] =  $load_repay['interest_money'];
										
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_repay",$detail,'UPDATE'," has_repay = 0 and id = ".intval($v['deal_load_repay_id']));
					
					//普通会员邀请返利
					get_referrals($v['deal_load_repay_id']);	
					
					//短信通知回款
					if($load_repay['t_user_id'] > 0)
						$loan_user_info = get_user_info("*","id=".intval($load_repay['t_user_id']));
					else
						$loan_user_info  = get_user_info("*","id=".intval($load_repay['user_id']));
					
					
					$deal = get_deal(intval($load_repay['deal_id']));
					
					$load_repay_rs = $GLOBALS['db']->getRow("SELECT (sum(true_interest_money) + sum(impose_money)) as shouyi,sum(impose_money) as total_impose_money FROM ".DB_PREFIX."deal_load_repay WHERE id=".$load_repay['id']);
					$all_shouyi_money= number_format($load_repay_rs['shouyi'],2);
					$all_impose_money = number_format($load_repay_rs['total_impose_money'],2);
					
					$unext_loan = $GLOBALS['db']->getRow("select *,repay_time as repay_day,repay_money as month_repay_money from ".DB_PREFIX."deal_load_repay where deal_id = ".$load_repay['deal_id']." AND l_key > ".$load_repay['l_key']);
					
					send_repay_reback_sms_mail($deal,$loan_user_info,$unext_loan,$detail,$all_shouyi_money,$all_impose_money);
														
			 	}
				//更新用户回款计划
				syn_deal_repay_status($yee_data["tenderOrderNo"],$yee_data["deal_repay_id"]);
				$result["deal_id"] = $yee_data["tenderOrderNo"];
			}
			return $result;
		}
		return 1;
	}
	
?>