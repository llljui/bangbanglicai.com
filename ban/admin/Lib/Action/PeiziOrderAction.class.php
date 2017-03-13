<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require_once APP_ROOT_PATH."/system/libs/peizi.php";
class PeiziOrderAction extends CommonAction{
	public function com_search(){
		$map = array ();
	
	
		if (isset($_REQUEST['end_time']) && $_REQUEST['end_time'] != '') {
			$map['end_time'] = trim($_REQUEST['end_time']);			
			$this->assign("end_time",$map['end_time']);
		}
		
		
		if (isset($_REQUEST['start_time']) && $_REQUEST['start_time'] != '') {
			$map['start_time'] = trim($_REQUEST['start_time']);
			$this->assign("start_time",$map['start_time']);
		}
		

		$op_type = -1;
		if (isset($_REQUEST['op_type']) && $_REQUEST['op_type'] != '') {
			$op_type = intval($_REQUEST['op_type']);
		}
		
		$map['op_type'] = $op_type;
		//配资类型;0:天;1周；2月
		$peizi_conf_id = -1;
		if (isset($_REQUEST['peizi_conf_id']) && $_REQUEST['peizi_conf_id'] != '') {
			$peizi_conf_id = intval($_REQUEST['peizi_conf_id']);
		}
		
		$map['peizi_conf_id'] = $peizi_conf_id;	

		
		if (isset($_REQUEST['invest_user_name']) && $_REQUEST['invest_user_name'] != '') {
			$map['invest_user_id'] = intval($_REQUEST['invest_user_id']);	
		}		
		
		$map['invest_user_name'] = $_REQUEST['invest_user_name'];
		
		$this->assign("invest_user_id",$map['invest_user_id']);
		$this->assign("invest_user_name",$map['invest_user_name']);
		
		
		$this->assign("op_type",$op_type);
		$this->assign("peizi_conf_id",$peizi_conf_id);
		
		return $map;
	}
		
	function op_base($map,$where = ''){
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		/*
		$sql_str = "select pc.name as conf_type_name,a.*,AES_DECRYPT(a.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd, u.user_name, AES_DECRYPT(u.money_encrypt,'".AES_DECRYPT_KEY."') as user_money, m.adm_name, a.cost_money+a.borrow_money as total_money
					 from ".DB_PREFIX."peizi_order a
					LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = a.peizi_conf_id
					LEFT JOIN ".DB_PREFIX."admin m on m.id = a.admin_id  where 1 = 1 ";
		*/
		$sql_str = "select pc.name as conf_type_name,a.*,AES_DECRYPT(a.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd, u.user_name, AES_DECRYPT(u.money_encrypt,'".AES_DECRYPT_KEY."') as user_money, m.adm_name, a.cost_money+a.borrow_money as total_money
					,iu.user_name as invest_user_name from ".DB_PREFIX."peizi_order a
					LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
					LEFT JOIN ".DB_PREFIX."user iu on iu.id = a.invest_user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = a.peizi_conf_id
					LEFT JOIN ".DB_PREFIX."admin m on m.id = a.admin_id  where 1 = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> ''){
			$sql_str .= " and a.create_date >= '".$map['start_time']."'";
		}
		
		if( isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and a.create_date <= '".$map['end_time']."'";
		}
		
		if ($map['peizi_conf_id'] != -1){
			$sql_str .= " and a.peizi_conf_id = '".$map['peizi_conf_id']."'";
		}
		
		
		
		if ($map['status'] != ''){
			$sql_str .= " and a.status in (".$map['status'].")";
		}
		
		/*
		//最后(近)一次扣费日期
		if( isset($map['last_fee_date']) && $map['last_fee_date'] <> ''){
			$sql_str .= " and a.last_fee_date = '".$map['last_fee_date']."'";
		}		
		*/

		
		if( isset($map['fee_start_date']) && $map['fee_start_date'] <> ''){
			$sql_str .= " and a.last_fee_date >= '".$map['fee_start_date']."'";
		}
		
		if( isset($map['fee_end_date']) && $map['fee_end_date'] <> ''){
			$sql_str .= " and a.last_fee_date <= '".$map['fee_end_date']."'";
		}
		
		//预计操作结束时间
		/*
		if( isset($map['end_date']) && $map['end_date'] <> ''){
			$sql_str .= " and a.end_date in (".$map['end_date'].")";
		}*/
		
		if( isset($map['end_start_date']) && $map['end_start_date'] <> ''){
			$sql_str .= " and a.end_date >= '".$map['end_start_date']."'";
		}
		
		if( isset($map['end_end_date']) && $map['end_end_date'] <> ''){
			$sql_str .= " and a.end_date <= '".$map['end_end_date']."'";
		}
						
		//1:自动继费失败  
		if( isset($map['is_arrearage']) && $map['is_arrearage'] <> ''){
			$sql_str .= " and a.is_arrearage = ".$map['is_arrearage']."";
		}

		//亏损警戒线
		if( isset($map['warning_line'])){
			$sql_str .= " and a.stock_money <= a.warning_line and a.stock_money > a.open_line ";
		}
		
		//亏损平仓线
		if( isset($map['open_line'])){
			$sql_str .= " and a.stock_money <= a.open_line ";
		}
		
		if( isset($map['next_start_date']) && $map['next_start_date'] <> ''){
			$sql_str .= " and a.next_fee_date >= '".$map['next_start_date']."'";
		}
		
		if( isset($map['next_end_date']) && $map['next_end_date'] <> ''){
			$sql_str .= " and a.next_fee_date <= '".$map['next_end_date']."'";
		}
		
		if( isset($map['invest_user_id']) && $map['invest_user_id'] > 0){
			$sql_str .= " and a.invest_user_id = '".$map['invest_user_id']."'";
		}
		
		
		$sql_str .= $where;
		
		$is_export = intval($_REQUEST['is_export']);
		
		if ($is_export == 0){
			$model = D();
			//print_r($map);
			//echo $sql_str;
			$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'a.create_time', false);
			//print_r($model->getLastSql());
			//exit;
			foreach ($voList as $k => $v) {
				//配资类型 
				$voList[$k] = get_peizi_order_fromat($v);
				if($v["rate_money"] == 0)
				{
					$voList[$k]["last_fee_date"] = $v["next_fee_date"];
				}
			}
		
		
			
			$this->assign('list', $voList);
		}else{
			set_time_limit(0);
			
			$list = $GLOBALS['db']->getAll($sql_str);
			//register_shutdown_function(array(&$this, ACTION_NAME), $page+1);
			$title = get_peizi_title();
			
			$data = array();
			$data[] = $title;
			foreach ($list as $k => $v) {
				$item = get_peizi_order_fromat($v);
				
				$vo = array();
				foreach ($title as $k => $v) {
					$vo[$k] = $item[$k];
				}
				
				$data[] = $vo;
			}
			
			$filename = "peizi_export";
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename={$filename}.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			
			outputCSV($data);
			exit;
		}
	}
	
	
	//待审核
	public function pzop1(){
		$map =  $this->com_search();
		$map['status'] = '1';
		$this->op_base($map);
	
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
	
		$this->assign("main_title","待审核");
		$this->display("index");
	}
	
	//待筹款
	public function pzop2(){
		$map =  $this->com_search();
		$map['status'] = '2';
		$this->op_base($map);
	
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
	
		$this->assign("main_title","待筹款");
		$this->display("index");
	}
	
	//待开户
	public function pzop4(){
		$map =  $this->com_search();
		$map['status'] = '4';
		$this->op_base($map);
	
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
	
		$this->assign("main_title","待开户");
		$this->display("index");
	}

	//审核失败
	public function pzop357(){
		$map =  $this->com_search();
		$map['status'] = '3,5,7';
		$this->op_base($map);
	
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
	
		$this->assign("main_title","审核失败");
		$this->display("index");
	}

	//操盘中
	public function pzop6(){
		$map =  $this->com_search();
		$map['status'] = '6';
		$this->op_base($map);
	
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
		
		$this->assign("main_title","操盘中");
		$this->display("pzop6");
	}
		
	
	//历史实盘: 8
	public function pzop8(){
		$map =  $this->com_search();
		$map['status'] = '8';
		
		if (!isset($_REQUEST['end_start_date']) || $_REQUEST['end_start_date'] == '') {
			$end_start_date = dec_date(to_date(TIME_UTC, 'Y-m-d'),7);
		}else{
			$end_start_date = $_REQUEST['end_start_date'];
		}
		
		if (!isset($_REQUEST['end_end_date']) || $_REQUEST['end_end_date'] == '') {
			$end_end_date = to_date(TIME_UTC, 'Y-m-d');
		}else{
			$end_end_date = $_REQUEST['end_end_date'];
		}
		
		$map['end_start_date'] = $end_start_date;
		$map['end_end_date'] = $end_end_date;
		
		$this->assign("end_start_date",$end_start_date);
		$this->assign("end_end_date",$end_end_date);
		
		$this->op_base($map);
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);	
	
		$this->assign("main_title","历史实盘");
		$this->display("pzop8");//$this->display("op5");
	}
	
	
	//今日扣费
	public function fee_date(){
		
		$map = array();
		$peizi_conf_id = -1;
		if (isset($_REQUEST['peizi_conf_id']) && $_REQUEST['peizi_conf_id'] != '') {
			$peizi_conf_id = intval($_REQUEST['peizi_conf_id']);
		}
		
		$map['peizi_conf_id'] = $peizi_conf_id;
		$this->assign("peizi_conf_id",$peizi_conf_id);
		
		//$map =  $this->com_search();
		$map['status'] = '6,8';
		//$map['last_fee_date'] = to_date(TIME_UTC,'Y-m-d');
		//print_r($_REQUEST);
		if (!isset($_REQUEST['fee_start_date']) || $_REQUEST['fee_start_date'] == '') {
			$map['fee_start_date'] = to_date(TIME_UTC, 'Y-m-d');
		}else{
			$map['fee_start_date'] = $_REQUEST['fee_start_date'];
		}
		
		if (!isset($_REQUEST['fee_end_date']) || $_REQUEST['fee_end_date'] == '') {
			$map['fee_end_date'] = to_date(TIME_UTC, 'Y-m-d');
		}else{
			$map['fee_end_date'] = $_REQUEST['fee_end_date'];
		}		
		
		$this->assign("fee_start_date",$map['fee_start_date']);
		$this->assign("fee_end_date",$map['fee_end_date']);
		
		
		//peizi_order_fee_list
		$sql_str = "select pc.name as conf_type_name, o.order_sn, o.next_fee_date, a.*, u.user_name,iu.user_name as invest_user_name from ".DB_PREFIX."peizi_order_fee_list a
					LEFT JOIN ".DB_PREFIX."peizi_order o on o.id = a.peizi_order_id
					LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
					LEFT JOIN ".DB_PREFIX."user iu on iu.id = a.invest_user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = o.peizi_conf_id
					  where a.has_pay = 1 ";
		
		if ($map['peizi_conf_id'] != -1){
			$sql_str .= " and o.peizi_conf_id = '".$map['peizi_conf_id']."'";
		}
		
		
		
		if ($map['status'] != ''){
			$sql_str .= " and o.status in (".$map['status'].")";
		}
		
		/*
		 //最后(近)一次扣费日期
		if( isset($map['last_fee_date']) && $map['last_fee_date'] <> ''){
		$sql_str .= " and a.last_fee_date = '".$map['last_fee_date']."'";
		}
		
		*/
		
		if( isset($map['fee_start_date']) && $map['fee_start_date'] <> ''){
			$sql_str .= " and a.create_date >= '".$map['fee_start_date']."'";
		}
		
		if( isset($map['fee_end_date']) && $map['fee_end_date'] <> ''){
			$sql_str .= " and a.create_date < '".dec_date($map['fee_end_date'], -1) ."'";
		}
		
		//echo $sql_str;exit;
		
		//$this->op_base($map);
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$is_export = intval($_REQUEST['is_export']);		
		if ($is_export == 0){
			$model = D();
			//print_r($map);
			//echo $sql_str;
			$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'a.fee_date', false);
			$this->assign('list', $voList);
			
			
			$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
			$this->assign("type_list",$type_list);	
		
			$this->assign("main_title","今日扣费");
			$this->display("fee_date");
			
		}else{
			set_time_limit(0);
				
			$list = $GLOBALS['db']->getAll($sql_str);
			//register_shutdown_function(array(&$this, ACTION_NAME), $page+1);
			$title = get_peizi_title();
				
			$data = array();
			$data[] = $title;
			foreach ($list as $k => $v) {
				$item = get_peizi_order_fromat($v);
		
				$vo = array();
				foreach ($title as $k => $v) {
					$vo[$k] = $item[$k];
				}
		
				$data[] = $vo;
			}
				
			$filename = "peizi_export";
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename={$filename}.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
				
			outputCSV($data);
			exit;
		}
	}
	
	//补单	
	public function append_trade_list(){
		
		$cur_date = to_date(TIME_UTC,"Y-m-d");
		
		$sql_str = "select * from ".DB_PREFIX."peizi_order where type = 0 and rate_type = 1 and status = 6 and next_fee_date <= '".$cur_date."' order by next_fee_date asc";
		$peizi_order_list = $GLOBALS['db']->getAll($sql_str);
		
		foreach ($peizi_order_list as $k => $v) {
			$order_id = $v['id'];

			$stock_date = $v['next_fee_date'];
			
			while ($stock_date <= $cur_date){
			
				$sql = "select id from ".DB_PREFIX."peizi_order_stock_money where peizi_order_id = ".$order_id." and stock_date = '".$stock_date."'";
				
				$id = intval($GLOBALS['db']->getOne($sql));			
				
				if ($id == 0){
					$stock = array();
					$stock['peizi_order_id'] = $order_id;
					$stock['stock_date'] = $stock_date;
					$stock['warning_line'] = $v['warning_line'];
					$stock['open_line'] = $v['open_line'];
										
					//周末节假日免费;type=0时有效;0:不免费;1:免费
					if ($v['is_holiday_fee'] == 0){
						//取上次的记录
						$sql = "select stock_money from ".DB_PREFIX."peizi_order_stock_money where peizi_order_id = ".$order_id." and stock_date < '".$stock_date."' limit 1";
						$stock_money = $GLOBALS['db']->getOne($sql);
						
						
						$stock['stock_money'] = $stock_money;
					}
					
					$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_stock_money",$stock); //插入
				}
				
				$stock_date = get_peizi_end_date($stock_date, 1,$v['type'],$v['is_holiday_fee']);
				
			};
		}
		
		$this->success('补单完成',0);
	}
	
	//按实际交易金额扣费
	public function trade_fee_date(){
	
		$map = array();
		$peizi_conf_id = -1;
		if (isset($_REQUEST['peizi_conf_id']) && $_REQUEST['peizi_conf_id'] != '') {
			$peizi_conf_id = intval($_REQUEST['peizi_conf_id']);
		}
		
		$has_pay = -1;
		if (isset($_REQUEST['has_pay']) && $_REQUEST['has_pay'] != '') {
			$has_pay = intval($_REQUEST['has_pay']);
		}
				
	
		$map['has_pay'] = $has_pay;
		
		$map['peizi_conf_id'] = $peizi_conf_id;
		$this->assign("peizi_conf_id",$peizi_conf_id);
		$this->assign("has_pay",$has_pay);
		
		//$map =  $this->com_search();
		$map['status'] = '6,8';
		//$map['last_fee_date'] = to_date(TIME_UTC,'Y-m-d');
		//print_r($_REQUEST);
		if (!isset($_REQUEST['fee_start_date']) || $_REQUEST['fee_start_date'] == '') {
			$map['fee_start_date'] = to_date(TIME_UTC, 'Y-m-d');
		}else{
			$map['fee_start_date'] = $_REQUEST['fee_start_date'];
		}
	
		if (!isset($_REQUEST['fee_end_date']) || $_REQUEST['fee_end_date'] == '') {
			$map['fee_end_date'] = to_date(TIME_UTC, 'Y-m-d');
		}else{
			$map['fee_end_date'] = $_REQUEST['fee_end_date'];
		}
	
		$this->assign("fee_start_date",$map['fee_start_date']);
		$this->assign("fee_end_date",$map['fee_end_date']);
	
	
		//peizi_order_fee_list
		$sql_str = "select pc.name as conf_type_name, o.order_sn, o.next_fee_date,o.borrow_money,o.cost_money,o.borrow_money + o.cost_money as total_money, a.*, u.user_name from ".DB_PREFIX."peizi_order_stock_money a
					LEFT JOIN ".DB_PREFIX."peizi_order o on o.id = a.peizi_order_id
					LEFT JOIN ".DB_PREFIX."user u on u.id = o.user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = o.peizi_conf_id
					  where o.rate_type = 1 and o.type = 0 ";
	
		if ($map['peizi_conf_id'] != -1){
			$sql_str .= " and o.peizi_conf_id = '".$map['peizi_conf_id']."'";
		}
	
		if ($map['has_pay'] != -1){
			$sql_str .= " and a.has_pay = '".$map['has_pay']."'";
		}
			
	
		if ($map['status'] != ''){
			$sql_str .= " and o.status in (".$map['status'].")";
		}
	
		/*
		 //最后(近)一次扣费日期
		if( isset($map['last_fee_date']) && $map['last_fee_date'] <> ''){
		$sql_str .= " and a.last_fee_date = '".$map['last_fee_date']."'";
		}
	
		*/
	
		if( isset($map['fee_start_date']) && $map['fee_start_date'] <> ''){
			$sql_str .= " and a.stock_date >= '".$map['fee_start_date']."'";
		}
	
		if( isset($map['fee_end_date']) && $map['fee_end_date'] <> ''){
			$sql_str .= " and a.stock_date <= '".$map['fee_end_date']."'";
		}
	
		//echo $sql_str;exit;
	
		//$this->op_base($map);
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
	
		$is_export = intval($_REQUEST['is_export']);
		if ($is_export == 0){
			$model = D();
			//print_r($map);
			//echo $sql_str;
			$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'a.stock_date', true);
			$this->assign('list', $voList);
				
				
			$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
			$this->assign("type_list",$type_list);
	
			$this->assign("main_title","按实际成交金额扣费");
			$this->display("trade_fee_date");
				
		}else{
			set_time_limit(0);
	
			$list = $GLOBALS['db']->getAll($sql_str);
			//register_shutdown_function(array(&$this, ACTION_NAME), $page+1);
			$title = get_peizi_title();
	
			$data = array();
			$data[] = $title;
			foreach ($list as $k => $v) {
				$item = get_peizi_order_fromat($v);
	
				$vo = array();
				foreach ($title as $k => $v) {
					$vo[$k] = $item[$k];
				}
	
				$data[] = $vo;
			}
	
			$filename = "peizi_export";
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename={$filename}.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
	
			outputCSV($data);
			exit;
		}
	}
	
	
	public function trade_fee_date_pay(){
		$id = intval($_REQUEST['id']);
		
		$sql_str = "select id,peizi_order_id, trade_money,stock_date from ".DB_PREFIX."peizi_order_stock_money where has_pay = 0 and id = ".$id;
		$osm =	$GLOBALS['db']->getRow($sql_str);
			
		if (intval($osm['id']) == 0 || intval($osm['peizi_order_id']) == 0){
			$this->error("数据已结算或不存在");
		}
		
		//判断
		$sql_str = "select count(*) from ".DB_PREFIX."peizi_order_stock_money where has_pay = 0 and peizi_order_id = '".$osm['peizi_order_id']."' and stock_date < '".$osm['stock_date']."'";		
		if (intval($GLOBALS['db']->getOne($sql_str)) > 0){
			$this->error("请先结清 ".$osm['stock_date']." 以前的数据");
		}
		
		$order_id = intval($osm['peizi_order_id']);
		
		$sql = "select id,rate_type,user_id,invest_user_id,order_sn, stock_sn,site_money,borrow_money,rate_money,begin_date,last_fee_date,next_fee_date,rate_money,type,is_holiday_fee,
			p_invest_user_id,invite_invest_interest_rate,invite_invest_commission_rate,p_user_id,invite_borrow_interest_rate,invite_borrow_commission_rate,
			invest_commission_rate,site_rate,rate	from ".DB_PREFIX."peizi_order where id = ".intval($order_id);
		$v = $GLOBALS['db']->getRow($sql);
		
		
		$trade_money = $osm['trade_money'];
		$rate_money = $trade_money * $v['rate'];
		$site_money = $trade_money * $v[''];
		
		$sever_money = $rate_money + $site_money;
		
		$user_id = intval($v['user_id']);
		
		$user_info =get_user_info("*","id = ".$user_id);
		$money = floatval($user_info['money']);
		
		//print_r($user_info);exit;
		if ($sever_money >= $money){
			//扣费失败，余额不足
			$sql = "update ".DB_PREFIX."peizi_order set is_arrearage = 1 where id = ".$order_id;
			$GLOBALS['db']->query($sql);
			$this->error("扣费失败，余额不足");
		}else{
			$sql = "update ".DB_PREFIX."peizi_order_stock_money set has_pay = 1 where has_pay = 0 and id = ".$id;
			//echo $sql; exit;
			$GLOBALS['db']->query($sql);			
			if($GLOBALS['db']->affected_rows()){
				pay_fee($order_id,$trade_money,$rate_money,$site_money,false);			
			}
			
			$this->success('结算完成',0);
		}
	}
	
	
	//崔单
	public function next_fee_date(){
	
	
		$map =  $this->com_search();
		$map['status'] = '6';
		//$map['last_fee_date'] = to_date(TIME_UTC,'Y-m-d');
	
		if (!isset($_REQUEST['next_start_date']) || $_REQUEST['next_start_date'] == '') {
			$map['next_start_date'] = to_date(TIME_UTC, 'Y-m-d');
		}else{
			$map['next_start_date'] = $_REQUEST['next_start_date'];
		}
	
		if (!isset($_REQUEST['next_end_date']) || $_REQUEST['next_end_date'] == '') {
			$map['next_end_date'] = dec_date(to_date(TIME_UTC, 'Y-m-d'),-3);
		}else{
			$map['next_end_date'] = $_REQUEST['next_end_date'];
		}
	
		$this->assign("next_start_date",$map['next_start_date']);
		$this->assign("next_end_date",$map['next_end_date']);
	
		
		$this->op_base($map," and a.rate_money + a.site_money > AES_DECRYPT(u.money_encrypt,'".AES_DECRYPT_KEY."') ");
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);	
	
		$this->assign("main_title","催单【近期需要缴费且帐户余额不足的配资单】");
		$this->display("next_fee_date");
	}	 
	
	
	//快到期
	public function next_end_date(){
		$map =  $this->com_search();
		$map['status'] = '6';
		
		if (!isset($_REQUEST['end_start_date']) || $_REQUEST['end_start_date'] == '') {
			$end_start_date = to_date(TIME_UTC, 'Y-m-d');
		}else{
			$end_start_date = $_REQUEST['end_start_date'];
		}
		
		if (!isset($_REQUEST['end_end_date']) || $_REQUEST['end_end_date'] == '') {
			$end_end_date = dec_date(to_date(TIME_UTC, 'Y-m-d'),-3);
		}else{
			$end_end_date = $_REQUEST['end_end_date'];
		}
		
		$map['end_start_date'] = $end_start_date;
		$map['end_end_date'] = $end_end_date;
		
		$this->assign("end_start_date",$end_start_date);
		$this->assign("end_end_date",$end_end_date);
		
		
		//$map['end_date'] = date_in($end_start_date, $end_end_date);
		$this->op_base($map);
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);	
	
		$this->assign("main_title","快到期");
		$this->display("next_end_date");
	}
	
	//扣费失败
	public function arrearage(){
		$map =  $this->com_search();
		$map['status'] = '6,8';
		$map['is_arrearage'] = '1';
		
		if (!isset($_REQUEST['next_start_date']) || $_REQUEST['next_start_date'] == '') {
			//$map['fee_start_date'] = dec_date(to_date(TIME_UTC, 'Y-m-d'),7);
		}else{
			$map['next_start_date'] = $_REQUEST['next_start_date'];
			$this->assign("next_start_date",$map['next_start_date']);
		}
		
		if (!isset($_REQUEST['next_end_date']) || $_REQUEST['next_end_date'] == '') {
			//$map['fee_end_date'] = to_date(TIME_UTC, 'Y-m-d');
		}else{
			$map['next_end_date'] = $_REQUEST['next_end_date'];
			$this->assign("next_end_date",$map['next_end_date']);
		}
		
		
		
		$this->op_base($map);
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);	
	
		$this->assign("main_title","扣费失败");
		$this->display("arrearage");
	}

	//预警线
	public function warning_line(){
		$map =  $this->com_search();
		$map['status'] = '6';
		$map['warning_line'] = true;
		
		if (!isset($_REQUEST['end_start_date']) || $_REQUEST['end_start_date'] == '') {
			$end_start_date = '';
		}else{
			$end_start_date = $_REQUEST['end_start_date'];
			$map['end_start_date'] = $end_start_date;
		}
		
		if (!isset($_REQUEST['end_end_date']) || $_REQUEST['end_end_date'] == '') {
			$end_end_date = '';
		}else{
			$end_end_date = $_REQUEST['end_end_date'];
			$map['end_end_date'] = $end_end_date;
		}
		
		$this->assign("end_start_date",$end_start_date);
		$this->assign("end_end_date",$end_end_date);
		
		$this->op_base($map);
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);	
	
		$this->assign("main_title","预警线");
		$this->display("warning_line");
	}

	//平仓线
	public function open_line(){
		$map =  $this->com_search();
		$map['status'] = '6';
		$map['open_line'] = true;
		
		if (!isset($_REQUEST['end_start_date']) || $_REQUEST['end_start_date'] == '') {
			$end_start_date = '';
		}else{
			$end_start_date = $_REQUEST['end_start_date'];
			$map['end_start_date'] = $end_start_date;
		}
		
		if (!isset($_REQUEST['end_end_date']) || $_REQUEST['end_end_date'] == '') {
			$end_end_date = '';
		}else{
			$end_end_date = $_REQUEST['end_end_date'];
			$map['end_end_date'] = $end_end_date;
		}
		
		
		$this->assign("end_start_date",$end_start_date);
		$this->assign("end_end_date",$end_end_date);
		
		$this->op_base($map);
		
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);	
	
		$this->assign("main_title","平仓线");
		$this->display("open_line");
	}	
	
	public function op_edits(){
		$id = intval($_REQUEST ['id']);
		
		$condition['id'] = $id;
		//$vo = M("PeiziOrder")->where($condition)->find();
		
		$sql_str = "select pc.name as conf_type_name,a.*,AES_DECRYPT(a.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd, u.user_name, m.adm_name
					,iu.user_name as invest_user_name from ".DB_PREFIX."peizi_order a
					LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
					LEFT JOIN ".DB_PREFIX."user iu on iu.id = a.invest_user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = a.peizi_conf_id
					LEFT JOIN ".DB_PREFIX."admin m on m.id = a.admin_id  where 1 = 1 and a.id = ".$id;
		
		//$sql = "select a.*,AES_DECRYPT(a.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd,iu.user_name as invest_user_name  FROM ".DB_PREFIX."peizi_order a LEFT JOIN ".DB_PREFIX."user iu on iu.id = a.invest_user_id WHERE a.id=".$id;
		
		$vo = $GLOBALS['db']->getRow($sql_str);
		
		$vo = get_peizi_order_fromat($vo);
		
		if ($vo['status'] == 6){
			
			$this->assign("stock_date",to_date(TIME_UTC,'Y-m-d'));
		}	
		
		$this->assign("main_title","详情");
		$this->assign ( 'status', $vo['status'] );
		$this->assign ( 'vo', $vo );
		
		/************************************************************/
		if ($vo['status'] == 6 || $vo['status'] == 8){
			/*操盘列表*/
			$op_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id=".$id." order by id desc ");		
	
			foreach($op_list as $k => $v)
			{
				$op_list[$k]["op_type_format"] = get_peizi_op_type($v["op_type"]);
				$op_list[$k]["op_status_format"] = get_peizi_op_status($v["op_status"]);
				
				if($v["op_status"] == 3 || $v["op_status"] == 4)
				{
					$op_list[$k]["op_date"] = $v["op_date2"];
				}
				else
				{
					$op_list[$k]["op_date"] = $v["op_date1"];
				}
			}
			$this->assign('op_list', $op_list);
			
			/**********************/
			/*资金列表*/
			//1:业务审核费;2:日利息;3:月利息;4:其它费用',
			$fee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_fee_list where peizi_order_id=".$id." order by id desc");		
			
			foreach($fee_list as $k => $v)
			{
				$fee_list[$k]["fee_type_format"] = get_peizi_fee_type($v["fee_type"]);
			}
			$this->assign('fee_list', $fee_list);
			
			/*历史金额*/
			$history_list = $GLOBALS['db']->getAll("select m.stock_date,m.stock_money from ".DB_PREFIX."peizi_order_stock_money m where m.peizi_order_id=".$id." order by m.stock_date asc");		
			
			$this->assign('history_list', $history_list);
		}
		
		
		$user_info = M("User") -> getById($vo['user_id']);
		$old_imgdata_str = unserialize($user_info['view_info']);
	
		foreach($old_imgdata_str as $k=>$v){
			$old_imgdata_str[$k]['key'] = $k;  
		}
		$this->assign("old_imgdata_str",$old_imgdata_str);
		
		$this->display ();
		
	}
	
	public function set_price(){
		$stock_money =  floatval($_REQUEST['price']);
		$order_id = intval($_REQUEST['id']);
		$stock_date = to_date(TIME_UTC,"Y-m-d");
		$user_id = M("PeiziOrder")->where("id=".$order_id)->field("user_id");
		if($user_id > 0){
			set_peizi_order_stock_money($order_id,$user_id,$stock_date,$stock_money);
			save_log("编号：".$order_id." 股票总值改为：".format_price($stock_money),1);
			$this->ajaxReturn(format_price($stock_money),"改价成功",1);die();
		}
		else{
			$this->error("改价失败",1);
		}
	}
	
	public function update(){
		
		
		/**
		 
		===========================================================================================================	
		
		订单状态 status:0:正在申请;1:支付成功;2:审核通过;3:审核失败;4:筹款成功;5:筹款失败;6:开户成功;7:开户失败;8:平仓结束;9:已撤消
		
		申请配资(支付保证金) ====》审核 ====》筹款====》开户==》操盘中===》结束
		
		待支付====》status = 0;  
			支付成功 status = 1;[冻结配资人的：本金cost_money,首次付款 first_rate_money, 业务审核费manage_money; 更新 借款推荐人及返利信息] 前台操作 peiziModule.order_confirm
		
		待审核====》status = 1； 
			审核通过: status = 2;[只更新状态(status = 2)，确认交易开始，结束时间 其它的不更改] 
			审核失败: stauts = 3;[退冻结：本金cost_money,首次付款 first_rate_money, 业务审核费manage_money]
			
		待筹款====》status = 2;  
			筹款通过: status = 4;[冻结 投资人的: 投资资金 borrow_money] 
			筹款失败: stauts = 5;[退冻结：本金cost_money,首次付款 first_rate_money, 业务审核费manage_money]
			
		待开户====》status = 4;	 
			开户成功: status = 6;
				[1、需要填写：股票账户，股票密码; 
				 2、更新第一次收费时间 next_fee_date = begin_date; 
				 3、交易开始，结束时间 ，允许修改; 
				 4、更新 投资推荐人及返利信息
				 5、判断当前用户，冻结 资金是否足够，并收取： 首次付款 first_rate_money, 业务审核费manage_money; 本金cost_money继续冻结,直至平仓		 
				 6、计算 返佣 
						invite_invest_money 投资返利【投资推荐人p_invest_user_id获得的: 投资金额返利 = borrow_money * invite_invest_money_rate】
						invite_borrow_money 借款返利【借款推荐人p_user_id获得的: 借款金额返利 = borrow_money * invite_borrow_rate】
						
				 7、更新：投资推荐人的 累计邀请人员的投资金额 =fanwe_user.total_invite_invest_money + borrow_money
				 8、更新：借款推荐人的 累计邀请人员的借款金额 =fanwe_user.total_invite_borrow_money + borrow_money
				 
				 ]
			开户失败: status = 7;[退冻结：本金cost_money,首次付款 first_rate_money, 业务审核费manage_money, 投资人的: 投资资金 borrow_money]
		
		操盘中===》status = 6; 
			平仓结束: status = 8;[
				1、填入平仓金额(股票帐户总值) stock_money ,其它费用:other_fee; 
				2、计算盈亏 total_payoff_fee = stock_money - (borrow_money + cost_money + other_fee); 盈亏 = 股票帐户总值 - (借款金额 + 本金 + 其它费用)
				3、盈利分配
					if($total_payoff_fee >0)
					{
						//实际盈利了
						$data['re_cost_money'] = $cost_money; //返还保证金
						$data['user_payoff_fee'] = $total_payoff_fee * $peiziorder['payoff_rate']; //用户获利
						
						$payoff_fee = $total_payoff_fee - $data['user_payoff_fee'];//醒资者分配完，剩下部分
						
						$data['invest_payoff_fee'] = $payoff_fee * $peiziorder['invest_payoff_rate']; //投资者获得				
						$data['site_payoff_fee'] = $payoff_fee - $data['invest_payoff_fee']; //平台获得										
					}else {
						//亏本				
						$data['re_cost_money'] = $cost_money + $total_payoff_fee; //返还保证金, 有可能投现负数; 投现负数时,则说明：配资者的本金不够还亏损
						$data['user_payoff_fee'] = $total_payoff_fee; //用户获利（亏损)
						$data['site_payoff_fee'] = 0; //平台获得	
						$data['invest_payoff_fee'] = 0; //投资者获得
					}
					
				4、解冻配资人的：本金cost_money，返还保证金: re_cost_money
				5、退还：投资人投资金额 borrow_money
				6、if (user_payoff_fee > 0) 时,更新用户帐户余额 + user_payoff_fee  
				7、if (site_payoff_fee > 0) 时,更新平台帐户余额 + site_payoff_fee
				8、if (invest_payoff_fee > 0) 时,更新投资者帐户余额 + invest_payoff_fee
				9、if (other_fee > 0) 时,插入其它费用到日志表
				
				]
			
		结束===>status = 8
		撤消===>status = 9 [只有状态status = 1时，才可以撤消; 退冻结：本金cost_money,首次付款 first_rate_money, 业务审核费manage_money]
		
		=======================================================================
		
		 */		
		$data = M("PeiziOrder")->create ();
		$peiziorder = M("PeiziOrder")->where("id =".$data['id'])->find();
		$cost_money = $peiziorder['cost_money'];
		$first_rate_money = $peiziorder['first_rate_money'];
		$user_id = $peiziorder['user_id'];
		//$invest_user_id = intval($peiziorder['invest_user_id']);//旧的：投资人
		$manage_money = $peiziorder['manage_money'];
		$order_id = $data['id'];
		
		$data['update_time'] = to_date(TIME_UTC);
		
		$str_where = " id = ".intval($order_id);
			
	// 更新数据
		if($peiziorder['status'] == 1 && $data['status']==2)  //审核通过
		{
			//只更新状态(status = 2)，确认交易开始，结束时间 其它的不更改
			if($data['begin_date']==""){
				$this->error(L("请填写开始时间"));
			}
			if($data['end_date']==""){
				$this->error(L("请填写结束时间"));
			}
			
			$str_where .= ' and status = 1 ';
			
			
			$data['invest_begin_time'] = to_date(TIME_UTC);
			
			$this->assign("jumpUrl",u("PeiziOrder/pzop1"));
		}elseif(($peiziorder['status'] == 1 || $peiziorder['status'] == 2 || $peiziorder['status'] == 4) &&  ($data['status']==3 || $data['status']==5 || $data['status']==7))//审核失败,筹款失败,开户失败
		{	//退冻结：本金cost_money,首次付款 first_rate_money, 业务审核费manage_money
			//退 冻结 投资人的: 投资资金  borrow_money
			if($data['op_memo']==""){
				$this->error(L("请填写失败原因"));
			}
			
			if ($peiziorder['status'] == 1){
				$str_where .= ' and status = 1 ';
				$this->assign("jumpUrl",u("PeiziOrder/pzop1"));
			}else if ($peiziorder['status'] == 2){
				$str_where .= ' and status = 2 ';
				$this->assign("jumpUrl",u("PeiziOrder/pzop2"));
			}else if ($peiziorder['status'] == 4){
				$str_where .= ' and status = 4 ';
				$this->assign("jumpUrl",u("PeiziOrder/pzop4"));
			}
			
		}elseif($peiziorder['status'] == 2 && $data['status']==4)	//筹款通过
		{	
			$str_where .= ' and status = 2 and invest_user_id = 0 ';
			
			//冻结 投资人的: 投资资金
			$data['invest_end_time'] = to_date(TIME_UTC);
			
			
			if(intval($peiziorder['invest_user_id']) != 0 ){
				$this->error(L("投资人已经存在"));
			}
			
			
			$invest_user_id = intval($_REQUEST['invest_user_id']);
			if($invest_user_id== 0 ){
				$this->error(L("投资人不能为空"));
			}
			
			$invest_user_user = get_user_info('*',"id = ".$invest_user_id);
			
			if (!$invest_user_user){
				$this->error(L("投资人不存在"));
			}
			
			//有切换新的投资人或添加首次投资人，需要判断：投资人余额足不足
			if ($peiziorder['borrow_money'] > $invest_user_user['money']){
				$this->error(L("投资人余额不足，需要:".format_price($peiziorder['borrow_money']).';实际:'.format_price($invest_user_user['money'])));
			}

			
			
			$this->assign("jumpUrl",u("PeiziOrder/pzop2"));
	
		}elseif($peiziorder['status'] == 4 && $data['status']==6)	//开户成功
		{
			$str_where .= ' and status = 4 ';
			//,；
			/**
			 * 1、需要填写：股票账户，股票密码
			 * 2、更新第一次收费时间 next_fee_date
			 * 3、交易开始，结束时间 ，允许修改
			 * 4、判断当前用户，冻结 资金是否足够，并收取： 首次付款 first_rate_money, 业务审核费manage_money; 本金cost_money继续冻结,直至平仓
			 */
			
			if(isset($data['begin_date']) && $data['begin_date']==""){
				$this->error(L("请填写开始时间"));
			}
			if(isset($data['end_date']) && $data['end_date']==""){
				$this->error(L("请填写结束时间"));
			}
						
			if($data['stock_sn']==""){
				$this->error(L("请填写股票账户"));
			}
				
			$limit_num = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where stock_sn = '".$data['stock_sn']."'"));
			if($limit_num > 0){
				$this->error(L("该股票账户,已经被分配过"));
			}
			
			
			if($_REQUEST['stock_pwd']==""){
				$this->error(L("请填写股票密码"));
			}
				
			$data['stock_pwd_encrypt'] = "AES_ENCRYPT('".$_REQUEST['stock_pwd']."','".AES_DECRYPT_KEY."')";
			
			//第一次 自动扣费时间
			$data['next_fee_date'] = $peiziorder['begin_date'];
			
			
			//投资推荐人
			$p_invest_user_id = intval(get_user_info('pid',"id = ".intval($peiziorder['invest_user_id']),'ONE'));
			
			if ($p_invest_user_id > 0){
				$peizi_invite = load_auto_cache("peizi_invite",array('type'=>0,'user_id'=>$p_invest_user_id));
			
				$data['p_invest_user_id'] = $p_invest_user_id;
				$data['invite_invest_money_rate'] = floatval($peizi_invite['money_rate']);
				$data['invite_invest_interest_rate'] = floatval($peizi_invite['interest_rate']);
				$data['invite_invest_commission_rate'] = floatval($peizi_invite['commission_rate']);
				
				//投资返利【投资推荐人p_invest_user_id获得的: 投资金额返利 = borrow_money * invite_invest_money_rate】
				$data['invite_invest_money'] = floatval($peiziorder['borrow_money'] * $data['invite_invest_money_rate']);
			}else{
				$data['p_invest_user_id'] = 0;
				$data['invite_invest_money'] = 0;
			}
			
			
			
			
			//lock_money
			//判断当前用户，冻结 资金是否足够
			$sql = "select lock_money from ".DB_PREFIX."user where id = ".$user_id;
			$lock_money = $GLOBALS['db']->getOne($sql);
			$tmoney = $peiziorder['cost_money'] + $peiziorder['first_rate_money'] + $peiziorder['manage_money'];
			if ($lock_money < $tmoney){
				$this->error(L("冻结的余额不足:".format_price($lock_money).';实际:'.format_price($tmoney)));
			}
				
			$this->assign("jumpUrl",u("PeiziOrder/pzop4"));
		}elseif($peiziorder['status'] == 6 && $data['status']==8)	//平仓操作
		{
			
			$str_where .= ' and status = 6 ';
			
			if (empty($data['stock_date']))
				$data['stock_date'] = to_date(TIME_UTC,'Y-m-d');
			
			if(floatval($data['stock_money'])==0){
				$this->error("股票帐户总值不能为空或0");
			}
			
			$data = do_peizi_pc_calc_1($order_id,$data['stock_money'],$data['other_fee'],$data['other_memo']);
			
			$data['id'] = $order_id;
		}else{
			$this->error('状态不匹配,上一状态:'.get_peizi_status($peiziorder['status']).';准备变更状态:'.get_peizi_status($data['status']));
		}
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order",$data,"UPDATE",$str_where);
		
		if($GLOBALS['db']->affected_rows()){
			//成功提示
			save_log($data['order_sn'].L("UPDATE_SUCCESS"),1);
			
			require_once APP_ROOT_PATH.'system/libs/user.php';

			if($data['status']==3 || $data['status']==5 || $data['status']==7)
			{
				//解冻：本金 cost_money
				modify_account(array('money'=>$cost_money,'lock_money'=>-$cost_money), $user_id,'配资申请失败解冻配资本金,配资编号:'.$order_id,30);
				
				//解冻：首次付款  first_rate_money
				modify_account(array('money'=>$first_rate_money,'lock_money'=>-$first_rate_money), $user_id,'配资申请失败解冻预交款,配资编号:'.$order_id,31);
				
				//解冻：业务审核费  manage_money
				if ($manage_money > 0)
					modify_account(array('money'=>$manage_money,'lock_money'=>-$manage_money), $user_id,'配资申请失败解冻服务费,配资编号:'.$order_id,32);
				
				$invest_user_id = intval($peiziorder['invest_user_id']);
				//解冻 投资人 金额  $data['status']==7 时，才有投资人
				if ($data['status']==7 && $invest_user_id > 0)
					modify_account(array('money'=>$peiziorder['borrow_money'],'lock_money'=>-$peiziorder['borrow_money']), $invest_user_id,'配资投资解冻,配资编号:'.$order_id,36);
								
			}else if($data['status']==4){
				//冰结投资人 金额
				modify_account(array('money'=>-$peiziorder['borrow_money'],'lock_money'=>$peiziorder['borrow_money']), $invest_user_id,'配资投资冰结,配资编号:'.$order_id,36);
							
			}elseif($data['status']==6) //6:开户成功;
			{
				
				//30:配资本金(冻结); 31:配资预交款(冻结);32:配资审核费(冻结);33:配资服务费(平台收入);34:配资利息(投资者收入);35:配资平仓收益;36:配资投资
				
				//解冻并收取：业务审核费 (32借款服务费) manage_money
				if ($manage_money > 0){
					
					modify_account(array('lock_money'=>-$manage_money,'site_money'=>$manage_money), $user_id,'配资申请复审通过,解冻服务费,增加平台收入,配资编号:'.$order_id,32);
					/*
					$fee_data = array();
					$fee_data['user_id'] = $user_id;
					$fee_data['peizi_order_id'] = $order_id;
					$fee_data['create_date'] = to_date(TIME_UTC);
					$fee_data['fee_date'] = to_date(TIME_UTC);
					$fee_data['fee'] = $manage_money;
					$fee_data['fee_type'] = 1;//费用类型;1:业务审核费				
					$fee_data['memo'] = '复审通过,收取：业务审核费 ';					
					$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_fee_list",$fee_data,"INSERT");
					*/
					
				}
				//冻结：首次付款  first_rate_money
				modify_account(array('money'=>$first_rate_money,'lock_money'=>-$first_rate_money), $user_id,'配资申请失败解冻预交款,配资编号:'.$order_id,31);
				//自动收取 第一个月或第一天的 利息
				auto_charging_rate_money(false,true);
				
				
				
				$invest_user_id = intval($peiziorder['invest_user_id']);
				//直接在：冻结资金 里面扣除： 配资 金额
				if ($invest_user_id > 0)
					modify_account(array('lock_money'=>-$peiziorder['borrow_money']), $invest_user_id,'配资投资(冻结转投资),配资编号:'.$order_id,36);
				
				
				//发放：投资推荐人 返利
				if ($data['p_invest_user_id'] > 0 && $data['invite_invest_money'] > 0){
					modify_account(array('money'=>-$data['invite_invest_money'],'site_money'=>-$data['invite_invest_money']), $data['p_invest_user_id'],'发放投资款返利,配资编号:'.$order_id,23);
				}
				
				$peiziorder['url'] = url("index","peizi#detail",array("id"=>$peiziorder['id']));
				if(intval($data['load_score']) > 0){
					modify_account(array('score'=>intval($data['load_score'])),$peiziorder['invest_user_id'],"[<a href='".$peiziorder['url']."' target='_blank'>".$peiziorder['peiz_name']."</a>],募资成功",28);
				}
				
				if(intval($data['score']) > 0){
					
					modify_account(array('score'=>intval($data['load_score'])),$peiziorder['user_id'],"[<a href='".$peiziorder['url']."' target='_blank'>".$peiziorder['peiz_name']."</a>],募资成功",28);
				}
				
				//发放：借款推荐人 返利
				if ($peiziorder['p_user_id'] > 0 && $peiziorder['invite_borrow_money'] > 0){
					modify_account(array('money'=>$peiziorder['invite_borrow_money'],'site_money'=>-$peiziorder['invite_borrow_money']), $peiziorder['p_user_id'],'发放配资款返利,配资编号:'.$order_id,23);
				}

				//更新，借款 推荐人 的：累计被邀请人员的借款金额；累计被邀请人员的投资金额
				if ($peiziorder['p_user_id'] > 0){
					update_invite_money($peiziorder['p_user_id'],1,$peiziorder['borrow_money']);
				}
				
				//更新，投资 推荐人 的：累计被邀请人员的投资金额
				if ($data['p_invest_user_id'] > 0){
					update_invite_money($peiziorder['p_invest_user_id'],0,$peiziorder['borrow_money']);
				}
								
				//初始股票帐户金额 
				$total_money = $peiziorder['cost_money'] + $peiziorder['borrow_money'];
				set_peizi_order_stock_money($order_id,$user_id,$peiziorder['begin_date'],$total_money);
								

				
				//通知用户开户成功
				if (app_conf("SMS_ON") == 1 && app_conf('SMS_PEIZI_SUCCESS_MSG')==1){
					$user_info = get_user_info("*","id=".$user_id);
					
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_SMS_PEIZI_SUCCESS_MSG'");
					$tmpl_content = $tmpl['content'];
				
					//$peiziorder
					
					$notice['site_name'] = app_conf("SHOP_TITLE");
					$notice['user_name'] = $user_info["user_name"];
					$notice['order_sn'] = $peiziorder['order_sn'];
					$notice['stock_sn'] = $peiziorder['stock_sn'];
					$notice['stock_pwd'] = $peiziorder['stock_pwd'];
					$notice['begin_date'] = $peiziorder['begin_date'];
				
					$GLOBALS['tmpl']->assign("notice",$notice);
				
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
				
					$msg_data['dest'] = $user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['title'] = "配资开户通知";
					$msg_data['content'] = addslashes($msg);;
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}				
			}elseif($data['status']==8)	//平仓操作
			{
				do_peizi_pc_calc_2($order_id);
								
				$this->assign("jumpUrl",u("PeiziOrder/pzop6"));
			}
			
			
			$this->success(L("UPDATE_SUCCESS"),0);
		} else {
			//错误提示
			save_log($data['order_sn'].L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$data['name'].L("UPDATE_FAILED"));
		}
	}
	
	public function deals(){
		$map =  $this->com_search();
		$map['status'] = "6,8";
		$where = "";
		if(isset($_REQUEST['deal_status'])){
			if(intval($_REQUEST['deal_status']) == 0)
				$where = " and a.deal_id = 0 ";
			elseif(intval($_REQUEST['deal_status']) == 1)
				$where = " and a.deal_id > 0 ";
			

			$this->assign("deal_status",intval($_REQUEST['deal_status']));
		}
		else{
			$where = " and a.deal_id = 0 ";
			$this->assign("deal_status",0);
		}
		
		$this->op_base($map,$where);
	
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);
	
		$this->assign("main_title","配资理财包");
		$this->display();
	}
	public function makedeals(){
		$ids  = trim($_REQUEST['ids']);
		$ids_arr = explode($ids_arr,$ids);
		if(count($ids_arr)==0){
			$this->error("请选择配资",0);
		}
		
		$map['id'] = $smap['id'] = array("in",$ids);
		$map['deal_id'] = array("gt",0);
		$map['status'] = $smap['status'] = array("in","6,8");
		
		$has_deal = M("PeiziOrder")->where($map)->getField("group_concat(id) as ids ");
		
		if($has_deal){
			$this->error("配资编号:“".$has_deal['ids']."”已分配理财包",0);
		}
		
		//判断是否是同一个用户
		$user_count = M("PeiziOrder")->where($smap)->count("distinct invest_user_id");
		
		if($user_count > 1){
			
			$this->error("配资不是属于同一个会员的",0);
		}
		
		//获取配置列表
		$this->op_base(array("status"=>"6,8",'peizi_conf_id'=>-1)," and a.id in (".$ids.")");
		
		$peizi_list = $this->get("list");
	
		if(!$peizi_list){
			$this->error("配资不存在",0);
		}
		
		$this->assign("peizi_list",$peizi_list);
				
		$vo['user_id'] = M("PeiziOrder")->where($smap)->getField("invest_user_id");
		$level_list = load_auto_cache("level");
		$u_level = M("User")->where("id=".$vo['user_id'])->getField("level_id");
		$vo['services_fee'] = $level_list['services_fee'][$u_level];
		
		$deal_sn = "MER".to_date(TIME_UTC,"Y")."".str_pad(D("Deal")->where()->max("id") + 1,7,0,STR_PAD_LEFT);
		$this->assign("deal_sn",$deal_sn);
		
		$user_info = M("User") -> getById($vo['user_id']);
		$old_imgdata_str = unserialize($user_info['view_info']);
	
		foreach($old_imgdata_str as $k=>$v){
			$old_imgdata_str[$k]['key'] = $k;  /*+一个key*/
		}
		$this->assign("user_info",$user_info);
		$this->assign("old_imgdata_str",$old_imgdata_str);

		
		if($vo['manage_fee'] ==""){
			// VIP状态处于有效 、采用 VIP借款管理费 比例 计算
			$vip_id = M("User")->where("vip_state='1' and id=".$vo['user_id'])->getField("vip_id");
			//echo $vip_id;
			$load_mfee = M("VipSetting")->where("vip_id='$vip_id'")->getField("load_mfee");
			if($load_mfee){
				$vo['manage_fee']=$load_mfee;
			}else{
				$vo['manage_fee'] = app_conf("MANAGE_FEE");
			}
		}
		
		//计算平均多少利率
		$vo['rate'] = 12;
		
		//计算贷款多少钱
		$vo['borrow_amount'] = M("PeiziOrder")->where($smap)->getField(" sum(borrow_money) as borrow_amount ");
		
		$this->assign("vo",$vo);
		
		$citys = M("DealCity")->where('is_delete= 0 and is_effect=1 ')->findAll();
		
		$this->assign ( 'citys', $citys );
		
		if(trim($_REQUEST['type'])=="deal_status"){
			$this->display ("Deal:deal_status");
			exit();
		}
				
		$deal_cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$deal_cate_tree = D("DealCate")->toFormatTree($deal_cate_tree,'name');
		$this->assign("deal_cate_tree",$deal_cate_tree);
		
		$deal_agency = M("User")->where('is_effect = 1 and user_type =2')->order('sort DESC')->findAll();
		$this->assign("deal_agency",$deal_agency);
		
		$deal_type_tree = M("DealLoanType")->findAll();
		$deal_type_tree = D("DealLoanType")->toFormatTree($deal_type_tree,'name');
		$this->assign("deal_type_tree",$deal_type_tree);
		
		$loantype_list = load_auto_cache("loantype_list");
    	$this->assign("loantype_list",$loantype_list);
    	
    	$contract_list = load_auto_cache("contract_cache");
    	$this->assign("contract_list",$contract_list);
		
		$this->assign("peizi_ids",$ids);
		$this->assign("reback_url","javascript:history.back();");
		
		$this->display("Deal:add");
	}
	
	public function domakedeals(){
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M("Deal")->create ();

		//开始验证有效性
		$this->assign("jumpUrl","javascript:history.back(-1);");
		
		if(!check_empty($data['name']))
		{
			$this->error(L("DEAL_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['sub_name']))
		{
			$this->error(L("DEAL_SUB_NAME_EMPTY_TIP"));
		}	
		if($data['cate_id']==0)
		{
			$this->error(L("DEAL_CATE_EMPTY_TIP"));
		}
		if($data['type_id']==0)
		{
			$this->error(L("DEAL_TYPE_EMPTY_TIP"));
		}
		
		if(D("Deal")->where("deal_sn='".$data['deal_sn']."'")->count() > 0){
			$this->error("借款编号已存在");
		}
		
		
		$loantype_list = load_auto_cache("loantype_list");
		if(!in_array($data['repay_time_type'],$loantype_list[$data['loantype']]['repay_time_type'])){
			$this->error("还款方式不支持当前借款期限类型");
		}
		
		// 更新数据
		$data['publish_wait'] = 1;
		
		$log_info = $data['name'];
		$data['create_time'] = TIME_UTC;
		$data['update_time'] = TIME_UTC;
		$data['start_time'] = trim($data['start_time'])==''?0:to_timespan($data['start_time']);
		if($data['start_time'] > 0)
			$data['start_date'] = to_date($data['start_time'],"Y-m-d");
		
		$list=M("Deal")->add($data);
		if (false !== $list) {
			foreach($_REQUEST['city_id'] as $k=>$v){
				if(intval($v) > 0){
					$deal_city_link['deal_id'] =$list;
					$deal_city_link['city_id'] = intval($v);
					M("DealCityLink")->add ($deal_city_link);
				}
			}
			
			$GLOBALS['db']->query("UPDATE ".DB_PREFIX."peizi_order SET deal_id=".$list." WHERE id in (".trim($_REQUEST['peizi_ids']).") ");
			
			require_once(APP_ROOT_PATH."app/Lib/common.php");
			//成功提示
			syn_deal_status($list);
			syn_deal_match($list);
			save_log("编号：$list，".$log_info.L("INSERT_SUCCESS"),1);
			$this->assign("jumpUrl",u("PeiziOrder"."/deals"));
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($log_info.L("INSERT_FAILED").$dbErr,0);
			$this->error(L("INSERT_FAILED").$dbErr);
		}
	}
	
	
	public function import_money(){
		$this->assign("stock_date",to_date(TIME_UTC,'Y-m-d'));
		$this->display();
	}
	
	public function do_import_money(){
		header("Content-Type:text/html; charset=utf-8");
		//header("Content-Type:text/html; charset=gb3212");
		$dir = APP_ROOT_PATH."/public/upload/";
		if (is_dir($dir) == false) {
			mkdir($dir, 0777);//在页面目录下要新建upload文件夹用来保存上传csv文件
		}
		
		
		//1，存储csv文件
		$csv_filename = $_FILES["csv_file"]["name"];
		move_uploaded_file($_FILES["csv_file"]["tmp_name"], $dir. $_FILES["csv_file"]["name"]);
		
		//4，以csv列名为键名获取csv所有数据
		$csv_file = fopen($dir.$csv_filename, 'r');
		$result_arr = input_csv($csv_file);
		//print_r($result_arr);
		
		gbk2utf8($result_arr);
		
		$sql = "delete from ".DB_PREFIX."peizi_import_homs_money";// where `日期` = '".$_POST['stock_date']."'";
		//echo $sql; exit;
		$GLOBALS['db']->query($sql);
		
		foreach($result_arr as $k=>$v){
			//echo '借资账户:'. print_r($v['借资账户'])."<br>";		
			//$v['借资账户'];exit;
			
			$v['日期'] = $_POST['stock_date'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_import_homs_money",$v); //插入
		}
		
		
		
		fclose($csv_file);
		
		@unlink($csv_file);
		
		
		
		$this->import_money_list();
		
	}
	
	public function import_money_list(){
	
		$map = array();
		
	
		//peizi_order_fee_list
		$sql_str = "select o.id as order_id,pc.name as conf_type_name, o.order_sn, o.borrow_money,o.warning_line,o.open_line, a.*, u.user_name,iu.user_name as invest_user_name from ".DB_PREFIX."peizi_import_homs_money a
					LEFT JOIN ".DB_PREFIX."peizi_order o on o.stock_sn = a.`借资账户`
					LEFT JOIN ".DB_PREFIX."user u on u.id = o.user_id
					LEFT JOIN ".DB_PREFIX."user iu on iu.id = o.invest_user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = o.peizi_conf_id
					  where 1 = 1 ";
	
		//echo $sql_str;exit;
	
		//$this->op_base($map);
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
	
	
		$model = D();
		//print_r($map);
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'a.`借资账户`', false);
		$this->assign('list', $voList);
	
	
		$this->assign("main_title","homs资金列表");
		$this->display("import_money_list");
	}	
	
	
	public function do_import_money_list(){
		$sql_str = "select o.id as order_id, pc.name as conf_type_name, o.order_sn, o.borrow_money,o.warning_line,o.open_line, a.*, u.user_name,iu.user_name as invest_user_name from ".DB_PREFIX."peizi_import_homs_money a
					LEFT JOIN ".DB_PREFIX."peizi_order o on o.stock_sn = a.`借资账户`
					LEFT JOIN ".DB_PREFIX."user u on u.id = o.user_id
					LEFT JOIN ".DB_PREFIX."user iu on iu.id = o.invest_user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = o.peizi_conf_id
					  where 1 = 1 ";
		
		
		$list = $GLOBALS['db']->getAll($sql_str);
		
		foreach($list as $k=>$v){
			if ($v['order_id'] > 0){
				$osm_id = set_peizi_order_stock_money($v['order_id'],0,$v['日期'],$v['总资产']);
				
				$sql_str = "select sum(IF(a.buy_sell = '买入',a.money,0)) as buy_money,sum(IF(a.buy_sell = '卖出',a.avg_buy_money,0)) as sell_cost_money from ".DB_PREFIX."peizi_order_trade as a					
					where a.order_id = ".$v['order_id']." and a.trade_date = '".$v['日期']."'";
				
				$pot =	$GLOBALS['db']->getRow($sql_str);
				
				
				//计算当日 计息金额
				//1、获得昨天的计息金额
				$sql_str = "select trade_money,sell_money from ".DB_PREFIX."peizi_order_stock_money where peizi_order_id = ".$v['order_id']." and stock_date < '".$v['日期']."' order by stock_date desc limit 1 ";
				$posm =	$GLOBALS['db']->getRow($sql_str);
					
				$last_trade_money = floatval($posm['trade_money']);
				$last_sell_money = floatval($posm['sell_money']);
					
				//当日 计息金额 = 上次计息金额  - 上次卖出金额  + 当天的买入金额
				$trade_money = $last_trade_money - $last_sell_money + $v['buy_money'];
				
				$stock = array();
				$stock['peizi_order_id'] = $v['order_id'];
				$stock['stock_date'] = $v['日期'];
				$stock['trade_money'] = $trade_money;
				$stock['sell_money'] = $pot['sell_cost_money'];
				$stock['buy_money'] = $pot['buy_money'];

				$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_stock_money",$stock,'UPDATE',' has_pay = 0 and id ='.$osm_id); //更新
			}	
		}
		
		save_log("导入homs资金",1);
		$this->assign("jumpUrl",u("PeiziOrder/import_money"));
		$this->success("成功导入homs资金");
	}
	
	
	
	public function import_trade(){
		$this->display();
	}
	
	public function do_import_trade(){
		header("Content-Type:text/html; charset=utf-8");
		//header("Content-Type:text/html; charset=gb3212");
		$dir = APP_ROOT_PATH."/public/upload/";
		if (is_dir($dir) == false) {
			mkdir($dir, 0777);//在页面目录下要新建upload文件夹用来保存上传csv文件
		}
		
		
		//1，存储csv文件
		$csv_filename = $_FILES["csv_file"]["name"];
		move_uploaded_file($_FILES["csv_file"]["tmp_name"], $dir. $_FILES["csv_file"]["name"]);
		
		//4，以csv列名为键名获取csv所有数据
		$csv_file = fopen($dir.$csv_filename, 'r');
		$result_arr = input_csv($csv_file);
		//print_r($result_arr);
		
		gbk2utf8($result_arr);
		
		//print_r($result_arr);
		
		$sql = "delete from ".DB_PREFIX."peizi_import_homs_trade";// where `日期` = '".$_POST['stock_date']."'";
		//echo $sql; exit;
		$GLOBALS['db']->query($sql);
		
		foreach($result_arr as $k=>$v){
			//echo '借资账户:'. print_r($v['借资账户'])."<br>";
			//$v['借资账户'];exit;
				
			$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_import_homs_trade",$v); //插入
		}
		
		
		
		fclose($csv_file);
		
		@unlink($csv_file);
		
		
		//$sql_str = "update ".DB_PREFIX."peizi_import_homs_trade set test_sell_num = sell_num where a.`买卖方向` = '买入' and a.`成交数量` > a.sell_num ";
		//$GLOBALS['db']->query($sql_str);
		
		$sql_str = "update ".DB_PREFIX."peizi_order_trade set test_sell_num = sell_num where a.buy_sell = '买入' and a.num > a.sell_num ";
		$GLOBALS['db']->query($sql_str);
				
		$sql_str = "select a.*,o.id as order_id from ".DB_PREFIX."peizi_import_homs_trade a LEFT JOIN  ".DB_PREFIX."peizi_order o on o.stock_sn = a.`借资账户` where a.`买卖方向` = '卖出'";
		
		//echo $sql_str;
		//$sql_str = "select a.* from ".DB_PREFIX."peizi_import_homs_trade a where a.`买卖方向` = '卖出'";		
		//计算卖出成本价
		$list = $GLOBALS['db']->getAll($sql_str);		
		foreach($list as $k=>$v){
			
			/*
			//a.`借资账户`,a.`交易日期` 数据,已经结算,请务重复导入
			$sql_str = "select count(*) from ".DB_PREFIX."peizi_order_fee_list where is_commission = 1 and peizi_order_id = ".$v['order_id']." and fee_date = '".$v['交易日期']."'";
			if ($GLOBALS['db']->getOne($sql_str) == 0){
				
				$sql = "delete from ".DB_PREFIX."peizi_import_homs_trade";// where `日期` = '".$_POST['stock_date']."'";
				//echo $sql; exit;
				$GLOBALS['db']->query($sql);
				$this->error('交易日期:'.$v['交易日期'].' 已经结算,请务重复导入');
			}
			*/
			
			
			$total_num = $v['成交数量'];
			
			$num_list = array();
			
			
			//$sql_str = "select a.id,a.`定位串` as sn, a.`成交数量` as num, a.test_sell_num, a.`成交价格` as price from ".DB_PREFIX."peizi_import_homs_trade a where a.`买卖方向` = '买入' and a.`证券代码` = '".$v['证券代码']."' and a.`成交数量` > a.test_sell_num order by a.`交易日期`, a.id";			
			
			
			$sql_str = "select * from (
						select 0 as type, a.id,a.trade_sn as sn, a.num, a.test_sell_num, a.price, a.trade_date from ".DB_PREFIX."peizi_order_trade a where a.buy_sell = '买入' and a.stock_code = '".$v['证券代码']."' and a.sell_num > a.test_sell_num
						UNION ALL
						select 1 as type, a.id,a.`定位串` as sn, a.`成交数量` as num, a.test_sell_num, a.`成交价格` as price, a.`交易日期` as trade_date from ".DB_PREFIX."peizi_import_homs_trade a where a.`买卖方向` = '买入' and a.`证券代码` = '".$v['证券代码']."' and a.`成交数量` > a.test_sell_num)
						as a order by a.trade_date, a.id";
			
			
			//echo $sql_str."<br>";
			$list2 = $GLOBALS['db']->getAll($sql_str);
			foreach($list2 as $k2=>$v2){
				$store_num = $v2['num'] - $v2['test_sell_num'];
				
				$item = array();
				$item['id'] = $v2['id'];
				$item['sn'] = $v2['sn'];
				$item['price'] = $v2['price'];
				$item['num'] = 0;
				
				if ($store_num > $total_num){				
					$num = $total_num;					
				}else{
					$num = $store_num;
				}
				
				$item['num'] = $num;
				$total_num = $total_num - $num;
				
				$num_list[] = $item;
				
				//更新卖出数量
				if ($v2['type'] == 0){
					$sql_str = "update ".DB_PREFIX."peizi_order_trade set test_sell_num = test_sell_num + ".$num." where id = ".$v2['id'];
				}else{
					$sql_str = "update ".DB_PREFIX."peizi_import_homs_trade set test_sell_num = test_sell_num + ".$num." where id = ".$v2['id'];
				}
				
				
				//echo $sql_str."<br>";
				$GLOBALS['db']->query($sql_str);
				
				if ($total_num == 0){
					break;
				}				
			}
			
			
			if ($total_num > 0){
				//数据出错,没有找到：买入记录 或 卖出数大于买入数
				
				$sql = "delete from ".DB_PREFIX."peizi_import_homs_trade";// where `日期` = '".$_POST['stock_date']."'";
				//echo $sql; exit;
				$GLOBALS['db']->query($sql);				
				//`定位串`
				$this->error('定位串:'.$v['定位串'].',卖出数量大于买入数量');
			}else{
				//$total_num = 0;
				//$avg_buy_price = 0;
				$total_money = 0;
				foreach($num_list as $k3=>$v3){
					$total_money = $total_money + $v3['price'] * $v3['num'];
				}
								
				//更新买入成本价
				$sql_str = "update ".DB_PREFIX."peizi_import_homs_trade set avg_buy_money = ".$total_money.",avg_buy_price = ".$total_money." / `成交数量`, avg_text = '".json_encode($num_list)."'  where id = ".$v['id'];
				//echo $sql_str."<br>";
				$GLOBALS['db']->query($sql_str);
			}
		}
		
		
		
		$this->import_trade_list();
	}
	
	public function import_trade_list(){
		$map = array();
		
		
		//peizi_order_fee_list
		$sql_str = "select o.id as order_id,pc.name as conf_type_name, o.order_sn, o.borrow_money,o.warning_line,o.open_line, a.*
					,o.invest_commission_rate * a.`佣金差` as 投资人佣金, (a.`佣金差` - o.invest_commission_rate * a.`佣金差`) as 平台佣金,
					u.user_name,iu.user_name as invest_user_name from ".DB_PREFIX."peizi_import_homs_trade a
					LEFT JOIN ".DB_PREFIX."peizi_order o on o.stock_sn = a.`借资账户`
					LEFT JOIN ".DB_PREFIX."user u on u.id = o.user_id
					LEFT JOIN ".DB_PREFIX."user iu on iu.id = o.invest_user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = o.peizi_conf_id
					  where 1 = 1 ";
		
		//echo $sql_str;exit;
		
		//$this->op_base($map);
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		
		$model = D();
		//print_r($map);
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'a.`交易日期`, a.id,a.`借资账户`', false);
		$this->assign('list', $voList);
		
		
		$this->assign("main_title","导入homs交割查询数据【只有状态为:操盘中的配资单才能导入】");
		$this->display("import_trade_list");
		
		/*
		$sql_str = "select o.id as order_id, pc.name as conf_type_name, o.order_sn, o.borrow_money,o.warning_line,o.open_line, a.*, u.user_name,iu.user_name as invest_user_name from ".DB_PREFIX."import_trade a
					LEFT JOIN ".DB_PREFIX."peizi_order o on o.stock_sn = a.`借资账户`
					LEFT JOIN ".DB_PREFIX."user u on u.id = o.user_id
					LEFT JOIN ".DB_PREFIX."user iu on iu.id = o.invest_user_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = o.peizi_conf_id
					  where 1 = 1 ";
		
		
		$list = $GLOBALS['db']->getAll($sql_str);
		
		
		
		save_log("导入homs交割查询数据",1);
		$this->assign("jumpUrl",u("PeiziOrder/import_trade"));
		$this->success("成功导入homs交割查询数据");
		*/
	}
	
	public function do_import_trade_list(){
		/*
		$sql_str = "insert into  ".DB_PREFIX."peizi_order_trade(
				`trade_sn`,`order_id`,`trade_date`,`stock_type`,`trade_type`,`stock_code`,`stock_name`,`num`,`price`,`money`,
				`total_money`,`commission_money`,`commission_brokerage_money`,`commission_diff_money`,`commission_site_money`,
				`commission_invest_money`,`stamp_tax`,`transfer_fee`,`is_buy`,`status`)		
			select
				a.`定位串`,
				o.`id`,
				a.`交易日期`,
				a.`证券类别`,
				a.`交易类别`,
				a.`证券代码`,
				a.`证券名称`,
				a.`成交数量`,
				a.`成交价格`,
				a.`成交金额`,
				a.`清算金额`,
				a.`佣金`,
				a.`标准佣金`,
				a.`佣金差`,
				o.invest_commission_rate * a.`佣金差` as 投资人佣金,
				(a.`佣金差` - o.invest_commission_rate * a.`佣金差`) as 平台佣金,
				a.`印花税`,
				a.`过户费`,
				if(a.`买卖方向` = '卖出',1,0) is_buy,
				0 as status		
		from  ".DB_PREFIX."peizi_import_homs_trade a
		LEFT JOIN  ".DB_PREFIX."peizi_order o on o.stock_sn = a.`借资账户` where a.处理标志 = '已处理' and o.id > 0 and o.status = 6";
		
		$sql_str = "insert into  ".DB_PREFIX."peizi_order_trade(
				trade_sn,order_id,trade_date,stock_type,trade_type,stock_code,stock_name,num,price,money,
				total_money,commission_money,commission_brokerage_money,commission_diff_money,commission_site_money,
				commission_invest_money,stamp_tax,transfer_fee,is_buy,status)
			select
				a.定位串,
				o.id,
				a.交易日期,
				a.证券类别,
				a.交易类别,
				a.证券代码,
				a.证券名称,
				a.成交数量,
				a.成交价格,
				a.成交金额,
				a.清算金额,
				a.佣金,
				a.标准佣金,
				a.佣金差,
				o.invest_commission_rate * a.佣金差 as 投资人佣金,
				(a.佣金差 - o.invest_commission_rate * a.佣金差) as 平台佣金,
				a.印花税,
				a.过户费,
				0 as is_buy,
				0 as status
		from  ".DB_PREFIX."peizi_import_homs_trade a
		LEFT JOIN  ".DB_PREFIX."peizi_order o on o.stock_sn = a.借资账户  where a.处理标志 = '已处理' and o.id > 0 and o.status = 6";
		
		echo $sql_str."<br>";
		$GLOBALS['db']->query($sql_str);
		*/
		
		$sql_str = "select
				a.`定位串` as trade_sn,
				o.`id` as order_id,
				a.`交易日期` as trade_date,
				a.`证券类别` as stock_type,
				a.`交易类别` as trade_type,
				a.`证券代码` as stock_code,
				a.`证券名称` as stock_name,
				a.`成交数量` as num,
				a.`成交价格` as price,
				a.`成交金额` as money,
				a.`清算金额` as total_money,
				a.`佣金` as commission_money,
				a.`标准佣金` as commission_brokerage_money,
				a.`佣金差` as commission_diff_money,
				o.invest_commission_rate * a.`佣金差` as commission_invest_money,
				(a.`佣金差` - o.invest_commission_rate * a.`佣金差`) as commission_site_money,
				a.`印花税` as stamp_tax,
				a.`过户费` as transfer_fee,
				a.`买卖方向` as buy_sell,
				a.avg_buy_price,
				a.avg_buy_money,
				a.test_sell_num,
				a.avg_text,
				0 as status
		from  ".DB_PREFIX."peizi_import_homs_trade a
		LEFT JOIN  ".DB_PREFIX."peizi_order o on o.stock_sn = a.`借资账户` where a.`处理标志` = '已处理' and o.id > 0 and o.status = 6";
		
	
		$list = $GLOBALS['db']->getAll($sql_str);
		
		
		foreach($list as $k=>$v){
						
			$sql_str = "select count(*) from ".DB_PREFIX."peizi_order_trade where trade_sn = '".$v['trade_sn']."'";
			if ($GLOBALS['db']->getOne($sql_str) == 0){		
				//防止重复插入	
				$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_trade",$v); //插入
			}
		}
		
		
		$sql_str = "select o.user_id,o.invest_user_id, o.p_invest_user_id,o.p_user_id, o.invite_invest_commission_rate,o.invite_borrow_commission_rate, a.order_id, a.trade_date, sum(a.commission_site_money) as commission_site_money, sum(a.commission_invest_money) as commission_invest_money 
					,sum(IF(a.buy_sell = '买入',a.money,0)) as buy_money,sum(IF(a.buy_sell = '卖出',a.avg_buy_money,0)) as sell_cost_money from ".DB_PREFIX."peizi_order_trade as a 
					LEFT JOIN  ".DB_PREFIX."peizi_order o on o.id = a.order_id		
					where a.status = 0 and o.status = 6 group by a.order_id, a.trade_date";
		$list = $GLOBALS['db']->getAll($sql_str);
		
		foreach($list as $k=>$v){
			$user_id = $v['user_id'];
			$order_id = $v['order_id'];
			$trade_date = $v['trade_date'];
			
			
			//计算当日 计息金额
			//1、获得昨天的计息金额
			$sql_str = "select trade_money,sell_money from ".DB_PREFIX."peizi_order_stock_money where peizi_order_id = ".$order_id." and stock_date < '".$trade_date."' order by stock_date desc limit 1 ";
			$posm =	$GLOBALS['db']->getRow($sql_str);
			
			$last_trade_money = floatval($posm['trade_money']);
			$last_sell_money = floatval($posm['sell_money']);
			
			//当日 计息金额 = 上次计息金额  - 上次卖出金额  + 当天的买入金额
			$trade_money = $last_trade_money - $last_sell_money + $v['buy_money'];

			$sql = "select id from ".DB_PREFIX."peizi_order_stock_money where peizi_order_id = ".$order_id." and stock_date = '".$trade_date."'";
			$osm_id = intval($GLOBALS['db']->getOne($sql));
			$stock = array();
			$stock['peizi_order_id'] = $order_id;
			$stock['stock_date'] = $trade_date;
			$stock['trade_money'] = $trade_money;
			$stock['sell_money'] = $v['sell_cost_money'];
			$stock['buy_money'] = $v['buy_money'];
			if ($osm_id == 0){
				$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_stock_money",$stock); //插入
			}else{
				$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_stock_money",$stock,'UPDATE',' has_pay = 0 and id ='.$osm_id); //更新
			}
			
			
			
			$sql_str = "select count(*) from ".DB_PREFIX."peizi_order_fee_list where is_commission = 1 and peizi_order_id = ".$order_id." and fee_date = '".$trade_date."'";
			if ($GLOBALS['db']->getOne($sql_str) == 0){
				//防止，重复导入时，多次机算佣金数据
				
				$fee_data = array();
				$fee_data['user_id'] = $v['user_id'];
				$fee_data['invest_user_id'] = $v['invest_user_id'];
				$fee_data['peizi_order_id'] = $v['order_id'];
				$fee_data['create_date'] = to_date(TIME_UTC);
				$fee_data['fee_date'] = $trade_date;
				
				$fee_data['trade_money'] = 0;
				$fee_data['rate_money'] = 0;//投资人 收取的利息费用
				$fee_data['site_money'] = 0;//平台 收取的 服务费用
				
	
				//投资者获得交易佣金差比率;如果填0,则所有的佣金差，都有平台获得;填:0.4则；40%投资者;60%平台
				$fee_data['site_commission_money'] =$v['commission_site_money'];			
				$fee_data['invest_commission_money'] = $v['commission_invest_money'];
				
				//投资者推荐人
				$fee_data['p_invest_user_id'] = $v['p_invest_user_id'];
				//投资推荐人（p_invest_user_id）获得的: 投资人利息收益的n%返利 = rate_money * fanwe_peiziorder.invite_invest_interest_rate
				//$fee_data['invite_invest_interest_money'] = $fee_data['rate_money'] * $v['invite_invest_interest_rate'];
				//投资推荐人（p_invest_user_id）获得的: 投资人佣金收益的n%返利 = invest_commission_money * fanwe_peiziorder.invite_invest_commission_rate
				$fee_data['invite_invest_commission_money'] = $fee_data['invest_commission_money'] * $v['invite_invest_commission_rate'];
				
				//配资者推荐人
				$fee_data['p_user_id'] = $v['p_user_id'];
				//借款推荐人（p_user_id）获得的: 平台利息收益的n%返利 = site_money * fanwe_peiziorder.invite_borrow_interest_rate
				//$fee_data['invite_borrow_interest_money'] = $fee_data['site_money'] * $v['invite_borrow_interest_rate'];
				//借款推荐人（p_user_id）获得的: 平台佣金收益的n%返利 = site_commission_money * fanwe_peiziorder.invite_borrow_commission_rate
				$fee_data['invite_borrow_commission_money'] = $fee_data['site_commission_money'] * $v['invite_borrow_commission_rate'];
				
				$fee_data['memo'] = '';
				$fee_data['has_pay'] = 1;//是否支付;0:未支付;1:已支付
				$fee_data['is_commission'] = 1;//1:佣金费用记录;一个订单;一天只有一条记录
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_fee_list",$fee_data,"INSERT");
				
				if($GLOBALS['db']->affected_rows()){
					require_once APP_ROOT_PATH.'system/libs/user.php';
					
					$sql_str = "update ".DB_PREFIX."peizi_order_trade set status = 1 where order_id = ".$order_id." and trade_date = '".$fee_data['fee_date']."'";
					$GLOBALS['db']->query($sql_str);							
								
					 //平台获得的交易佣金  券商直接从 配资人中，收取，然后部分返还给 平台
					 if ($fee_data['site_commission_money'] > 0){
						 modify_account(array('site_money'=>$fee_data['site_commission_money']), $user_id,'平台获得的交易佣金,从券商中返回:'.$order_id,38);
					 }
				
					 //投资者获得的交易佣金;  平台垫付给投资人；平台再从券商中收取
					 if ($fee_data['invest_commission_money'] > 0){
						 modify_account(array("money"=>$fee_data['invest_commission_money'],'site_money'=>-$fee_data['invest_commission_money']), $fee_data['invest_user_id'],'交易佣金,配资编号:'.$order_id,38);
					 }
				
				
					 //投资推荐人（p_invest_user_id）获得的: 投资人佣金收益的n%返利 = invest_commission_money * fanwe_peiziorder.invite_invest_commission_rate
					 if ($fee_data['invite_invest_commission_money'] > 0){
						 modify_account(array("money"=>$fee_data['invite_invest_commission_money'],'site_money'=>-$fee_data['invite_invest_commission_money']), $fee_data['p_invest_user_id'],'投资人交易佣金收益返利,配资编号:'.$order_id,23);
					 }
				
				
					 //借款推荐人（p_user_id）获得的: 平台佣金收益的n%返利 = site_commission_money * fanwe_peiziorder.invite_borrow_commission_rate
					 if ($fee_data['invite_borrow_commission_money'] > 0){
					 	modify_account(array("money"=>$fee_data['invite_borrow_commission_money'],'site_money'=>-$fee_data['invite_borrow_commission_money']), $fee_data['p_user_id'],'平台交易佣金收益返利,配资编号:'.$order_id,23);
					 }		
					 

					 //`total_site_commission_money` decimal(10,2) NOT NULL default '0.00' COMMENT '平台累计，收到：佣金',
					 //`total_invest_commission_money` decimal(10,2) NOT NULL default '0.00' COMMENT '投资者累计，收到：佣金',
					 
					 $sql = "update ".DB_PREFIX."peizi_order a set a.total_site_commission_money = (select sum(b.site_commission_money) from ".DB_PREFIX."peizi_order_fee_list b where b.peizi_order_id = a.id and b.is_commission = 1),
															 a.total_invest_commission_money = (select sum(b.invest_commission_money) from ".DB_PREFIX."peizi_order_fee_list b where b.peizi_order_id = a.id and b.is_commission = 1)
															   where id = ".$order_id;
					 $GLOBALS['db']->query($sql);
				}	
			}
		}
		save_log("导入homs交割查询数据",1);
		$this->assign("jumpUrl",u("PeiziOrder/import_trade"));
		$this->success("成功导入homs交割查询数据");
		
	}
	
	public function trade_list(){
		$map = array();
	
		$order_id = intval($_REQUEST['order_id']);
		
		$map['order_id'] = $order_id;
		//peizi_order_fee_list
		$sql_str = "select * from ".DB_PREFIX."peizi_order_trade a where 1 = 1 and order_id = ".$order_id;
	
		//echo $sql_str;exit;
	
		//$this->op_base($map);
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
	
	
		$model = D();
		//print_r($map);
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'a.`trade_date`', false);
		//$this->assign('list', $voList);
	
	
		$this->assign("main_title","历史交易");
		$this->display("trade_list");

	}	
	
}
?>