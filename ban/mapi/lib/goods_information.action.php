<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class goods_information
{
	public function index(){
		
		$root = get_baseroot();
		
		
		$id = intval($GLOBALS['request']['id']);
		$user_id = intval($GLOBALS['user_info']['id']);
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		require APP_ROOT_PATH.'app/Lib/deal.php';
		require APP_ROOT_PATH.'app/Lib/uc_goods_func.php';
		
		$root['user_login_status'] = 1;
		$root['response_code'] = 1;
		
		if($id==0){
			app_redirect(url("index")); 
		}
		
		$goods = get_goods_info($id,$user_id);
		$root['img'] = get_abs_img_root($goods['img']);
		$root['img'] = get_abs_wap_url_root($root['img']);
		
		if(!$goods){
			app_redirect(url("index")); 
		}
		
		$goods['description'] = format_html_content_image($goods['description'],300,300);
			
		$goods['description'] = str_replace("./public/",SITE_DOMAIN.APP_ROOT."/../public/",$goods['description']);
		$root['goods_description'] = $goods['description'];
		$root['user_can_buy_number'] = $goods['user_can_buy_number'];
		if($goods['goods_type_id'] >0){
			$goods_type_attr = get_goods_attr($goods['goods_type_id'],$id);
			$root['has_stock'] = $goods_type_attr['has_stock'];
			$root['goods_type_attr'] = $goods_type_attr['goods_type_attr'];
			$root['json_attr_stock'] = json_encode($goods_type_attr['attr_stock']);
			
		}else{
			$has_stock=1;
			$root['has_stock'] = $has_stock;
		}
		
		$root['user_id'] = $user['id'];
		$root['goods_id'] = $id;
		$root['uscore'] = $user['score'];
		$root['goods'] = $goods;
		
		$root['program_title'] = "商品详情";
		output($root);		
	}
}
?>
