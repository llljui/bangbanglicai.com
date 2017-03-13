<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
$payment_lang = array(
		'name'	=>	'易宝资金托管',
		'platform_no'	=>	'商户编号',
		'check_url'		=>	'验证地址',
		'is_debug'		=>	'测试帐户',
		'is_debug_0'		=>	'否',
		'is_debug_1'		=>	'是',	
);


$config = array(
		'platform_no'	=>	array(
				'INPUT_TYPE'	=>	'0'
		),
		'check_url'	=>	array(
				'INPUT_TYPE'	=>	'0'
		),	
		'is_debug'	=>	array(
				'INPUT_TYPE'	=>	'1',
				'VALUES'	=>	array(0,1),
		),	
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == TRUE)
{
	$module['class_name']    = 'Yeepay';

	/* 名称 */
	$module['name']    = $payment_lang['name'];

	/* 配送 */
	$module['config'] = $config;

	$module['lang'] = $payment_lang;

	/* 插件作者的官方网站 */
	$module['reg_url'] = 'http://www.fanwe.com';

	return $module;
}
//易宝签名验证
function cfca($req)
{
	/* 签名 */
	
	$sign_url =  $GLOBALS['cache']->get("Yeepay_sign_url")? $GLOBALS['cache']->get("Yeepay_sign_url"):"http://127.0.0.1:8088/";
	$url = $sign_url .'sign';
	$ch = curl_init($url);
	curl_setopt_array($ch, array(
		CURLOPT_POST => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_POSTFIELDS => 'req=' . rawurlencode($req)
	));
	$response = curl_exec($ch);
	return $response;
	 
}

function verify($req,$verify)
{
	##验证签名
	$sign_url =  $GLOBALS['cache']->get("Yeepay_sign_url")? $GLOBALS['cache']->get("Yeepay_sign_url"):"http://127.0.0.1:8088/";
	$url = $sign_url .'verify';
	$ch = curl_init($url);
	curl_setopt_array($ch, array(
		CURLOPT_POST => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_POSTFIELDS => 'req=' . rawurlencode($req) . "&sign=" . rawurlencode($verify)
	));
	$result = curl_exec($ch);
	return $result;
}

require_once(APP_ROOT_PATH.'system/libs/collocation.php');
class Yeepay_collocation implements collocation {

	/*$xml = "<request platformNo=\"10040011137\"><platformUserNo>张三</platformUserNo></request>";
	$ch = curl_init("http://127.0.0.1:8088/sign");
	curl_setopt_array($ch, array(
		CURLOPT_POST => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_POSTFIELDS => 'req=' . rawurlencode($xml)
	));
	$response = curl_exec($ch);
	print($response);*/
	
	private $platformNo = "";
	private $cert_md5= "";
	private $is_debug = 0;
	//正式环境
	private $post_url="https://member.yeepay.com/member";
	private $ws_url= "https://member.yeepay.com/member";
	
	//测试环境
	//private $post_url="http://119.161.147.110:8088/member";
	//private $ws_url= "http://119.161.147.110:8088/member";

	function __construct(){		
	
		$collocation_item = $GLOBALS['db']->getRow("select config from ".DB_PREFIX."collocation where class_name='Yeepay'");
		$collocation_cfg = unserialize($collocation_item['config']);
		$this->platformNo = $collocation_cfg['platform_no'];
		$this->is_debug = $collocation_cfg['is_debug'];
		$check_url = $collocation_cfg['check_url'];

		if ($collocation_cfg['is_debug'] == 1){
			$this->post_url="http://220.181.25.233:8081/member";
			$this->ws_url= "http://220.181.25.233:8081/member";
		}else{
			$this->post_url= "https://member.yeepay.com/member";
			$this->ws_url= "https://member.yeepay.com/member";
		}	
		
		$GLOBALS['cache']->set("Yeepay_sign_url",$check_url);
	
	} 
	
	
	/**
	 * 创建新帐户
	 * @param int $user_id
	 * @param int $user_type 0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return string
	 */
	function CreateNewAcct($user_id,$user_type){
		if($user_type == 0) //个人
		{
			require_once(APP_ROOT_PATH.'system/collocation/yeepay/CreateNewAcct.php');
			return CreateNewAcct($user_id,$this->platformNo,$this->post_url);
		}  
		else  // 企业用户
		{
			require_once(APP_ROOT_PATH.'system/collocation/yeepay/CreateNewEpAcct.php');
			return CreateNewEpAcct($user_id,$this->platformNo,$this->post_url);
		}		
	}
	
	/**
	 * 标的登记 及 流标
	 * @param int $deal_id
	 * @param int $pOperationType 标的操作类型，1：新增，2：结束,3:满标 “新增”代表新增标的，“结束”代表标的正常还清、丌 需要再还款戒者标的流标等情况。标的“结束”后，投资 人投标冻结金额、担保方保证金、借款人保证金均自劢解 冻
	 * @param int $status; 0:新增; 1:标的正常结束; 2:流标结束
	 * @param string $status_msg 主要是status_msg=2时记录的，流标原因
	 */
	function RegisterSubject($deal_id,$pOperationType,$status, $status_msg){
		
		if ($pOperationType == 1){
			$data = array();		
			$data['ips_bill_no'] = $deal_id;
			$data['mer_bill_no'] = $deal_id;
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$data,'UPDATE',"id=".$deal_id);
			
			showIpsInfo('同步成功',SITE_DOMAIN.APP_ROOT);
			
		}else if ($pOperationType == 2 && $status == 2){
			require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoBids.php');
			$result = DoBids($deal_id,$pOperationType, $status, $status_msg, $this->platformNo,$this->post_url);
			return showIpsInfo($result["info"],SITE_DOMAIN.APP_ROOT."/m.php?m=Deal&a=full");
		}else if ($pOperationType == 2 && $status == 1){
			//本地解冻:借款保证金,担保保证金0
			$sql = "update ".DB_PREFIX."deal set ips_over = 1 ,un_real_freezen_amt = real_freezen_amt,un_guarantor_real_freezen_amt = guarantor_real_freezen_amt where id = ".$deal_id;
			$GLOBALS['db']->query($sql);	
			//http://p2p.fanwe.net/m.php?m=Deal&a=index&
			$url = SITE_DOMAIN.APP_ROOT.'/m.php?m=Deal&a=index';
			showSuccess('操作成功',0,$url);
		}
	}	
	
	
	/**
	 * 投标
	 * @param int $user_id  用户ID
	 * @param int $deal_id  标的ID
	 * @param float $pAuthAmt 投资金额
	 * @return string
	 */
	function RegisterCreditor($user_id,$deal_id,$pAuthAmt){
		
		require_once(APP_ROOT_PATH.'system/collocation/yeepay/RegisterCreditor.php');
		
		return RegisterCreditor($user_id,$deal_id,$pAuthAmt,$this->platformNo, $this->post_url);
	}	
	/**
	 * 登记债权转让
	 * @param int $transfer_id  转让id
	 * @param int $t_user_id  受让用户ID
	 * @param int $MerCode  商户ID
	 * @param string $cert_md5 
	 * @param string $post_url
	 * @return string
	 */
	function RegisterCretansfer($transfer_id,$t_user_id){
		require_once(APP_ROOT_PATH.'system/collocation/yeepay/RegisterCretansfer.php');
		
		return RegisterCretansfer($transfer_id,$t_user_id, $this->platformNo,$this->post_url);
	}
	
		/**
	 * 账户余额查询(WS) 
	 * @param int $user_id
	 * @param int $user_type 0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $ws_url
	 * @return
	 * 			pMerCode 6 “平台”账号 否 由IPS颁发的商户号
				pErrCode 4 返回状态 否 0000成功； 9999失败；
				pErrMsg 100 返回信息 否 状态0000：成功 除此乊外：反馈实际原因
				pIpsAcctNo 30 IPS账户号 否 查询时提交
				pBalance 10 可用余额 否 带正负符号，带小数点，最多保留两位小数
				pLock 10 冻结余额 否 带正负符号，带小数点，最多保留两位小数
				pNeedstl 10 未结算余额 否 带正负符号，带小数点，最多保留两位小数
	 */
	function QueryForAccBalance($user_id,$user_type){
		require_once(APP_ROOT_PATH.'system/collocation/yeepay/QueryForAccBalance.php');
		//echo 'sss'; exit;
		return QueryForAccBalance($user_id,$this->platformNo,$this->post_url);			
	}
	
	
	/**
	 * 解冻保证金
	 * @param int $deal_id 标的号
	 * @param int $pUnfreezenType 解冻类型 否 1#解冻借款方；2#解冻担保方
	 * @param float $money 解冻金额;默认为0时，则解冻所有未解冻的金额
	 * @return string
	 */
	function GuaranteeUnfreeze($deal_id,$pUnfreezenType, $money){
		require_once(APP_ROOT_PATH.'system/collocation/ips/GuaranteeUnfreeze.php');
				
		return GuaranteeUnfreeze($deal_id,$pUnfreezenType, $money,$this->MerCode,$this->cert_md5,$this->ws_url);
	}	

	/**
	 * 充值
	 * @param int $user_id
	 * @param int $user_type 0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id
	 * @param float $pTrdAmt 充值金额
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return string
	 */
	function DoDpTrade($user_id,$user_type,$pTrdAmt,$pTrdBnkCode){	
		require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoDpTrade.php');
		
		return DoDpTrade($user_id,$this->platformNo,$pTrdAmt,$this->post_url);		
	}
	
	/**
	 * 绑定银行卡
	 * @param unknown_type $user_id
	 */
	function BindBankCard($user_id){
		require_once(APP_ROOT_PATH.'system/collocation/yeepay/BindBankCard.php');
		
		return BindBankCard($user_id,$this->platformNo,$this->post_url);
	}
		
	/**
	 * 用户提现
	 * @param int $user_id
	 * @param int $user_type 0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id
	 * @param float $pTrdAmt 提现金额
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return string
	 */
	function DoDwTrade($user_id,$user_type,$pTrdAmt){
		require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoDwTrade.php');
		
		return DoDwTrade($user_id,$this->platformNo,$pTrdAmt,$this->post_url);
	}
	
	/**
	 * 商户端获取银行列表查询(WS) 
	 * @param int $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return  
	 * 		  pMerCode 6 “平台”账号 否 由IPS颁发的商户号
	 * 		  pErrCode 4 返回状态 否 0000成功； 9999失败；
	 * 		  pErrMsg 100 返回信息 否 状态0000：成功 除此乊外：反馈实际原因 
	 * 		  pBankList 银行名称|银行卡别名|银行卡编号#银行名称|银行卡别名|银行卡编号
	 * 		  BankList[] = array('name'=>银行名称,'sub_name'=>银行卡别名,'id'=>银行卡编号);
	 */
	function GetBankList(){ 
		
		$result = array ();
		$result ['pErrCode'] = '0000';
		$result ['pErrMsg'] = '';
		
		$BankList = array();
		$BankList[] = array('name'=>'易宝资金托管','sub_name'=>'在线充值','id'=>'1');
		$result ['BankList'] = $BankList;
		
		return $result;
		//require_once(APP_ROOT_PATH.'system/collocation/baofoo/GetBankList.php');
		
		//return GetBankList($this->MerCode,$this->cert_md5,$this->post_url);
	}
	
	/**
	 * 登记担保方
	 * @param int $deal_id
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return string
	 */
	function RegisterGuarantor($deal_id){
		require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterGuarantor.php');
		
		return RegisterGuarantor($deal_id,$this->MerCode,$this->cert_md5,$this->post_url);		
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
	function RepaymentNewTrade($deal, $repaylist, $deal_repay_id){

		require_once(APP_ROOT_PATH.'system/collocation/yeepay/RepaymentNewTrade.php');

		return RepaymentNewTrade($deal,$repaylist,$deal_repay_id, $this->platformNo,$this->post_url);
		
	}
	
	/**
	 * 转帐
	 * @param int $pTransferType;//转账类型  否  转账类型  1：投资（报文提交关系，转出方：转入方=N：1），  2：代偿（报文提交关系，转出方：转入方=1：N），  3：代偿还款（报文提交关系，转出方：转入方=1：1），  4：债权转让（报文提交关系，转出方：转入方=1：1），  5：结算担保收益（报文提交关系，转出方：转入方=1： 1）
	 * @param int $deal_id  标的id
	 * @param string $ref_data 逗号分割的,代偿，代偿还款列表; 债权转让: id; 结算担保收益:金额，如果为0,则取fanwe_deal.guarantor_pro_fit_amt ;
	 * @return string
	 */
	function Transfer($pTransferType, $deal_id, $ref_data){
		
		if ($pTransferType == 1){
			//满标放款
			require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoLoans.php');
			$result = DoLoans($pTransferType,$deal_id,$ref_data,$this->platformNo,$this->post_url);	
			showIpsInfo($result["info"],SITE_DOMAIN.APP_ROOT."/m.php?m=Deal&a=full");
		}else{
		
			require_once(APP_ROOT_PATH.'system/collocation/yeepay/Transfer.php');					
			return Transfer($pTransferType,$deal_id,$ref_data,$this->MerCode,$this->cert_md5,$this->ws_url);
		}
		
	}
	
	//(显式回调)
	function response($request,$class_act){
		//print_r($request); exit;
		
		$yeepay_log = array();
		$yeepay_log['code'] = 'response';
		$yeepay_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$yeepay_log['strxml'] =$_POST["resp"];
		$yeepay_log['html'] = $_POST["sign"];
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log);
		
		$resp = $_POST["resp"];
		$sign = $_POST["sign"];
		
		//$localSign = $sign; //正式版本,需要加上验证;
		
		if(verify($resp,$sign) || $this->is_debug == 1){
			//echo "<br/>验签通过";exit;
			
			//echo $resp;
			require_once(APP_ROOT_PATH.'system/collocation/yeepay/xml.php');
						
			$str3ParaInfo = @XML_unserialize($resp);
			$str3Req = $str3ParaInfo['response'];
			
					
			//
			if ($class_act == 'CreateNewAcct'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/CreateNewAcct.php');				
				$user_type = CreateNewAcctCallBack($str3Req);
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}
				else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);
				}
				
			}else if ($class_act == 'CreateNewEpAcct'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/CreateNewEpAcct.php');				
				$user_type = CreateNewEpAcctCallBack($str3Req);
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}
				else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);
				}
			}else if ($class_act == 'DoDpTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoDpTrade.php');				
				DoDpTradeCallBack($str3Req);				

				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/index.php?ctl=uc_center');
				}
			}else if ($class_act == 'BindBankCard'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/BindBankCard.php');				
				BindBankCardCallBack($str3Req);				

				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);
				}
			}else if ($class_act == 'DoDwTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoDwTrade.php');				
				DoDwTradeCallBack($str3Req);				
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{					
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);	
				}		
			}else if ($class_act == 'DoBids'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoBids.php');
				DoBidsCallBack($str3Req);		
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{					
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);	
				}				
			}else if ($class_act == 'RegisterCreditor'){
				//投资,登记债权人
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RegisterCreditor.php');				
				$ipsdata = RegisterCreditorCallBack($str3Req);
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					if($ipsdata['deal_id'])
						showIpsInfo($str3Req["description"],url("index","deal",array("id"=>$ipsdata['deal_id'])));	
					else
						showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);	
				}
				
				
							
			}else if ($class_act == 'DoLoans'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoLoans.php');
				$ipsdata = DoLoansCallBack($str3Req);
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					if($ipsdata['deal_id'])
						showIpsInfo($ipsdata["info"],SITE_DOMAIN.APP_ROOT."/m.php?m=Deal&a=full");
						//showIpsInfo($str3Req["description"],url("index","uc_deal#quick_refund",array("id"=>$ipsdata['deal_id'])));
					else
						showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);
				}
				
					
			}else if ($class_act == 'RepaymentNewTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RepaymentNewTrade.php');
				$ipsdata = RepaymentNewTradeCallBack($str3Req,$this->platformNo,$this->post_url);
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					if($ipsdata['deal_id'])
						showIpsInfo($str3Req["description"],url("index","uc_deal#quick_refund",array("id"=>$ipsdata['deal_id'])));
					else
						showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);
				}
			}else if ($class_act == 'RepaymentRepayCallBack'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RepaymentNewTrade.php');
				$ipsdata = RepaymentRepayCallBack($str3Req);
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					if($ipsdata['deal_id'])
						showIpsInfo($str3Req["description"],url("index","uc_deal#quick_refund",array("id"=>$ipsdata['deal_id'])));
					else
						showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);
				}
			}else if ($class_act == 'RegisterCretansfer'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RegisterCretansfer.php');				
				RegisterCretansferCallBack($str3Req,$this->platformNo,$this->post_url);
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					if ($request['from'] == 'app'){
						showIpsInfo($str3Req["description"]);
					}else{
						showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);	
					}
				}
				
							
			}else if ($class_act == 'RegisterCretansferBack'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RegisterCretansfer.php');				
				RegisterCretansferBack($str3Req);
				
				
				if ($request['from'] == 'app'){
					showIpsInfo($str3Req["description"]);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					if ($request['from'] == 'app'){
						showIpsInfo($str3Req["description"]);
					}else{
						showIpsInfo($str3Req["description"],SITE_DOMAIN.APP_ROOT);	
					}
				}			
			}
			/*else if ($class_act == 'GuaranteeUnfreeze'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/GuaranteeUnfreeze.php');				
				GuaranteeUnfreezeCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);				
				showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);				
			}else if ($class_act == 'RegisterGuarantor'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterGuarantor.php');				
				RegisterGuarantorCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);				
				showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);				
			}*/
			else if ($class_act == 'Transfer'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/Transfer.php');
				$result = TransferCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else if ($request['from'] == 'wap'){
					if(intval($str3Req["pTransferType"])==4)
						showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
					else
						showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);					
				}else{
					if(intval($str3Req["pTransferType"])==4)
						showIpsInfo($pErrMsg,url("index","transfer#detail",array("id"=>$result['id'])));
					else
						showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);
				}
				
				
			}
			
		}else{
			echo "<br/>验签不通过:$localSign";exit;
		}	
	}
	
	//(后台回调)
	function notify($request,$class_act){
		
		$yeepay_log = array();
		$yeepay_log['code'] = 'notify';
		$yeepay_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$yeepay_log['strxml'] =$_POST["notify"];
		$yeepay_log['html'] = $_POST["sign"];
		$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log);
		
		$resp = $_POST["notify"];
		$sign = $_POST["sign"];
		
		//$localSign = $sign; //正式版本,需要加上验证;
		
		if(verify($resp,$sign)|| $this->is_debug == 1){
			//echo "<br/>验签通过";
			
			require_once(APP_ROOT_PATH.'system/collocation/yeepay/xml.php');
			$str3ParaInfo = @XML_unserialize($resp);
			$str3Req = $str3ParaInfo['notify'];
			
			//
			if ($class_act == 'CreateNewAcct'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/CreateNewAcct.php');
				CreateNewAcctCallBack($str3Req);
	
	
			}
			else if ($class_act == 'CreateNewEpAcct'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/CreateNewEpAcct.php');
				CreateNewEpAcctCallBack($str3Req);

			}
			else if ($class_act == 'DoDpTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoDpTrade.php');				
				DoDpTradeCallBack($str3Req);				

			}else if ($class_act == 'BindBankCard'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/BindBankCard.php');				
				BindBankCardCallBack($str3Req);				

			}else if ($class_act == 'DoDwTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoDwTrade.php');				
				DoDwTradeCallBack($str3Req);				
				
			}else if ($class_act == 'DoBids'){
				//
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoBids.php');
				DoBidsCallBack($str3Req);
			}else if ($class_act == 'RegisterSubject'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RegisterSubject.php');
				RegisterSubjectCallBack($str3Req);
	
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
	
			}else if ($class_act == 'RegisterCreditor'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RegisterCreditor.php');
				RegisterCreditorCallBack($str3Req);
	
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}else if ($class_act == 'DoLoans'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/DoLoans.php');
				$ipsdata = DoLoansCallBack($str3Req);
					
			}else if ($class_act == 'RegisterCretansfer'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RegisterCretansfer.php');
				RegisterCretansferCallBack($str3Req);
	
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}/*else if ($class_act == 'GuaranteeUnfreeze'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/GuaranteeUnfreeze.php');
				GuaranteeUnfreezeCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}*/else if ($class_act == 'RepaymentNewTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RepaymentNewTrade.php');
				RepaymentNewTradeCallBack($str3Req,$this->platformNo,$this->post_url);
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}else if ($class_act == 'RepaymentRepayCallBack'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RepaymentNewTrade.php');
				$ipsdata = RepaymentRepayCallBack($str3Req);

			}else if ($class_act == 'RegisterCretansferBack'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/RegisterCretansfer.php');				
				RegisterCretansferBack($str3Req);
						
			}else if ($class_act == 'Transfer'){
				require_once(APP_ROOT_PATH.'system/collocation/yeepay/Transfer.php');
				TransferCallBack($str3Req);
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}
				
			
		}else{
			echo "<br/>验签不通过:$localSign<br/>";
			
			require_once(APP_ROOT_PATH.'system/collocation/yeepay/xml.php');
			$str3ParaInfo = @XML_unserialize($str3XmlParaInfo);
			print_r($str3ParaInfo);
			
			exit;
		}
	}

	
}
?>
