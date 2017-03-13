<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'支付宝手机支付(WAP版本)',
	'alipay_partner'	=>	'合作者身份ID',
	'alipay_account'	=>	'支付宝帐号',
	'alipay_key'	=>	'安全校验码（Key）',
);
$config = array(
	'alipay_partner'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //合作者身份ID
	'alipay_account'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //支付宝帐号: 
	//支付宝公钥
	'alipay_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	)
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Walipay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付; 2:手机支付 */
    $module['online_pay'] = '2';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
   	$module['reg_url'] = 'http://act.life.alipay.com/systembiz/fangwei/';
    return $module;
}

// 支付宝手机支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Walipay_payment implements payment {

	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
			
		
		//$sql = "select name from ".DB_PREFIX."deal_order_item where order_id =". intval($payment_notice['order_id']);
		//$title_name = $GLOBALS['db']->getOne($sql);

		
		$subject = $payment_notice['order_sn'];
		
		//$data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=Walipay';
		$notify_url =  SITE_DOMAIN.APP_ROOT.'/callback/pay/walipay.php?id='.$payment_notice_id;
		$notify_url = str_replace(array("/mapi","/wap"), "", $notify_url);
		$pay = array();
		$pay['subject'] = $subject;
		$pay['body'] = '会员充值';
		$pay['total_fee'] = $money;
		$pay['total_fee_format'] = format_price($money);
		$pay['out_trade_no'] = $payment_notice['notice_sn'];
		$pay['notify_url'] = $notify_url;
		
		$pay['partner'] = '';//$payment_info['config']['alipay_partner'];//合作商户ID
		$pay['seller'] = '';//$payment_info['config']['alipay_account'];//账户ID
				
		$pay['key'] = '';//$payment_info['config']['alipay_key'];//支付宝(RSA)公钥		
		$pay['is_wap'] = 1;//
		$pay['pay_code'] = 'walipay';//,支付宝;mtenpay,财付通;mcod,货到付款
				
		return $pay;
	}
	
	public function get_payment(){
		require_once(APP_ROOT_PATH.'system/payment/walipay/lib/alipay_submit.class.php');
		
		
		$payment_notice_id = intval($_REQUEST['id']);
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where class_name='Walipay'");
		$payment['config'] = unserialize($payment_info['config']);
		
		require_once(APP_ROOT_PATH.'system/payment/walipay/alipay.config.php');
		
		if(!$payment_info){
			showIpsInfo("不支持的支付方式",wap_url("member","uc_incharge#index"));
			exit();
		}
		
		if (empty($payment_notice)){
			showIpsInfo("支付单号不存在",wap_url("member","uc_incharge#index"));;
			exit();
		}
		
		/**************************调用授权接口alipay.wap.trade.create.direct获取授权码token**************************/
			
		//返回格式
		$format = "xml";
		//必填，不需要修改
		
		//返回格式
		$v = "2.0";
		//必填，不需要修改
		
		//请求号
		$req_id = date('Ymdhis');
		//必填，须保证每次请求都是唯一
		
		//**req_data详细信息**
		
		//服务器异步通知页面路径
		
        $notify_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/walipay_notify.php';
        $notify_url = str_replace(array("/mapi","/wap","/callback/pay/callback/pay/"), array("","","/callback/pay/"), $notify_url);
        
		//需http://格式的完整路径，不允许加?id=123这类自定义参数
		
		//页面跳转同步通知页面路径
		$call_back_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/walipay_response.php';
        $call_back_url = str_replace(array("/mapi","/wap","/callback/pay/callback/pay/"), array("","","/callback/pay/"), $call_back_url);
		
		//需http://格式的完整路径，不允许加?id=123这类自定义参数
		
		//卖家支付宝帐户
		$seller_email = $alipay_config['account'];
		//必填
		
		
		
		//商户网站订单系统中唯一订单号，必填
		
		//订单名称
		$subject = $payment_notice['notice_sn'];
		
		//必填
		
		//付款金额
		$pay_price = $payment_notice['money'];
		
		//商户订单号
		$out_trade_no = $payment_notice['notice_sn'];
		
		$total_fee = $money;
		
		//必填
		//请求业务参数详细
		$req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee></direct_trade_create_req>';
		//必填
		
		/************************************************************/
		
		//构造要请求的参数数组，无需改动
		$para_token = array(
				"service" => "alipay.wap.trade.create.direct",
				"partner" => trim($alipay_config['partner']),
				"sec_id" => trim($alipay_config['sign_type']),
				"format"	=> $format,
				"v"	=> $v,
				"req_id"	=> $req_id,
				"req_data"	=> $req_data,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		
		
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($para_token);
		
		
		//URLDECODE返回的信息
		$html_text = urldecode($html_text);
		
		//解析远程模拟提交后返回的信息
		$para_html_text = $alipaySubmit->parseResponse($html_text);
		
		
		//获取request_token
		$request_token = $para_html_text['request_token'];
		
		
		/**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
		
		//业务详细
		$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
		//必填
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "alipay.wap.auth.authAndExecute",
				"partner" => trim($alipay_config['partner']),
				"sec_id" => trim($alipay_config['sign_type']),
				"format"	=> $format,
				"v"	=> $v,
				"req_id"	=> $req_id,
				"req_data"	=> $req_data,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		
		//print_r($parameter); exit;
		
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '页面跳转中，如果未跳转点此');
		return $html_text;
	}
	
	public function response($request)
	{
		require_once(APP_ROOT_PATH.'system/payment/walipay/lib/alipay_notify.class.php');	
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where class_name='Walipay'");
		$payment['config'] = unserialize($payment_info['config']);
		
		require_once(APP_ROOT_PATH.'system/payment/walipay/alipay.config.php');	
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
		
			//商户订单号
			$out_trade_no = $_GET['out_trade_no'];
		
			//支付宝交易号
			$trade_no = $_GET['trade_no'];
		
			//交易状态
			$result = $_GET['result'];
		
		
			//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
			
		
		   $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$out_trade_no."'");
		   //file_put_contents(APP_ROOT_PATH."/alipaylog/payment_notice_sn_3.txt",$payment_notice_sn);
		 
		   require_once APP_ROOT_PATH."system/libs/cart.php";
		   require_once APP_ROOT_PATH."app/Lib/common.php";
		   $rs = payment_paid($payment_notice['id'],$trade_no);					
		   $is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
		   if ($is_paid == 1){	
		   		showIpsInfo("支付成功",wap_url("member","uc_incharge_log#index"));
		   }else{
		   		//file_put_contents(APP_ROOT_PATH."/alipaylog/2.txt","");
				showIpsInfo("支付失败",wap_url("member","uc_incharge#index"));
		   }
		
			
		}
		else {
		    //验证失败
		    //如要调试，请看alipay_notify.php页面的verifyReturn函数
		    showIpsInfo("支付失败",wap_url("member","uc_incharge#index"));
		}
	}
	
	public function notify($request){
		require_once(APP_ROOT_PATH.'system/payment/walipay/lib/alipay_notify.class.php');	
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where class_name='Walipay'");
		$payment['config'] = unserialize($payment_info['config']);
		
		require_once(APP_ROOT_PATH.'system/payment/walipay/alipay.config.php');
		
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		//echo "verify_result:".$verify_result;
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代
			
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
			//解密（如果是RSA签名需要解密，如果是MD5签名则下面一行清注释掉）
			//$notify_data = decrypt($_POST['notify_data']);
			$notify_data = $_POST['notify_data'];
			
		    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//解析notify_data
			//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
			$doc = new DOMDocument();
			$doc->loadXML($notify_data);
			
			if( ! empty($doc->getElementsByTagName("notify")->item(0)->nodeValue) ) {
				//商户订单号
				$out_trade_no = $doc->getElementsByTagName("out_trade_no")->item(0)->nodeValue;
				//支付宝交易号
				$trade_no = $doc->getElementsByTagName("trade_no")->item(0)->nodeValue;
				//交易状态
				$trade_status = $doc->getElementsByTagName("trade_status")->item(0)->nodeValue;
				
				if($trade_status == 'TRADE_FINISHED') {
					//判断该笔订单是否在商户网站中已经做过处理
						//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
						//如果有做过处理，不执行商户的业务程序
							
					//注意：
					//该种交易状态只在两种情况下出现
					//1、开通了普通即时到账，买家付款成功后。
					//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
			
					//调试用，写文本函数记录程序运行情况是否正常
					//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
					
				   $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$out_trade_no."'");
				   
				   $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
				   require_once APP_ROOT_PATH."system/libs/cart.php";
				   $rs = payment_paid($payment_notice['id'],$trade_no);	
				   $is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
				   if ($is_paid == 1){	
				   		echo "success";		//请不要修改或删除
				   }
					
				}
				else if ($trade_status == 'TRADE_SUCCESS') {
					echo "success";		//请不要修改或删除
				}
			}
		
			
		}
		else {
		    //验证失败
		    echo "fail";
		}
	}
	
	public function get_display_code(){
		return "";
	}
}
?>