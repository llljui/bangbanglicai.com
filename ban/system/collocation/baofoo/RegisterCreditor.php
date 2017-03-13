<?php
	/**
	 * 
	 * @param unknown_type $pMerBillNo
	 * @return string
	 */
	function RegisterCreditorXml($data,$actions){	
		
		$strxml = "<?xml version='1.0' encoding='UTF-8'?>"
				."<custody_req>"
				."<action_type>" .$data['action_type'] ."</action_type>"
				."<merchant_id>" .$data['merchant_id'] ."</merchant_id>"
				."<order_id>" .$data['order_id'] ."</order_id>"
				."<cus_id>" .$data['cus_id'] ."</cus_id>"
				."<cus_name><![CDATA[" .$data['cus_name'] ."]]></cus_name>"		
				."<brw_id>" .$data['brw_id'] ."</brw_id>"
				."<req_time>" .$data['req_time'] ."</req_time>"
				.$actions
				."<fee>" .$data['fee'] ."</fee>"
				."</custody_req>";
		
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
	
	
	function RegisterCreditor($cfg,$user_id,$deal_id,$pAuthAmt,$post_url){
		$merchant_id = $cfg['merchant_id'];
		$terminal_id = $cfg['terminal_id'];
		$key=$cfg['key'];
		$iv=$cfg['iv'];

		
		//$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Baofoo&class_act=RegisterCreditor&from=".$_REQUEST['from'];//web方式返回
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=deal&id=".$deal_id;//
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Baofoo&class_act=RegisterCreditor&from=".$_REQUEST['from'];//s2s方式返回		
	
		$user = get_user_info("*","id = ".$user_id);
		$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
		
		
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
		
		if(intval($_REQUEST['learn_id']) > 0){
			$today = to_date(TIME_UTC,"Y-m-d");
			$learn_info = $GLOBALS['db']->getRow("select lsl.* FROM ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id WHERE lsl.id =".intval($_REQUEST['learn_id'])." AND  lt.invest_type = 0 AND lsl.is_use = 0 AND lsl.begin_time <= '".$today."' AND '".$today."' <= lsl.end_time  AND lsl.is_recycle = 0 AND lt.is_effect = 1 AND lsl.user_id=".$user_id);
			if($learn_info){
				$data['learn_id'] = $learn_info['id'];
				$pAuthAmt = $pAuthAmt - $learn_info['money'];
			}
			
		}
		
		$data['merchant_id'] = $merchant_id;//商户号
		$data['terminal_id'] = $terminal_id;//终端号
		$data['action_type'] = 1;//请求类型，投标为1，满标为2，流标为3，还标为4
		$data['order_id'] = 0;
		
		$data['cus_id'] = $deal_id;
		$data['cus_name'] = $deal['sub_name'];//项目名称
		$data['brw_id'] = intval($deal['user_id']);//借款人	
		$data['req_time'] =  microtime_float();// get_gmtime();//请求时间 例如 1405668253874    （当前时间转换毫秒）
		
		$data['fee'] = 0; //手续费(涉及到满标、还款接口)
		
		$data['load_user_id'] = $user_id;// 记录：投标人
		$data['load_amount'] = $pAuthAmt;// 记录：投标金额
		$data['create_time'] = TIME_UTC;
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data,'INSERT');
		$id = $GLOBALS['db']->insert_id();

		
		$data_update = array();
		$data_update['order_id'] = $id;
		$data['order_id'] = $id;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data_update,'UPDATE','id='.$id);
		
		//user_id:投资人; amount:投资金额
		$actions = "<actions><action><user_id>".$user_id."</user_id><user_name></user_name><amount>".$pAuthAmt."</amount></action></actions>";
									
		//print_r($data);exit;
		$strxml = RegisterCreditorXml($data,$actions);			
		
		$pSign = md5($strxml."~|~".$key);
		//$aes=new MyAES();
		//$requestParams=$aes->encrypt($strxml,$key,$iv); //加密
		
		
				
		$html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>
		<form name="form1" id="form1" method="post" action="'.$post_url.'custody/businessPage.do" target="_self">		
			
				<input type="hidden" name="merchant_id" value="'.$merchant_id.'" /><br>
				<input type="hidden" name="terminal_id" value="'.$terminal_id.'" /><br>
				<input type="hidden" name="sign" value="'.$pSign.'" /><br>					
				<textarea name="requestParams" cols="100" rows="5" style="display:none">'.$strxml.'</textarea>	<br>
				<input type="hidden" name="page_url" value="'.$pWebUrl.'" /><br>
				<input type="hidden" name="service_url" value="'.$pS2SUrl.'" /><br>
				<input type="submit" value="提交"></input>
		</form>
		</body></html>
		<script language="javascript">document.form1.submit();</script>';
		//echo $html; exit;
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'business_1';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$strxml;
		$baofoo_log['html'] = $html;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		
		return $html;
	}
	
	//投资回调
	function RegisterCreditorCallBack($str3Req){
		//print_r($str3XmlParaInfo);
		$order_id = $str3Req["order_id"];
		$where = " order_id = '".$order_id."'";
		$sql = "update ".DB_PREFIX."baofoo_business set is_callback = 1 where is_callback = 0 and ".$where;
		$GLOBALS['db']->query($sql);
		if ($GLOBALS['db']->affected_rows()){		
			//操作成功
			$data = array();
						
			$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$str3Req,'UPDATE',$where);
							
			if ($str3Req['code'] == 'CSD000'){
				
				$ipsdata = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."baofoo_business where ".$where);
				$user_id = intval($ipsdata['load_user_id']);
				$user = get_user_info("*","id = ".$user_id);
				
				$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".(int)$ipsdata['cus_id']);
				
				$data['pMerBillNo'] = $order_id;
				$data['pContractNo'] = $order_id;
				$data['pP2PBillNo'] = $order_id;
				$data['user_id'] = $user_id;
				$data['user_name'] = $user['user_name'];
				$data['deal_id'] = $ipsdata['cus_id'];
				$data['money'] = $ipsdata['load_amount'];
				$data['ecv_id'] = 0;
				if($ipsdata['ecv_id'] > 0){
					$GLOBALS['db']->query("UPDATE ".DB_PREFIX."ecv SET use_count = use_count + 1 WHERE (begin_time =0 OR (begin_time > 0 AND begin_time < ".TIME_UTC.")) AND (end_time = 0 OR (end_time > 0  AND (end_time +24*3600 - 1) > ".TIME_UTC.")) AND (use_limit =0  OR (use_limit >0 AND use_limit - use_count > 0)) AND id=".$ipsdata['ecv_id']." AND user_id=".$user_id);
					if($GLOBALS['db']->affected_rows()){
						$ecv_money = $GLOBALS['db']->getOne("SELECT `money` FROM ".DB_PREFIX."ecv WHERE id=".$ipsdata['ecv_id']);
						$data['money'] +=$ecv_money;
						$data['ecv_id'] = $ipsdata['ecv_id'];
					}
				}
				
				$data['learn_id'] = 0;
				if($ipsdata['learn_id'] > 0){
					$today = to_date(TIME_UTC,"Y-m-d");
					$now_time = to_date(TIME_UTC,"Y-m-d H:i:s");
					$GLOBALS['db']->query("UPDATE ".DB_PREFIX."learn_send_list SET is_use = '1',use_time ='".$now_time."',use_date='".$today."' WHERE is_use = '0' AND id ='".$ipsdata['learn_id']."' AND  user_id = ".$user_id." ");
					if($GLOBALS['db']->affected_rows()){
						$learn_money = $GLOBALS['db']->getOne("SELECT `money` FROM ".DB_PREFIX."learn_send_list WHERE id=".$ipsdata['learn_id']);
						$data['money'] +=$learn_money;
						$data['learn_id'] = $ipsdata['learn_id'];
					}
				}
				
				$insertdata = return_deal_load_data($data,$user,$deal);
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load",$insertdata,"INSERT");
				$load_id = $GLOBALS['db']->insert_id();
				if($load_id > 0){				
					require APP_ROOT_PATH.'app/Lib/deal_func.php';
					dobid2_ok($ipsdata['cus_id'], $user_id);	
					return $ipsdata;			
				}
			}			
		}else{
			return 1;
		}
	}	
	
?>