<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class PeiziHolidayAction extends CommonAction{
	public function index()
	{

		$map = array ();
		
		
		if (isset($_REQUEST['year']) && $_REQUEST['year'] != '') {
			$map['year'] = trim($_REQUEST['year']);			
		}else{
			$map['year'] = to_date(TIME_UTC,'Y');
		}
		
		$this->assign("year",$map['year']);
		
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}
		
		
		$sql_str = "select a.*  from ".DB_PREFIX."peizi_holiday a  where  1 = 1 ";
		
		
		//日期期间使用in形式，以确保能正常使用到索引
		if( isset($map['year']) && $map['year'] <> ''){
			$sql_str .= " and a.year = '".$map['year']."'";
		}
		
		$sql_str .= ' order by year, holiday';
	
		$model = D();
		//print_r($map);
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter, '', false);
		
		//dump($result);exit;
		$this->display ();
		return;
	}
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;
		$vo = M("PeiziHoliday")->where($condition)->find();
		$this->assign ( 'vo', $vo );
		$this->display ();
	
	}
	
	public function update(){
		$data = M("PeiziHoliday")->create ();
		

		if($data['holiday']==""){
			$this->error(L("请填写日期"));
		}
		
		$data['year'] = to_date(to_timespan($data['holiday']),'Y');
		
		if($data['year']==""){
			$this->error(L("请填写年份"));
		}
		
		
		$list=M("PeiziHoliday")->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($data['name'].L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"),0,url("index","PeiziOrder#op0"));
		} else {
			//错误提示
			save_log($data['name'].L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$data['name'].L("UPDATE_FAILED"));
		}
	}
	
	public function insert()
	{
		B('FilterString');
		$data = M("PeiziHoliday")->create ();
	
		if($data['holiday']==""){
			$this->error(L("请填写日期"));
		}
		
		$data['year'] = to_date(to_timespan($data['holiday']),'Y');
				
		if($data['year']==""){
			$this->error(L("请填写年份"));
		}
		
		
		// 更新数据
		$list=M("PeiziHoliday")->add ($data);
	
		if (false !== $list) {
			$this->assign("jumpUrl",u(MODULE_NAME."/index"));
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$dbErr = M()->getDbError();
			save_log($data['name'].L("INSERT_FAILED").$dbErr,0);
			$this->error(L("INSERT_FAILED").$dbErr);
		}
	}
		
	public function delete(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
			//删除的验证
			$list = M("PeiziHoliday")->where ( $condition )->delete();
			if ($list!==false) {
				save_log(l("FOREVER_DELETE_SUCCESS"),1);
				$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
			} else {
				save_log(l("FOREVER_DELETE_FAILED"),0);
				$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
			}
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
}
?>