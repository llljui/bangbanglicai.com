<?php

 function get_learn_list($limit){
     
    $rs_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."learn where is_effect = 1 ");
    $list =  array();
    if($rs_count > 0){
        $list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."learn where is_effect = 1 order by id desc limit ".$limit);
         // status 0 未开始  1进行中  2 已结束
        foreach($list as $k => $v)
        {
           format_learn_item($v);
           $list[$k] = $v;
        }
    }
    
   return array("rs_count"=>$rs_count,"list"=>$list);
 }
 
 function get_learn($id){
     $info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."learn where is_effect = 1 and id =".$id);
     if(!$info)
        return flase;
     
     format_learn_item($info);
     
     return $info;
 }
 
 function format_learn_item(&$v){
     $now_time = to_date(TIME_UTC,"Y-m-d H:i:s");
    if($v['load_money'] > 0)
      $v['learn_money'] = $GLOBALS['db']->getOne("select sum(money) FROM ".DB_PREFIX."learn_load  where learn_id = '".$v['id']."' ");
    
    if($v['begin_time'] > $now_time ){
       $v['status'] = 0;
    }elseif($v['begin_time'] < $now_time && $v['end_time'] > $now_time  && ($v['load_money'] == 0 || ($v['load_money'] > 0 && $v['learn_money'] < $v['load_money']))){
       $v['status'] = 1;
    }elseif($v['end_time'] < $now_time){
       $v['status'] = 2;
    }else{
       $v['status'] = 3;
    }
 }
 
/*
 * Created on 2015-6-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
  /**
  * 体验金收益领取
  * @param 
  */
 function do_receive_benefits(){ 	
 	
 	require_once APP_ROOT_PATH.'system/libs/user.php';
 	$today = to_date(TIME_UTC,"Y-m-d");
 	$user_id = intval($GLOBALS['user_info']['id']);
 	$learn_load_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."learn_load  where is_send = 0 and user_id = ".$user_id);
 	
 	foreach($learn_load_list as $k => $v)
	{
		$learn_end_time[$k] = strtotime($learn_load_list[$k]['create_date']) + $learn_load_list[$k]['time_limit'] * 24 * 3600 ;
		$learn_expire_end_time[$k] = strtotime($learn_load_list[$k]['create_date']) + ($learn_load_list[$k]['time_expire_limit'] +$learn_load_list[$k]['time_limit']) * 24 * 3600 ;
		$end_date[$k] = to_date($learn_end_time[$k],'Y-m-d');
		$expire_end_date[$k] = to_date($learn_expire_end_time[$k],'Y-m-d');
		
		if($today>= $end_date[$k] && $today<= $expire_end_date[$k]){
			$msg = "体验金收益领取";
			$learn_data['is_send'] = 1;
			$learn_data['send_date'] = to_date(TIME_UTC,"Y-m-d");
			$learn_data['send_time'] = to_date(TIME_UTC,"Y-m-d H:i:s");
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."learn_load",$learn_data,"UPDATE","id=".$learn_load_list[$k]['id']);
			modify_account(array('money'=>$learn_load_list[$k]['interest']), $user_id,$msg,47);
			
		}
		
	}
 	
 }
 
  /**
  * 体验金投资
  * @param 
  */
  function learn_invest($learn_id,$money){
        $return  = array("status"=>0,"info"=>"");
        
        $today = to_date(TIME_UTC,"Y-m-d");
		$now_time = to_date(TIME_UTC,"Y-m-d H:i:s"); 
		
        if(!$GLOBALS['user_info']){
            $return["status"] = 0;
            $return["info"] = "请先登录";
            return $return;
        }
        
       	
  		if($money > $GLOBALS['db']->getOne("select sum(lsl.money) FROM ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id WHERE lt.invest_type = 0 and lsl.is_use = 0 and lsl.begin_time <= '".$today."' and '".$today."' <= lsl.end_time and lsl.user_id='".intval($GLOBALS['user_info']['id'])."' and lsl.is_recycle = 0 and lt.is_effect = 1 "))
  		{
  		    $return["status"] = 0;
            $return["info"] = "体验金不足";
            return $return;
  		}
		
		
		$sql = "select * from ".DB_PREFIX."learn where id =".$learn_id." ";	
		$learn_info = $GLOBALS['db']->getRow($sql);	
        
        if(!$learn_info){
            $return["status"] = 0;
            $return["info"] = "体验产品不存在";
            return $return;
        }
        
        if($learn_info['begin_time']  > $now_time){
            $return["status"] = 0;
            $return["info"] = "体验产品未开始";
            return $return;
        }
        
        if($learn_info['end_time']  < $now_time){
            $return["status"] = 0;
            $return["info"] = "体验产品已结束开始";
            return $return;
        }
        
        
        if($learn_info['load_money']  > 0  && $learn_info['load_money']  < $money + floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."learn_load  WHERE learn_id=".$learn_info['id']))){
            $return["status"] = 0;
            $return["info"] = "所投体验金已超出产品所能筹集的体验资金";
            return $return;
        }
        
		
		$learn_load_data['learn_id'] = $learn_info['id'];
		$learn_load_data['money'] = $money;
		$learn_load_data['rate'] = $learn_info['rate'];
		$learn_load_data['time_limit'] = $learn_info['time_limit'];
		$learn_load_data['time_expire_limit'] = $learn_info['time_expire_limit'];
		$learn_load_data['create_date'] = $today;
		$learn_load_data['create_time'] = $now_time;
		$learn_load_data['interest'] =  (intval($money)*$learn_info['rate']*0.01*$learn_info['time_limit'])/365;
		
		$learn_load_data['user_id'] = intval($GLOBALS['user_info']['id']);
		
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."learn_load",$learn_load_data);
 		if($GLOBALS['db']->affected_rows()){
 		    
            $sql_send = "update ".DB_PREFIX."learn_send_list lsl left join ".DB_PREFIX."learn_type lt on lsl.type_id = lt.id set lsl.is_use = '1',lsl.use_time ='".$now_time."',lsl.use_date='".$today."' where lt.invest_type = 0 and lsl.is_use = '0' and  lsl.user_id = ".intval($GLOBALS['user_info']['id']);
            $GLOBALS['db']->query($sql_send);
            
			$return["status"] = 0;
            $return["info"] = "投资成功";
            return $return;
		}
		else{
			 $return["status"] = 0;
            $return["info"] = "投资失败";
            return $return;
		}
		
 }
 
 
?>
