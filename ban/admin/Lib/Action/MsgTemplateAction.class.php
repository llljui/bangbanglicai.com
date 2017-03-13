<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class MsgTemplateAction extends CommonAction{
	public function index()
	{
		$type = intval($_REQUEST['type']);
		$tpl_list = M("MsgTemplate")->where("type=".$type)->findAll();
		$this->assign("tpl_list",$tpl_list);
		$this->display();
	}	
	public function load_tpl()
	{
		$name = trim($_REQUEST['name']);
		$tpl = M("MsgTemplate")->where("name='".$name."'")->find();
		if($tpl)
		{
			$tpl['tip'] = l("MSG_TIP_".strtoupper($name));
			$this->ajaxReturn($tpl,'',1);
		}
		else
		{
			$this->ajaxReturn('','',0);
		}		
	}
	
	public function update()
	{
		$data = M(MODULE_NAME)->create ();
		if($data['name']==''||$data['id']==0)
		{
			$this->error(l("SELECT_MSG_TPL"));
		}
		$log_info = $data['name'];
		$this->assign("jumpUrl",u(MODULE_NAME."/index"));
		$data['content'] = str_replace(array("copy(",'fopen','fclose','fread','file_get_contents(','file_put_contents('),"strim(", $data['content']);			
	
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
}
?>