<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class PeiziOrderStockMoneyAction extends CommonAction{
	
	public function index()
	{
		$pid = intval($_REQUEST['pid']);
		$sql_str = "select * from ".DB_PREFIX."peizi_order_stock_money where peizi_order_id = ".$pid;
		$model = D();
		//print_r($map);
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "", 'stock_date', false);
		$this->assign('pid', $pid);
		$this->assign('list', $voList);
		
		$this->assign('add_url', u("PeiziOrderStockMoney/add",array('pid'=>$pid)));
		$this->display ();
	}
	
	public function add()
	{
		$pid = intval($_REQUEST['pid']);
		$this->assign('peizi_order_id', $pid);
		$this->assign('stock_date', to_date(TIME_UTC,'Y-m-d'));
		
		
		$sql = "select warning_line,open_line from ".DB_PREFIX."peizi_order where id = ".$pid;
		$peizi_order = $GLOBALS['db']->getRow($sql);
		
		$this->assign('warning_line', $peizi_order['warning_line']);
		$this->assign('open_line', $peizi_order['open_line']);
		
		
		$this->assign('back_url', u("PeiziOrderStockMoney/index",array('pid'=>$pid)));
		$this->display();
	}
	
	public function edit() {		
		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		
		$status = intval($_REQUEST ['status']);
		if ($status == 0){
			$this->assign('back_url', u("PeiziOrderStockMoney/index",array('pid'=>$vo['peizi_order_id'])));
		}else{
			$this->assign('back_url', u("PeiziOrder/trade_fee_date"));
		}
		
		$this->assign('status', $status);
		$this->assign('vo', $vo);
		$this->display ();
	}
	
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				
				if ($list!==false) {
					save_log(l("DELETE_SUCCESS"),1);
					clear_auto_cache("get_help_cache");
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log(l("DELETE_SUCCESS"),0);
					$this->error (l("DELETE_SUCCESS"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add",array('pid'=>$data['peizi_order_id'])));
		
		
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			
			$sql = "select count(*) from ".DB_PREFIX."peizi_order where id = ".$data['peizi_order_id']. " and stock_date <= '".$data['stock_date']."'";
			$count = $GLOBALS['db']->getOne($sql);
			if ($count == 1){		
				$sql = "update ".DB_PREFIX."peizi_order set stock_money = '".$data['stock_money']."', stock_date = '".$data['stock_date']."' where id = ".$data['peizi_order_id'];
				$GLOBALS['db']->query($sql);
			}
			//成功提示
			save_log(L("INSERT_SUCCESS"),1);
			clear_auto_cache("get_help_cache");
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log(L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();	
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		
		$list=M(MODULE_NAME)->save($data);
		if (false !== $list) {
			$sql = "select count(*) from ".DB_PREFIX."peizi_order where id = ".$data['peizi_order_id']. " and stock_date <= '".$data['stock_date']."'";
			$count = $GLOBALS['db']->getOne($sql);
			if ($count == 1){
				
				$sql = "update ".DB_PREFIX."peizi_order set stock_money = '".$data['stock_money']."', stock_date = '".$data['stock_date']."' where id = ".$data['peizi_order_id'];
				$GLOBALS['db']->query($sql);
			}
			
			//成功提示
			save_log(L("UPDATE_SUCCESS"),1);
			clear_auto_cache("get_help_cache");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log(L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,L("UPDATE_FAILED"));
		}
	}
}
?>