<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class PeiziIndexshowAction extends CommonAction{
	public function index()
	{
		if(trim($_REQUEST['user_name'])!='')
		{
			$condition['user_name'] = array('like','%'.trim($_REQUEST['user_name']).'%');			
		}
		
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function add()
	{
		$conf_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("conf_list",$conf_list);
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign('vo', $vo);
		$conf_list = M("PeiziConf")->where('is_effect = 1')->findAll();
		$this->assign("conf_list",$conf_list);
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
					save_log($info.l("DELETE_SUCCESS"),1);
					clear_auto_cache("get_help_cache");
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_SUCCESS"),0);
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
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['user_name']))
		{
			$this->error("会员名称不能为空");
		}	
		if(!check_empty($data['money']))
		{
			$this->error("使用发财金盈利不能为空");
		}
		if(!check_empty($data['rate']))
		{
			$this->error("收益率不能为空");
		}
		if(!check_empty($data['stock_money']))
		{
			$this->error("操盘金额不能为空");
		}
		if(!check_empty($data['lever']))
		{
			$this->error("杠杆（倍数）不能为空");
		}
		if(!check_empty($data['cost_money']))
		{
			$this->error("操盘投入不能为空");
		}
		if(floatval($data['money'])<=0)
		{
			$this->error("请输入正确的使用发财金盈利");
		}
		if(floatval($data['stock_money'])<=0)
		{
			$this->error("请输入正确的操盘金额");
		}	
		if(floatval($data['cost_money'])<=0)
		{
			$this->error("请输入正确的操盘投入");
		}	
		// 更新数据
		$log_info = "排行榜列表，".$data['user_name'];
		$data['user_name'] = strim($data['user_name']);
		$data['money'] = floatval($data['money']);
		$data['rate'] = floatval($data['rate']);
		$data['stock_money'] = floatval($data['stock_money']);
		$data['lever'] = intval($data['lever']);	
		$data['cost_money'] = floatval($data['cost_money']);		
		$data["type"] = intval($data["type"]);	
		
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			clear_auto_cache("get_help_cache");
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();	
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("deal_name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['user_name']))
		{
			$this->error("会员名称不能为空");
		}	
		if(!check_empty($data['money']))
		{
			$this->error("使用发财金盈利不能为空");
		}
		if(!check_empty($data['rate']))
		{
			$this->error("收益率不能为空");
		}
		if(!check_empty($data['stock_money']))
		{
			$this->error("操盘金额不能为空");
		}
		if(!check_empty($data['lever']))
		{
			$this->error("杠杆（倍数）不能为空");
		}
		if(!check_empty($data['cost_money']))
		{
			$this->error("操盘投入不能为空");
		}
		if(floatval($data['money'])<=0)
		{
			$this->error("请输入正确的使用发财金盈利");
		}
		if(floatval($data['stock_money'])<=0)
		{
			$this->error("请输入正确的操盘金额");
		}	
		if(floatval($data['cost_money'])<=0)
		{
			$this->error("请输入正确的操盘投入");
		}	
		// 更新数据
		$log_info = "排行榜列表，".$data['user_name'];
		$data['user_name'] = strim($data['user_name']);
		$data['money'] = floatval($data['money']);
		$data['rate'] = floatval($data['rate']);
		$data['stock_money'] = floatval($data['stock_money']);
		$data['lever'] = intval($data['lever']);	
		$data['cost_money'] = floatval($data['cost_money']);		
		$data["type"] = intval($data["type"]);	
		$data["id"] = intval($data["id"]);
		
		$list=M(MODULE_NAME)->save($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			clear_auto_cache("get_help_cache");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
}
?>