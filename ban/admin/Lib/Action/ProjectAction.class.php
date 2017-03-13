<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

require_once APP_ROOT_PATH."/app/Lib/project_func.php"; 
class ProjectAction extends CommonAction{
	public function online_index()
	{	
		$deal_info['support_amount'] = doubleval($GLOBALS['db']->getOne("select sum(deal_price) from ".DB_PREFIX."project_order where deal_id = 64 and order_status=3 and is_refund=0"));
		$now=TIME_UTC;
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['time_status'])==1)
		{
			$map['_string'] = '(begin_time > '.TIME_UTC.')';			
		}
		
		if(intval($_REQUEST['time_status'])==2)
		{
			$map['_string'] = "(begin_time < '".TIME_UTC."') and ((end_time > '".TIME_UTC."') or (end_time = 0))";
		}
		
		if(intval($_REQUEST['time_status'])==3)
		{
			$map['_string'] = '(end_time < '.TIME_UTC.') and (end_time <> 0)';	
		}
		if($_REQUEST['type']=='NULL'){
			unset($_REQUEST['type']);
		}
		if($_REQUEST['type']!=NULL){
			$map['type']=intval($_REQUEST['type']);
		}
		
		if($_REQUEST['ips_bill_no']=='NULL'){
			unset($_REQUEST['ips_bill_no']);
		}
		if($_REQUEST['ips_bill_no']!=NULL){
			$ips_bill_no=intval($_REQUEST['ips_bill_no']);
			if($ips_bill_no>0){
				$map['_string'] = '(ips_bill_no !="")';
			}else{
				$map['_string'] = '(ips_bill_no = "")';
			}
			
		}
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			$map['cate_id'] = intval($_REQUEST['cate_id']);
		}
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$map['user_name'] = array('like','%'.trim($_REQUEST['user_name']).'%');
		}
		
		$create_time_2=empty($_REQUEST['create_time_2'])?to_date($now,'Y-m-d'):strim($_REQUEST['create_time_2']);
		$create_time_2=to_timespan($create_time_2)+24*3600;
		if(trim($_REQUEST['create_time_1'])!='')
		{
			$map[DB_PREFIX.'project.create_time'] = array('between',array(to_timespan($_REQUEST['create_time_1']),$create_time_2));
		}
		
		$map['is_effect'] = 1;		
		$map['is_delete'] = 0;		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$cate_list = M("ProjectCate")->findAll();
		$this->assign("cate_list",$cate_list);
		$this->display ();
	}
	
	public function submit_index()
	{
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			$map['cate_id'] = intval($_REQUEST['cate_id']);
		}
		
		if(intval($_REQUEST['user_id'])>0)
		{
			$map['user_id'] = intval($_REQUEST['user_id']);
		}
		$create_time_2=empty($_REQUEST['create_time_2'])?to_date($now,'Y-m-d'):strim($_REQUEST['create_time_2']);
		$create_time_2=to_timespan($create_time_2)+24*3600;
		if(trim($_REQUEST['create_time_1'])!='')
		{
			$map[DB_PREFIX.'project.create_time'] = array('between',array(to_timespan($_REQUEST['create_time_1']),$create_time_2));
		}
		
		if($_REQUEST['type']=='NULL'){
			unset($_REQUEST['type']);
		}
		
		if($_REQUEST['type']!=NULL){
 			$map['type']=intval($_REQUEST['type']);
		}
		if($_REQUEST['ips_bill_no']=='NULL'){
			unset($_REQUEST['ips_bill_no']);
		}
		if($_REQUEST['ips_bill_no']!=NULL){
			$ips_bill_no=intval($_REQUEST['ips_bill_no']);
			if($ips_bill_no>0){
				$map['_string'] = '(ips_bill_no !="")';
			}else{
				$map['_string'] = '(ips_bill_no = "")';
			}
			
		}
		$map['is_effect'] = array("in",array(0,2));
		$map['is_delete'] = 0;		

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$cate_list = M("ProjectCate")->findAll();
		$this->assign("cate_list",$cate_list);
		$this->display ();
	}
	
	public function delete_index()
	{
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			$map['cate_id'] = intval($_REQUEST['cate_id']);
		}
		
		if(strim($_REQUEST['user_name'])!='')
		{
			$map['user_name'] = array("like",'%'.strim($_REQUEST['user_name']).'%');
		}
		

		$map['is_delete'] = 1;		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$cate_list = M("ProjectCate")->findAll();
		$this->assign("cate_list",$cate_list);
		$this->display ();
	}
	
	public function add()
	{
		
		$cate_list = M("ProjectCate")->findAll();
		$cate_list = D("ProjectCate")->toFormatTree($cate_list,"name");
		$this->assign("cate_list",$cate_list);
		
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by pid asc");  //二级地址
		$this->assign("region_lv2",$region_lv2);
		//项目等级
		$user_level = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_level order by id ASC");
		$this->assign("user_level",$user_level);
		
		$this->assign("new_sort", M("Project")->max("sort")+1);
		$this->display();
	}
	
	public function add_investor(){
		$cate_list = M("ProjectCate")->findAll();
		$cate_list = D("ProjectCate")->toFormatTree($cate_list);
		$this->assign("cate_list",$cate_list);
		
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by id asc");  //二级地址
		$this->assign("region_lv2",$region_lv2);
		//项目等级
		$user_level = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_level order by level ASC");
		$this->assign("user_level",$user_level);
  		$this->assign("new_sort", M("Project")->max("sort")+1);
 		
 		$this->assign('stock_num',0);
 		$this->assign('unstock_num',0);
		//
		$this->assign("history_num",0);
		$plan_html=$this->fetch("add_new_history");
		$this->assign('history_html',$plan_html);
		
		$this->assign('plan_num',0);
		$this->assign('plan_step_num',0);
		$plan_html=$this->fetch("add_new_plan");
		$this->assign('plan_html',$plan_html);
		
		$this->assign("action",'insert_investor');
		$this->assign('attach_num',1);
		$attach_html=$this->fetch("add_new_attachment");
		$this->assign('attach_html',$attach_html);
		
		$this->display("edit_investor");
	}
	public function add_investor_item(){
		$return=array('status'=>1,'html'=>'');
		$num=intval($_REQUEST['num']);
		$html=strim($_REQUEST['html']);
		if($html=='add_new_plan'){
			$this->assign('plan_num',$num);
		}elseif($html='add_new_history'){
			$this->assign('history_num',$num);
		}
		$this->assign('num',$num);
		$return['html']=$this->fetch($html);
		ajax_return($return);
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		
		$vo['begin_time'] = $vo['begin_time']!=0?to_date($vo['begin_time']):'';
		$vo['end_time'] = $vo['end_time']!=0?to_date($vo['end_time']):'';
 		$this->assign ( 'vo', $vo );
		$this->assign ( 'aa', 10 );
		$cate_list = M("ProjectCate")->findAll();
		$cate_list = D("ProjectCate")->toFormatTree($cate_list,"name");
		$this->assign("cate_list",$cate_list);
		
		$region_pid = 0;
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by pid asc");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['name'] == $vo['province'])
			{
				$region_lv2[$k]['selected'] = 1;
				$region_pid = $region_lv2[$k]['id'];
				break;
			}
		}
		$this->assign("region_lv2",$region_lv2);
		
		
		if($region_pid>0)
		{
			$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$region_pid." order by pid asc");  //三级地址
			foreach($region_lv3 as $k=>$v)
			{
				if($v['name'] == $vo['city'])
				{
					$region_lv3[$k]['selected'] = 1;
					break;
				}
			}
			$this->assign("region_lv3",$region_lv3);
		}
		
		$qa_list = M("ProjectFaq")->where("deal_id=".$vo['id'])->order("sort asc")->findAll();
		$this->assign("faq_list",$qa_list);
		
		$user_level = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_level order by id ASC");
		$this->assign("user_level",$user_level);
		
		$this->display ();
	}
	
	public function edit_investor() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		if($vo['user_id']==0)$vo['user_id']  = '';
		$vo['begin_time'] = $vo['begin_time']!=0?to_date($vo['begin_time']):'';
		$vo['end_time'] = $vo['end_time']!=0?to_date($vo['end_time']):'';
		$vo['pay_end_time'] = $vo['pay_end_time']!=0?to_date($vo['pay_end_time']):'';
		$vo['business_create_time'] = $vo['business_create_time']!=0?to_date($vo['business_create_time']):'';
		$vo['history']=unserialize($vo['history']);
 		$history_num=$vo['history']?count($vo['history']):0;
  		$this->assign('history_num',$history_num);
		$vo['plan']=unserialize($vo['plan']);
 		$plan_num=$vo['plan']?count($vo['plan']):0;
   		$this->assign('plan_step_num',$plan_num);
		$vo['attach']=unserialize($vo['attach']);
  		$attach_num=$vo['attach']?count($vo['attach']):0;
		$this->assign('attach_num',$attach_num);
		$vo['stock']=unserialize($vo['stock']);
 		$stock_num=$vo['stock']?count($vo['stock']):0;
 		$this->assign('stock_num',$stock_num);

		$vo['unstock']=unserialize($vo['unstock']);
		$unstock_num=$vo['unstock']?count($vo['unstock']):0;
 		$this->assign('unstock_num',$unstock_num);
 		
 		//企业资质材料信息
 		$vo['audit_data']=unserialize($vo['audit_data']);
 		$audit_data=$vo['audit_data'];
 		$this->assign('audit_data',$audit_data);
 		
		$this->assign ( 'vo', $vo );
		$this->assign("action",'update_investor');
		$plan_html=$this->fetch("add_new_history");
 		$this->assign('history_html',$plan_html);
		
 		$this->assign('plan_num',1);
		$plan_html=$this->fetch("add_new_plan");
		$this->assign('plan_html',$plan_html);
		
		$user_level = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_level order by level ASC");
		$this->assign("user_level",$user_level);
		
		$cate_list = M("ProjectCate")->findAll();
		$cate_list = D("ProjectCate")->toFormatTree($cate_list,"name");
		$this->assign("cate_list",$cate_list);
		
		$region_pid = 0;
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by id asc");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['name'] == $vo['province'])
			{
				$region_lv2[$k]['selected'] = 1;
				$region_pid = $region_lv2[$k]['id'];
				break;
			}
		}
		$this->assign("region_lv2",$region_lv2);
		
		
		if($region_pid>0)
		{
			$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$region_pid." order by id asc");  //三级地址
			foreach($region_lv3 as $k=>$v)
			{
				if($v['name'] == $vo['city'])
				{
					$region_lv3[$k]['selected'] = 1;
					break;
				}
			}
			$this->assign("region_lv3",$region_lv3);
		}
		
		$qa_list = M("ProjectFaq")->where("deal_id=".$vo['id'])->order("sort asc")->findAll();
		$this->assign("faq_list",$qa_list);
		
 
		
		$this->display ();
	}
	
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error("请输入名称");
		}	
		
		if(intval($data['cate_id'])==0)
		{
			$this->error("请选择分类");
		}	
		if(floatval($data['limit_price'])<=0){
			$this->error("目标金额要大于0");
		}
			
		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
		if($data['begin_time']>$data['end_time']){
			$this->error("开始时间不能大于结束 时间");
		}
 		$data['create_time'] = TIME_UTC;
		
		$data['user_id'] = M("User")->where("user_name='".$data['user_name']."'")->getField("id");
		
		if(!$data['user_name'] )$data['user_name'] ="";
		if($data['vedio']!="")
		{
			require_once APP_ROOT_PATH."system/utils/vedio.php";
			$vedio = fetch_vedio_url($data['vedio']);		
			if($vedio!="")
			{
				$data['source_vedio'] =  $vedio;
			}
			else
			{
				$this->error("非法的视频地址");
			}
		}
		
		// 更新数据
		$log_info = $data['name'];
		
		$list=M(MODULE_NAME)->add($data);

		if (false !== $list) {
			//成功提示
			
			if($data['is_effect']==1&&$data['user_id']>0)
			{
				$deal_count = M("Project")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
				M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);
			}
			
			foreach($_REQUEST['question'] as $k=>$v)
			{
				if(trim($v)!=""||trim($_REQUEST['answer'][$k])!='')
				{
					$qa = array();
					$qa['deal_id'] = $list;
					$qa['question'] = trim($v);
					$qa['answer'] = trim($_REQUEST['answer'][$k]);
					$qa['sort'] = intval($k)+1;
					M("ProjectFaq")->add($qa);
				}
			}
			if($_REQUEST['ips_bill_no']>0){
				$GLOBALS['db']->query("update ".DB_PREFIX."project set ips_bill_no=$list where id=".$list);
			}
			
			syn_project($list["id"]);
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	public function insert_investor(){
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
 		$data = M(MODULE_NAME)->create ();
 		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add_investor"));
		if(!check_empty($data['name']))
		{
			$this->error("请输入名称");
		}	
		
		if(intval($data['cate_id'])==0)
		{
			$this->error("请选择分类");
		}	
		if(floatval($data['limit_price'])<=0){
			$this->error("目标金额要大于0");
		}
		$history_info=deal_investor_info($data['history'],'history');
   		if($history_info['status']){
			$data['history']=serialize(array_filter($history_info['data']));
		}else{
			$this->error($history_info['info']);
		}
		if($data['stock']){
			$stock_info=deal_investor_info($data['stock'],'stock');
			if($stock_info['status']){
				$data['stock']=serialize(array_filter($stock_info['data']));
			}else{
	 			$this->error($stock_info['info']);
			}
		}
	 		
 		$unstock_info=deal_investor_info($data['unstock'],'unstock');
		if($unstock_info['status']){
			$data['unstock']=serialize(array_filter($unstock_info['data']));
		}else{
			$this->error($unstock_info['info']);
		}
 		$plan_info=deal_investor_info($data['plan'],'plan');
		if($plan_info['status']){
			$data['plan']=serialize(array_filter($plan_info['data']));
		}else{
			$this->error($plan_info['info']);
		}
   		$attach_info=deal_investor_info($data['attach'],'attach');
 		if($attach_info['status']){
			$data['attach']=serialize(array_filter($attach_info['data']));
		}else{
			$this->error($attach_info['info']);
		}
		
		$data['audit_data']=serialize($data['audit_data']);
		if($data['end_time']>$data['pay_end_time']){
			$this->error("支付结束时间要大于项目结束时间");
		}elseif($data['begin_time']>$data['end_time']){
			$this->error("项目结束时间要大于项目开始时间");
		}
		
 		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
		$data['pay_end_time'] = trim($data['pay_end_time'])==''?0:to_timespan($data['pay_end_time']);
		$data['business_create_time'] = trim($data['business_create_time'])==''?0:to_timespan($data['business_create_time']);
		
		$data['create_time'] = TIME_UTC;
		$data['user_name'] = M("User")->where("id=".intval($data['user_id']))->getField("user_name");
		if(!$data['user_name'] )$data['user_name'] ="";
		if($data['vedio']!="")
		{
			require_once APP_ROOT_PATH."system/utils/vedio.php";
			$vedio = fetch_vedio_url($data['vedio']);		
			if($vedio!="")
			{
				$data['source_vedio'] =  $vedio;
			}
			else
			{
				$this->error("非法的视频地址");
			}
		}
		
		// 更新数据
		$log_info = $data['name'];
		
		$list=M(MODULE_NAME)->add($data);

		if (false !== $list) {
			//成功提示
			
			if($data['is_effect']==1&&$data['user_id']>0)
			{
				$deal_count = M("Project")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
				M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);
			}
			
			foreach($_REQUEST['question'] as $k=>$v)
			{
				if(trim($v)!=""||trim($_REQUEST['answer'][$k])!='')
				{
					$qa = array();
					$qa['deal_id'] = $list;
					$qa['question'] = trim($v);
					$qa['answer'] = trim($_REQUEST['answer'][$k]);
					$qa['sort'] = intval($k)+1;
					M("ProjectFaq")->add($qa);
				}
			}
			if($_REQUEST['ips_bill_no']>0){
				$GLOBALS['db']->query("update ".DB_PREFIX."project set ips_bill_no=$list where id=".$list);
			}
			syn_project($list["id"]);
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create();
	
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		
 			$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
			
			$this->deal_update(intval($data['id']));
	 		//开始验证有效性
			
			if(!check_empty($data['name']))
			{
				$this->error("请输入名称");
			}	
			if(intval($data['cate_id'])==0)
			{
				$this->error("请选择分类");
			}
			if(floatval($data['limit_price'])<=0){
				$this->error("目标金额要大于0");
			}
			if(strim($data['user_name'])=="")
				$this->error("请填写发起人");
			
			$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
			$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
			if($data['begin_time']>$data['end_time']){
				$this->error("开始时间不能大于结束 时间");
			}
			
			$data['user_id'] = M("User")->where("user_name='".strim($data['user_name'])."'")->getField("id");
			
			
			if($data['vedio']!="")
			{
				require_once APP_ROOT_PATH."system/utils/vedio.php";
				$vedio = fetch_vedio_url($data['vedio']);		
				if($vedio!="")
				{
					$data['source_vedio'] =  $vedio;
				}
				else
				{
					$this->error("非法的视频地址");
				}
			}
			else
			{
				$data['source_vedio'] = "";
			}
		if($_REQUEST['ips_bill_no']>0){
			$data['ips_bill_no'] = intval($data['id']);
 		}else{
 			$data['ips_bill_no'] = '';
 		}
 		if($data['is_effect']==2&&$data['user_id']>0)
		{
			$data['is_edit'] = 1;
		}
		
		$list=M(MODULE_NAME)->save ($data);
		
		if (false !== $list) {
			if($data['is_effect']==1&&$data['user_id']>0)
			{
				$deal_count = M("Project")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
				M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);
			}
			//成功提示			
			M("ProjectFaq")->where("deal_id=".$data['id'])->delete();
			foreach($_REQUEST['question'] as $k=>$v)
			{
				if(trim($v)!=""||trim($_REQUEST['answer'][$k])!='')
				{
					$qa = array();
					$qa['deal_id'] = $data['id'];
					$qa['question'] = trim($v);
					$qa['answer'] = trim($_REQUEST['answer'][$k]);
					$qa['sort'] = intval($k)+1;
					M("ProjectFaq")->add($qa);
				}
			}
			M("Project")->where("id=".$data['id'])->setField("deal_extra_cache","");
			M("ProjectLog")->where("deal_id=".$data['id'])->setField("deal_info_cache","");
			M("ProjectComment")->where("deal_id=".$data['id'])->setField("deal_info_cache","");
			syn_project($data['id']);
			syn_project_status($data['id']);
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	public function update_all(){
		$re=$GLOBALS['db']->getAll("select * from  ".DB_PREFIX."Project where  is_effect = 1 and is_delete=0 ");
		foreach($re as $k=>$v){
			syn_project($v['id']);
			syn_project_status($v['id']);
		}
		syn_user_level();
		ajax_return(array('status'=>1));
	}
	public function update_investor() {
		B('FilterString');
 		$data = M(MODULE_NAME)->create();
	 	
			$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
			//开始验证有效性
			$this->assign("jumpUrl",u(MODULE_NAME."/edit_investor",array("id"=>$data['id'])));
			if(!check_empty($data['name']))
			{
				$this->error("请输入名称");
			}	
			if(intval($data['cate_id'])==0)
			{
				$this->error("请选择分类");
			}
			if(floatval($data['limit_price'])<=0){
				$this->error("目标金额要大于0");
			}
			$this->deal_update(intval($data['id']));
			
	    	$history_info=deal_investor_info($data['history'],'history');
	   		if($history_info['status']){
				$data['history']=serialize(array_filter($history_info['data']));
			}else{
				$this->error($history_info['info']);
			}
	  		$stock_info=deal_investor_info($data['stock'],'stock');
			if($stock_info['status']){
				$data['stock']=serialize(array_filter($stock_info['data']));
			}else{
	 			$this->error($stock_info['info']);
			}
	 		$unstock_info=deal_investor_info($data['unstock'],'unstock');
			if($unstock_info['status']){
				$data['unstock']=serialize(array_filter($unstock_info['data']));
			}else{
				$this->error($unstock_info['info']);
			}
	 		$plan_info=deal_investor_info($data['plan'],'plan');
			if($plan_info['status']){
				$data['plan']=serialize(array_filter($plan_info['data']));
			}else{
				$this->error($plan_info['info']);
			}
	   		$attach_info=deal_investor_info($data['attach'],'attach');
	 		if($attach_info['status']){
				$data['attach']=serialize(array_filter($attach_info['data']));
			}else{
				$this->error($attach_info['info']);
			}
			//企业资质材料信息
			$data['audit_data']=serialize($data['audit_data']);
			if($data['end_time']>$data['pay_end_time']){
				$this->error("支付结束时间要大于项目结束时间");
			}elseif($data['begin_time']>$data['end_time']){
				$this->error("项目结束时间要大于项目开始时间");
			}
			
			
			$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
			$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
			$data['pay_end_time'] = trim($data['pay_end_time'])==''?0:to_timespan($data['pay_end_time']);
			
			$data['business_create_time'] = trim($data['business_create_time'])==''?0:to_timespan($data['business_create_time']);
		
			$data['user_name'] = M("User")->where("id=".intval($data['user_id']))->getField("user_name");
			if(!$data['user_name'] )$data['user_name'] ="";
			if($data['vedio']!="")
			{
				require_once APP_ROOT_PATH."system/utils/vedio.php";
				$vedio = fetch_vedio_url($data['vedio']);		
				if($vedio!="")
				{
					$data['source_vedio'] =  $vedio;
				}
				else
				{
					$this->error("非法的视频地址");
				}
			}
			else
			{
				$data['source_vedio'] = "";
			}
	 	if($_REQUEST['ips_bill_no']>0){
			$data['ips_bill_no'] = intval($data['id']);
 		}else{
 			$data['ips_bill_no'] = '';
 		}
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			if($data['is_effect']==1&&$data['user_id']>0)
			{
				$deal_count = M("Project")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
				M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);
			}
			//成功提示			
			M("ProjectFaq")->where("deal_id=".$data['id'])->delete();
			foreach($_REQUEST['question'] as $k=>$v)
			{
				if(trim($v)!=""||trim($_REQUEST['answer'][$k])!='')
				{
					$qa = array();
					$qa['deal_id'] = $data['id'];
					$qa['question'] = trim($v);
					$qa['answer'] = trim($_REQUEST['answer'][$k]);
					$qa['sort'] = intval($k)+1;
					M("ProjectFaq")->add($qa);
				}
			}
			M("Project")->where("id=".$data['id'])->setField("deal_extra_cache","");
			M("ProjectLog")->where("deal_id=".$data['id'])->setField("deal_info_cache","");
			M("ProjectComment")->where("deal_id=".$data['id'])->setField("deal_info_cache","");
			//syn_project($data['id']);
			//syn_project_status($data['id']);
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M("Project")->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M("Project")->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function delete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField("is_delete",1);		
						
				if ($list!==false) {
					foreach($rel_data as $data)
					{						
						$deal_count = M("Project")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
						M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);						
					}
					save_log($info."成功移到回收站",1);
					$this->success ("成功移到回收站",$ajax);
				} else {
					save_log($info."移到回收站出错",0);					
					$this->error ("移到回收站出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function restore() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField("is_delete",0);				
				if ($list!==false) {
					save_log($info."恢复成功",1);
					$this->success ("恢复成功",$ajax);
				} else {
					save_log($info."恢复出错",0);
					$this->error ("恢复出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$link_condition = array ('deal_id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();				
				if ($list!==false) {					
					M("ProjectFaq")->where($link_condition)->delete();
					M("ProjectComment")->where($link_condition)->delete();
					M("ProjectFocusLog")->where($link_condition)->delete();
					M("ProjectItem")->where($link_condition)->delete();
					M("ProjectItemImage")->where($link_condition)->delete();
					M("ProjectOrder")->where($link_condition)->delete();
					M("ProjectPayLog")->where($link_condition)->delete();
					M("ProjectSupportLog")->where($link_condition)->delete();
					M("ProjectVisitLog")->where($link_condition)->delete();
					M("ProjectLog")->where($link_condition)->delete();
					M("UserProjectNotify")->where($link_condition)->delete();
					M("ProjectNotify")->where($link_condition)->delete();
					
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
	
	public function add_faq()
	{
		$this->display();
	}
	
	public function deal_item()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Project")->getById($deal_id);
		$this->assign("deal_info",$deal_info);
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];		
			if (method_exists ( $this, '_filter' )) {
				$this->_filter ( $map );
			}
			$name=$this->getActionName();
			$model = D ("ProjectItem");
			if (! empty ( $model )) {
				$this->_list ( $model, $map );
			}
		}
		
		$this->display();
	}
	
	public function add_deal_item()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Project")->getById($deal_id);
		$Count = M('ProjectItem')->where('deal_id = '.$deal_id)->count();
		if($deal_info['type'] ==2){
			if($Count >=1)
			{
				$this->error("该项目为众筹买房，只能添加一个子项");
			}
		}
		$this->assign("deal_info",$deal_info);
		$this->display();
	}
	
	
	public function insert_deal_item() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M("ProjectItem")->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add_deal_item",array("id"=>$data['deal_id'])));
		if(!check_empty($data['price'])&&$data['type']==0)
		{
			$this->error("请输入价格");
		}
		
		if( $data['is_limit_user']==1 && $data['virtual_person'] > $data['limit_user'])
				$this->error("虚拟购买人数不能大于限购人数");	
		
		if($data["is_delivery"] == 0)
		{
			$data["delivery_fee"] = 0;
		}
		if($data["is_limit_user"] == 0)
		{
			$data["limit_user"] = 0;
		}
		if($data["is_share"] == 0)
		{
			$data["share_fee"] = 0;
		}
		
		// 更新数据
		$list=M("ProjectItem")->add($data);
		$log_info =  "项目ID".$data['deal_id'].":".format_price($data['price']);	
		
		if (false !== $list) {
			//成功提示
			
			M("ProjectItemImage")->where("deal_item_id=".$data['id'])->delete();
			$imgs=array($_REQUEST['img0'],$_REQUEST['img1'],$_REQUEST['img2'],$_REQUEST['img3']);
			
			//$imgs = $_REQUEST['image'];
			foreach($imgs as $k=>$v)
			{
				if($v!='')
				{
					$img_data['deal_id'] = $data['deal_id'];
					$img_data['deal_item_id'] = $list;
					$img_data['image'] = $v;
					M("ProjectItemImage")->add($img_data);
				}
			}
			M("Project")->where("id=".$data['deal_id'])->setField("deal_extra_cache","");
			save_log($log_info.L("INSERT_SUCCESS"),1);
			syn_project($data['deal_id']);
			syn_project_status($data['deal_id']);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function edit_deal_item()
	{
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M("ProjectItem")->where($condition)->find();
		$this->assign ( 'vo', $vo );
		//输出图片集
		$img_list = M("ProjectItemImage")->where("deal_item_id=".$vo['id'])->findAll();
		$imgs = array();
		foreach($img_list as $k=>$v)
		{
			$imgs[$k] = $v['image']; 
		}
		$this->assign("img_list",$imgs);
		
		$this->display();
	}
	
	public function update_deal_item() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M("ProjectItem")->create ();
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit_deal_item",array("id"=>$data['id'])));
		
		$deal_item=M("ProjectItem")->getById(intval($data['id']));
		
		if(!$deal_item)
			$this->error("更新失败");
			
		if(!check_empty($data['price']))
		{
			$this->error("请输入价格");
		}
		if( $data['is_limit_user']==1 && $data['virtual_person'] > $data['limit_user'])
				$this->error("虚拟购买人数不能大于限购人数");
		
		if( $data['is_limit_user']==1 && $deal_item['support_count'] >0 && ($data['virtual_person']+$deal_item['support_count']) > $data['limit_user'])
				$this->error('限购人数小于"虚拟购买人数('.$data['virtual_person'].')+支持人数('.$deal_item['support_count'].')"');
		
		if($data["is_delivery"] == 0)
		{
			$data["delivery_fee"] = 0;
		}
		if($data["is_limit_user"] == 0)
		{
			$data["limit_user"] = 0;
		}
		if($data["is_share"] == 0)
		{
			$data["share_fee"] = 0;
		}
		
		
		// 更新数据
		$this->deal_update(intval($data['deal_id']));
		$list=M("ProjectItem")->save($data);
		$log_info =  "项目ID".$data['deal_id'].":".format_price($data['price']);	
		if (false !== $list) {
			if($data['virtual_person']>0){
				
			}
			//成功提示
			//开始处理图片
			M("ProjectItemImage")->where("deal_item_id=".$data['id'])->delete();
			$imgs=array($_REQUEST['img0'],$_REQUEST['img1'],$_REQUEST['img2'],$_REQUEST['img3']);
			//$imgs = $_REQUEST['image'];
			foreach($imgs as $k=>$v)
			{
				if($v!='')
				{
					$img_data['deal_item_id'] = $data['id'];
					$img_data['deal_id'] = $data['deal_id'];
					$img_data['image'] = $v;
					M("ProjectItemImage")->add($img_data);
				}
			}
			M("Project")->where("id=".$data['deal_id'])->setField("deal_extra_cache","");
			M("ProjectLog")->where("deal_id=".$data['deal_id'])->setField("deal_info_cache","");
			//end 处理图片
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			syn_project($data['deal_id']);
			syn_project_status($data['deal_id']);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"));
		}
	}
	
	public function del_deal_item()
	{
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );				
				$rel_data = M("ProjectItem")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$deal_id = $data['deal_id'];
					$info[] = format_price($data['price']);	
				}
				if($info) $info = implode(",",$info);
				$info = "项目ID".$deal_id.":".$info;
				$list = M("ProjectItem")->where ( $condition )->delete();				
				if ($list!==false) {					
					M("Project")->where("id=".$deal_id)->setField("deal_extra_cache","");
					syn_project($deal_id);
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
	
	
	
	//pay_log 放款日志
	public function pay_log()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Project")->getById($deal_id);
		
		//拥金
		$deal_info['commission'] = $deal_info['support_amount'] + $deal_info['delivery_fee_amount'] - ($deal_info['pay_amount']+$deal_info['share_fee_amount']) ;
		$this->assign("deal_info",$deal_info);
		
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];	
			//$map['is_delete'] = 0;	

			$model = D ("ProjectPayLog");
			$paid_money = $model->where($map)->sum("money");
			
			$map['is_delete'] = 0;
			
			$remain_money = $deal_info['pay_amount'] - $paid_money;
			$this->assign("remain_money",$remain_money);
			$this->assign("paid_money",$paid_money);
			if (! empty ( $model )) {
				$this->_list ( $model, $map );
			}
			
			//分红情况
			$share_fee_total =  D ("ProjectOrder")->where("deal_id=".$deal_id."")->sum("share_fee");
			$share_fee_issue =  D ("ProjectOrder")->where("deal_id=".$deal_id." and share_status=1 ")->sum("share_fee");
			$this->assign("share_fee_total",$share_fee_total);
			$this->assign("share_fee_issue",$share_fee_issue);
		}
		$this->display();
	}
	
	public function add_pay_log()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Project")->getById($deal_id);
		
		//拥金
		$deal_info['commission'] = $deal_info['support_amount'] + $deal_info['delivery_fee_amount'] - ($deal_info['pay_amount']+$deal_info['share_fee_amount']) ;
		
		$this->assign("deal_info",$deal_info);
		
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];		
			
			$model = D ("ProjectPayLog");
			$paid_money = $model->where($map)->sum("money");
			$remain_money = $deal_info['pay_amount'] - $paid_money;
			$this->assign("paid_money",$paid_money);
			$this->assign("remain_money",$remain_money);
		}
		
		$this->display();
	}
	
	public function save_pay_log()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Project")->getById($deal_id);
	
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];		
		
			$model = D ("ProjectPayLog");
			$paid_money = $model->where($map)->sum("money");
			$remain_money = $deal_info['pay_amount'] - $paid_money;
			
			$money = doubleval($_REQUEST['money']);
			$log_info = strim($_REQUEST['log_info']);
			
			/*if($deal_info['ips_bill_no']>0){
				if($remain_money>0){
					$url= APP_ROOT."/index.php?ctl=collocation&act=Transfer&pTransferType=1&deal_id=".$deal_id."&ref_data=".$loan_data['repay_start_time']; 
 	 				 app_redirect($url);
				}else{
					$this->error("筹款发放完成");
				}
				
			}*/
			if($money<=0||$money>$remain_money)
			{
				$this->error("金额出错");
			}
			else
			{
				if($deal_info['user_id']>0)
				{
					if($deal_info['ips_bill_no']>0){
						
					}else{
						require_once APP_ROOT_PATH."system/libs/user.php";
						if($log_info=="")$log_info = $deal_info['name']."项目筹款发放";
						modify_account(array("money"=>$money),$deal_info['user_id'],$log_info);
						$log['deal_id'] = $deal_info['id'];
						$log['money'] = $money;
						$log['create_time'] = TIME_UTC;
						$log['log_info'] = $log_info;
						$model->add($log);
						save_log($log_info.$money,1);
						send_pay_success($log_info,$log['deal_id']);
						syn_project($deal_info["id"]);
	 					$this->success("筹款发放成功");
					}
 				}
				else
				{
					$this->error("管理员创建项目，无需发放筹款");
				}
			}
			
		}
		else
		{
			$this->error("项目不存在");
		}
	}
	
	public function del_pay_log()
	{
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );				
				$rel_data = M("ProjectPayLog")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$deal_id = $data['deal_id'];
					$info[] = format_price($data['money']);	
				}
				if($info) $info = implode(",",$info);
				$info = "项目ID".$deal_id.":".$info;

				$list = M("ProjectPayLog")->where( $condition )->setField ( 'is_delete', 1 );			
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
	
	//项目日志
	public function deal_log()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Project")->getById($deal_id);
		$this->assign("deal_info",$deal_info);
		
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];	
			$model = D ("ProjectLog");
			if (! empty ( $model )) {
				$this->_list ( $model, $map );
			}
		}
		
		$this->display();
	}
	
	public function del_deal_log()
	{
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );		
				$condition_log = array ('log_id' => array ('in', explode ( ',', $id ) ) );				
				$rel_data = M("ProjectLog")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$deal_id = $data['deal_id'];
					$info[] = $data['id'];	
				}
				if($info) $info = implode(",",$info);
				$info = "项目ID".$deal_id."的日志:".$info;
				$list = M("ProjectLog")->where ( $condition )->delete();	
							
				if ($list!==false) {		
					$GLOBALS['db']->query("update ".DB_PREFIX."project set log_count = log_count - ".intval($list)." where id = ".$deal_id);			
					M("ProjectComment")->where($condition_log)->delete();
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
	
	public function batch_refund()
	{
		$page = intval($_REQUEST['page']);

		$page=($page<=0)?1:$page;

		$page_size = 100;
		$deal_id = intval($_REQUEST['id']);
		
		$limit = (($page-1)*$page_size).",".$page_size;
		
		$deal_info = M("Project")->where("id=".$deal_id." and is_delete = 0 and is_effect = 1 and is_success = 0 and end_time <>0 and end_time <".TIME_UTC)->find();
		if(!$deal_info)
		{
			$this->error("该项目不能批量退款");
		}
		else
		{
			require_once APP_ROOT_PATH."system/libs/user.php";
			$refund_order_list = M("ProjectOrder")->where("deal_id=".$deal_id." and is_refund = 0 and order_status = 3")->limit($limit)->findAll();
			foreach($refund_order_list as $k=>$v)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."project_order set is_refund = 1 where id = ".$v['id']);
				if($GLOBALS['db']->affected_rows()>0)
				{	
					modify_account(array("money"=>($v['online_pay']+$v['credit_pay'])),$v['user_id'],$v['deal_name']."退款",51);
					//退回积分
					if($v['score'] >0)
	 				{
						$log_info=$v['deal_name']."退款，退回".$v['score']."积分";
						modify_account(array("score"=>$v['score']),$v['user_id'],$log_info,51);
	 				}
					
					//扣掉购买时送的积分和信用值
					$sp_multiple=unserialize($v['sp_multiple']);
					if($sp_multiple['score_multiple']>0)
					{
						$score=intval($v['total_price']*$sp_multiple['score_multiple']);
						$log_info=$v['deal_name']."退款，扣掉".$score."积分";
						modify_account(array("score"=>"-".$score),$v['user_id'],$log_info,51);
					}	
					
					if($sp_multiple['point_multiple']>0)
					{
						$point=intval($v['total_price']*$sp_multiple['point_multiple']);
						$log_info=$v['deal_name']."退款，扣掉".$point."信用值";
						modify_account(array("point"=>"-".$point),$v['user_id'],$log_info,51);
					}
					
					//红包
					$GLOBALS['db']->query("UPDATE ".DB_PREFIX."ecv SET use_count = use_count - 1 WHERE id=".$v["ecv_id"]." AND user_id=".$v['user_id']);
				
				}
			}
			
			//同步商品记录
			syn_project($deal_info['id']);
			$deal_item_list=M("ProjectItem")->where("deal_id=".intval($deal_info['id']))->findAll();
			foreach($deal_item_list as $k=>$v)
			{									
				$deal_item['support_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_order where deal_id = ".$v['deal_id']." and order_status=3 and is_refund=0 and deal_item_id=".intval($v['id'])));
				$deal_item['support_amount'] = doubleval($GLOBALS['db']->getOne("select sum(deal_price) from ".DB_PREFIX."project_order where deal_id = ".$v['deal_id']." and order_status=3 and is_refund=0 and deal_item_id=".intval($v['id'])));
				$GLOBALS['db']->autoExecute(DB_PREFIX."project_item", $deal_item, $mode = 'UPDATE', "id=".intval($v['id']), $querymode = 'SILENT');
			}

			$remain = M("ProjectOrder")->where("deal_id=".$deal_id." and is_refund = 0 and order_status = 3")->count();
			if($remain==0)
			{
				$jump_url = u("Project/online_index");
				$this->assign("jumpUrl",$jump_url);
				M("Project")->where("id=".$deal_info['id'])->setField("deal_extra_cache","");
				M("ProjectLog")->where("deal_id=".$deal_info['id'])->setField("deal_info_cache","");
				$this->success("批量退款成功");
			}
			else
			{
				$jump_url = u("Project/batch_refund",array("id"=>$deal_id,"page"=>$page+1));
				$this->assign("jumpUrl",$jump_url);
				$this->success("批量退款中，请勿刷新页面，剩余".$remain."条订单未退款");
			}
			
		}
		
	}
	function deal_update($deal_id){
		$deal=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."project where id=$deal_id");
 		$now_time= TIME_UTC;
 		if(($deal['begin_time']<$now_time||$deal['end_time']<$now_time)&&($deal['invote_money']>0||$deal['virtual_price']>0||$deal['support_amount']>0)){
 			// $this->error("项目已经开始无法编辑");
		} 
	}
	
	function sharefee_list(){
		$deal_id=intval($_REQUEST['deal_id']);
		$deal_info = M("Project")->getById($deal_id);
		$map['deal_id'] = $deal_id;
		$user_id=intval($_REQUEST['user_id']);
		$user_name=strim($_REQUEST['user_name']);
		$deal_item_id=intval($_REQUEST['deal_item_id']);
		
		//搜索条件
		if(isset($_REQUEST['share_status']))
		{
			$share_status = intval($_REQUEST['share_status']);
			if($share_status == -1)
				unset($_REQUEST['share_status']);
			else
				$map['share_status']=$share_status;
		}
		else
			$share_status=-1;
		if($user_id>0)
			$map['user_id'] = $user_id;
		else
			unset($user_id);
			
		if($deal_item_id >0)
			$map['deal_item_id'] = $deal_item_id;
		if($user_name !='')
			$map['user_name'] = array('like','%'.$user_name.'%');
		
		$map['share_fee'] = array('gt',0);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		//子项目列表
		if($deal_info)
		{
			$model = D ("ProjectOrder");
			if (! empty ( $model )) {
				$this->_list ( $model, $map );
			}
			
			$deal_item_list=D('ProjectItem')->where("deal_id=".intval($deal_info['id']))->findAll();
			
		}
		
		//分红情况
		$share_fee_total =  D ("ProjectOrder")->where("deal_id=".$deal_id."")->sum("share_fee");
		$share_fee_issue =  D ("ProjectOrder")->where("deal_id=".$deal_id." and share_status=1 ")->sum("share_fee");
		$this->assign("share_fee_total",$share_fee_total);
		$this->assign("share_fee_issue",$share_fee_issue);
		
		$this->assign("share_status",$share_status);
		$this->assign("user_id",$user_id);
		$this->assign("user_name",$user_name);
		$this->assign("deal_item_id",$deal_item_id);
		$this->assign("deal_info",$deal_info);
		$this->assign("deal_item_list",$deal_item_list);
		$this->assign("back_pay_log",u("Project/pay_log",array("id"=>$deal_info['id'])));
		$this->assign("back_deal",u("Project/online_index"));
		$this->display();
	}
	public function download(){
		$url=strim($_REQUEST['file']);
		if($url){
			header("Location: ".$url);
			exit;
		}else{
			return false;
		}
	}
}
?>