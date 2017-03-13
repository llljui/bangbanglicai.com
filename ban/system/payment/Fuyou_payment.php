<?php

// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: jobin.lin(jobin.lin@gmail.com)
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | 国付报 直连银行支付
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'富友支付',
	'merchant_id'	=>	'合作者身份ID',
	//'virCardNoIn'	=>	'富友账户',
	'VerficationCode'		=>	'商户码',
	'GO_TO_PAY'	=>	'前往富友在线支付',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',
	'fuyou_gateway'	=>	'支持的银行',
	'fuyou_gateway_0'	=>	'纯网关支付',

	'fuyou_gateway_CCB'	=>	'中国建设银行',
	'fuyou_gateway_CMB'	=>	'招商银行',
	'fuyou_gateway_ICBC'	=>	'中国工商银行',
	'fuyou_gateway_BOC'	=>	'中国银行',
	'fuyou_gateway_ABC'	=>	'中国农业银行',
	'fuyou_gateway_BOCOM'	=>	'中国交通银行',
	'fuyou_gateway_CMBC'	=>	'中国民生银行',
	'fuyou_gateway_HXBC'	=>	'华夏银行',
	'fuyou_gateway_CIB'	=>	'兴业银行',
	'fuyou_gateway_SPDB'	=>	'上海浦东发展银行',
	'fuyou_gateway_GDB'	=>	'广东发展银行',
	'fuyou_gateway_CITIC'	=>	'中信银行',
	'fuyou_gateway_CEB'	=>	'光大银行',
	'fuyou_gateway_PSBC'	=>	'中国邮政储蓄银行',
	//'fuyou_gateway_SDB'	=>	'深圳发展银行',
	'fuyou_gateway_BOBJ'	=>	'北京银行',
	'fuyou_gateway_PAB'	=>	'平安银行',
	//'fuyou_gateway_BOS'	=>	'上海银行',
	//'fuyou_gateway_NBCB'	=>	'宁波银行',
	//'fuyou_gateway_NJCB'	=>	'南京银行',
	//'fuyou_gateway_TCCB'	=>	'天津银行',
	'fuyou_gateway_LYB'	=>	'洛阳银行',
	'fuyou_gateway_XHCB'	=>	'鑫汇村镇银行',
	'fuyou_gateway_ZXB'	=>	'浙商银行',
);

$config = array(
    'merchant_id' => '', //商户ID
    //'virCardNoIn' => '', //富友账户
    'VerficationCode'=>'',  //商户识别码
    'fuyou_gateway' => array(
    	'INPUT_TYPE'	=>	'3',
    	'VALUES'	=>	array(
    		'0', //纯网关
	        'CCB', //中国建设银行
	        'CMB', //招商银行
	        'ICBC', //中国工商银行
	        'BOC', //中国银行
	        'ABC', //中国农业银行
	        'BOCOM', //交通银行
	        'CMBC', //中国民生银行
	        'HXBC', //华夏银行
	        'CIB', //兴业银行
	        'SPDB', //上海浦东发展银行
	        'GDB', //广东发展银行
	        'CITIC', //中信银行
	        'CEB', //光大银行
	        'PSBC', //中国邮政储蓄银行
	        //'SDB', //深圳发展银行
	        'BOBJ', //北京银行
	        'PAB', //平安银行
	        //'BOS', //上海银行
	        //'NBCB', //宁波银行
	        //'NJCB', //南京银行
	        //'TCCB', //天津银行
            'LYB',
            'XHCB',
            'ZXB',
        ),
    ),
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true){
    
    /* 会员数据整合插件的代码必须和文件名保持一致 */
    $module['class_name']    = 'Fuyou';

    /* 被整合的第三方程序的名称 */
    $module['name'] = $payment_lang['name'];
    
    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';
	
	 /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
	
    $module['reg_url'] = 'https://www.gopay.com.cn';
    
    return $module;
}

// 富友模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Fuyou_payment implements payment {

    public $_page_notify_url;
    public $_back_notify_url;
    public $_order_pay_type = 'B2C';
    public $_ver = '1.0.1';
    public $_mchnt_cd;
    public $_mchnt_key;
    public $_rem = ""; //备注
    public $_order_valid_time = ""; //超时时间
    public $_payment_info;
    public $_iss_ins_cd = '';
    public $_goods_name = ""; //商品名称
    public $_goods_display_url = ""; //商品展示网址
    public $_order_id;
    public $_order_amt;

    public $_bank_code = array(
        'CCB'	=>	'0801050000', //'中国建设银行',
        'CMB'	=>	'0803080000', //'招商银行',
        'ICBC'	=>	'0801020000', //'中国工商银行',
        'BOC'	=>	'0801040000', //'中国银行',
        'ABC'	=>	'0801030000', //'中国农业银行',
        'BOCOM'	=>	'0803010000', //'中国交通银行',
        'CMBC'	=>	'0803050000', //'中国民生银行',
        'HXBC'	=>	'0803040000', //'华夏银行',
        'CIB'	=>	'0803090000', //'兴业银行',
        'SPDB'	=>	'0803100000', //'上海浦东发展银行',
        'GDB'	=>	'0803060000', //'广东发展银行',
        'CITIC'	=>	'0803020000', //'中信银行',
        'CEB'	=>	'0803030000', //'光大银行',
        'PSBC'	=>	'0801000000', //'中国邮政储蓄银行',
        'BOBJ'	=>	'0804031000', //'北京银行',
        'PAB'	=>	'0804105840', //'平安银行',
        'LYB'	=>	'0804184930', //'洛阳银行',
        'XHCB'	=>	'0803202220', //'鑫汇村镇银行',
        'ZXB'	=>	'0803160000', //'浙商银行',
    );

    //初始化
    function __construct()
    {
        $this->_page_notify_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/fuyou_callback.php?act=notify';
        $this->_back_notify_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/fuyou_callback.php?act=response'; //后台通知URL

		$this->_payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where class_name ='Fuyou'");
		$this->_payment_info['config'] = unserialize($this->_payment_info['config']);
        //$this->_mchnt_cd = $this->_payment_info['config']['merchant_id']; //商户代码
        //$this->_mchnt_key = $this->_payment_info['config']['VerficationCode']; //商户key
        $this->_mchnt_cd = "0001000F0040992"; //商户代码
        $this->_mchnt_key = "vau6p7ldawpezyaugc0kopdrrwm4gkpu"; //商户代码
    }

    public function get_payment_code($payment_notice_id) {
        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order = $GLOBALS['db']->getRow("select order_sn,bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);

		$this->_order_id = $payment_notice['notice_sn'];
		$this->_order_amt = intval($payment_notice['money'] * 100);

        $bank_id = $payment_notice['bank_id'];
        if ($bank_id == '0')
        {
            return '请银行';
        }

        $this->_iss_ins_cd = $this->_bank_code[$bank_id]; //银行

        //拼接数据
        $_data = "";
        $_data .= $this->_mchnt_cd."|";
        $_data .= $this->_order_id."|";
        $_data .= $this->_order_amt."|";
        $_data .= $this->_order_pay_type."|";
        $_data .= $this->_page_notify_url."|";
        $_data .= $this->_back_notify_url."|";
        $_data .= $this->_order_valid_time."|";
        $_data .= $this->_iss_ins_cd."|";
        $_data .= $this->_goods_name."|";
        $_data .= $this->_goods_display_url."|";
        $_data .= $this->_rem."|";
        $_data .= $this->_ver."|";
        $_data .= $this->_mchnt_key;

        $_md5 = MD5($_data); //签名数据

        /*交易参数*/
        $parameter = array(
			"md5" => $_md5,
            "mchnt_cd" => $this->_mchnt_cd,
            "order_id" => $this->_order_id,
            "order_amt" => $this->_order_amt,
            "order_pay_type" => $this->_order_pay_type,
            "page_notify_url" => $this->_page_notify_url,
            "back_notify_url" => $this->_back_notify_url,
            "order_valid_time" => $this->_order_valid_time,
            "iss_ins_cd" => $this->_iss_ins_cd,
            "goods_name" => $this->_goods_name,
            "goods_display_url" => $this->_goods_display_url,
            "rem" => $this->_rem, 
            "ver" => $this->_ver,
        );
        
        //$url = 'https://pay.fuiou.com/smpGate.do';
        $url = 'http://www-1.fuiou.com:8888/wg1_run/smpGate.do';
        $def_url = '<form style="text-align:center;" action="'.$url.'" target="_blank" style="margin:0px;padding:0px" method="post" >';

        foreach ($parameter AS $key => $val) {
            $def_url .= "<input type='hidden' name='$key' value='$val' />";
        }
        $def_url .= "<input type='submit' class='paybutton' value='前往富友在线支付' />";
        $def_url .= "</form>";
        $def_url.="<br /><div style='text-align:center' class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($this->_order_amt/100)."</div>";
        return $def_url;
    }

    public function response($request) {
		$return_res = array(
            'info' => '',
            'status' => false,
        );

        /* 取返回参数 */
        //判md5值否确，是否已支付
        $mchnt_cd = $request["mchnt_cd"];
		$order_id = $request["order_id"];
		$order_date = $request["order_date"];
		$order_amt = $request["order_amt"];
		$order_st = $request["order_st"];
		$order_pay_code = $request["order_pay_code"];
		$order_pay_error = $request["order_pay_error"];
		$resv1 = $request["resv1"];
		$fy_ssn = $request["fy_ssn"];
		$md5 = $request["md5"];

        /*比对连接加密字符串*/
        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$order_id."'");

        $this->_iss_ins_cd = $this->_bank_code[$payment_notice['bank_id']]; //银行

        $_data = "";
        $_data .= $mchnt_cd."|";
        $_data .= $order_id."|";
        $_data .= $order_date."|";
        $_data .= $order_amt."|";
        $_data .= $order_st."|";
        $_data .= $order_pay_code ."|";
        $_data .= $order_pay_error ."|";
        $_data .= $resv1 ."|";
        $_data .= $fy_ssn ."|";
        $_data .= $this->_mchnt_key;
        $_md5 = MD5($_data); //签名数据

        //var_dump($_data);
        //echo $_md5 . '</br>';
        //echo $md5;die;

        if ($md5 != $_md5 || $order_pay_code != "0000") {
        	echo "RespCode=9999|JumpURL=".SITE_DOMAIN.url("index","payment#pay",array("id"=>$order_id)); 
        } else {
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$fy_ssn);	
			
			$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
			if ($is_paid == 1){
				echo "RespCode=0000|JumpURL=".SITE_DOMAIN.url("index","payment#incharge_done",array("id"=>$payment_notice['id'])); //支付成功
			}else{
				echo "RespCode=9999|JumpURL=".SITE_DOMAIN.url("index","payment#pay",array("id"=>$payment_notice['id'])); 
			}									
        }
    }
    
     public function notify($request) {
        //var_dump($request);die;
		$return_res = array(
            'info' => '',
            'status' => false,
        );
		
        /* 取返回参数 */
        //判md5值否确，是否已支付
        $mchnt_cd = $request["mchnt_cd"];
		$order_id = $request["order_id"];
		$order_date = $request["order_date"];
		$order_amt = $request["order_amt"];
		$order_st = $request["order_st"];
		$order_pay_code = $request["order_pay_code"];
		$order_pay_error = $request["order_pay_error"];
		$resv1 = $request["resv1"];
		$fy_ssn = $request["fy_ssn"];
		$md5 = $request["md5"];

        /*比对连接加密字符串*/
        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$order_id."'");

        $this->_iss_ins_cd = $this->_bank_code[$payment_notice['bank_id']]; //银行

        $_data = "";
        $_data .= $mchnt_cd."|";
        $_data .= $order_id."|";
        $_data .= $order_date."|";
        $_data .= $order_amt."|";
        $_data .= $order_st."|";
        $_data .= $order_pay_code ."|";
        $_data .= $order_pay_error ."|";
        $_data .= $resv1 ."|";
        $_data .= $fy_ssn ."|";
        $_data .= $this->_mchnt_key;
        $_md5 = MD5($_data); //签名数据

        //var_dump($_data);
        //echo $_md5 . '</br>';
        //echo $md5;die;

        if ($md5 != $_md5 || $order_pay_code != "0000") {
        	showErr($GLOBALS['payment_lang']["VALID_ERROR"]);
        } else {
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$fy_ssn);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);				
				if($rs)
				{
					//开始更新相应的outer_notice_sn					
					//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$gopayOutOrderId."' where id = ".$payment_notice['id']);
					if($order_info['type']==0)
						app_redirect(url("index","payment#done",array("id"=>$payment_notice['order_id']))); //支付成功
					else
						app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['order_id']))); //支付成功
				}
				else 
				{
					if($order_info['pay_status'] == 2)
					{				
						if($order_info['type']==0)
							app_redirect(url("index","payment#done",array("id"=>$payment_notice['order_id']))); //支付成功
						else
							app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['order_id']))); //支付成功
					}
					else
						app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
				}
			}
			else
			{
				app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
			}
        }
    }

    public function get_display_code() {
        $payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Fuyou'");
		if($payment_item)
		{
			$payment_cfg = unserialize($payment_item['config']);

	        $html = "<style type='text/css'>.fuyou_types{float:left; display:block; background:url(".SITE_DOMAIN.APP_ROOT."/system/payment/Fuyou/banklist_hnapay.jpg); font-size:0px; width:150px; height:10px; text-align:left; padding:15px 0px;}";
	        $html .=".gfb_type_0{background-position:15px -908px; }"; //中国建设银行
	        $html .=".gfb_type_CCB{background-position:15px -72px; }"; //中国建设银行
	        $html .=".gfb_type_CMB{background-position:15px -196px; }"; //招商银行
	        $html .=".gfb_type_ICBC{background-position:15px 3px; }"; //中国工商银行
	        $html .=".gfb_type_BOC{background-position:15px -113px; }"; //中国银行
	        $html .=".gfb_type_ABC{background-position:15px -34px; }"; //中国农业银行
	        $html .=".gfb_type_BOCOM{background-position:15px -155px; }"; //交通银行
	        $html .=".gfb_type_CMBC{background-position:15px -230px; }"; //中国民生银行
	        $html .=".gfb_type_HXBC{background-position:15px -358px; }"; //华夏银行
	        $html .=".gfb_type_CIB{background-position:15px -270px; }"; //兴业银行
	        $html .=".gfb_type_SPDB{background-position:15px -312px; }"; //上海浦东发展银行
	        $html .=".gfb_type_GDB{background-position:15px -475px; }"; //广东发展银行
	        $html .=".gfb_type_CITIC{background-position:15px -396px; }"; //中信银行
	        $html .=".gfb_type_CEB{background-position:15px -435px; }"; //光大银行
	        $html .=".gfb_type_PSBC{background-position:15px -513px; }"; //中国邮政储蓄银行
	        $html .=".gfb_type_SDB{background-position:15px -558px; }"; //深圳发展银行
	        $html .=".gfb_type_BOBJ{background-position:15px -697px; }"; //北京银行
			$html .=".gfb_type_PAB{background-position:15px -827px; }"; //平安银行
			$html .=".gfb_type_BOS{background-position:15px -789px; }"; //上海银行
			$html .=".gfb_type_NBCB{background-position:15px -649px; }"; //宁波银行
			$html .=".gfb_type_NJCB{background-position:15px -743px; }"; //南京银行
			$html .=".gfb_type_TCCB{background-position:15px -869px; }"; //天津银行
	        $html .="</style>";
	        $html .="<script type='text/javascript'>function set_bank(bank_id)";
			$html .="{";
			$html .="$(\"input[name='bank_id']\").val(bank_id);";
			$html .="}</script>";
			$html.= "<h3 class='clearfix tl'><b>富友支付</b></h3><div class='blank1'></div><hr />";
	       foreach ($payment_cfg['fuyou_gateway'] AS $key=>$val)
	        {
	            $html  .= "<label class='fuyou_types gfb_type_".$key." ui-radiobox' rel='common_payment'><input type='radio' name='payment' value='".$payment_item['id']."' rel='".$key."' onclick='set_bank(\"".$key."\")' /></label>";
	        }
	        $html .= "<input type='hidden' name='bank_id' /><div class='blank'></div>";
			return $html;
		}
		else{
			return '';
		}
    }
    /**
     * 字符转义
     * @return string
     */
    function fStripslashes($string)
    {
            if(is_array($string))
            {
                    foreach($string as $key => $val)
                    {
                            unset($string[$key]);
                            $string[stripslashes($key)] = fStripslashes($val);
                    }
            }
            else
            {
                    $string = stripslashes($string);
            }

            return $string;
    }

}

?>
