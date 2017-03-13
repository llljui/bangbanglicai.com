<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class PeiziInviteAction extends CommonAction{
	public function index()
	{
		$sql_str = "select * from ".DB_PREFIX."peizi_invite ";
		$model = D();
		//print_r($map);
		//echo $sql_str;
		$voList = $this->_Sql_list($model, $sql_str, "", 'type,min_money', false);
		//print_r($model->getLastSql());
		//exit;
		foreach ($voList as $k => $v) {
			if ($v['type'] == 0){
				$voList[$k]["type_format"] = '投资人';
			}else{
				$voList[$k]["type_format"] = '借款人';
			}
		}
		
		$this->assign('list', $voList);
		$this->display ();
	}
	
	public function add()
	{
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
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
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		
		
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
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