<?php
	/**
	 * 
	 * @param unknown_type $pMerBillNo
	 * @return string
	 */
	function RegisterCreditorXml($data,$details,$extend,$pWebUrl,$pS2SUrl){		
		$strxml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"
				."<request platformNo=\"".$data['platformNo']."\">"
				."<requestNo>" .$data['requestNo'] ."</requestNo>"
				."<platformUserNo>" .$data['platformUserNo'] ."</platformUserNo>"
				."<userType>" .$data['userType'] ."</userType>"						
				."<bizType>" .$data['bizType'] ."</bizType>"
				.$details.$extend		  		
				."<callbackUrl><![CDATA[" .$pWebUrl ."]]></callbackUrl>"
				."<notifyUrl><![CDATA[" .$pS2SUrl ."]]></notifyUrl>"
				."</request>";	
				
		
		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$strxml=str_replace('\\','',$strxml);//去除转义反斜杠\		
		return $strxml;		
	}
	

	
	/**
	 * 投标
	 * @param int $user_id  用户ID
	 * @param int $deal_id  标的ID
	 * @param float $pAuthAmt 投资金额
	 * @param int $MerCode  商户ID
	 * @param string $cert_md5 
	 * @param string $post_url
	 * @return string
	 */
	function RegisterCreditor($user_id,$deal_id,$pAuthAmt,$platformNo,$post_url){
	
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Yeepay&class_act=RegisterCreditor&from=".$_REQUEST['from'];//web方式返回
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Yeepay&class_act=RegisterCreditor&from=".$_REQUEST['from'];//s2s方式返回		
	
		$user = get_user_info("*","id = ".$user_id);
		$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
		
		$yeepay_log = array();
		$yeepay_log['code'] = 'toCpTransaction';
		$yeepay_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log);
		$requestNo = $GLOBALS['db']->insert_id();
				
		$data = array();
		$data['ecv_id'] = 0;
		if(intval($_REQUEST['ecv_id']) > 0){
			//获得火爆的金额
			$ecv_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv where id = ".intval($_REQUEST['ecv_id'])." AND (begin_time =0 OR (begin_time > 0 AND begin_time < ".TIME_UTC.")) AND (end_time = 0 OR (end_time > 0  AND (end_time +24*3600 - 1) > ".TIME_UTC.")) AND (use_limit =0  OR (use_limit >0 AND use_limit - use_count > 0)) AND user_id=".$user_id);
			if($ecv_info){
				$data['ecv_id'] = $ecv_info['id'];
				$pAuthAmt = $pAuthAmt - $ecv_info['money'];
			}
			
		}
		
		$data['requestNo'] = $requestNo;//请求流水号
		$data['platformUserNo'] = $user_id;//
		$data['platformNo'] = $platformNo;// 商户编号
		$data['paymentAmount'] = $pAuthAmt;// 记录投标金额
		
		//用户类型 0普通用户 1 企业用户；现在只支持 普通用户
		if ($user['user_type'] == 0||$user['user_type'] == 1){
			$data['userType'] = 'MEMBER';//出款人用户类型
		}else{
			$data['userType'] = 'MERCHANT';//出款人用户类型MEMBER 个人会员 MERCHANT 商户 
		}
		
		//TENDER 投标 REPAYMENT 还款 CREDIT_ASSIGNMENT 债权转让 TRANSFER 转账 COMMISSION 分润，仅在资金转账明细中使用
		$data['bizType'] = 'TENDER';//根据业务的不同，需要传入不同的值，见【业务类型】。并参考下面的详细信息
		
		//投标 扩展字段
		$data['tenderOrderNo'] = $deal_id;//项目编号
		$data['tenderName'] = $deal['sub_name'];//项目名称 
		$data['tenderAmount'] = $deal['borrow_amount'];//标的金额
		$data['tenderDescription'] = $deal['name'];//项目描述信息
		$data['borrowerPlatformUserNo'] = $deal['user_id'];//项目的借款人平台用户编号		  
		
		
		$targetUserType = intval(get_user_info("user_type","id = ".intval($deal['user_id']),"ONE"));
		
		if ($targetUserType == 0||$targetUserType == 1){
			$targetUserType = 'MEMBER';//出款人用户类型
		}else{
			$targetUserType = 'MERCHANT';//出款人用户类型MEMBER 个人会员  商户
		}
		
		//成交服务费
		$fee = round($pAuthAmt * $deal['services_fee'] / 100,2);
		$data["fee"] = $fee;
		//实际可到账金额
		$targetAmount = $pAuthAmt - $fee;
		
		$details = "<details><detail><targetUserType>".$targetUserType."</targetUserType><targetPlatformUserNo>".intval($deal['user_id'])."</targetPlatformUserNo><amount>".$targetAmount."</amount><bizType>TENDER</bizType></detail>"  
				  ."<detail><targetUserType>MERCHANT</targetUserType><targetPlatformUserNo>$platformNo</targetPlatformUserNo><amount>$fee</amount><bizType>COMMISSION</bizType></detail></details>";
				
		$extend = '<extend>'
				.'<property name="tenderOrderNo" value="'.$data['tenderOrderNo'].'" />'
				.'<property name="tenderName" value="'.$data['tenderName'].'" />'
				.'<property name="tenderAmount" value="'.$data['tenderAmount'].'" />'
				.'<property name="tenderDescription" value="'.$data['tenderDescription'].'" />'
				.'<property name="borrowerPlatformUserNo" value="'.$deal["user_id"].'" />'
				.'</extend>';		
		
		$data['details'] = $details;//资金明细记录
		$data['extend'] = $extend;//业务扩展属性，根据业务类型的不同，需要传入不同的参数
		$data['create_time'] = TIME_UTC;
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_cp_transaction",$data,'INSERT');
		
		$id = $GLOBALS['db']->insert_id();
		
		
		$strxml = RegisterCreditorXml($data,$details,$extend,$pWebUrl,$pS2SUrl);			
		
		$pSign= cfca($strxml);
		
		$html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>
		<form style="display:none;" name="form1" id="form1" method="post" action="'.$post_url.'/bha/toCpTransaction" target="_self">
			<input type="hidden" name="sign" value="'.$pSign.'" />
			<textarea name="req" cols="100" rows="5" style="display:none">'.$strxml.'</textarea>
			<input type="submit" value="提交"></input>
		</form>
		</body></html>
		<script language="javascript">document.form1.submit();</script>';
		//echo $html; exit;
		
		$yeepay_log = array();
		$yeepay_log['strxml'] =$strxml;
		$yeepay_log['html'] = $html;
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log,'UPDATE','id='.$requestNo);
		
		return $html;
	}
	
	//投资回调
	function RegisterCreditorCallBack($str3Req){
		
		$requestNo = $str3Req["requestNo"];
		$where = " requestNo = '".$requestNo."'";
		$sql = "update ".DB_PREFIX."yeepay_cp_transaction set is_callback = 1 where is_callback = 0 and ".$where;
		$GLOBALS['db']->query($sql);
		if($GLOBALS['db']->affected_rows()){
			//操作成功
			if ($str3Req["code"] == "1"){		
				//print_r($str3XmlParaInfo);
				
				//unset($str3Req['bizType']);
				//unset($str3Req['status']);
				
				$reuslt = array();
				$result['code'] = $str3Req['code'];
				$result['description'] = strim($str3Req['description'])==""?$str3Req['message']:$str3Req['description'];
				
				$data = array();
							
				$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_cp_transaction",$result,'UPDATE',$where);
								
				if ($GLOBALS['db']->affected_rows()){
					
					$ipsdata = $GLOBALS['db']->getRow("select *,tenderOrderNo as deal_id from ".DB_PREFIX."yeepay_cp_transaction where ".$where);
					$user_id = intval($ipsdata['platformUserNo']);
					$user = get_user_info("*","id = ".$user_id);
					
					$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$ipsdata['tenderOrderNo']);
					
					$data['pMerBillNo'] = $requestNo;
					$data['pContractNo'] = $requestNo;
					$data['pP2PBillNo'] = $requestNo;
					$data['user_id'] = $user_id;
					$data['user_name'] = $user['user_name'];
					$data['deal_id'] = $ipsdata['tenderOrderNo'];
					$data['money'] = $ipsdata['paymentAmount'];
					$data['ecv_id'] = 0;
					if($ipsdata['ecv_id'] > 0){
						$GLOBALS['db']->query("UPDATE ".DB_PREFIX."ecv SET use_count = use_count + 1 WHERE (begin_time =0 OR (begin_time > 0 AND begin_time < ".TIME_UTC.")) AND (end_time = 0 OR (end_time > 0  AND (end_time +24*3600 - 1) > ".TIME_UTC.")) AND (use_limit =0  OR (use_limit >0 AND use_limit - use_count > 0)) AND id=".$ipsdata['ecv_id']." AND user_id=".$user_id);
						if($GLOBALS['db']->affected_rows()){
							$ecv_money = $GLOBALS['db']->getOne("SELECT `money` FROM ".DB_PREFIX."ecv WHERE id=".$ipsdata['ecv_id']);
							$data['money'] +=$ecv_money;
							$data['ecv_id'] = $ipsdata['ecv_id'];
						}
					}
					
					$insertdata = return_deal_load_data($data,$user,$deal);
					
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load",$insertdata,"INSERT");
					$load_id = $GLOBALS['db']->insert_id();
					if($load_id > 0){				
						require APP_ROOT_PATH.'app/Lib/deal_func.php';
						dobid2_ok($ipsdata['deal_id'], $user_id);	
						return $ipsdata;			
					}
				}			
			}else{
				$ipsdata = $GLOBALS['db']->getRow("select *,tenderOrderNo as deal_id from ".DB_PREFIX."yeepay_cp_transaction where ".$where);
				return $ipsdata;
			}
		}
	}	
	
?>