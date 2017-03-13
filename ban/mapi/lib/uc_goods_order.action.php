<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_goods_order
{
	public function index(){
		
		$root = get_baseroot();
		
		$id = intval($GLOBALS['request']['id']);
		//$user_id = intval($GLOBALS['user_info']['id']);
		$page = intval($GLOBALS['request']['page']);
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		if ($user_id >0){
			require APP_ROOT_PATH.'app/Lib/deal.php';
			require APP_ROOT_PATH.'app/Lib/uc_goods_func.php';
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
			$order_sn = strim($_REQUEST['order_sn']);
			$time = isset($_REQUEST['time']) ?  to_date(to_timespan($_REQUEST['time'],"Y-m-d"),"Y-m-d") : "";
			
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
			
			$condition = " 1=1 ";
			if($order_sn != "")
			$condition.=" and go.order_sn = '".$order_sn."' ";
			
			if($time!=""){
				$condition.=" and go.ex_date = '".$time."' ";
				$root['time'] = $time;
			}
			
			$root['user_id'] = $user_id;
			
			//$orders = get_order($limit,$user_id,$condition);
			
			$count_sql = "SELECT count(*) from ".DB_PREFIX."goods_order go where $condition and go.user_id = ".$user_id;
			$count = $GLOBALS['db']->getOne($count_sql);
			
			if($count > 0){
				$sql = "SELECT go.* from ".DB_PREFIX."goods_order go where $condition and go.user_id = ".$user_id."  order by go.id  DESC LIMIT ".$limit;
			
				$order_info = $GLOBALS['db']->getAll($sql);
				$attr_str = "";
				foreach($order_info as $k=>$v){
					$order_info[$k]['attr_format'] = unserialize($v['attr']);
					foreach($order_info[$k]['attr_format'] as $kk=>$vv){
						$attr_str .= $GLOBALS['db']->getOne("select name from ".DB_PREFIX."goods_type_attr where id =".$kk );
						$attr_str .=":";
						$attr_str .= $GLOBALS['db']->getOne("select name from ".DB_PREFIX."goods_attr where id =".$vv );
						$attr_str .="  ";
					}
					$order_info[$k]['attr_format'] = $attr_str;
					$attr_str = "";
					if($order_info[$k]['is_delivery'] == 0)
					{	$order_info[$k]['is_delivery_format'] = "否";}
					else{
						$order_info[$k]['is_delivery_format'] = "是";
					}
					if($order_info[$k]['order_status'] == 0){
						$order_info[$k]['order_status_format'] = "未发货";
					}elseif($order_info[$k]['order_status'] == 1){
						$order_info[$k]['order_status_format'] = "已发货";
					}elseif($order_info[$k]['order_status'] == 2){
						$order_info[$k]['order_status_format'] = "无效订单";
					}elseif($order_info[$k]['order_status'] == 3){
						$order_info[$k]['order_status_format'] = "用户取消";
					}
					
					$a=get_goods($order_info[$k]['goods_id']);
					$order_info[$k]['img'] = get_abs_wap_url_root(get_abs_img_root($a['img']));
					$order_info[$k]['ex_time'] = to_date( $v['ex_time'],"Y-m-d H:i:s");
					$order_info[$k]['delivery_time'] = to_date( $v['delivery_time'],"Y-m-d H:i:s");
				}
			}
			
		//	return array("list"=>$order_info,'count'=>$count);			
			
			$root['page'] = array("page"=>$page,"page_total"=>ceil($count/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));
			$root['order_sn'] = $order_sn;
			$root['order_info'] = $order_info;
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "我的兑换";
		output($root);		
	}
}
?>
