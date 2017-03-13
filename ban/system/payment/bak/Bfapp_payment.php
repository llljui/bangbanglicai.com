<?php
// +----------------------------------------------------------------------
// |  邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'宝付SDK支付',
	'baofoo_account'	=>	'商户号',
	'baofoo_key'		=>	'密钥',
	'baofoo_terminal'		=>	'终端号',	
);

$config = array(
	'baofoo_account'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户号: 
	'baofoo_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //密钥
	'baofoo_terminal'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //终端号	
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Bfapp';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


   	/* 支付方式：1：在线支付；0：线下支付; 2:手机支付 */
    $module['online_pay'] = '3';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = 'http://www.baofoo.com';
    return $module;
}

require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Bfapp_payment implements payment {	
	public function get_payment_code($payment_notice_id) {
		
        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order = $GLOBALS['db']->getRow("select order_sn,bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		
					
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
       
       // $_Merchant_url =  SITE_DOMAIN.APP_ROOT.'/baofoo_callback.php?act=response';
       // $_Return_url = SITE_DOMAIN.APP_ROOT.'/baofoo_callback.php?act=notify';

        $_Merchant_url = '';
        $_Return_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/bfapp_notify.php';
        $_Merchant_url = str_replace("/mapi", "", $_Merchant_url);
        $_Return_url = str_replace("/mapi", "", $_Return_url);
        
        //商户号
        $_MerchantID = $payment_info['config']['baofoo_account'];
        //
        $_Md5Key = $payment_info['config']['baofoo_key'];
        //终端号
        $_TerminalID = $payment_info['config']['baofoo_terminal'];
        
    
        $_PayID = '';//4010001
        
        /* 交易日期 */
        $_TradeDate = to_date($payment_notice['create_time'], 'YmdHis');
		
        //商户流水号 商户唯一订单号，8-50位
        $_TransID = $payment_notice['notice_sn'].$_MerchantID;
                
		//订单金额  单位：分
        $_OrderMoney = round($payment_notice['money'],2);
        
        //商品名称 需URL编码
        $_ProductName = '';
        
        //通知方式
        $_NoticeType = 0;
       
		$_AdditionalInfo = $payment_notice_id;
		$_Md5_OrderMoney = $_OrderMoney*100;
		$MARK = "|";
		$mark_str = $_MerchantID.$MARK.$_PayID.$MARK.$_TradeDate.$MARK.$_TransID.$MARK.$_Md5_OrderMoney.$MARK.$_Merchant_url.$MARK.$_Return_url.$MARK.$_NoticeType.$MARK.$_Md5Key;
      	$_Signature=md5($mark_str);

      	
      	
      	
      	/*交易参数*/
        $parameter = array(
            'MemberID' => $_MerchantID,
        	'TerminalID' => $_TerminalID,            
        	'InterfaceVersion' => '4.0',//接口版本号
			'PayID' => $_PayID,//支付方式
			'TradeDate' => $_TradeDate,//交易时间
        	'TransID' => $_TransID,//流水号
			'OrderMoney' => $_Md5_OrderMoney,//订单金额
			'ProductName' => $_TransID,//产品名称
			'Amount' => 1,//数量			
			//'Username' => '',//支付用户名
			'AdditionalInfo' => $_AdditionalInfo,//订单附加消息
			//'PageUrl' => $_Merchant_url,//商户通知地址 
			'ReturnUrl' => $_Return_url,//用户通知地址
			'NoticeType' => $_NoticeType,//通知方式
        	'KeyType'=> 1,       //加密类型 1：md5
			'Signature'=>$_Signature			
        );
        
        
        require_once(APP_ROOT_PATH.'system/payment/Baofoo/httpClient.php');
        
        $bf_url = 'https://tgw.baofoo.com/paysdk/index';
		if ($payment_info['config']['baofoo_account'] == '100000178'){
			$bf_url = 'http://tgw.baofoo.com/paysdk/index';
		}
        
		$result = sendHttpRequest ($parameter, $bf_url);
		
		//返回结果展示
		
		//preg_match("/\[(.*)\]/i", $result,$matches);
		
		//$result_arr =  $this->coverStringToArray2 ( $result);
		
        $pay = array();
        $pay['subject'] = $_TransID;
        $pay['body'] = '会员充值';
        $pay['total_fee'] = $_OrderMoney;
        $pay['total_fee_format'] = format_price($_OrderMoney);
        $pay['out_trade_no'] = $payment_notice['notice_sn'];
        
        $pay['notify_url'] = $_Return_url;
        $pay['parameter'] = $parameter;
        
        $pay['is_wap'] = 0;//
        $pay['pay_code'] = 'bfapp';
        
        if (empty($result)){
        	$pay['config'] = null;
        }else{
        	$pay['config'] = json_decode($result,1);
        }
        
        //print_r($pay);exit;
        
        return $pay;
    }
    
    function coverStringToArray2($str) {
    	$result = array ();
    
    	if (! empty ( $str )) {
    		$temp = preg_split ( '/,/', $str );
    		if (! empty ( $temp )) {
    			foreach ( $temp as $key => $val ) {
    				$arr = preg_split ( '/=/', $val, 2 );
    				if (! empty ( $arr )) {
    					$k = trim($arr ['0']);
    					$v = trim($arr ['1']);
    					$result [$k] = $v;
    				}
    			}
    		}
    	}
    	return $result;
    }

     public function response($request) {
		
    }
    
     public function notify($request) {
		$return_res = array(
            'info' => '',
            'status' => false,
        );
		
		//file_put_contents(APP_ROOT_PATH."/system/payment/Baofoo/call_back_url_1_".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($_REQUEST,true));
         /* 取返回参数 */
        $MemberID=$request['MemberID'];//商户号
		$TerminalID =$request['TerminalID'];//商户终端号
		$TransID =$request['TransID'];//商户流水号
		$Result=$request['Result'];//支付结果
		$ResultDesc=$request['ResultDesc'];//支付结果描述
		$FactMoney=$request['FactMoney'];//实际成功金额
		$AdditionalInfo=$request['AdditionalInfo'];//订单附加消息
		$SuccTime=$request['SuccTime'];//支付完成时间
		$Md5Sign=$request['Md5Sign'];//md5签名
		

        /*获取支付信息*/
        $payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Bfapp'");  
    	$payment['config'] = unserialize($payment['config']);
    	
		$_Md5Key= $payment['config']['baofoo_key'];
		$payment_notice_sn = intval($AdditionalInfo);
		$gopayOutOrderId =  $TransID;
		
		$MARK = "~|~";
        /*比对连接加密字符串*/
		$WaitSign=md5('MemberID='.$MemberID.$MARK.'TerminalID='.$TerminalID.$MARK.'TransID='.$TransID.$MARK.'Result='.$Result.$MARK.'ResultDesc='.$ResultDesc.$MARK.'FactMoney='.$FactMoney.$MARK.'AdditionalInfo='.$AdditionalInfo.$MARK.'SuccTime='.$SuccTime.$MARK.'Md5Sign='.$_Md5Key);

        if ($Md5Sign != $WaitSign) {
        	echo "Md5CheckFail";
        } else {
	        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_notice_sn."'");
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$gopayOutOrderId);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);				
				if($rs)
				{
					//开始更新相应的outer_notice_sn					
					//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$gopayOutOrderId."' where id = ".$payment_notice['id']);
					echo "OK";
				}
				else 
				{
					echo "OK";
				}
			}
			else
			{
				echo "OrderFail";
			}
        }
    }

    public function get_display_code() {
        return '';
    }

    
    public function orderquery($payment_notice_id){
    	
    }
}
?>
