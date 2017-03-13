<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class PeiziConfAction extends CommonAction{
	public function index()
	{
		$peiziconf = M("PeiziConf")->findAll();
		foreach($peiziconf as $k=>$v){
			if($peiziconf[$k]['type']==0){
				$peiziconf[$k]['type'] = "天天赢";
			}elseif($peiziconf[$k]['type']==1){
				$peiziconf[$k]['type'] = "周周盈";
			}elseif($peiziconf[$k]['type']==2){
				$peiziconf[$k]['type'] = "月月赚";
			}
			if($peiziconf[$k]['is_holiday_fee']==0){
				$peiziconf[$k]['is_holiday_fee'] = "否";
			}else{
				$peiziconf[$k]['is_holiday_fee'] = "是";
			}
		}
		//$contract = M("Contract")->where("is_effect=1 and is_delete=0")->findAll();
		$this->assign("main_title","配资参数表");
		//$this->assign("contract",$contract);
		
		$this->assign("peiziconf",$peiziconf);
		$this->display();
	}
	
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;
	}	

	//配资参数编辑
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		$conf = M("PeiziConf")->where("id = ".$id)->find();
		//协议
		$contract = M("Contract")->where("is_effect=1 and is_delete=0")->findAll();
		$this->assign("contract",$contract);
		
		$this->assign("main_title","编辑");
		$this->assign("conf",$conf);
		$this->display();
	}
	
	//配置
	public function edits()
	{
		$id = intval($_REQUEST ['id']);
		
		//$type = M("PeiziConf")->where("id = ".$id)->field("type");
		$conf = M("PeiziConf")->where("id = ".$id)->find();

		
		$list = M("PeiziConfRateList")->where("pid = ".$id)->order('min_lever,min_month,min_money')->findAll();
		$this->assign("main_title","配置列表");
		$this->assign("list",$list);
		$this->assign("type",$conf['type']);
		$this->assign("pid",$id);
		$this->display();
	}
	
	//配置编辑
	public function peizhi_edit()
	{
		$id = intval($_REQUEST ['id']);
		$list = M("PeiziConfRateList")->where("id = ".$id)->find();
		$pid = intval($list['pid']);
		$peizi = M("PeiziConf")->where("id = ".$pid)->find();
		$this->assign("peizi",$peizi);
		
		//配资倍数[暂时没用]
		//$beishu = M("PeiziConfLeverList")->where("pid = ".$id)->findAll(); 
		
		$this->assign("main_title","配置列表");
		$this->assign("list",$list);
		$this->display();
	}
	
	//配置更新操作
	public function update_peizhi_edit(){
		B('FilterString');
		$data = M("PeiziConfRateList")->create ();
		
		$list=M("PeiziConfRateList")->save($data);
		// 更新数据
		if (false !== $list) {
			//成功提示
			save_log(L("UPDATE_SUCCESS"),1);
			//$this->assign("jumpUrl",u(MODULE_NAME."/edits",array('id'=>$data['pid'])));
			$this->success(L("UPDATE_SUCCESS"));			
		} else {
			//错误提示
			save_log(L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,L("UPDATE_FAILED"));
		}
	}
	
	//倍率配置
	public function op_edit()
	{
		$pid = intval($_REQUEST ['pid']);
		$lever_conf = M("PeiziConfLeverList")->where("pid = ".$pid)->order('min_money')->findAll();
		$this->assign("main_title","等级配置");
		$this->assign("pid",$pid);
		$this->assign("lever_conf",$lever_conf);
		
		$peizi = M("PeiziConf")->where("id = ".$pid)->find();
		$this->assign("peizi",$peizi);
		
		$this->display();
	}
	
	//倍率配置更新操作
	public function update_op_edit()
	{
		B('FilterString');
		$data = M("PeiziConfLeverList")->create ();
		
		$pid = intval($_REQUEST['pid']);
		
		$config_data = array();
		$id_list = $_REQUEST['config']['id'];
		$min_money_list = $_REQUEST['config']['min_money'];
		$max_money_list = $_REQUEST['config']['max_money'];
		$lever_list_list = $_REQUEST['config']['lever_list'];
		//print_r($_REQUEST['config']);exit;
		/*
		foreach($ids as $k=>$v){
			if($min_moneys[$k]!="" || $max_moneys[$k]!="" || $lever_lists[$k]!=""){
				$sv = array();
				$sv['id'] = intval($ids[$k]);
				$sv['pid'] = intval($pid);
				$sv['min_money'] = intval($min_moneys[$k]);
				$sv['max_money'] = intval($max_moneys[$k]);
				$sv['lever_list'] = strim($lever_lists[$k]);
				$config_data[] = $sv;
			}
		}
		*/
		
		M("PeiziConfLeverList")->where("pid = ".$pid)->delete();
		foreach($min_money_list as $k=>$v){
			$datas = array();
			$datas['id'] =  $id_list[$k];
			$datas['pid'] =  $pid;
			$datas['min_money'] =  intval($min_money_list[$k]);
			$datas['max_money'] =  intval($max_money_list[$k]);
			$datas['lever_list'] =  strim($lever_list_list[$k]);
			if($datas['min_money']!="" || $datas['max_money']!="" || $datas['lever_lists']!=""){
				
				$list=M("PeiziConfLeverList")->add($datas);
			}
		}
		// 更新数据
		if (false !== $list) {
			//成功提示
			save_log(L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log(L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,L("UPDATE_FAILED"));
		}
	}
	
	
	//警戒,平仓系数配置
	public function lever_coe_edit()
	{
		$pid = intval($_REQUEST ['pid']);
		$lever_conf = M("PeiziConfLeverCoefficientList")->where("pid = ".$pid)->order('lever')->findAll();
		$this->assign("main_title","警戒,平仓系数配置");
		$this->assign("pid",$pid);
		$this->assign("lever_conf",$lever_conf);
		$this->display();
	}
	
	//警戒,平仓系数配置 更新操作
	public function update_lever_coe_edit()
	{
		B('FilterString');
		$data = M("PeiziConfLeverCoefficientList")->create ();
	
		$pid = intval($_REQUEST['pid']);
	
		//$config_data = array();
		//$config_data = $_REQUEST['config'];	

		$id_list = $_REQUEST['config']['id'];
		$lever_list = $_REQUEST['config']['lever'];
		$warning_coefficient_list = $_REQUEST['config']['warning_coefficient'];
		$open_coefficient_list = $_REQUEST['config']['open_coefficient'];
		
		
		M("PeiziConfLeverCoefficientList")->where("pid = ".$pid)->delete();
		foreach($lever_list as $k=>$v){
			
			$datas = array();
			$datas['id'] =  intval($id_list[$k]);
			$datas['pid'] = $pid;
			$datas['lever'] =  $lever_list[$k];
			$datas['warning_coefficient'] =  floatval($warning_coefficient_list[$k]);
			$datas['open_coefficient'] = floatval($open_coefficient_list[$k]);
		//	print_r($datas);
		//	exit;
			if($datas['lever']!="" && $datas['pid']!="" && $datas['warning_coefficient']!="" && $datas['open_coefficient']!=""){
	
				$list=M("PeiziConfLeverCoefficientList")->add($datas);
			}
		}
		// 更新数据
		if (false !== $list) {
			//成功提示
			save_log(L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log(L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,L("UPDATE_FAILED"));
		}
	}
		
	//股票配资index更新操作
	public function update()
	{
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		//$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log(L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log(L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,L("UPDATE_FAILED"));
		}
	}
	
	
	
	public function add()
	{
		$pid = intval($_REQUEST['pid']);
		$peizi = M("PeiziConf")->where("id = ".$pid)->find();
		$this->assign("peizi",$peizi);
		$this->assign("pid",$pid);
		
		$this->assign("main_title","新增");
		$this->display();
	}
	
	
	public function insert()
	{
		B('FilterString');
		$data = M("PeiziConfRateList")->create ();
	
		// 更新数据
		$list=M("PeiziConfRateList")->add ($data);
	
		if (false !== $list) {
			$this->assign("jumpUrl",u(MODULE_NAME."/edits",array('id'=>$data['pid'])));
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($data['name'].L("INSERT_FAILED").$dbErr,0);
			$this->error(L("INSERT_FAILED").$dbErr);
		}
	}
	

	public function get_is_show_today()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M("PeiziConfRateList")->where("id=".$id)->getField("name");
		$c_is_effect = M("PeiziConfRateList")->where("id=".$id)->getField("is_show_today");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M("PeiziConfRateList")->where("id=".$id)->setField("is_show_today",$n_is_effect);
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
	
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;
	}
	
	public function delete(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
			//删除的验证
				
			$rel_data = M("PeiziConfRateList")->where($condition)->findAll();
			foreach($rel_data as $data)
			{
				$info[] = $data['name'];
			}
			if($info) $info = implode(",",$info);
			$list = M("PeiziConfRateList")->where ( $condition )->delete();
	
			if ($list!==false) {
				save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
				$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
			} else {
				save_log($info.l("FOREVER_DELETE_FAILED"),0);
				$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
			}
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function add_index(){
		$id = intval($_REQUEST ['id']);
		$conf = M("PeiziConf")->where("id = ".$id)->find();
		//协议
		$contract = M("Contract")->where("is_effect=1 and is_delete=0")->findAll();
		$this->assign("contract",$contract);
		
		$this->assign("main_title","新增");
		$this->assign("conf",$conf);
		$this->display();
	}
	
	public function insert_index(){
		B('FilterString');
		$data = M("PeiziConf")->create ();
		// 更新数据
		$list=M("PeiziConf")->add ($data);
	
		if (false !== $list) {
			$this->assign("jumpUrl",u(MODULE_NAME."/add"));
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($data['name'].L("INSERT_FAILED").$dbErr,0);
			$this->error(L("INSERT_FAILED").$dbErr);
		}
	}
	

	public function del_index(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
			$pcondition = array ('pid' => array ('in', explode ( ',', $id ) ) );
			//删除的验证
	
			$rel_data = M("PeiziConf")->where($condition)->findAll();
			foreach($rel_data as $data)
			{
				$info[] = $data['name'];
			}
			if($info) $info = implode(",",$info);
			$list = M("PeiziConf")->where ( $condition )->delete();
	
			$list = M("PeiziConfLeverList")->where ( $pcondition )->delete();
			$list = M("PeiziConfLeverCoefficientList")->where ( $pcondition )->delete();
			$list = M("PeiziConfRateList")->where ( $pcondition )->delete();
			
			if ($list!==false) {
				save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
				$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
			} else {
				save_log($info.l("FOREVER_DELETE_FAILED"),0);
				$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
			}
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
}
?>