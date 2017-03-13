<?php

class LearnAction extends CommonAction{

    public function learn_send_list()
	{
		
		$sql = " SELECT lsl.*,lt.type,lt.invest_type  FROM  ".DB_PREFIX."learn_send_list lsl LEFT JOIN ".DB_PREFIX."learn_type lt ON lsl.type_id=lt.id AND lsl.is_effect=1 WHERE 1=1  ";
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$sqluid  ="SELECT group_concat(id) FROM ".DB_PREFIX."user WHERE user_name LIKE '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sqluid);
			$sql .= "and lsl.user_id in ($ids)";
			
		}
		$sql .= " GROUP BY lsl.id desc";
		//echo $sql;
		
		$model = D();
		$voList = $this->_Sql_list($model, $sql, false);
		$this->display();
		
	}
	
	public function learn_load()
	{
		
		$sql = " SELECT ll.*,l.name  FROM  ".DB_PREFIX."learn_load ll LEFT JOIN ".DB_PREFIX."learn l ON ll.learn_id=l.id AND l.is_effect=1 WHERE 1=1 ";
	
		if(trim($_REQUEST['user_name'])!='')
		{
			$sqluid  ="SELECT group_concat(id) FROM ".DB_PREFIX."user WHERE user_name LIKE '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sqluid);
			$sql .= "and ll.user_id in ($ids)";
			
		}
		
		if(trim($_REQUEST['load_name'])!='')
		{
			$sqluid  ="SELECT group_concat(id) FROM ".DB_PREFIX."learn WHERE name LIKE '%".trim($_REQUEST['load_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sqluid);
			$sql .= "and l.id in ($ids)";
			
		}
		
		$sql .= " GROUP BY ll.id desc";
		
		 //echo "$sql";
		$model = D();
		$voList = $this->_Sql_list($model, $sql, false);
		$this->display();
		
	}
	
	public function activity_setting()
	{
		$this->assign("learn_send_list",M("LearnType")->where('is_effect = 1 order by id ')->findAll());
		
		
		$model = D ("LearnType");
		
		if (! empty ( $model )) {
			$this->_list ( $model);
		}
		
		
		$this->display ();
		
	}
	
	public function add()
	{
		$this->assign("newsort",M(MODULE_NAME)->where("is_delete=0")->max("sort")+1);
		$this->display();
		
	}
	
	public function insert_activity()
	{
		B('FilterString');
		$data = M("LearnType")->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
//		if(!check_empty($data['name']))
//		{
//			$this->error("活动名不能为空！");
//		}
//		if(!check_empty($data['money']))
//		{
//			$this->error("体验金不能为空！");
//		}
//		if(!check_empty($data['max_money']))
//		{
//			$this->error("会员赠送最高金额不能为空！");
//		}	
//		if(!check_empty($data['time_limit']))
//		{
//			$this->error("体验金有效天数不能为空！");
//		}	

		// 更新数据
		$log_info = $data['name'];
		$list=M("LearnType")->add($data);
		if (false !== $list) {
			
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			rm_auto_cache("cache_learn_type");
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
		
	}
    
    public function edit() {		
		$id = intval($_REQUEST ['id']);
		$this->assign ( 'id', $id );
		
		$condition['id'] = $id;		
		$vo = M("LearnType")->where($condition)->find();
		//dump($vo);
		$this->assign ( 'vo', $vo );		
		
		$this->display ();
	}
	
	 public function update_activity() {		
		B('FilterString');
		$data = M("LearnType")->create ();
		$log_info = M("LearnType")->where("id=".intval($data['id']))->getField("title");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		// 更新数据
		$list=M("LearnType")->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			rm_auto_cache("cache_learn_type");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
		
	}
	
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				
				if(M("LearnSendList")->where(array ('type_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("存在关联发放列表"),$ajax);
				}
				$rel_data = M("LearnType")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M("LearnType")->where ( $condition )->delete();

				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					rm_auto_cache("cache_vip_festivals");
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	public function search_activity() {	
			
		$id = $_REQUEST ['id'];	
		$sql = " SELECT lsl.*,lt.type  FROM  ".DB_PREFIX."learn_send_list lsl LEFT JOIN ".DB_PREFIX."learn_type lt ON lsl.type_id=lt.id AND lsl.is_effect=1 WHERE lsl.type_id=$id  ";
		$model = D();
		$voList = $this->_Sql_list($model, $sql, false);
		$this->display();
	}
	
	public function add_learn(){
		
		$this->display ();
	}
	
	public function insert_learn(){
		B('FilterString');
		$data = M("Learn")->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));

		// 更新数据
		$log_info = $data['name'];
		$list=M("Learn")->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			rm_auto_cache("cache_learn");
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function edit_learn() {		
		$id = intval($_REQUEST ['id']);
		$this->assign ( 'id', $id );
		
		$condition['id'] = $id;		
		$vo = M("Learn")->where($condition)->find();
		//dump($vo);
		$this->assign ( 'vo', $vo );		
		
		$this->display ();
	}
	
	public function update_learn() {		
		B('FilterString');
		$data = M("Learn")->create ();
		$log_info = M("Learn")->where("id=".intval($data['id']))->getField("title");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit_learn",array("id"=>$data['id'])));
		// 更新数据
		$list=M("Learn")->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			rm_auto_cache("cache_learn");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	
	
	//收回发放列表
	public function take_back(){
		
		require_once(APP_ROOT_PATH."system/libs/user.php");
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
			$rel_data = M("LearnSendList")->where($condition)->findAll();	
			
			$send_date=to_date(TIME_UTC,"Y-m-d");	
			foreach($rel_data as $data)
			{
				if($data['is_recycle']==0&&$data['is_effect']==1)
				{
					$GLOBALS['db']->getAll("update  ".DB_PREFIX."learn_send_list set  is_recycle = 1 where is_effect = 1 and is_recycle = 0 and id ='".$data['id']."'");
				}
			}
			
			$this->success(L("收回成功")); 
		}
		else 
		{
			$this->error (l("收回失败"));
		}		
	}
	
	
	public function learn_list(){
		$this->assign("learn",M("Learn")->where('is_effect = 1 order by id ')->findAll());
		
		
		$model = D ("Learn");
		
		if (! empty ( $model )) {
			$this->_list ( $model);
		}
		
		
		$this->display ();
		
	}
	
	//删除理财产品列表
	public function foreverdelete() {
		
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				
				if(M("Learn_load")->where(array ('learn_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("存在关联理财体验投资表"),$ajax);
				}
				$rel_data = M("Learn")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M("Learn")->where ( $condition )->delete();

				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					rm_auto_cache("cache_learn");
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	public function search_learn() {	
		
		$id = $_REQUEST ['id'];	
		$sql = " SELECT ll.*,l.name  FROM  ".DB_PREFIX."learn_load ll LEFT JOIN ".DB_PREFIX."learn l ON ll.learn_id=l.id AND l.is_effect=1 WHERE l.id=$id ";
		$model = D();
		$voList = $this->_Sql_list($model, $sql, false);
		$this->display();
	}
    
    public function send()
	{
		$id = intval($_REQUEST['id']);
		$learn_type = M("LearnType")->getById($id);
		
		$user_level = M("UserLevel")->findAll();
		$this->assign("user_level",$user_level);
		
		$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		
		
		$this->assign("learn_type",$learn_type);
		$this->display();
	}	
	
	public function doSend()
	{
		require_once APP_ROOT_PATH."system/libs/voucher.php";
		$learn_id = intval($_REQUEST['learn_id']);
		$need_password = intval($_REQUEST['need_password']);
		$send_type = intval($_REQUEST['send_type']);
		$user_level = intval($_REQUEST['user_level']);
		if(intval($_REQUEST['vip_id'])>0){
			$vip_id = intval($_REQUEST['vip_id']);
		}
		
		$user_ids = trim($_REQUEST['user_id']);
		$gen_count = intval($_REQUEST['gen_count']);
		$page = intval($_REQUEST['page'])==0?1:intval($_REQUEST['page']);  //大数据量时的载入的页数
		$page_size = app_conf("BATCH_PAGE_SIZE"); //每次运行的次数， 开发时可根据实际环境改变此大小
		$page_limit = ($page-1)*$page_size.",".$page_size;
		switch($send_type)
		{
			case 0:
				//按信用等级
				$user_list = M("User")->where("level_id=".$user_level)->order("id asc")->limit($page_limit)->findAll();
				if($user_list)
				{
					foreach($user_list as $v)
					{
						send_learn($learn_id,$v['id'],$need_password);
					}
					$this->assign("jumpUrl",u("Learn/doSend",array("learn_id"=>$learn_id,'need_password'=>$need_password,'send_type'=>$send_type,'user_level'=>$user_level,'user_id'=>$user_ids,'gen_count'=>$gen_count,'page'=>($page+1))));
					$msg = sprintf("体验金发放成功",($page-1)*$page_size,$page*$page_size);
					$this->success($msg);
				}
				else
				{
					save_log("ID".$learn_id."体验金发放成功",1);
					$this->assign("jumpUrl",u("Learn/activity_setting"));
					$this->success("体验金发放成功");
				}
				break;
			case 1:
				//按会员ID
				$user_list = M("User")->where("id in(".$user_ids.")")->order("id asc")->limit($page_limit)->findAll();
				if($user_list)
				{
					foreach($user_list as $v)
					{
						send_learn($learn_id,$v['id'],$need_password);
					}
					$this->assign("jumpUrl",u("Learn/doSend",array("learn_id"=>$learn_id,'need_password'=>$need_password,'send_type'=>$send_type,'user_group'=>$user_group,'user_id'=>$user_ids,'gen_count'=>$gen_count,'page'=>($page+1))));
					$msg = sprintf("体验金发放成功",($page-1)*$page_size,$page*$page_size);
					$this->success($msg);
				}
				else
				{
					save_log("ID".$learn_id."体验金发放成功",1);
					$this->assign("jumpUrl",u("Learn/activity_setting"));
					$this->success("体验金发放成功");
				}
				break;
			case 2:
				//线下
				for($i=0;$i<$page_size;$i++)
				{					
					if(($page-1)*$page_size+$i==$gen_count)
					{
						save_log("ID".$learn_id."体验金发放成功",1);
						$this->assign("jumpUrl",u("Learn/activity_setting"));
						$this->success("体验金发放成功");
						break;
					}
					send_learn($learn_id,0,$need_password);	
				}
				$this->assign("jumpUrl",u("Learn/doSend",array("learn_id"=>$learn_id,'need_password'=>$need_password,'send_type'=>$send_type,'user_group'=>$user_group,'user_id'=>$user_ids,'gen_count'=>$gen_count,'page'=>($page+1))));
				$msg = sprintf("体验金发放成功",($page-1)*$page_size,$page*$page_size);
				$this->success($msg);
				break;
			case 3:
				//按VIP等级
				$user_list = M("User")->where("vip_id=".$vip_id)->order("id asc")->limit($page_limit)->findAll();
				if($user_list)
				{
					foreach($user_list as $v)
					{
						send_learn($learn_id,$v['id'],$need_password);
					}
					$this->assign("jumpUrl",u("Learn/doSend",array("learn_id"=>$learn_id,'need_password'=>$need_password,'send_type'=>$send_type,'user_level'=>$user_level,'user_id'=>$user_ids,'gen_count'=>$gen_count,'page'=>($page+1))));
					$msg = sprintf("体验金发放成功",($page-1)*$page_size,$page*$page_size);
					$this->success($msg);
				}
				else
				{
					save_log("ID".$learn_id."体验金发放成功",1);
					$this->assign("jumpUrl",u("Learn/activity_setting"));
					$this->success("体验金发放成功");
				}
				break;
		}
		
	}
}
?>