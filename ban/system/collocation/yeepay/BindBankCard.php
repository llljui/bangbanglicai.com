<?php
	/**
	 * 
	 * @param unknown_type $pMerBillNo
	 * @return string
	 */
	function BindBankCardXml($data,$pWebUrl,$pS2SUrl){	

		$strxml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"
				."<request platformNo='".$data['platformNo']."'>"
				."<requestNo>" .$data['requestNo'] ."</requestNo>"
				."<platformUserNo>" .$data['platformUserNo'] ."</platformUserNo>"
				."<callbackUrl><![CDATA[" .$pWebUrl ."]]></callbackUrl>"
				."<notifyUrl><![CDATA[" .$pS2SUrl ."]]></notifyUrl>"
				."</request>";	
				
		
		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$strxml=str_replace('\\','',$strxml);//去除转义反斜杠\		
		return $strxml;		
	}
	

	///绑卡
	///user_id 用户名
	///
	function BindBankCard($user_id,$platformNo,$post_url){
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Yeepay&class_act=BindBankCard&from=".$_REQUEST['from'];//web方式返回
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Yeepay&class_act=BindBankCard&from=".$_REQUEST['from'];//s2s方式返回		
	
		$user = array();
		$user = get_user_info("*","id = ".$user_id);

		$yeepay_log = array();
		$yeepay_log['code'] = 'toBindBankCard';
		$yeepay_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log);
		$requestNo = $GLOBALS['db']->insert_id();

		$data = array();
		$data['requestNo'] = $requestNo;//请求流水号
		$data['platformUserNo'] = $user_id;//
		$data['platformNo'] = $platformNo;// 商户编号

		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_bind_bank_card",$data,'INSERT');
		$id = $GLOBALS['db']->insert_id();
	
		$strxml = BindBankCardXml($data,$pWebUrl,$pS2SUrl);

		$pSign= cfca($strxml);
				
		$html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>
		<form style="display:none;" name="form1" id="form1" method="post" action="'.$post_url.'/bha/toBindBankCard" target="_self">		
			<input type="hidden" name="sign" value="'.$pSign.'" />		
			<textarea name="req" cols="100" rows="5" style="display:none">'.$strxml.'</textarea>
			<input type="submit" value="提交"></input>
		</form>
		</body></html>
		<script language="javascript">document.form1.submit();</script>';

		$yeepay_log = array();
		$yeepay_log['strxml'] =$strxml;
		$yeepay_log['html'] = $html;
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log,'UPDATE','id='.$requestNo);
		
		return $html;
	
	}
	
	function BindBankCardCallBack($str3Req){
		$requestNo = $str3Req["requestNo"];
		$where = " requestNo = '".$requestNo."'";
		$sql = "update ".DB_PREFIX."yeepay_bind_bank_card set is_callback = 1 where is_callback = 0 and ".$where;
		$GLOBALS['db']->query($sql);
		if ($GLOBALS['db']->affected_rows()){	
			$result = array();
			$reuslt['code'] = $str3Req['code'];
			$reuslt['description'] = strim($str3Req['description'])==""?$str3Req['message']:$str3Req['description'];		
			$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_bind_bank_card",$result,'UPDATE',$where);
		}
		return 1;
	}	
	
?>