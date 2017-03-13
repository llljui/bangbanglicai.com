<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class FuturesAction extends CommonAction{
	public function index()
	{
		$everwin = M("PeiziFutures")->find();
		$this->assign("everwin",$everwin);
		$this->display();
	}
	
	public function rate()
	{
		$everwin_rate = M("PeiziFuturesRateList")->find();
		$this->assign("everwin_rate",$everwin_rate);
		$this->display();
	}
	
	public function update_index()
	{
		
	}
	
	public function update_rate()
	{
	
	}
}
?>