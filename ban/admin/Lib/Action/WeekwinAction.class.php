<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class WeekwinAction extends CommonAction{
	public function index()
	{
		$everwin = M("PeiziWeekwin")->find();
		$this->assign("everwin",$everwin);
		$this->display();
	}
	
	public function update_index()
	{
		
	}
	
}
?>