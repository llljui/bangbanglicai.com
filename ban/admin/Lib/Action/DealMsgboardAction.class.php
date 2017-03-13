<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class DealMsgboardAction extends CommonAction{
	public function index()
	{
		if(strim($_REQUEST["user_name"])!="")
		{
			$condition = " where user_name like '%".strim($_REQUEST["user_name"])."%'";
		}
		$list = $GLOBALS["db"]->getAll("select * from ".DB_PREFIX."deal_msgboard ".$condition." order by id desc");
		$this->assign("list",$list);
		$this->assign("main_title","留言式贷款申请");
		$this->display();
	}
	
	public function delete() {
		//删除指定记录
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
					save_log($info.l("DELETE_SUCCESS"),1);
					clear_auto_cache("get_help_cache");
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error(l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$status = intval($_REQUEST['status']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		M(MODULE_NAME)->where("id=".$id)->setField("status",$status);  //当前状态
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		clear_auto_cache("get_help_cache");
		$this->ajaxReturn($n_is_effect,"修改成功",1)	;	
	}
}
?>