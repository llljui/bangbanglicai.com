<?php
/**
 * 后台交易 HttpClient通信
 * @param unknown_type $params
 * @param unknown_type $url
 * @return mixed
 */
function sendHttpRequest($params, $url) {
	$opts = getRequestParamString ( $params );
	
	$ch = curl_init ();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_TIMEOUT,60);

	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
	curl_setopt($ch, CURLOPT_ENCODING,'gzip,deflate');
	curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.168 Safari/535.19");
	curl_setopt($ch, CURLOPT_MAXREDIRS,11);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $opts );
	
	
	// 运行cURL，请求网页
	$html = curl_exec ( $ch );
	// close cURL resource, and free up system resources
	
	curl_close ( $ch );
	return $html;
}

/**
 * 组装报文
 *
 * @param unknown_type $params        	
 * @return string
 */
function getRequestParamString($params) {
	$params_str = '';
	foreach ( $params as $key => $value ) {
		$params_str .= ($key . '=' . (!isset ( $value ) ? '' : urlencode( $value )) . '&');
	}
	return substr ( $params_str, 0, strlen ( $params_str ) - 1 );
}