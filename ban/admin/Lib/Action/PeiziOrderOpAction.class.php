<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require_once APP_ROOT_PATH."/system/libs/peizi.php";
class PeiziOrderOpAction extends CommonAction{
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
		
		//0:追加保证金;1:申请延期;2:申请增资;3:申请减资;4:提取赢余;5:申请结束配资
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

		
		
		$this->assign("op_type",$op_type);
		$this->assign("peizi_conf_id",$peizi_conf_id);
		
		return $map;
	}
		
	function op_base($map){
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		$sql_str = "select a.*,pc.name as conf_type_name,u.user_name, b.order_sn,b.type,b.cost_money,b.borrow_money,
					b.lever,b.begin_date as op_begin_data,b.time_limit_num, b.cost_money+b.borrow_money as total_money
					,b.invest_user_id from ".DB_PREFIX."peizi_order_op a
					LEFT JOIN ".DB_PREFIX."peizi_order b on b.id = a.peizi_order_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = b.peizi_conf_id
					LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id  where 1 = 1 ";
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['start_time']) && $map['start_time'] <> ''){
			$sql_str .= " and a.create_date <= '".$map['start_time']."'";
		}
		
		if( isset($map['end_time']) && $map['end_time'] <> ''){
			$sql_str .= " and a.create_date >= '".$map['end_time']."'";
		}
		
		if ($map['op_type'] != -1){
			$sql_str .= " and a.op_type in (".$map['op_type'].")";
		}
		
		if ($map['peizi_conf_id'] != -1){
			$sql_str .= " and b.peizi_conf_id = '".$map['peizi_conf_id']."'";
		}
		
		if ($map['op_status'] != ''){
			$sql_str .= " and a.op_status in (".$map['op_status'].")";
		}
		
		$model = D();
		//print_r($map);
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'a.create_date', false);

		foreach ($voList as $k => $v) {
			
			//申请类型 
			$voList[$k]['op_type_format'] = get_peizi_op_type($v['op_type']);
			
			//审核状态
			$voList[$k]['op_status_format'] = get_peizi_op_status($v['op_status'],$v['op_type']);
		}
		$type_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("type_list",$type_list);	
		
		$this->assign('list', $voList);
	}
	
	
	//审核状态;0:未审核;1:投资人审核通过;2:投资人审核未通过;3:平台审核通过;4:平台审核未通过;5:撤消申请
	//延期，增资; 
	public function op0(){
		$map =  $this->com_search();
		$map['op_status'] = '0';
		if ($map['op_type'] == -1){
			$map['op_type'] = '1,2';
		}
		$this->op_base($map);
				
		$this->assign("main_title","投资人审核: 延期，增资");
		$this->display("op0");		
	}
	
	//追加保证金，减资，提取赢余，平仓 这4项，不需要投资审核，平台直接审核就行了
	//0:追加保证金;1:申请延期;2:申请增资;3:申请减资;4:提取赢余;5:申请结束配资
	public function op01(){
		$map =  $this->com_search();
		$map['op_status'] = '0';
		if ($map['op_type'] == -1){
			$map['op_type'] = '0,3,4,5';
		}
		$this->op_base($map);
	
		$this->assign("main_title","平台审核: 追加保证金，减资，提取赢余，平仓");
		$this->display("op01");
	}
		
	//审核失败:2
	public function op1(){
		$map =  $this->com_search();
		$map['op_status'] = '2,4';
		$this->op_base($map);
		
		$this->assign("main_title","审核失败");
		$this->display("index");
	}	
	
	//平台审核:1
	public function op2(){
		$map =  $this->com_search();
		$map['op_status'] = '1';
		$this->op_base($map);
		
		$this->assign("main_title","待复审");
		$this->display("index");
	}	
	
	//操作结束: 3
	public function op3(){
		$map =  $this->com_search();
		$map['op_status'] = '3';
		$this->op_base($map);
		
		$this->assign("main_title","操作结束");
		$this->display("index");
	}	
	
	public function op_edits(){
		
		$id = intval($_REQUEST ['id']);
		$type = strim($_REQUEST["from"]);
		
		$sql_str = "select a.*,pc.name as conf_type_name,u.user_name, b.order_sn,b.type,b.cost_money,b.borrow_money,
					b.lever,b.begin_date,b.time_limit_num, b.cost_money+b.borrow_money as total_money,
					b.warning_line,b.open_line,b.rate_money,b.site_money,b.end_date,b.stock_money,b.stock_date,b.other_fee,b.other_memo
					,iu.user_name as invest_user_name,b.invest_user_id from ".DB_PREFIX."peizi_order_op a
					LEFT JOIN ".DB_PREFIX."peizi_order b on b.id = a.peizi_order_id
					LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = b.peizi_conf_id
					LEFT JOIN ".DB_PREFIX."user iu on iu.id = b.invest_user_id
					LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id where a.id =".$id;
		$vo = $GLOBALS['db']->getRow($sql_str);
	
		$vo["total_money_format"] = format_price($vo["total_money"]);
		$vo["cost_money_format"] = format_price($vo["cost_money"]);
		$vo["warning_line_format"] = format_price($vo["warning_line"]);
		$vo["open_line_format"] = format_price($vo["open_line"]);
		$vo["rate_money_format"] = format_price($vo["rate_money"]);
		$vo["site_money_format"] = format_price($vo["site_money"]);
		//申请类型
		$vo['op_type_format'] = get_peizi_op_type($vo['op_type']);
			
		//审核状态
		$vo['op_status_format'] = get_peizi_op_status($vo['op_status'],$vo['op_type']);
		
		$vo['time_limit_num_format'] = $vo['time_limit_num'].get_peizi_type($vo['type'],true);
		
		if ($vo['op_type'] == 0){
			$label = "调整后的保证金为";
			$label_val = format_price($vo['cost_money']+$vo['op_val']);
		}else if ($vo['op_type'] == 1){
			$label = "调整后的结束时间";
			$label_val = to_date(to_timespan($vo['end_date'])+$vo['op_val']*3600*24);
		}else if ($vo['op_type'] == 2){
			$label = "调整后的借款金额";
			$label_val = format_price($vo['borrow_money']+($vo['op_val'] - $vo['lever']) * $vo['cost_money']);
		}else if ($vo['op_type'] == 3){
			$label = "调整后的借款金额";
			$label_val = format_price($vo['borrow_money']-($vo['lever'] - $vo['op_val']) * $vo['cost_money']);
		}
			
		if($vo['op_status'] == 0)
		{
			$this->assign("main_title","投资人审核   <a href='".u(MODULE_NAME."/op0")."' class='back_list'>返回列表</a>");
		}
		elseif($vo['op_status'] == 1)
		{
			$this->assign("main_title","平台审核   <a href='".u(MODULE_NAME."/op2")."' class='back_list'>返回列表</a>");
		}
		else
		{
			$this->assign("main_title","详细   <a href='".u(MODULE_NAME."/op3")."' class='back_list'>返回列表</a>");
		}
			
		$this->assign ( 'label', $label );
		$this->assign ( 'label_val', $label_val );
		$this->assign ( 'vo', $vo );
		$this->display ("op_edits");
	}
	
	public function update(){
		//$data = M("PeiziOrderOp")->create ();
		$data = array();
		
		$data["id"] = $id = intval($_REQUEST['id']);
		$data["op_status"] = intval($_REQUEST['op_status']);;
		
		$sql = "select op.*,od.invest_user_id,od.cost_money,od.time_limit_num,od.borrow_money,od.lever,od.peizi_conf_id,od.manage_money,od.stock_money,od.re_cost_money,
				od.warning_line,od.rate,od.rate_money,od.site_rate,od.site_money,od.end_date,od.status,od.type,od.is_holiday_fee,od.total_rate_money,od.total_site_money,
				od.p_user_id,od.p_invest_user_id
				from ".DB_PREFIX."peizi_order_op op 
				left join ".DB_PREFIX."peizi_order od on op.peizi_order_id = od.id where op.id =".$id; 
		
		$op_info = $GLOBALS["db"]->getRow($sql);
		
		$user_user = get_user_info('*',"id = ".$op_info['user_id']);
		$invest_user_user = get_user_info('*',"id = ".$op_info['invest_user_id']);

		if (empty($op_info) || empty($user_user) || empty($invest_user_user)){
			$this->error("配资单投错");
		}

		
		if ($op_info['status'] == 8 && ($data['op_status']==1 || $data['op_status']==3)){
			$this->error("已经被平仓,无法通过审核操作");
		}
		
		if ($data['op_status'] == 1){
			$data["op_date1"] = to_date(TIME_UTC);
			$this->assign("jumpUrl",u(MODULE_NAME."/op0"));
		}else{
			$data["op_date2"] = to_date(TIME_UTC);
			$this->assign("jumpUrl",u(MODULE_NAME."/op2"));
		}
		
		if(($op_info['op_status'] == 0 && $data['op_status']==1) || ($op_info['op_status'] == 1 && $data['op_status']==3)){ //投次人审核通过 op_status = 1， 平台 审核通过 op_status = 3
			$data["op_date2"] = to_date(TIME_UTC);
			
			if ($op_info['op_type'] == 0){
				//追加保证金，判断用户余额，是否足以支付保证金
				if ($op_info['op_val'] > $user_user['money']){
					$this->error("配资人余额不足，需要:".format_price($op_info['op_val']).';实际:'.format_price($user_user['money']));
				}
			}else if ($op_info['op_type'] == 2){
				//增资  判断投资者帐户余额，是否足以 增资金额
				$def_num = $op_info["op_val"] - $op_info["lever"];
				$def_money = $op_info["cost_money"] * $def_num;
				
				if ($def_money > $invest_user_user['money']){
					$this->error("投资者帐户余额不足，需要:".format_price($def_money).';实际:'.format_price($invest_user_user['money']));
				}
				
				if($op_info["type"]==2)
				{
					//4、计算投，服务费,利息差（只有按月收费才有）
					//相差天数
					$q_date_diff = (to_timespan($op_info["next_fee_date"],'Y-m-d') - to_timespan(to_date(TIME_UTC,'Y-m-d')))/86400;
					if ($q_date_diff < 0) $q_date_diff = 0;
					
					$add_rate_fee = ($def_money * $op_info["rate"]) * $q_date_diff;
					$add_site_fee = ($def_money * $op_info["site_rate"]) * $q_date_diff;
					
					if ($add_rate_fee + $add_site_fee > $user_user['money']){
						$this->error("配资人余额不足补增资利息差，需要:".format_price($add_rate_fee + $add_site_fee).';实际:'.format_price($user_user['money']));
					}
				}
			}
		}else{
			//$this->error('状态不匹配,上一状态:'.get_peizi_op_status($op_info['op_status'],$op_info['op_type']).';准备变更状态:'.get_peizi_op_status($data['op_status'],$op_info['op_type']));
		}
		
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_op",$data,"UPDATE","id = ".$data['id']);
		if($GLOBALS['db']->affected_rows()){
			
			require_once APP_ROOT_PATH.'system/libs/user.php';
			
			if ($data['op_status']==3){
				if ($op_info['op_type'] == 0){
					//追加保证金
					
					modify_account(array('money'=>-$op_info["op_val"],'lock_money'=>$op_info["op_val"]), $op_info["user_id"],'冻结追加的保证金,配资编号:'.$op_info["peizi_order_id"],30);
						
					$op_data = array();
					$op_data["cost_money"] = $op_info["cost_money"] + $op_info["op_val"];
					$op_data["id"] = $op_info["peizi_order_id"];
					$result=M("PeiziOrder")->save ($op_data);
				}else if ($op_info['op_type'] == 1){
					//延期
					//1、调整结束时间
					$op_data = array();
					$op_data["end_date"] = get_peizi_end_date($op_info["end_date"],$op_info["op_val"],$op_info["type"],$op_info["is_holiday_fee"]);
					$op_data["id"] = $op_info["peizi_order_id"];
					//2、调整time_limit_num值
					$op_data["time_limit_num"] = $op_info["time_limit_num"] + $op_info["op_val"];
					
					$result=M("PeiziOrder")->save ($op_data);
				}else if ($op_info['op_type'] == 2){
					//增资  
					$def_num = $op_info["op_val"] - $op_info["lever"];
					$def_money = $op_info["cost_money"] * $def_num;
	
					$op_data = array();
					$op_data["borrow_money"] = $op_info["borrow_money"] + $def_money ;
					$op_data["lever"] = $op_info["op_val"];
					
					
					//借款利率，服务费利率，不变, 重算每天/月： 利息，服务费 以及 预警线，平仓线
					
					$op_data["rate_money"] = $op_info['rate'] * $op_data["borrow_money"];
					$op_data["site_money"] = $op_info['site_rate'] * $op_data["borrow_money"];
					
					$parma = get_peizi_conf($op_info['peizi_conf_id'],$op_data["borrow_money"],$op_data["lever"],0,0);
					
					$op_data["warning_coefficient"] = $parma['warning_coefficient'];
					$op_data["open_coefficient"] = $parma['open_coefficient'];
										
					$op_data["warning_line"] = $parma['warning_line'];
					$op_data["open_line"] = $parma['open_line'];
	
					if($op_info["type"]==2)
					{
						//4、计算投，服务费,利息差（只有按月收费才有）
						//相差天数
						$q_date_diff = (to_timespan($op_info["next_fee_date"],'Y-m-d') - to_timespan(to_date(TIME_UTC,'Y-m-d')))/86400;	
						if ($q_date_diff < 0) $q_date_diff = 0;
						
						$add_rate_fee = ($def_money * $op_info["rate"]) * $q_date_diff;						
						$add_site_fee = ($def_money * $op_info["site_rate"]) * $q_date_diff;
						
						if ($add_rate_fee > 0){						
							modify_account(array("money"=>-$add_rate_fee), $op_info["user_id"],'增资的月利息差额,配资编号:'.$op_info["peizi_order_id"],34);							
							$op_data["total_rate_money"] = $op_data["total_rate_money"] + $add_rate_fee;													
						}
						
						if ($add_site_fee > 0){
							modify_account(array("money"=>-$add_site_fee), $op_info["user_id"],'增资的服务费差额,配资编号:'.$op_info["peizi_order_id"],33);
							$op_data["total_site_money"] = $op_data["total_site_money"] + $add_site_fee;
						}
											
					}
					$op_data["id"] = $op_info["peizi_order_id"];
					
					$result=M("PeiziOrder")->save ($op_data);
					
					//扣除投资人的  $def_money 
					modify_account(array('money'=>-$def_money), $op_info['invest_user_id'],'配资人增资,配资编号:'.$op_info["peizi_order_id"],36);
					
					//更新，借款 推荐人 的：累计被邀请人员的借款金额；累计被邀请人员的投资金额
					if ($op_info['p_user_id'] > 0){
						update_invite_money($op_info['p_user_id'],1,$def_money);
					}
					
					//更新，投资 推荐人 的：累计被邀请人员的投资金额
					if ($op_info['p_invest_user_id'] > 0){
						update_invite_money($op_info['p_invest_user_id'],0,$def_money);
					}
					
					
				}else if ($op_info['op_type'] == 3){
					//减资
					$def_num = $op_info["lever"] - $op_info["op_val"] ;
					$def_money = $op_info["cost_money"] * $def_num;
					
					$op_data = array();
					$op_data["borrow_money"] = $op_info["borrow_money"] - $def_money;//新的借款金额
					$op_data["lever"] = $op_info["op_val"];//新的 倍率

					//借款利率，服务费利率，不变, 重算每天/月： 利息，服务费 以及 预警线，平仓线
						
					$op_data["rate_money"] = $op_info['rate'] * $op_data["borrow_money"];
					$op_data["site_money"] = $op_info['site_rate'] * $op_data["borrow_money"];
						
					$parma = get_peizi_conf($op_info['peizi_conf_id'],$op_data["borrow_money"],$op_data["lever"],0,0);
						
					$op_data["warning_coefficient"] = $parma['warning_coefficient'];
					$op_data["open_coefficient"] = $parma['open_coefficient'];
					
					$op_data["warning_line"] = $parma['warning_line'];
					$op_data["open_line"] = $parma['open_line'];
										
	
					$op_data["id"] = $op_info["peizi_order_id"];
					
					$result=M("PeiziOrder")->save ($op_data);
					
					//退还给投资人  $def_money
					modify_account(array('money'=>$def_money), $op_info['invest_user_id'],'配资人减资,配资编号:'.$op_info["peizi_order_id"],36);
				}else if ($op_info['op_type'] == 4){
					//提取赢余
					if($op_info["op_val"]>0)
					{
						require_once APP_ROOT_PATH.'system/libs/user.php';
						modify_account(array("money"=>$op_info["op_val"]), $op_info["user_id"],'提取赢余,配资编号:'.$op_info["peizi_order_id"],37);
					};
				}else if ($op_info['op_type'] == 5){
					
					//平仓
					$order_id = $op_info["peizi_order_id"];
					$data = do_peizi_pc_calc_1($order_id,$_REQUEST['stock_money'],$_REQUEST['other_fee'],$_REQUEST['other_memo']);
					
					$result=M("PeiziOrder")->save ($data);
					if (false !== $result) {
						do_peizi_pc_calc_2($order_id);			
					}
					
					$this->assign("jumpUrl",u("PeiziOrderOp/op2"));
				}
				
			}
			
			
			save_log(L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		}else{
			//错误提示
			save_log(L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,L("UPDATE_FAILED"));
		}
	}
}
?>