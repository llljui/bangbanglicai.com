<?php
	
	function QueryForAccBalance($user_id,$platformNo,$post_url){
		/* 请求参数 */
		$req = "<request platformNo=\"$platformNo\"><platformUserNo>$user_id</platformUserNo></request>";
		/* 签名数据 */
		
		$pSign= cfca($req);
		//print_r($sign."xx");die;
		/* 调用账户查询服务 */
		$service = "ACCOUNT_INFO";
		$ch = curl_init($post_url."/bhaexter/bhaController");
		curl_setopt_array($ch, array(
		CURLOPT_POST => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_SSL_VERIFYPEER=>0,
		CURLOPT_SSL_VERIFYHOST=>0,
		CURLOPT_POSTFIELDS => 'service=' . $service . '&req=' . rawurlencode($req) . "&sign=" . rawurlencode($pSign)
		));
		$resultStr = curl_exec($ch);
		//print($result);
		
		if (empty($resultStr)){
			$result = array();
			$result['pErrCode'] = 9999;
			$result['pErrMsg'] = '返回出错';
			$result['pIpsAcctNo'] = '';
			$result['pBalance'] = 0;
			$result['pLock'] = 0;
			$result['pNeedstl'] = 0;
		}else{
	
				
		
				require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
				$str3ParaInfo = @XML_unserialize($resultStr);
				//print_r($str3ParaInfo);exit;
				$str3Req = $str3ParaInfo['response'];
				$result = array();
				if($str3Req["code"] == 1)
				{
					$result['pErrCode'] = "0000";
					
					if($str3Req['memberType'] == 'ENTERPRISE') //更新企业用户状态
					{
						$result['activeStatus'] = $str3Req["activeStatus"];
						if($result['activeStatus'] == 'ACTIVATED'||$result['activeStatus'] == 'DEACTIVATED')
						{
							$sql = "update ".DB_PREFIX."yeepay_enterprise_register set activeStatus = '".$result['activeStatus']."' where platformUserNo =".$user_id;
							$GLOBALS['db']->query($sql);
						}
					}
				}
				else
					$result['pErrCode'] = $str3Req["code"];
				$result['pErrMsg'] = $str3Req["description"];
				$result['pIpsAcctNo'] = $user_id;
				$result['pBalance'] = $str3Req["balance"] - $str3Req["freezeAmount"];
				$result['pLock'] = $str3Req["freezeAmount"];
				$result['pNeedstl'] = 0;// $str3Req["availableAmount"];		
					
		}
		
		return $result;
	
	}	
	
?>