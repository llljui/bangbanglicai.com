<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

//关于代金券的全局函数
/**
 * 代金券发放
 * @param $ecv_type_id 代金券类型ID
 * @param $user_id  发放给的会员。0为线下模式的发放
 */
function send_voucher($ecv_type_id,$user_id=0,$is_password=false,$money = 0)
{
	$ecv_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv_type where id = ".$ecv_type_id);
	if(!$ecv_type)return false;
	if($is_password)$ecv_data['password'] = rand(10000000,99999999);
	$ecv_data['use_limit'] = $ecv_type['use_limit'];
	if($ecv_type['begin_time']=="")
	{
		$ecv_type['begin_time'] = TIME_UTC;
	}
	
	$ecv_data['begin_time'] = $ecv_type['begin_time'];
	if(app_conf("INTERESTRATE_TIME")>0)
	{
		$ecv_data['end_time'] = to_timespan(to_date($ecv_type['begin_time'])." ".app_conf("INTERESTRATE_TIME")." month -1 day");
	}
	
	if($money > 0)
	{
		$ecv_data['money'] = $money;
	}
	else
	{
		$ecv_data['money'] = $ecv_type['money'];
	}
	$ecv_data['ecv_type_id'] = $ecv_type_id;
	$ecv_data['user_id'] = $user_id;	

	do{
		$sn = unpack('H12',str_shuffle(md5(uniqid())));
		$sn = $sn[1];
		$ecv_data['sn'] = $sn;
		//$ecv_data['sn'] = md5(TIME_UTC);
		$GLOBALS['db']->autoExecute(DB_PREFIX."ecv",$ecv_data,'INSERT','','SILENT');
		$insert_id = $GLOBALS['db']->insert_id();
	}while(intval($insert_id) == 0);
	if($insert_id)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."ecv_type set gen_count = gen_count + 1 where id = ".$ecv_type_id);
	}
	return $insert_id;
}
//加息券发放
function send_interestrate($ecv_type_id,$user_id=0,$is_password=false)
{
	$ecv_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."interestrate_type where id = ".$ecv_type_id);
	if(!$ecv_type)return false;
	if($is_password)$ecv_data['password'] = rand(10000000,99999999);
	$ecv_data['use_limit'] = $ecv_type['use_limit'];
	$ecv_data['begin_time'] = $ecv_type['begin_time'];
	$ecv_data['end_time'] = $ecv_type['end_time'];
	$ecv_data['rate'] = $ecv_type['rate'];
	$ecv_data['ecv_type_id'] = $ecv_type_id;
	$ecv_data['user_id'] = $user_id;	
	$ecv_data['use_type'] = $use_type["use_type"];	

	do{
		$sn = unpack('H12',str_shuffle(md5(uniqid())));
		$sn = $sn[1];
		$ecv_data['sn'] = $sn;
		//$ecv_data['sn'] = md5(TIME_UTC);
		$GLOBALS['db']->autoExecute(DB_PREFIX."interestrate",$ecv_data,'INSERT','','SILENT');
		$insert_id = $GLOBALS['db']->insert_id();
	}while(intval($insert_id) == 0);
	if($insert_id)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."interestrate_type set gen_count = gen_count + 1 where id = ".$ecv_type_id);
	}
	return $insert_id;
}

//体验金发放
function send_learn($learn_id,$user_id=0,$is_password=false)
{

	$now_time = to_date(TIME_UTC,'Y-m-d');
	$learn_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."learn_type where is_effect = 1 and id = ".$learn_id);
	$learn_send_data['user_id'] = $user_id;
    $learn_send_data['type_id'] = $learn_id;
    $learn_send_data['money'] = $learn_type['money'];
    $learn_send_data['type'] = 2;
    $learn_send_data['begin_time'] = $now_time;
    
    $end_time = to_timespan($learn_send_data['begin_time'])+$learn_type['time_limit'] * 24 * 3600 ;
    $learn_send_data['end_time'] = to_date($end_time,'Y-m-d');
    $learn_send_data['is_use'] = 0;
    $learn_send_data['is_effect'] = 1;
    if($learn_type){
    	$GLOBALS['db']->autoExecute(DB_PREFIX."learn_send_list",$learn_send_data,'INSERT','','SILENT');
    }
	$insert_id = $GLOBALS['db']->insert_id();
	
	return $insert_id;
}

?>