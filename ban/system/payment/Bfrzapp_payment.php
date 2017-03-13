<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'宝付认证支付',
	'baofoo_account'	=>	'商户号',
	'baofoo_terminal'		=>	'终端号',	
	'baofoo_key'		=>	'私钥密码',
);

$config = array(
	'baofoo_account'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户号: 
	'baofoo_terminal'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //终端号	
	'baofoo_key'	=>	array(
		'INPUT_TYPE'	=>	'0',
		
	), //密钥
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Bfrzapp';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


   	/* 支付方式：1：在线支付；0：线下支付; 2:APP+WAP支付 3:APP支付 */
    $module['online_pay'] = '3';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = 'http://www.baofoo.com';
    return $module;
}

require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Bfrzapp_payment implements payment {	
	public function get_payment_code($payment_notice_id) {

        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order = $GLOBALS['db']->getRow("select order_sn,bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		
        $user_info =  get_user_info("*","id=".$GLOBALS['user_info']['id']);
		$idcard = strim($user_info['idno']);
		$realname = strim($user_info['real_name']);
		
		$bfrz_bankid = intval($GLOBALS['request']['old_bank_id']);
		if($bfrz_bankid > 0){
			$bfrz_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."bfrz_bank where id = ".$bfrz_bankid);
			
			$bfrz_bankcode = $bfrz_info['bankcode'];
			$bfrz_bankcard = $bfrz_info['bankcard'];
			$bfrz_mobile = $bfrz_info['mobile'];
		}
		else{
			$bfrz_bankcode = strim($GLOBALS['request']['bankcode']);
			$bfrz_bankcard = strim($GLOBALS['request']['bankcard']);
			$bfrz_mobile = strim($GLOBALS['request']['mobile']);
		}
		
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
       
        $_Return_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/bfrzapp_notify.php';
        //$_Merchant_url = str_replace("/mapi", "", $_Merchant_url);
        $_Return_url = str_replace("/mapi", "", $_Return_url);
        
        //商户号
        $_MerchantID = $payment_info['config']['baofoo_account'];
        //
        $baofoo_key = $payment_info['config']['baofoo_key'];
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
        $parameter = array(
	        "txn_sub_type"=>"02",
			"biz_type"=>"0000",
			"terminal_id"=>$_TerminalID,
			"member_id"=>$_MerchantID,
			"trans_id"=>$_TransID,
			"pay_code"=>$bfrz_bankcode,
			"acc_no"=>$bfrz_bankcard,
			"id_card_type"=>"01",
			"id_card"=>$idcard,
			"id_holder"=>$realname,
			"mobile"=>$bfrz_mobile,
			"valid_date"=>"",
			"valid_no"=>"",
			"trade_date"=>$_TradeDate,
			"txn_amt"=>$_OrderMoney*100,
			"commodity_name"=>$_TransID,
			"commodity_amount"=>"1",
			"user_name"=>"",
			"page_url"=>$_Return_url,
			"return_url"=>$_Return_url,
			"additional_info"=>$payment_notice['notice_sn'],
			"req_reserved"=>""
		);
        
      
        $data_content = json_encode($parameter);
        $data_content = str_replace("\\\"",'"',$data_content);
		
		//返回结果展示
		$data_type = "json";
		
		
		$path_pfx = APP_ROOT_PATH."/system/payment/bfrzapp/cer/bfkey.pfx";
		$path_cer = APP_ROOT_PATH."/system/payment/bfrzapp/cer/baofoo_pub.cer";
		require APP_ROOT_PATH."/system/payment/bfrzapp/BaofooSdk.php";
		
       	$baofooSdk = new BaofooSdk($_MerchantID, $_TerminalID,$data_type,$path_pfx,$path_cer,$baofoo_key);
       	
       
       	$encode =mb_detect_encoding($data_content, "UTF-8,GB2312,GBK");
		if (trim($encode) == "GBK" or trim($encode) == "GB2312")
		{ 
			$string = iconv($encode,"UTF-8",$data_content); 
		}
		else
		{
			$string = $data_content;
		}
		
		$data_content = $baofooSdk->encryptedByPrivateKey($string);
		
		$result_parameter['version'] = '4.0.0.0';
		$result_parameter['input_charset'] = '1';//1 代表 UTF-8; 2 代表 GBK; 3 代表 GB2312；
		$result_parameter['language'] = 1;
		$result_parameter['terminal_id'] = $_TerminalID;
		$result_parameter['member_id'] = $_MerchantID;
		$result_parameter['txn_type'] = '03311';
		$result_parameter['txn_sub_type'] = '02';
		$result_parameter['data_type'] = 'json';
		$result_parameter['back_url'] = $_Return_url;
		$result_parameter['data_content'] = $data_content;
		
		require_once(APP_ROOT_PATH.'system/payment/bfrzapp/httpClient.php');
        
        $bf_url = 'https://gw.baofoo.com/apipay/sdk';
		if ($payment_info['config']['baofoo_account'] == '100000178'){
			$bf_url = 'https://tgw.baofoo.com/apipay/sdk';
		}
        
		$result = sendHttpRequest ($result_parameter, $bf_url);
		$result_arr =  json_decode($result,1);
		
		if($result_arr['retCode']=="0000" && $bfrz_bankid==0){
			$bfrz_bankdata['user_id'] = $GLOBALS['user_info']['id'];
			$bfrz_bankdata['bankcode'] = $bfrz_bankcode;
			$bfrz_bankdata['bankcard'] = $bfrz_bankcard;
			$bfrz_bankdata['mobile'] = $bfrz_mobile;
			$bfrz_bankdata['create_time'] = TIME_UTC;
			$MODE = "INSERT";
			if($id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."bfrz_bank  WHERE user_id= ".$GLOBALS['user_info']['id']." AND bankcode='".$bfrz_bankdata['bankcode']."'")){
			    $MODE = "UPDATE";
			    $extWhere = "id=".$id;
			}
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."bfrz_bank",$bfrz_bankdata,$MODE,$extWhere);
		}
		
        $pay = array();
        $pay['subject'] = $_TransID;
        $pay['body'] = '会员充值';
        $pay['total_fee'] = $_OrderMoney;
        $pay['total_fee_format'] = format_price($_OrderMoney);
        $pay['out_trade_no'] = $payment_notice['notice_sn'];
        
        $pay['notify_url'] = $_Return_url;
        $pay['parameter'] = $parameter;
        
        $pay['is_wap'] = 0;//
        $pay['pay_code'] = 'bfrzapp';
        
        if (empty($result)){
        	$pay['config'] = null;
        }else{

            $result_arr['is_debug'] = $payment_info['config']['baofoo_account'] == '100000178' ?  1 : 0;
        	$pay['config'] = $result_arr;
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
        
        $path_pfx = APP_ROOT_PATH."/system/payment/bfrzapp/cer/bfkey.pfx";
		$path_cer = APP_ROOT_PATH."/system/payment/bfrzapp/cer/baofoo_pub.cer";
		require APP_ROOT_PATH."/system/payment/bfrzapp/BaofooSdk.php";
		
		$endata_content = $_REQUEST["data_content"];
		//@file_put_contents(APP_ROOT_PATH."/public/bfrz.txt", print_r($_POST),FILE_APPEND);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where class_name='Bfrzapp'");
		$payment_info['config'] = unserialize($payment_info['config']);
       
        $_Return_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/bfrzapp_notify.php';
        $_Merchant_url = str_replace("/mapi", "", $_Merchant_url);
        $_Return_url = str_replace("/mapi", "", $_Return_url);
        
        //商户号
        $_MerchantID = $payment_info['config']['baofoo_account'];
        $baofoo_key = $payment_info['config']['baofoo_key'];
        //终端号
        $_TerminalID = $payment_info['config']['baofoo_terminal'];
        
		
		$baofooSdk = new BaofooSdk($_MerchantID,$_TerminalID,'json',$path_pfx,$path_cer,$baofoo_key);
		$endata_content = $baofooSdk->decryptByPublicKey($endata_content);

        if (!$endata_content) {
        	echo "Fail";
        } else{
        	
        	$request = json_decode($endata_content,1);

        	if($request['resp_code']!="0000"){
        		echo "Fail";die();
        	}
							

	         /* 取返回参数 */
	        $MemberID=$request['member_id'];//商户号
			$TerminalID =$request['terminal_id'];//商户终端号
			$payment_notice_sn = $AdditionalInfo=$request['additional_info'];//订单附加消息
			
			$gopayOutOrderId=$request['trans_no'];
			
			
	        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
		
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
				echo "Fail";
			}
        }
    }

    public function get_display_code() {
        $result = array();
		$result['user_info'] = get_user_info("*","id=".$GLOBALS['user_info']['id']);
		$result['banklist'] = array(
			0 => array("key"=>"ICBC","name"=>"中国工商银行"),
			1 => array("key"=>"ABC","name"=>"中国农业银行"),
			2 => array("key"=>"CCB","name"=>"中国建设银行"),
			3 => array("key"=>"BOC","name"=>"中国银行"),
			4 => array("key"=>"BCOM","name"=>"中国交通银行"),
			5 => array("key"=>"CIB","name"=>"兴业银行"),
			6 => array("key"=>"CITIC","name"=>"中信银行"),
			7 => array("key"=>"CEB","name"=>"中国光大银行"),
			8 => array("key"=>"PAB","name"=>"平安银行"),
			9 => array("key"=>"PSBC","name"=>"中国邮政储蓄银行"),
			10 => array("key"=>"CEB","name"=>"上海银行"),
			11 => array("key"=>"SPDB","name"=>"浦东发展银行"),
		);
		
		$result['userbank'] = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."bfrz_bank WHERE user_id=".intval($result['user_info']['id']));
					
		return $result;
    }

    
    public function orderquery($payment_notice_id){
    	
    }
}
?>
