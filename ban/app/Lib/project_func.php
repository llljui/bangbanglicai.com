<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
define("DEAL_PAGE_SIZE",60);
define("DEAL_STEP_SIZE",4);
define("DEAL_SUPPORT_PAGE_SIZE",20);
define("DEAL_COMMENT_PAGE_SIZE",40);
define("DEALUPDATE_PAGE_SIZE",15);
define("DEALUPDATE_STEP_SIZE",5);
define("ACCOUNT_PAGE_SIZE",10);

function syn_project($deal_id)
{
	$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$deal_id);
	if($deal_info)
	{
		$deal_info['comment_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_comment where deal_id = ".$deal_info['id']." and log_id = 0"));
		$deal_info['support_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_order where deal_id = ".$deal_info['id']." and order_status=3 and is_refund=0"));
		$deal_info['focus_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_focus_log where deal_id = ".$deal_info['id']));
		$deal_info['view_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_visit_log where deal_id = ".$deal_info['id']));
		$deal_info['support_amount'] = floatval($GLOBALS['db']->getOne("select sum(deal_price) from ".DB_PREFIX."project_order where deal_id = ".$deal_info['id']." and order_status=3 and is_refund=0"));
		$deal_info['delivery_fee_amount'] = floatval($GLOBALS['db']->getOne("select sum(delivery_fee) from ".DB_PREFIX."project_order where deal_id = ".$deal_info['id']." and order_status=3 and is_refund=0"));
		$deal_info['share_fee_amount'] = floatval($GLOBALS['db']->getOne("select sum(share_fee) from ".DB_PREFIX."project_order where deal_id = ".$deal_info['id']." and order_status=3 and is_refund=0"));
		
		if($deal_info['pay_radio'] > 0){
			$deal_info['pay_amount'] = ($deal_info['support_amount']*(1-$deal_info['pay_radio']))+$deal_info['delivery_fee_amount']-$deal_info['share_fee_amount'];
		}
		else
		{
			$deal_info['pay_amount'] = ($deal_info['support_amount']*(1-app_conf("PAY_RADIO")))+$deal_info['delivery_fee_amount']-$deal_info['share_fee_amount'];
		
		}
		if($deal_info['type']==0){
			$deal_info["virtual_num"]=$GLOBALS['db']->getOne("select sum(virtual_person) from ".DB_PREFIX."project_item where deal_id=".$deal_id);
			$deal_info["virtual_price"]=$GLOBALS['db']->getOne("select sum(virtual_person*price) from ".DB_PREFIX."project_item where deal_id=".$deal_id);
			if(($deal_info['support_amount']+$deal_info["virtual_price"])>=$deal_info['limit_price'])
			{
				$deal_info['is_success'] = 1;
			}
			else
			{
				$deal_info['is_success'] = 0;
			}
			
		}elseif($deal_info['type']==1){
			$deal_info["gen_num"]=$GLOBALS['db']->getOne("select count(distinct(user_id)) from ".DB_PREFIX."investment_list where  type=2 and  deal_id=".$deal_id);
			$deal_info["xun_num"]=$GLOBALS['db']->getOne("select count(distinct(user_id)) from ".DB_PREFIX."investment_list where  type=0 and  deal_id=".$deal_id);
			$deal_info["invote_num"]=$GLOBALS['db']->getOne("select count(distinct(user_id)) from ".DB_PREFIX."investment_list where  deal_id=".$deal_id);
			$deal_info["invote_money"]=$GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."investment_list where deal_id=$deal_id ");
			if($deal_info['invote_money']>=$deal_info['limit_price'])
			{
				$deal_info['is_success'] = 1;
			}
			else
			{
				$deal_info['is_success'] = 0;
			}
		}
		//print_r("select sum(money) from ".DB_PREFIX."project_pay_log where deal_id=".$deal_id);die;
		if($deal_info['is_success']==1){
				$paid_money=$GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."project_pay_log where deal_id=".$deal_id);
				$deal_info['left_money']=$deal_info['pay_amount']-floatval($paid_money);
			}
  		
		$deal_info['tags_match'] = "";
		$deal_info['tags_match_row'] = "";
		$GLOBALS['db']->autoExecute(DB_PREFIX."project", $deal_info, $mode = 'UPDATE', "id=".$deal_info['id'], $querymode = 'SILENT');	
		$tags_arr = preg_split("/[, ]/",$deal_info["tags"]);
		foreach($tags_arr as $tgs){
			if(trim($tgs)!="")
			insert_match_item(trim($tgs),"project",$deal_info['id'],"tags_match");
		}
		
		$name_arr = div_str($deal_info['name']);
		foreach($name_arr as $name_item){
			if(trim($name_item)!="")
			insert_match_item(trim($name_item),"project",$deal_info['id'],"name_match");
		}

	}


}
function syn_project_status($deal_id)
{
	$deal_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id=$deal_id");

	$GLOBALS['db']->query("update ".DB_PREFIX."project set is_success = 1,success_time = ".TIME_UTC." where id = ".$deal_id." and is_effect=  1 and is_delete = 0 and (support_amount+virtual_price) >= limit_price and begin_time <".TIME_UTC." and (end_time > ".TIME_UTC." or end_time = 0)");
	if($GLOBALS['db']->affected_rows()>0)
	{	
		$GLOBALS['db']->query("update ".DB_PREFIX."project_order set is_success = 1 where deal_id = ".$deal_id);
		//无私奉献的用户，项目成功后，就默认发送成功回报
		$GLOBALS['db']->query("update ".DB_PREFIX."project_order set repay_make_time='".TIME_UTC."',repay_time='".TIME_UTC."',repay_memo='无私奉献' where order_status=3 and is_refund=0 and type=2 and repay_make_time = 0 and  deal_id =".$deal_id);
		//项目成功，加入项目成功的待发队列
		$deal_notify['deal_id'] = $deal_id;
		$deal_notify['create_time'] = TIME_UTC;
		$GLOBALS['db']->autoExecute(DB_PREFIX."project_notify",$deal_notify,"INSERT","","SILENT");	
	}
	/*if($deal_info["is_success"]==0)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."project_order set repay_make_time='".TIME_UTC."',repay_time='".TIME_UTC."',repay_memo='无私奉献' where order_status=3 and is_refund=0 and type=2 ");
	}*/
}
//项目成功发送短信、回报短信
function send_pay_success($log_info,$id){
	if(app_conf("SMS_ON")==0){
		return false;
	}
	//项目成功发起者短信
	//$deal_s_user=$GLOBALS['db']->getAll("select d.*,u.mobile from ".DB_PREFIX."project d LEFT JOIN ".DB_PREFIX."user u ON u.id = d.user_id where d.is_success='1' and d.is_has_send_success='0' and d.is_delete = 0 ");
	$deal_s_user=$GLOBALS['db']->getAll("select d.*,u.mobile from ".DB_PREFIX."project d LEFT JOIN ".DB_PREFIX."user u ON u.id = d.user_id where d.is_success='1' and d.is_has_send_success='0' and d.is_delete = 0 and d.id=".$id);
	
	$tmpl3=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name='TPL_SMS_USER_S'");
	$tmpl_content3 = $tmpl3['content'];
	foreach ($deal_s_user as $k=>$v){
		if($v['id']){
		$user_s_msg['user_name']=$v['user_name'];
		$user_s_msg['deal_name']=$v['name'];
	
		$GLOBALS['tmpl']->assign("user_s_msg",$user_s_msg);
		$msg3=$GLOBALS['tmpl']->fetch("str:".$tmpl_content3);
		$msg_data3['dest']=$v['mobile'];
		$msg_data3['send_type']=0;
		$msg_data3['content']=addslashes($msg3);
		$msg_data3['send_time']=0;
		$msg_data3['title']='众筹项目成功发起者-'.$v['name'];
		$msg_data3['is_send']=0;
		$msg_data3['create_time'] = TIME_UTC;
		$msg_data3['user_id'] = $v['user_id'];
		$msg_data3['is_html'] = $tmpl3['is_html'];
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data3); //插入
	
		}
	}	
}

function get_deal_list($limit="",$conditions="",$orderby=" sort asc ",$deal_type='deal'){

	if($limit!=""){
		$limit = " LIMIT ".$limit;
	}
	
	if($orderby!=""){
		$orderby = " ORDER BY ".$orderby;
	}
	
	$condition = " 1=1 AND d.is_delete = 0 AND d.is_effect = 1 and d.type=0 ";
	
	if($conditions!=""){
		$condition.=" AND ".$conditions;
	}
	
	//权限浏览控制
 
 	$deal_count = $GLOBALS['db']->getOne("select count(*)  from ".DB_PREFIX."project as d  where ".$condition);
 
  	/*（所需项目）准备虚拟数据 start*/
	$deal_list = array();
	$level_list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_level ");
	$level_list_array=array();
	foreach($level_list_array as $k=>$v){
		if($v['id']){
			$level_list_array[$v['id']]=$v['point'];
		}
	}
	//print_r("select d.* from ".DB_PREFIX."project  as d   where ".$condition.$orderby.$limit);die;
 	if($deal_count > 0){
		$now_time = TIME_UTC;
		$deal_list = $GLOBALS['db']->getAll("select d.* from ".DB_PREFIX."project  as d   where ".$condition.$orderby.$limit);
 		//file_put_contents("condition.txt", print_r("select d.* from ".DB_PREFIX."deal  as d   where ".$condition.$orderby.$limit,1));
		$deal_ids = array();
		foreach($deal_list as $k=>$v)
		{
			$deal_list[$k]['remain_days'] = ceil(($v['end_time'] - $now_time)/(24*3600));
			if($v['begin_time'] > $now_time){
				$deal_list[$k]['left_days'] = ceil(($v['begin_time'] - $now_time) / 24 / 3600);
			}
			$deal_list[$k]['num_days'] = ceil(($v['end_time'] - $v['begin_time'])/(24*3600));
			$deal_ids[] =  $v['id'];
			//查询出对应项目id的user_level
			$deal_list[$k]['deal_level']=$level_list_array[intval($deal_list[$k]['user_level'])];
			if($v['begin_time'] > $now_time){
				$deal_list[$k]['left_begin_days'] = intval(($v['begin_time']  - $now_time) / 24 / 3600);
				$deal_list[$k]['left_begin_day'] = intval(($v['begin_time']  - $now_time));
			}
			if($v['begin_time'] > $now_time){
					$deal_list[$k]['status']= '0';                                 
			}
			elseif($v['end_time'] < $now_time && $v['end_time']>0){
				if($deal_list[$k]['percent'] >=100){
					$deal_list[$k]['status']= '1';  
				}
				else{
						$deal_list[$k]['status']= '2'; 
				}
			} 
			else{
					if ($v['end_time'] > 0) {
						$deal_list[$k]['status']= '3'; 
					}
					else
					$deal_list[$k]['status']= '4'; 
			}
			
			if($v['type']==1){
				$deal_list[$k]['virtual_person']=$deal_list[$k]['invote_num'];
				$deal_list[$k]['support_count'] =$deal_list[$k]['invote_num'];
				$deal_list[$k]['support_amount'] =$deal_list[$k]['invote_money'];
				$deal_list[$k]['percent'] = round(($deal_list[$k]['support_amount'])/$v['limit_price']*100,2);
				$deal_list[$k]['limit_price_w']=($deal_list[$k]['limit_price'])/10000;
				$deal_list[$k]['invote_mini_money_w']=number_format(($deal_list[$k]['invote_mini_money'])/10000,2);
			}else{
				$deal_list[$k]['virtual_person']=$deal_list[$k]['virtual_num'];
				$deal_list[$k]['support_count'] =$deal_list[$k]['virtual_num']+$deal_list[$k]['support_count'];
				$deal_list[$k]['support_amount'] =$deal_list[$k]['virtual_price']+$deal_list[$k]['support_amount'];
				$deal_list[$k]['percent'] = round(($deal_list[$k]['support_amount'])/$v['limit_price']*100,2);
 			}
 			if($deal_type=='deal_cate'||$deal_type=='deal_cate_preheat'){
 				$deal_list[$k]['user_info']= get_user_info("*","id = ".$v['user_id']);
				$deal_list[$k]['deal_comment_num']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_comment where deal_id = ".$v['id']." and log_id = 0 and status=1 ");
				$deal_list[$k]['deal_comment_num']=intval($deal_list[$k]['deal_comment_num']);
				$deal_list[$k]['cate_name']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."project_cate where id=".$v['cate_id']);
  				if($deal_type=='deal_cate_preheat'){
  					//关注
  					$deal_list[$k]['focus_num']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_comment where deal_id = ".$v['id']." and log_id = 0 and status=1 ");
   				}
  			}
		}
 	 
	}
	
	return array("rs_count"=>$deal_count,"list"=>$deal_list);
}
//判断用户是否有权限
//0 表示未登录 1表示正常 2表示等级不够 3表示没有认证手机 4表示没有身份认证 5表示身份认证审核中 6表示身份认证审核失败
function get_level_access($user_info,$deal_info){
	if(!$user_info){
		//0 表示未登录
		if($deal_info['user_level']>0){
			return 0;
		}else{
			return 1;
		}
		
	}
	if($user_info['id']!=$deal_info['user_id']){
		$user_level=intval($GLOBALS['db']->getOne("select point from ".DB_PREFIX."user_level where id=".$user_info['level_id']));
		$deal_level=intval($GLOBALS['db']->getOne("select point from ".DB_PREFIX."user_level where id=".$deal_info['user_level']));
	if($deal_level!=0&&($deal_level>$user_level)){
		// 2表示等级不够
		return 2;
	}
	if($deal_info['type']==0){
		if(!$user_info['mobile']){
			return 3;
		}
 	}elseif($deal_info['type']==1){
		if($user_info['is_investor']==0){
			return 4;
		}elseif($user_info['investor_status']==0){
			return 5;
		}elseif($user_info['investor_status']==2){
			return 6;
		}
	}
	}
	return 1;
	
}

function log_deal_visit($deal_id)
{
	if(check_ipop_limit(get_client_ip(),"deal_show",600,$deal_id))
	{
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_visit_log where deal_id = ".$deal_id." and client_ip = '".get_client_ip()."' and ".TIME_UTC." - create_time < 600")==0)
		{
			$view_data['deal_id'] = $deal_id;
			$view_data['client_ip'] = get_client_ip();
			$view_data['create_time'] = TIME_UTC;
			$GLOBALS['db']->autoExecute(DB_PREFIX."project_visit_log",$view_data);
			$GLOBALS['db']->query("update ".DB_PREFIX."project set view_count = view_count + 1 where id = ".$deal_id);
		}
	}
	
}

//缓存项目信息
function cache_project_extra($deal_info,$is_pc = 1)
{
	if($deal_info['deal_extra_cache']=="")
	{
		$deal_extra_cache = array();
		$deal_info['deal_faq_list'] = $deal_extra_cache['deal_faq_list'] = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_faq where deal_id = ".$deal_info['id']." order by sort asc");		
		$deal_info['deal_item_list'] = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_item where deal_id = ".$deal_info['id']." order by price asc");
		
		foreach($deal_info['deal_item_list'] as $k=>$v)
		{
			$imgs = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_item_image where deal_id=".$deal_info['id']." and deal_item_id = ".$v['id']);
			
			if($is_pc == 0)
			{
				foreach($imgs as $i_k => $i_v)
				{
					$imgs[$i_k]['image'] = get_abs_url_root($i_v['image']);
				}
			}
			
			$deal_info['deal_item_list'][$k]['images'] = $imgs;
			
			$deal_info['deal_item_list'][$k]['format_price'] = format_price($v['price']);				
		
		}
		
		$deal_extra_cache['deal_item_list'] = $deal_info['deal_item_list'];
		$GLOBALS['db']->query("update ".DB_PREFIX."project set deal_extra_cache  = '".serialize($deal_extra_cache)."' where id = ".$deal_info['id']);
		
	}
	else
	{
		$deal_extra_cache = unserialize($deal_info['deal_extra_cache']);
		$deal_info['deal_faq_list'] = $deal_extra_cache['deal_faq_list'];
		$deal_info['deal_item_list'] = $deal_extra_cache['deal_item_list'];
	}
	return $deal_info;
}
//获取上线时间
function online_date($time,$online_time)
{
	if($time<$online_time)
	{
		return array("key"=>"online_0","info"=>"未上线");
	}
	else
	{
		$time_span = $time - $online_time;
		$day = ceil($time_span/(3600*24));
		return array("key"=>"online_".$day,"info"=>"上线第".$day."天");
	}
}
function cache_log_comment($log)
{
	if($log['comment_data_cache']==""&&$log['id']>0)
	{
		$comment_data_cache = array();
		$log['comment_count'] = $comment_data_cache['comment_count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_comment where log_id = ".$log['id']);
		$log['comment_list'] = $comment_data_cache['comment_list'] = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_comment where log_id = ".$log['id']." order by create_time desc limit 3");
		if($log['comment_count']<=count($log['comment_list']))
		{
			$log['more_comment'] = $comment_data_cache['more_comment']  = false;
		}
		else
		{
			$log['more_comment'] = $comment_data_cache['more_comment']  = true;
		}
		$GLOBALS['db']->query("update ".DB_PREFIX."project_log set comment_data_cache = '".serialize($comment_data_cache)."' where id = ".$log['id']);
	}
	else
	{
		$comment_data_cache = unserialize($log['comment_data_cache']);
		$log['comment_count'] = $comment_data_cache['comment_count'];
		$log['comment_list'] = $comment_data_cache['comment_list'];
		$log['more_comment'] = $comment_data_cache['more_comment'];
	}
	return $log;
}
//验证邮编
function check_postcode($postcode)
{
	if(!empty($postcode) && !preg_match("/^([0-9]{6})(-[0-9]{5})?$/",$postcode))
	{
		return false;
	}
	else
	return true;
}

function check_tg(){
    	$is_tg= app_conf("OPEN_IPS");
		$is_user_tg=$GLOBALS['user_info']["ips_acct_no"];
		$is_user_investor= $GLOBALS["user_info"]["idcardpassed"];//$GLOBALS['is_user_investor'];
		if($is_tg > 0){
			if(!$is_user_tg){
 				showErr('您未绑定资金托管账户，无法发起项目，点击确定后跳转到绑定页面',0,url("index","collocation#CreateNewAcct",array('user_type'=>0,'user_id'=>$GLOBALS['user_info']['id'])));
			}else{
 				return true;
			}
		}else{
			if(!$is_user_investor){
				showErr('您未进行身份认证，无法发起项目，点击确定后跳转到身份认证页面',0,url("index","settings#security",array('method'=>'setting-id-box')));
 			}else{
 				return true;
			}
		}
    }
	function show_ke_form($text_name,$width="300",$height="80",$cnt="")
	{
	
		if($cnt=="")
		{
			$cnt = "<h3>关于我</h3>
	<p>向支持者介绍一下你自己，以及你与所发起的项目之间的背景。这样有助于拉近你与支持者之间的距离。建议不超过100字。<br />
	<br />
	</p>
	<h3>我想要做什么</h3>
	<p>以图文并茂的方式简洁生动地说明你的项目，让大家一目了然，这会决定是否将你的项目描述继续看下去。建议不超过300字。<br />
	<br />
	</p>
	<h3>为什么我需要你的支持</h3>
	<p>这是加分项。说说你的项目不同寻常的特色、资金用途、以及大家支持你的理由。这会让更多人能够支持你，不超过200个汉字。<br />
	<br />
	</p>
	<h3>我的承诺与回报</h3>
	让大家感到你对待项目的认真程度，鞭策你将项目执行最终成功。同时向大家展示一下你为支持者准备的回报，来吸引更多人支持你。";
		}
	//	$GLOBALS['tmpl']->assign("text_name",$text_name);
	//	$GLOBALS['tmpl']->assign("width",$width);
	//	$GLOBALS['tmpl']->assign("height",$height);
	//	$GLOBALS['tmpl']->assign("box_id",$text_name);
	//	$GLOBALS['tmpl']->assign("cnt",$cnt);
		return "<div  style='margin-bottom:5px; '><textarea id='".$text_name."' name='".$text_name."' class='ketext' style='width:".$width."px; height:".$height."px;' >".$cnt."</textarea> </div>";
	  }
	 /*判断股份*/
 function deal_investor_info($info,$type='stock',$old_info=''){
   	$total=0.00;
 	$result=array("status"=>0,'info'=>'');
 	$result_info=array();
 	if($type=='attach'){
  		foreach($info as $k=>$v){
  			if(!empty($info[$k]['title'])&&!empty($info[$k]['file'])){
 				$result_info[]=$info[$k];
 			} 
 		}
  	}elseif($type=='stock'){
    		foreach($info as $k=>$v){
  			$info[$k]['share']=floatval($info[$k]['share']);
  			if($info[$k]['share']>0&&!empty($v['name'])){
	 			$total+=$v['share'];
	 			$result_info[]=$info[$k];
	 		}
 		}
  		if($total!=100){
	   		$result['info']='股份不等于100%';
	 		return $result;
	 	}
  	}elseif($type=='unstock'){
  		foreach($info as $k=>$v){
	   		if(!empty($info[$k]['name'])){
	 				$result_info[]=$info[$k];
	 		} 
  		}
  	}elseif($type=='history'||$type=='plan'){
  	
  		foreach($info as $k=>$v){
	  		if(!empty($v['info']['name'])){
	  			$info[$k]['info']['income_num']=1;
		 			$info[$k]['info']['out_num']=1;
		 			if($v['info']['is_income']==1){
		 				$num=0;
		 				foreach($v['income'] as $k1=>$v1){
		 					if(empty($v1['type'])||empty($v1['money'])){
		 						unset($info[$k]['income'][$k1]);
		 					}else{
		 						$info[$k]['info']['income_num']++;
		 					}
		 				}
		 			}else{
		 				unset($info[$k]['income']);
		 			}
		 			if($v['info']['is_out']==1){
		 				foreach($v['out'] as $k2=>$v2){
		 					if(empty($v2['type'])||empty($v2['money'])){
		 						unset($info[$k]['out'][$k2]);
	 	 					}else{
		 						$info[$k]['info']['out_num']++;
		 					}
		 				}
		 			}else{
		 				unset($info[$k]['out']);
		 			}
		 			
		 			if( $v['info']['begin_time'] !='' && $v['info']['end_time'] !='' )
		 			{	
		 				$begin_time=to_timespan($v['info']['begin_time']);
		 				$end_time=to_timespan($v['info']['end_time']);
		 				
		 				if( $end_time < $begin_time )
		 				{ 
		 					$result['info']='开始时间要小于结束时间';
	 						return $result;
		 					
		 				}
		 			}
 		 			$result_info[]=$info[$k];
	  		}
  		}
  	}elseif($type=='audit_data'){
    		foreach($info as $k=>$v){
  			if(is_array($v)){
  				
  				$result_info[$k]['status']=intval($old_info[$k]['status']);
	  			$result_info[$k]['reason']=$old_info[$k]['reason'];
	  			if(!empty($v['image'])){
	  				foreach($v['image'] as $k1=>$v1){
	  					$result_info[$k]['image'][]=$v1;
	  				}
	  			}
  			}else{
  				$result_info[$k]=$v;
  			} 
  		}
  		
   	}
 	
  	
 	return array('status'=>1,'data'=>$result_info);
 } 
 
 //是否有安装第三方托管
function is_tg($status=false){
	$collotion=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."collocation where is_effect=1 ");
	if($collotion){
		if($status){
			return $collotion;
		}else{
			return true;
		}
		
	}else{
		return false;
	}
}
//返回array: status:0:未支付 1:已支付(过期) 2:已支付(无库存) 3:成功  money:剩余需支付金额 4:已支付但未判定（锁住订单）5:订单内余额不足，购买失败
function pay_order($order_id)
{
	require_once APP_ROOT_PATH."system/libs/user.php";	
	$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project_order where id = ".$order_id);
	$user = get_user_info("*","id = ".$order_info['user_id']);
	//查出积分的数量score ,对应金额score_money
	if($order_info['score']>$user['score']){
		//积分不够，支付失败
		$result['status'] = 0;
		return $result;
	}
	//print_r($order_info);die;
	//if($order_info['is_tg']){
 	//	$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set order_status = 4 where id = ".$order_id." and  online_pay=total_price and order_status = 0");
	//}else{
		$ecv_money = 0 ;
		
		if($order_info["ecv_id"]>0)
		{
			$ecv_money = $GLOBALS['db']->getOne("SELECT `money` FROM ".DB_PREFIX."ecv WHERE id=".$order_info["ecv_id"]);
		}
		if($order_info['score'] == 0 && $order_info["total_price"] <= $ecv_money)
		{
			$ecv_money = $order_info["total_price"];
		}
 		$GLOBALS['db']->query("update ".DB_PREFIX."project_order set order_status = 4 where id = ".$order_id." and  (online_pay+credit_pay+score_money+".$ecv_money.")=total_price and order_status = 0");

	//}
  	if($GLOBALS['db']->affected_rows()>0) //订单已成功支付
	{
 		if(!$order_info['is_tg']){
			//积分转成余额， 扣掉积分
			if($order_info['score']>0)
			{
				$log_score=$order_info['deal_name']."购买，支付使用".$order_info['score']."积分,抵扣".format_price($order_info['score_money']);
				modify_account(array("score"=>"-".$order_info['score']),$order_info['user_id'],$log_score,53);
			}
			
			if($order_info['total_price'] >= $ecv_money + $order_info['score_money'])
			{
				$total_money = $order_info['total_price'] - $ecv_money - $order_info['score_money'];
			}
			else
			{
				$total_money = 0;
			}
			
	  		modify_account(array("money"=>"-".$total_money),$order_info['user_id'],$order_info['deal_name']."购买成功",50);
			
	   		/*if(!$re){
	 			$result['status'] = 5;
				$GLOBALS['db']->query("update ".DB_PREFIX."project_order set order_status = 0 where id = ".$order_info['id']);
	 			//扣款失败，积分退回
	 			$log_score=$order_info['deal_name']."购买，支付失败，退回".$order_info['score']."积分，扣除余额".format_price($order_info['score_money']);
				modify_account(array("money"=>"-".$order_info['score_money'],"score"=>$order_info['score']),$order_info['user_id'],$log_score,51);
	 			
	 			return $result;
	  		}*/
		}
		 
 		//$credit_pay=$order_info['total_price']-$order_info['online_pay'];
		//$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set credit_pay=".$credit_pay." where id=".$order_info['id']);
  		
 		$order_info['pay_time'] = TIME_UTC;
		if($order_info['type']==1){
			$GLOBALS['db']->query("update ".DB_PREFIX."project set support_count = support_count + 1,support_amount = support_amount + ".$order_info['deal_price'].",pay_amount = pay_amount + ".$order_info['total_price'].",delivery_fee_amount = delivery_fee_amount + ".$order_info['delivery_fee']." ,share_fee_amount = share_fee_amount + ".$order_info['share_fee']." where id = ".$order_info['deal_id']." and is_effect = 1 and is_delete = 0 and begin_time < ".TIME_UTC." and (pay_end_time > ".TIME_UTC." or pay_end_time = 0)");
		}else{
			$GLOBALS['db']->query("update ".DB_PREFIX."project set support_count = support_count + 1,support_amount = support_amount + ".$order_info['deal_price'].",pay_amount = pay_amount + ".$order_info['total_price'].",delivery_fee_amount = delivery_fee_amount + ".$order_info['delivery_fee']." ,share_fee_amount = share_fee_amount + ".$order_info['share_fee']." where id = ".$order_info['deal_id']." and is_effect = 1 and is_delete = 0 and begin_time < ".TIME_UTC." and (end_time > ".TIME_UTC." or end_time = 0)");
		}
		if($GLOBALS['db']->affected_rows()>0)
		{
			//记录支持日志
			$support_log['deal_id'] = $order_info['deal_id'];
			$support_log['user_id'] = $order_info['user_id'];
			$support_log['create_time'] = TIME_UTC;
			$support_log['price'] = $order_info['deal_price'];
			$support_log['deal_item_id'] = $order_info['deal_item_id'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."project_support_log",$support_log);
			$support_log_id = intval($GLOBALS['db']->insert_id());
			
			
			$GLOBALS['db']->query("update ".DB_PREFIX."project_item set support_count = support_count + 1,support_amount = support_amount +".$order_info['deal_price']." where (support_count + 1 <= limit_user or limit_user = 0) and id = ".$order_info['deal_item_id']);
			if($GLOBALS['db']->affected_rows()>0||($order_info['type']==1))
			{
				$result['status'] = 3;
				$order_info['order_status'] = 3;	

 				$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id = ".$order_info['deal_id']." and is_effect = 1 and is_delete = 0");
				//下单项目成功，准备加入准备队列
				if($deal_info['is_success'] == 0)
				{
					//未成功的项止准备生成队列
					/*$notify['user_id'] = $GLOBALS['user_info']['id'];
					$notify['deal_id'] = $deal_info['id'];
					$notify['create_time'] = TIME_UTC;
					$GLOBALS['db']->autoExecute(DB_PREFIX."user_deal_notify",$notify,"INSERT","","SILENT");
					*/
					//send_user_msg("回报发放通知",$param["content"],0,$order_info['user_id'],TIME_UTC,0,true,0);
				} 
				//发送信息 暂时取消
				//send_paid_msg(array('user_id'=> $order_info['user_id'],'money'=> $order_info['total_price'],'order_id'=>$order_info['id']));
				//$GLOBALS['msg']->manage_msg('MSG_PAID',$order_info['user_id'],array('money'=> $order_info['total_price'],'order_id'=>$order_info['id']));
				
				
				//更新用户的支持数
				$GLOBALS['db']->query("update ".DB_PREFIX."user set support_count = support_count + 1 where id = ".$order_info['user_id']);
				//同步deal_log中的deal_info_cache
				
				$GLOBALS['db']->query("update ".DB_PREFIX."project_log set deal_info_cache = '' where deal_id = ".$deal_info['id']);
				$GLOBALS['db']->query("update ".DB_PREFIX."project set deal_extra_cache = '' where id = ".$deal_info['id']);
				
				/*if($order_info['type']==1){
					$GLOBALS['db']->query("update ".DB_PREFIX."investment_list set investor_money_status=3 where order_id=".$order_info['id']);
				}*/
				
				
				$GLOBALS['db']->query("update ".DB_PREFIX."project_order set order_status = ".intval($order_info['order_status']).",pay_time = ".$order_info['pay_time'].",is_refund = ".$order_info['is_refund']." where id = ".$order_info['id']);	
				
				//同步项目状态
				syn_project_status($order_info['deal_id']);
							
				syn_project($order_info['deal_id']);
				
				//发放返利 
				if($user['pid'] > 0)
				{
					get_referrals($order_info['id'],1,$user,$order_info['id']);
				}
				//	send_buy_referrals($user,$order_info['id']);//$user_info 会员信息 要传入id,pid,user_name,referral_count
				
				//发放积分与信用值
				$score_multiple=floatval(app_conf("BUY_PRESEND_SCORE_MULTIPLE"));
				$point_multiple=floatval(app_conf("BUY_PRESEND_POINT_MULTIPLE"));
				$score_point=array("score_multiple"=>$score_multiple,"point_multiple"=>$point_multiple);
				$score_point=serialize($score_point); 
				$GLOBALS['db']->query("update ".DB_PREFIX."project_order set sp_multiple = '".$score_point."' where id = ".$order_info['id']);
				if($score_multiple >0)
				{
					$score=intval($order_info['total_price']*$score_multiple);
					$log_info=$order_info['deal_name']."购买成功,积分增加".$score;
					modify_account(array("score"=>$score),$order_info['user_id'],$log_info,50);
				}
				if($point_multiple >0)
				{
					$point=intval($order_info['total_price']*$point_multiple);
					$log_info=$order_info['deal_name']."购买成功,信用值增加".$point;
					modify_account(array("point"=>$point),$order_info['user_id'],$log_info,50);
				}
					
			}
			else
			{
				$result['status'] = 2;
				$order_info['order_status'] = 2;
				$order_info['is_refund'] =1;
				$GLOBALS['db']->query("update ".DB_PREFIX."project set support_count = support_count - 1,support_amount = support_amount - ".$order_info['deal_price'].",pay_amount = pay_amount - ".$order_info['total_price'].",delivery_fee_amount = delivery_fee_amount - ".$order_info['delivery_fee']." ,share_fee_amount = share_fee_amount - ".$order_info['share_fee']." where id = ".$order_info['deal_id']);
				$GLOBALS['db']->query("delete from ".DB_PREFIX."project_support_log where id = ".$support_log_id);
				modify_account(array("money"=>($order_info['online_pay']+$order_info['credit_pay'])),$order_info['user_id'],$order_info['deal_name']."限额已满，转存入会员帐户",52);
				//退回积分
				if($order_info['score'] >0)
 				{
 					$log_score=$order_info['deal_name']."限额已满，退回".$order_info['score']."积分";
					modify_account(array("score"=>$order_info['score']),$order_info['user_id'],$log_score,52);
 				}
				
			}
		}
		else
		{
			$result['status'] =1;
			$order_info['order_status'] =1;
			$order_info['is_refund'] =1;
			modify_account(array("money"=>($order_info['online_pay']+$order_info['credit_pay'])),$order_info['user_id'],$order_info['deal_name']."已过期，转存入会员帐户",52);
			//退回积分
			if($order_info['score'] >0)
			{
				$log_score=$order_info['deal_name']."限额已满，退回".$order_info['score']."积分";
				modify_account(array("score"=>$order_info['score']),$order_info['user_id'],$log_score,52);
			}
			
			
		}
		$GLOBALS['db']->query("update ".DB_PREFIX."project_order set order_status = ".intval($order_info['order_status']).",pay_time = ".$order_info['pay_time'].",is_refund = ".$order_info['is_refund']." where id = ".$order_info['id']);
		
	}
	else
	{
		$result['status'] = 0;
		$result['money'] = $order_info['total_price'] - $order_info['score_money']-$order_info['credit_pay']-$order_info['online_pay'];
	}
	return $result;
}
function app_redirect_preview()
{
	app_redirect(get_gopreview());
}	
 //更新当前项目的姿态
function set_project_status($deal){
   	$now_time=TIME_UTC;
 	if($deal['end_time']<$now_time){
 		$GLOBALS['db']->query("update ".DB_PREFIX."investment_list set investor_money_status=2 where deal_id=".$deal['id']." and investor_money_status=0 ");
 	}
 	if($deal['pay_end_time']<$now_time){
 		$GLOBALS['db']->query("update ".DB_PREFIX."investment_list set investor_money_status=4 where deal_id=".$deal['id']." and investor_money_status=1 ");
 	}
} 

//跟投、领投信息列表
function get_investor_info($deal_id,$type=''){
	if(!$GLOBALS['user_info'])
	{
		//app_redirect(url("user#login"));
	}
 	if($deal_id>0){
		if($type==1){
			//分页
			require APP_ROOT_PATH.'app/Lib/page.php';
			$page_size = 10;
			$page = intval($_REQUEST['p']);
			if($page==0)$page = 1;
			$limit = (($page-1)*$page_size).",".$page_size;
			//跟投信息(所有)
			$enquiry_info_list=$GLOBALS['db']->getAll("select i.*,u.user_name,u.user_level,u.is_investor from ".DB_PREFIX."investment_list i LEFT JOIN ".DB_PREFIX."user as u on u.id=i.user_id where i.deal_id=".$deal_id." and i.type=2    ORDER BY i.create_time DESC limit $limit");
			
			$user_level_array = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_level order by id asc");
			$user_level = array();
			foreach($user_level as $k => $v)
			{
				$user_level[$k] = $v;
			}
			
			foreach ($enquiry_info_list as $k=>$v){
				$enquiry_info_list[$k]['money']=number_format(($v['money']/10000),2);
				$enquiry_info_list[$k]['user_icon'] =$user_level[$v['user_level']]['icon'];//用户等级图标
			}
			//跟投信息(统计)
			$enquiry_count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."investment_list i where i.deal_id=".$deal_id." and i.type=2");
			$page = new Page($enquiry_count,$page_size);   //初始化分页对象
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign("enquiry_count",$enquiry_count);
			$GLOBALS['tmpl']->assign("enquiry_info_all",$enquiry_info_list);
		}else{
			//跟投信息(4条)
			$enquiry_info=$GLOBALS['db']->getAll("select i.*,u.user_name,u.user_level,u.is_investor from ".DB_PREFIX."investment_list i LEFT JOIN ".DB_PREFIX."user as u on u.id=i.user_id where i.deal_id=".$deal_id." and i.type=2   ORDER BY i.create_time DESC LIMIT 0,4");
			foreach ($enquiry_info as $k=>$v){
				$enquiry_info[$k]['money']=number_format(($v['money']/10000),2); 
				$enquiry_info[$k]['user_icon'] =$user_level[$v['user_level']]['icon'];//用户等级图标
			}
			$GLOBALS['tmpl']->assign("enquiry_info",$enquiry_info);
		}
		$GLOBALS['tmpl']->assign('type',intval($type));
		//领投信息
		$leader_info=$GLOBALS['db']->getRow("select i.*,u.user_name,u.identify_name,u.user_level,u.is_investor from ".DB_PREFIX."investment_list i LEFT JOIN ".DB_PREFIX."user as u on u.id=i.user_id where i.deal_id=".$deal_id." and i.type=1 and status=1 GROUP BY i.user_id,i.user_id ORDER BY i.user_id DESC");
		$leader_info['user_icon'] =$user_level[$leader_info['user_level']]['icon'];//用户等级图标
		if($leader_info>0){
			
			if($leader_info['leader_moban']){
				$leader_info['leader_moban_y']=urlencode(unserialize($leader_info['leader_moban']));
				$leader_info['leader_info_name']=substr(strrchr(unserialize($leader_info['leader_moban']), '.'), 1);
			 	switch($leader_info['leader_info_name']){
			 		case 'txt':
			 		$leader_info['leader_info_exe']='leader_t';
			 		break;
			 		case 'doc':
			 		$leader_info['leader_info_exe']='leader_w';
			 		break;
			 		case 'docx':
			 		$leader_info['leader_info_exe']='leader_w';
			 		break;
			 		case 'rar':
			 		$leader_info['leader_info_exe']='leader_r';
			 		break;
			 		case 'zip':
			 		$leader_info['leader_info_exe']='leader_r';
			 		break;
			 		
			 		case 'xls':
			 		$leader_info['leader_info_exe']='leader_x';
			 		break;
			 		case 'xlsx':
			 		$leader_info['leader_info_exe']='leader_x';
			 		break;
			 		case 'ppt':
			 		$leader_info['leader_info_exe']='leader_p';
			 		break;
			 	}
			}
			$leader_info['money']=number_format(($leader_info['money']/10000),2);
			$GLOBALS['tmpl']->assign("leader_info",$leader_info);
		}	
	}
}

/**
 * $user_level_id 会员等级id
 * $is_cut 是否要裁切 默认不裁切
 * $width 图片宽度
 * $height 图片高度
 * $width，$height 都不传输入或都为零，默认是16px*17px
 * */
function get_user_lever_icon($user_level_id,$is_cut=false,$width=0,$height=0)
{
	$width=intval($width);
	$height=intval($height);
	$user_level=load_auto_cache("user_level");
	$user_level_icon=$user_level[$user_level_id]['icon'];
	if($is_cut)
	{
		if($width <=0 && $height <= 0)
			return get_spec_image($user_level_icon,16,17);
		else
			return get_spec_image($user_level_icon,$width,$height);
	}else{
		return $user_level_icon;
	}
}


//得到用户自己所有（有效）的项目列表信息
function get_effective_project_info($id){
	if($id>0){
		$condition="user_id=".$id." AND is_effect=1 AND is_delete=0 AND begin_time<".TIME_UTC." AND ".TIME_UTC."<end_time ORDER BY id DESC";
		$effective_deal_info=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."project where ".$condition);
		return $effective_deal_info;
	}
}

//同步到微博
function syn_weibo($data)
{
	$api_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."api_login where is_weibo = 1");
	foreach($api_list as $k=>$v)
	{
		if($GLOBALS['user_info'][strtolower($v['class_name'])."_id"]==""||$GLOBALS['user_info'][strtolower($v['class_name'])."_token"]=="")
		{
			unset($api_list[$k]);
		}
		else
		{
			$class_name = $v['class_name']."_api";
			require_once APP_ROOT_PATH."system/api_login/".$class_name.".php";
			$o = new $class_name($v);
			$o->send_message($data);
		}
	}
}

//积分转成余额
function score_to_money($score)
{
	$score_array=array();
	$score_trade_number=intval(app_conf("SCORE_TRADE_NUMBER"))>0?intval(app_conf("SCORE_TRADE_NUMBER")):0;
	$score_array['score_money']=floatval(intval($score/$score_trade_number*100)/100);
	$score_array['score']=intval($score_trade_number*$score_array['score_money']);
	return $score_array;
}

function get_project_list($limit="",$conditions="",$orderby=" d.sort asc ",$deal_type='project',$is_pc = 1){
	
	
	if($limit!=""){
		$limit = " LIMIT ".$limit;
	}
	
	if($orderby!=""){
		$orderby = " ORDER BY ".$orderby;
	}
	
	$condition = " d.is_delete = 0 AND d.is_effect = 1 ";
	
	if($conditions!=""){
		$condition.=" AND ".$conditions;
	}

	//权限浏览控制
 
 	$deal_count = $GLOBALS['db']->getOne("select count(*)  from ".DB_PREFIX."project as d where ".$condition);
	
 	/*（所需项目）准备虚拟数据 start*/
	$deal_list = array();
	$level_list=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_level ");
	$level_list_array=array();
	foreach($level_list_array as $k=>$v){
		if($v['id']){
			$level_list_array[$v['id']]=$v['point'];
		}
	}
	
 	if($deal_count > 0){
		$now_time = TIME_UTC;
		$deal_list = $GLOBALS['db']->getAll("select d.* from ".DB_PREFIX."project  as d where ".$condition." GROUP BY d.id ".$orderby.$limit);

 		//file_put_contents("condition.txt", print_r("select d.* from ".DB_PREFIX."deal  as d   where ".$condition.$orderby.$limit,1));
		$deal_ids = array();
		foreach($deal_list as $k=>$v)
		{
			$deal_list[$k]['remain_days'] = ceil(($v['end_time'] - $now_time)/(24*3600));
			if($v['begin_time'] > $now_time){
				$deal_list[$k]['left_days'] = ceil(($v['begin_time'] - $now_time) / 24 / 3600);
			}
			$deal_list[$k]['num_days'] = ceil(($v['end_time'] - $v['begin_time'])/(24*3600));
			$deal_ids[] =  $v['id'];
			//查询出对应项目id的user_level
			$deal_list[$k]['deal_level']=$level_list_array[intval($deal_list[$k]['user_level'])];
			if($v['begin_time'] > $now_time){
				$deal_list[$k]['left_begin_days'] = intval(($v['begin_time']  - $now_time) / 24 / 3600);
				$deal_list[$k]['left_begin_day'] = intval(($v['begin_time']  - $now_time));
				$deal_list[$k]['status']= '0';                                 
			}
			elseif($v['end_time'] < $now_time && $v['end_time']>0){
				if($deal_list[$k]['percent'] >=100){
					$deal_list[$k]['status']= '1';  
				}
				else{
						$deal_list[$k]['status']= '2'; 
				}
			} 
			else{
					if ($v['end_time'] > 0) {
						$deal_list[$k]['status']= '3'; 
					}
					else
					$deal_list[$k]['status']= '4'; 
			}
			
			if($v['type']==1){
				$deal_list[$k]['virtual_person']=$deal_list[$k]['invote_num'];
				$deal_list[$k]['support_count'] =$deal_list[$k]['invote_num'];
				$deal_list[$k]['support_amount'] =$deal_list[$k]['invote_money'];
				$deal_list[$k]['percent'] = round(($deal_list[$k]['support_amount'])/$v['limit_price']*100,2);
				$deal_list[$k]['limit_price_w']=($deal_list[$k]['limit_price'])/10000;
				$deal_list[$k]['invote_mini_money_w']=number_format(($deal_list[$k]['invote_mini_money'])/10000,2);				
				//$deal_list[$k]['bonus_count']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_bonus where status = 1 and deal_id =".$v['id']);
				
			}else{
				$deal_list[$k]['virtual_person']=$deal_list[$k]['virtual_num'];
				$deal_list[$k]['support_count'] =$deal_list[$k]['virtual_num']+$deal_list[$k]['support_count'];
				$deal_list[$k]['support_amount'] =$deal_list[$k]['virtual_price']+$deal_list[$k]['support_amount'];
				$deal_list[$k]['percent'] = round(($deal_list[$k]['support_amount'])/$v['limit_price']*100,2);
 			}
 			if($deal_type=='deal_cate'||$deal_type=='deal_cate_preheat'){
 				$deal_list[$k]['user_info']=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."user where id=".$v['user_id']);
				$deal_list[$k]['deal_comment_num']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_comment where deal_id = ".$v['id']." and log_id = 0 and status=1 ");
				$deal_list[$k]['deal_comment_num']=intval($deal_list[$k]['deal_comment_num']);
				$deal_list[$k]['cate_name']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."project_cate where id=".$v['cate_id']);
  				if($deal_type=='deal_cate_preheat'){
  					//关注
  					$deal_list[$k]['focus_num']=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_comment where deal_id = ".$v['id']." and log_id = 0 and status=1 ");
   				}
  			}
			if($is_pc == 0)
			{
				$deal_list[$k]["image"] = get_abs_url_root($v["image"]);
			}
		}
 	 
	}
	
	return array("rs_count"=>$deal_count,"list"=>$deal_list);
}

function init_project_page_wap($deal_info)
{
	$root["page_title"]=$deal_info['name'];
	if($deal_info['seo_title']!="")
	$root["seo_title"]=$deal_info['seo_title'];
	if($deal_info['seo_keyword']!="")
	$root["seo_keyword"]=$deal_info['seo_keyword'];
	if($deal_info['seo_description']!="")
	$root["seo_description"]=$deal_info['seo_description'];
	$type_array=array();
	//开启限购后剩余几位
	$deal_info['deal_item_count']=0;
	foreach ($deal_info['deal_item_list'] as $k=>$v){
			// 统计所有真实+虚拟（钱）
			$deal_info['total_virtual_person']+= $v['virtual_person'];
			$deal_info['total_virtual_price']+=$v['price'] * $v['virtual_person']+$v['support_amount'];
 			//统计每个子项目真实+虚拟（钱）
 			$deal_info['deal_item_list'][$k]['person']=$v['virtual_person']+$v['support_count'];
 			$deal_info['deal_item_list'][$k]['money']=$v['price'] * $v['virtual_person']+$v['support_amount'];
			$deal_info['deal_item_list'][$k]['cart_url']=wap_url("index","project_cart",array("id"=>$v['id']));
			$deal_info['deal_item_list'][$k]['price_format']= format_price($v["price"]);
			
			if($v['limit_user']){
				$deal_info['deal_item_list'][$k]['remain_person']=$v['limit_user']-$v['virtual_person']-$v['support_count'];
			}
			
			$deal_info['deal_item_count']++;
			if($v['type']==1){
  				$type_array[]=$v;
 				unset($deal_info['deal_item_list'][$k]);
 			}
			
			
		}
	if($type_array){
		$deal_info['deal_item_list']=array_merge($deal_info['deal_item_list'],$type_array);
	}
//	$deal_info['deal_type']=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."project_cate where id=".$deal_info['cate_id']);
	$deal_info['tags_arr'] = preg_split("/[ ,]/",$deal_info['tags']);
	
	$deal_info['support_amount_format'] = format_price($deal_info['support_amount']);
	$deal_info['limit_price_format'] = format_price($deal_info['limit_price']);
 	$deal_info['total_virtual_price_format']=format_price($deal_info['total_virtual_price']);
	$deal_info['remain_days'] = ceil(($deal_info['end_time'] - TIME_UTC)/(24*3600));
	$deal_info['percent'] = round($deal_info['support_amount']/$deal_info['limit_price']*100,2);
	
	//$deal_info['deal_level']=$GLOBALS['db']->getOne("select level from ".DB_PREFIX."project_level where id=".intval($deal_info['user_level']));
	$deal_info['person']=$deal_info['total_virtual_person']+$deal_info['support_count'];
	$deal_info['percent']=round(($deal_info['total_virtual_price']/$deal_info['limit_price'])*100,2);
	
	$deal_info['update_url']=wap_url("index","project_update#index",array("id"=>$deal_info['id']));
	$deal_info['comment_url']=wap_url("index","project_comment#index",array("id"=>$deal_info['id']));
	$deal_info['info_url']=wap_url("index","project_info#index",array("id"=>$deal_info['id']));
	
 
	if($deal_info['begin_time'] > TIME_UTC){
		$deal_info['status']= '0';  
		$deal_info['left_days']  = ceil(($deal_info['begin_time'] - TIME_UTC)/(24*3600));                               
	}
	elseif($deal_info['end_time'] < TIME_UTC && $deal_info['end_time']>0){
		if($deal_info['percent'] >=100){
			$deal_info['status']= '1';  
		}
		else{
				$deal_info['status']= '2'; 
		}
	} 
	else{
			if ($deal_info['end_time'] > 0) {
				$deal_info['status']= '3'; 
			}
			else
				$deal_info['status']= '4'; 
	}
	if($GLOBALS['user_info'])
	{
		$is_focus = $GLOBALS['db']->getOne("select  count(*) from ".DB_PREFIX."project_focus_log where deal_id = ".$deal_info['id']." and user_id = ".intval($GLOBALS['user_info']['id']));
		$root["is_focus"]=$is_focus;
	}
	if($deal_info['user_id']>0)
	{
		$deal_info["user_info"] = $GLOBALS['db']->getRow("select id,user_name,province_id,city_id,login_time from ".DB_PREFIX."user where id = ".$deal_info['user_id']." and is_effect = 1");
		
	}
	
	if(!empty($deal_info['vedio'])&&!preg_match("/http://player.youku.com/embed/i",$deal_info['source_video'])){
 		$deal_info['source_vedio']= preg_replace("/id_(.*)\.html(.*)/i","http://player.youku.com/embed/\${1}",baseName($deal_info['vedio'])); 
  		$GLOBALS['db']->query("update ".DB_PREFIX."project set source_vedio='".$deal_info['source_vedio']."'  where id=".$deal_info['id']);
  	}
	
	return $deal_info;
}

//获取用户头像的文件名
function get_user_avatar_wap($id,$type)
{
	$uid = sprintf("%09d", $id);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$path = $dir1.'/'.$dir2.'/'.$dir3;
				
	$id = str_pad($id, 2, "0", STR_PAD_LEFT); 
	$id = substr($id,-2);
	$avatar_file = APP_ROOT."/public/avatar/".$path."/".$id."virtual_avatar_".$type.".jpg";
	$avatar_check_file = APP_ROOT_PATH."public/avatar/".$path."/".$id."virtual_avatar_".$type.".jpg";
	if(file_exists($avatar_check_file))	
	return $avatar_file;
	else
	return APP_ROOT."/public/avatar/noavatar_".$type.".gif";
	//@file_put_contents($avatar_check_file,@file_get_contents(APP_ROOT_PATH."public/avatar/noavatar_".$type.".gif"));
}


?>