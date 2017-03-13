<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

class LicaiHistoryAction extends CommonAction{
	public function index()
	{	
		$id = intval($_REQUEST["id"]);
		if(!$id)
		{
			$this->error("操作失败，请返回重试");
		}
		$condition = " and licai_id = ".$id;
				
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		
		$page_size = 10;
		
		$limit = (($page-1)*$page_size).",".$page_size;
		$result = array();
		
		$licai = $GLOBALS["db"]->getRow("select name,id from ".DB_PREFIX."licai where id=".$id);

		$this->assign("licai",$licai);
		
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."licai_history  where 1=1 ".$condition);
		
		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."licai_history where 1=1 ".$condition." order by history_date desc limit ".$limit);
			
		}
		
		foreach($result['list']  as $k => $v)
		{
			$result['list'][$k]["rate_format"] = number_format($v['rate'],2)."%";
			$result['list'][$k]["net_value_format"] = format_price($v['net_value']);
		}
		
		$this->assign("list",$result['list']);
		
		$page = new Page($result['count'],$page_size);   //初始化分页对象 		
		$p  =  $page->show();
		$this->assign('page',$p);
		$this->assign('main_title',"收益率列表");
		$this->display ();
	}
	
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		$vo = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."licai_history where  id = ".$id);
		$this->assign ( 'vo', $vo );
		
		$this->display ();
	}
	public function update()
	{
		B('FilterString');
		$data = M(MODULE_NAME)->create();
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		
		$log_info_array = M(MODULE_NAME)->where("id=".intval($data['id']))->find();
		
		$log_info = $log_info["licai_id"]."--".$log_info["id"];
		//print_r($log_info);die;
		//开始验证有效性
		
		if(!check_empty($data['history_date']))
		{
			$this->error("请输入日期");
		}	
		if(!check_empty($data['net_value']))
		{
			$this->error("请输入当日净利");
		}
		if(!check_empty($data['rate']))
		{
			$this->error("请输入利率");
		}
		
		$list=M(MODULE_NAME)->save ($data);
		
		if (false !== $list) {
			
			require_once(APP_ROOT_PATH."system/libs/licai.php");
			
			syn_licai_status($log_info_array['licai_id'],0);
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
			
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
		
	}
	public function delete()
	{
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['title'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
		
				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					clear_auto_cache("get_help_cache");
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	public function add()
	{
		$id = intval($_REQUEST["id"]);
		
		if(!$id)
		{
			$this->error("操作失败，请返回重试");
		}
		
		$licai = $GLOBALS["db"]->getRow("select name,id from ".DB_PREFIX."licai where id=".$id);

		$this->assign("licai",$licai);
		
		$this->display ();
	}
	public function insert()
	{
		B('FilterString');
		$data = M(MODULE_NAME)->create();
		$this->assign("jumpUrl",u(MODULE_NAME."/add",array("id"=>$data["licai_id"])));
		
		$log_info = $data["licai_id"];
		
		//开始验证有效性
		
		if(!check_empty($data['history_date']))
		{
			$this->error("请输入日期");
		}	
		if(!check_empty($data['net_value']))
		{
			$this->error("请输入当日净利");
		}
		if(!check_empty($data['rate']))
		{
			$this->error("请输入利率");
		}
		
		
		$list=M(MODULE_NAME)->add ($data);
		
		$log_info .= "--".$list["id"];
		
		if (false !== $list) {
			
			require_once(APP_ROOT_PATH."system/libs/licai.php");
			
			syn_licai_status($data['licai_id'],0);
			
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"),0,$log_info.L("INSERT_FAILED"));
		}
	}
	public function add_list()
	{
		$id = intval($_REQUEST["id"]);
		
		if(!$id)
		{
			$this->error("操作失败，请返回重试");
		}
		
		$licai = $GLOBALS["db"]->getRow("select name,id from ".DB_PREFIX."licai where id=".$id);

		$this->assign("licai",$licai);
		
		$this->display ();
	}
	public function insert_list()
	{
		B('FilterString');
		
		$data = array();
		$history_begin_date = strim($_REQUEST['history_begin_date']);
		$history_end_date = strim($_REQUEST['history_end_date']);
		$data['licai_id'] = intval($_REQUEST['licai_id']);
		$data['net_value'] = floatval($_REQUEST['net_value']);
		$data['rate'] = floatval($_REQUEST['rate']);
		
		$this->assign("jumpUrl",u(MODULE_NAME."/add_list",array("id"=>$data["licai_id"])));
		
		$log_info = $data["licai_id"];

		//开始验证有效性
		
		if(!check_empty($history_begin_date))
		{
			$this->error("请输入开始日期");
		}	
		if(!check_empty($history_end_date))
		{
			$this->error("请输入结束日期");
		}
		if(!check_empty($data['net_value']))
		{
			$this->error("请输入当日净利");
		}
		if(!check_empty($data['rate']))
		{
			$this->error("请输入利率");
		}
		
		$begin_date = to_timespan($history_begin_date);
		$end_date = to_timespan($history_end_date);

		if($begin_date > $end_date)
		{
			$this->error("开始时间不能大于结束时间");
		}
		
		$item_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."licai_history where history_date >= '".$begin_date."' and history_date <= '".$end_date."' and licai_id =".$data['licai_id']);
		
		if($item_count>0)
		{
			$this->error("有重复的日期，请调整时间");
		}
		while($begin_date<=$end_date)
		{
			$data['history_date'] = to_date($begin_date);
			
			$list=M(MODULE_NAME)->add ($data);
			
			$log_info .= "--".$list["id"];
			
			if (false !== $list) {
				
				require_once(APP_ROOT_PATH."system/libs/licai.php");
				
				syn_licai_status($data['licai_id'],0);
				
				save_log($log_info.L("INSERT_SUCCESS"),1);
				//$this->success(L("INSERT_SUCCESS"));
			} else {
				//错误提示
				save_log($log_info.L("INSERT_FAILED"),0);
				//$this->error(L("INSERT_FAILED"),0,$log_info.L("INSERT_FAILED"));
			}
			$begin_date = to_timespan(to_date($begin_date+3600*24));
		}
		
		$this->success(L("INSERT_SUCCESS"));
	}
}
?>