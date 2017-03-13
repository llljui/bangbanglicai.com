<?php
	/**
	 * 解冻保证金
	 * @param int $deal_id 标的号
	 * @param int $pUnfreezenType 解冻类型 否 1#解冻借款方；2#解冻担保方
	 * @param float $money 解冻金额;默认为0时，则解冻所有未解冻的金额
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return string
	 */
	function GuaranteeUnfreeze($deal_id,$pUnfreezenType, $money, $platformNo,$post_url){
		/* 请求参数 */
		$freezeRequestNo = '';
		
		$req = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"
			."<request platformNo=\"".$platformNo."\">"
			."<freezeRequestNo>".$data['freezeRequestNo']."</freezeRequestNo>"
			."</request>";
		/* 签名数据 */
		
		$pSign= cfca($req);
		//print_r($sign."xx");die;
		/* 调用账户查询服务 */
		$service = "UNFREEZE";
		$ch = curl_init($post_url."/bhaexter/bhaController");
		curl_setopt_array($ch, array(
		CURLOPT_POST => TRUE,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_SSL_VERIFYPEER=>0,
		CURLOPT_SSL_VERIFYHOST=>0,
		CURLOPT_POSTFIELDS => 'service=' . $service . '&req=' . rawurlencode($req) . "&sign=" . rawurlencode($pSign)
		));
		$resultStr = curl_exec($ch);
		
		var_dump($result);
	}
	
?>