<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class DepartmentsAction extends CommonAction{
	
	private function auth(){
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		return $adm_session;
	}
	
	public function index()
	{	
		$condition['is_delete'] = 0;
		$condition['is_department'] = 1;
		$adm_session = $this->auth();
		
		if($adm_session['pid'] > 0){
			$this->error (l("NO_AUTH"));
		}
		
		if($adm_session['adm_name']!=app_conf("DEFAULT_ADMIN")){
			if($adm_session['is_department'] == 1){
				$condition['id'] = $adm_session['adm_id'];
				$this->assign("is_department",1);
			}
		}
		 
		if(strim($_REQUEST['adm_name'])!=""){
			$condition['adm_name'] = array("eq",strim($_REQUEST['adm_name']));
			$this->assign("adm_name",strim($_REQUEST['adm_name']));
		}
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $condition );
		}
		
		$model = D ("Admin");
		if (! empty ( $model )) {
			$this->_list ( $model, $condition );
		}
		$list = $this->get("list");
		
		
		$row = 0;
		foreach($list as $k=>$v)
		{
			$v['role_id_format'] = M("Role")->where("id=".$v['role_id'])->getField("name");
			$list[$k] = $v;
			$row++;
		}
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	public function trash()
	{
		$adm_session = $this->auth();
	
		
		if($adm_session['is_department'] == 1 || $adm_session['pid'] > 0){
			$this->error (l("NO_AUTH"));
		}
		
		$department = M("Admin")->where('is_delete = 1')->findAll();
		
		$this->assign("department",$department);
		$this->display ();
	}
	
	public function edit()
	{
		$adm_session = $this->auth();
		
		if($adm_session['adm_name']!=app_conf("DEFAULT_ADMIN")){
			$this->assign("is_department",$adm_session['is_department']);
		}
		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;
		$vo = M("Admin")->where($condition)->find();
		$this->assign ( 'vo', $vo );
		
		$roles = M("Role")  -> findAll();
		
		$roles_arr = explode(",",$vo['role_ids']);
		
		foreach($roles as $k=>$v){
			if(in_array($v['id'],$roles_arr)){
				$roles[$k]['selected'] = 1;
			}
		}
		
		$this->assign ( 'roles', $roles );
		
		$this->display ();
		
	}
	
	public function update()
	{
		$data = M("Admin")->create ();
		
		if(!check_empty($data['adm_password']))
		{
			unset($data['adm_password']);  //不更新密码
		}
		else
		{
			$data['adm_password'] = md5(trim($data['adm_password']));
		}
		
		if($data['role_id']==0){
			$this->error("请选择部门角色");
		}
		
		$data['role_ids'] = implode(",",$_REQUEST['role_ids']);
		
		if(intval($data['referrals_rate']) > 10){
			$this->error("提成系数不得超过10%");
		}
		
		// 更新数据
		$list=M("Admin")->save ($data);
		
		if (false !== $list) {
			//成功提示
			$total = M("Admin")->where("pid=".$data['id'])->count();
			M("Admin")->where("id=".$data['id'])->setField("referrals_count",$total);
			
			save_log($data['name'].L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($data['name'].L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$data['name'].L("UPDATE_FAILED"));
		}
	}
	
	public function delete() {
		
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		
		$adm_session = $this->auth();
		
		if($adm_session['is_department'] == 1 || $adm_session['pid'] > 0){
			$this->error (l("NO_AUTH"),$ajax);
		}
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {

			$condition = array ();
			$condition['id'] = array ('in', explode ( ',', $id ) );
			$condition['is_delete'] = 0;
			
			$rel_data = M("Admin")->where($condition)->findAll();
			foreach($rel_data as $k=>$v){
				$info[] =$v['name'];
			}
			if($info) $info = implode(",",$info);
			$list = M("Admin")->where ( $condition )->setField ( 'is_delete', 1 );
			if ($list!==false) {
				save_log($info.l("DELETE_SUCCESS"),1);
				$this->success (l("DELETE_SUCCESS"),$ajax);
			} else {
				save_log($info.l("DELETE_FAILED"),0);
				$this->error (l("DELETE_FAILED"),$ajax);
			}
			
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function restore(){
		$adm_session = $this->auth();
		
		if($adm_session['is_department'] == 1 || $adm_session['pid']>0){
			$this->error (l("NO_AUTH"));
		}
		
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
		
			$condition = array ();
			$condition['id'] = array ('in', explode ( ',', $id ) );
			$condition['is_delete'] = 1;
				
			$rel_data = M("Admin")->where($condition)->findAll();
			foreach($rel_data as $k=>$v){
				$info[] =$v['adm_name'];
			}
			if($info) $info = implode(",",$info);
			$list = M("Admin")->where ( $condition )->setField ( 'is_delete', 0);
			if ($list!==false) {
				
				save_log($info.l("RESTORE_SUCCESS"),1);
				$this->success (l("RESTORE_SUCCESS"),$ajax);
			} else {
				save_log($info.l("RESTORE_FAILED"),0);
				$this->error (l("RESTORE_FAILED"),$ajax);
			}
		
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function foreverdelete(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		
		$adm_session = $this->auth();
		
		if($adm_session['is_department'] == 1 || $adm_session['pid'] >0){
			$this->error (l("NO_AUTH"),$ajax);
		}
		
		
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
			//删除的验证

			$list = M("Admin")->where ( $condition )->delete();
				
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
	
	public function add() {
		
		$adm_session = $this->auth();
		
		if($adm_session['is_department'] == 1||$adm_session['pid'] > 0){
			$this->error (l("NO_AUTH"));
		}
		
		
		/*
		$sql= " SELECT id,adm_name FROM ".DB_PREFIX."admin WHERE is_department=1 and is_delete=0 and is_effect=1 ";
		$departs = $GLOBALS['db']->getAll($sql);
		$this->assign ( 'departs', $departs );
		*/
		$roles = M("Role")  -> findAll();
		$this->assign ( 'roles', $roles );
		$this->display ();
		
	}
	
	
	public function insert()
	{
		$adm_session = $this->auth();
		
		if($adm_session['is_department'] == 1||$adm_session['pid'] > 0){
			$this->error (l("NO_AUTH"));
		}
		
		B('FilterString');
		$data = M("Admin")->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['adm_name']))
		{
			$this->error(L("ADM_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['adm_password']))
		{
			$this->error(L("ADM_PASSWORD_EMPTY_TIP"));
		}
		if($data['role_id']==0)
		{
			$this->error(L("ROLE_EMPTY_TIP"));
		}
		if(M("Admin")->where("adm_name='".$data['adm_name']."'")->count()>0)
		{
			$this->error(L("ADMIN_EXIST_TIP"));
		}
		
		if(intval($data['referrals_rate']) > 10){
			$this->error("提成系数不得超过10%");
		}
		
		// 更新数据
		$log_info = $data['adm_name'];
		$data['adm_password'] = md5(trim($data['adm_password']));
		$data['is_department'] = 1;
		$data['role_ids'] = implode(",",$_REQUEST['role_ids']);
		
		$list=M("Admin")->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
		
	}
	
	public function set_effect()
	{
		
		$ajax = intval($_REQUEST['ajax']);
		
		$adm_session = $this->auth();
		
		if($adm_session['is_department'] == 1||$adm_session['pid'] > 0){
			$this->error (l("NO_AUTH"));
		}
		
		$id = intval($_REQUEST['id']);
		$info = M("Admin")->where("id=".$id)->getField("name");
		$c_is_effect = M("Admin")->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M("Admin")->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	public function export_csv($page=1)
	{	
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		$condition['is_delete'] = 0;
		$condition['is_department'] = 1;
		
		$adm_session = $this->auth();
		if($adm_session['pid'] > 0){
			$this->error (l("NO_AUTH"));
		}
		
		if($adm_session['adm_name']!=app_conf("DEFAULT_ADMIN")){
			if($adm_session['is_department'] == 1){
				$condition['id'] = $adm_session['adm_id'];
				$this->assign("is_department",1);
			}
		}
		
		if(strim($_REQUEST['adm_name'])!=""){
			$condition['adm_name'] = array("eq",strim($_REQUEST['adm_name']));
		}
		
		
		
		$model = D ("Admin");
		
		$list = $model->where($condition)->limit($limit)->findAll();
		
		if($list){
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
	
			$export_value = array('id'=>'""','name'=>'""','real_name'=>'""','mobile'=>'""','role'=>'""','rate'=>'""','rcount'=>'""','money'=>'""','status'=>'""','time'=>'""','ip'=>'""');
			if($page == 1)
				$content = iconv("utf-8","utf-8","编号,部门,姓名,手机,所属角色,提成系数(%),投资人人数,可以用余额,状态,最后登录时间,最后登录IP");
	
			if($page==1)
				$content = $content . "\n";
			
			foreach($list as $k=>$v)
			{
				$export_value['id'] =  iconv('utf-8','utf-8','"' . $v['id'] . '"');
				$export_value['name'] = iconv('utf-8','utf-8','"' . $v['adm_name'] . '"');
				$export_value['real_name'] = iconv('utf-8','utf-8','"' . $v['real_name'] . '"');
				$export_value['mobile'] = iconv('utf-8','utf-8','"' . $v['mobile'] . '"');
				$export_value['role'] = iconv('utf-8','utf-8','"' . M("Role")->where("id=".$v['role_id'])->getField("name") . '"');
				$export_value['rate'] = iconv('utf-8','utf-8','"' . $v['referrals_rate'] . '"');
				$export_value['rcount'] = iconv('utf-8','utf-8','"' . $v['referrals_count'] . '"');
				$export_value['money'] = iconv('utf-8','utf-8','"' . $v['referrals_money'] . '"');
				$export_value['status'] = iconv('utf-8','utf-8','"' . ($v['is_effect']== 1 ? "有效": "无效") . '"'); 
				$export_value['time'] = iconv('utf-8','utf-8','"' . to_date($v['login_time']) . '"'); 
				$export_value['ip'] = iconv('utf-8','utf-8','"' . $v['login_ip'] . '"'); 
				
				$content .= implode(",", $export_value) . "\n";
			}
			header("Content-Disposition: attachment; filename=部门.csv");
			echo $content;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	}
	
	public function referrals(){
		$condition['is_delete'] = 0;
		$condition['is_department'] = 1;
		$adm_session = $this->auth();
		if($adm_session['pid'] > 0 || $adm_session['is_department'] == 1){
			$this->error (l("NO_AUTH"));
		}
		
		if($adm_session['adm_name']!=app_conf("DEFAULT_ADMIN")){
			if($adm_session['is_department'] == 1){
				$condition['id'] = $adm_session['adm_id'];
			}
		}
		
		if(strim($_REQUEST['adm_name'])!=""){
			$condition['adm_name'] = array("eq",strim($_REQUEST['adm_name']));
			$this->assign("adm_name",strim($_REQUEST['adm_name']));
		}
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $condition );
		}
		
		$model = D ("Admin");
		if (! empty ( $model )) {
			$this->_list ( $model, $condition );
		}
		$list = $this->get("list");
		
		$row = 0;
		foreach($list as $k=>$v)
		{
			$v['role_id_format'] = M("Role")->where("id=".$v['role_id'])->getField("name");
			$list[$k] = $v;
			$row++;
		}
		$this->assign("list",$list);
		
		$total_money = M("Admin")->where($map)->sum("referrals_money");
		$this->assign("total_money",$total_money);
		
		$this->display ();
		return;
	}
	
	
	public function referrals_log()
	{	
		$adm_id = intval($_REQUEST['id']);
		
		$adm_session = $this->auth();
		if($adm_session['pid'] > 0 || $adm_session['is_department'] == 1){
			$this->error (l("NO_AUTH"));
		}
		
		if($adm_session['adm_name']!=app_conf("DEFAULT_ADMIN")){
			if($adm_session['is_department'] == 1){
				$adm_id = $adm_session['adm_id'];
			}
		}
		
		$start_time = strim($_REQUEST['start_time']);
		$end_time = strim($_REQUEST['end_time']);
		
		
		//列表过滤器，生成查询Map对象
		if($start_time!="" && $end_time !=""){
			$map['create_date'] = array("in",date_in($start_time,$end_time));
		}
		elseif($start_time!="" && $end_time ==""){
			$map['create_time'] = array("gt",to_timespan($start_time));
		}
		elseif($start_time=="" && $end_time !=""){
			$map['create_time'] = array("lt",to_timespan($end_time));
		}
		
		$this->assign("start_time",$start_time);
		$this->assign("end_time",$end_time);
		
		if($adm_id > 0)
			$map['rel_admin_id'] = array("eq",$adm_id);
			
		$this->assign("admin_id",$adm_id);
		
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ("AdminReferrals");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		//echo $model->getLastSql();
		
		$total_money = M("AdminReferrals")->where($map)->sum("money");
		$this->assign("total_money",$total_money);
		
		$this->display ();
		return;
	}
	
}
?>