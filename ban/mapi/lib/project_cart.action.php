<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require_once  APP_ROOT_PATH.'app/Lib/project_func.php';
class project_cart
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;

		if(!$GLOBALS['user_info'])
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		
		$root['user_info'] = get_user_info("*","id=".$user_id,"ROW");
		$root['response_code'] = 1;
		//红包
		$sql = "select e.*,et.name from ".DB_PREFIX."ecv as e left join ".DB_PREFIX."ecv_type as et on e.ecv_type_id = et.id where e.user_id = ".$user_id." AND if(e.use_limit > 0 ,(e.use_limit - e.use_count) > 0,1=1) AND if(e.begin_time >0 , e.begin_time < ".TIME_UTC.",1=1) AND if(e.end_time>0,(e.end_time + 24*3600 - 1) > ".TIME_UTC.",1=1) and et.use_type in (0,2)  order by e.id desc ";
		
		$ecv_list = $GLOBALS['db']->getAll($sql);
		
		foreach($ecv_list as $e_k => $e_v)
		{
			$ecv_list[$e_k]["money_format"] = "抵".number_format($e_v["money"],2)."元";
		}
		
		$root["ecv_list"] = $ecv_list;
		
		$id = intval($GLOBALS["request"]['id']);
		
		$root['status'] = 0;
		//(普通众筹)支持之前需要用户绑定手机号
		if(!$GLOBALS['user_info']['mobile'])
		{
			$root['show_err'] = "请先绑定手机";
			output($root);
		}
		
		$deal_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_item where id = ".$id);
		
		if(!$deal_item)
		{
			$root['show_err'] = "项目不存在或者未通过审核，请返回重新操作!";
			output($root);
		}
		elseif(($deal_item['support_count']+$deal_item['virtual_person'])>=$deal_item['limit_user']&&$deal_item['limit_user']!=0)
		{
			$root['show_err'] = "项目已满，请选择其他项目!";
			output($root);
		}
		
		$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where is_delete = 0 and is_effect = 1 and id = ".$deal_item['deal_id']);
		
		if(!$deal_info)
		{
			$root['show_err'] = "项目不存在或者未通过审核，请返回重新操作!";
			output($root);
		}
		
		$deal_info = cache_project_extra($deal_info);
		
		$deal_info = init_project_page_wap($deal_info);
		
		if($deal_info['user_id'] == $user_id)
		{
			$root['show_err'] = "不能购买自己的产品";
			output($root);
		}
		
		elseif($deal_info['begin_time']>TIME_UTC||($deal_info['end_time']<TIME_UTC&&$deal_info['end_time']!=0))
		{
			$root['show_err'] = "项目未开始或者已结束，请返回重新操作!";
			output($root);
		}
		
		$deal_item['consigee_url']=wap_url("index","uc_address");
		
		//无私奉献
		if($deal_item['type']==1){
			$pay_money=floatval($GLOBALS["request"]['pay_money']);
			if($pay_money<=0){
				$root['show_err'] = "您输入的金额错误";
				output($root);
			}
			$deal_item['price']=$pay_money;
			$root['pay_money']=$pay_money;
  		} 
		
		
		$deal_item['price_format'] = format_price($deal_item['price']);
		$deal_item['delivery_fee_format'] = format_price($deal_item['delivery_fee']);
		
		if($deal_item['type'] ==2)
		{
			
			$deal_item['total_price'] = $deal_item['price']*$num+$deal_item['delivery_fee'];
			$deal_item['num']=$num;
			
		}else{
			
			$deal_item['total_price'] = $deal_item['price']+$deal_item['delivery_fee'];
			$deal_item['num']=1;
			
		}
		$deal_item['total_price_format'] = format_price($deal_item['total_price']);
		$deal_info['percent'] = round($deal_info['support_amount']/$deal_info['limit_price']*100,2);
		$deal_info['remain_days'] = ceil(($deal_info['end_time'] - TIME_UTC)/(24*3600));
		
		$deal_info['image'] = get_abs_url_root($deal_info['image']);
		
		$root["deal_info"] = $deal_info;
		
		$root["deal_item"]=$deal_item;
		 
		if($deal_item['is_delivery'])
		{
			$consignee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_address where user_id = ".$user_id);
			if($consignee_list)
				$root["consignee_list"]=$consignee_list;
			else
			{
				$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by id asc");  //二级地址
				$root["region_lv2"]=$region_lv2;
			}
		}
		
		$root["program_title"]="提交订单";
		
		output($root);
	}
}
?>
