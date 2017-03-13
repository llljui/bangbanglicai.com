<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
//<!--借款 投资  提现-->
define("SALE_COLOR","#404d60");
define("REFUND_COLOR","#10b9a5");
define("VERIFY_COLOR","#ff6600");
define("LICAI_COLOR","#0000ff");
class OfcAction extends CommonAction{

	public function sale_line()
	{
		//定义天数最近30天
		$begin_time = TIME_UTC - 30*24*3600;
		$end_time = TIME_UTC;
		$begin_time_date = to_date($begin_time,"Y-m-d");
		$end_time_date = to_date($end_time,"Y-m-d");
		
		
		
		$x_labels = array();  //x轴的标题
		for($i=0;$i<30;$i++)
		{
			$x_labels[] = to_date($begin_time+$i*24*3600,"d");
		}		
		$result['x_axis'] = array("labels"=>array("labels"=>$x_labels));
		
		
		//借款
		
		$sql = "SELECT borrow_amount as money,repay_start_date as stat_time FROM ".DB_PREFIX."deal where publish_wait = 0 and is_effect = 1 and is_delete = 0 and deal_status >=4 and repay_start_date > '".$begin_time_date."' and repay_start_date <= '".$end_time_date."'";
				
		$stat_result = $GLOBALS['db']->getAll($sql);

		//开始定义每个数据的线条元素
		$max_value = 0;
		
		
		$sale_line_values = array();
		for($i=0;$i<=30;$i++)
		{
			$stat_time = to_date($begin_time+$i*24*3600,"Y-m-d");
			$data_row = array("value"=>0,"tip"=>$stat_time."借款0元");
			foreach($stat_result as $row)
			{				
				if($row['stat_time']==$stat_time)
				{				
					$data_row['value'] += floatval($row['money']);
				}				
			}
			
			if($data_row['value'] > $max_value)$max_value = $data_row['value'];
			
			$data_row['tip'] = $stat_time."借款".round($data_row['value'],2)."元";
			$sale_line_values[] = $data_row;
		}
		$sale_line_element = array("type"=>"line","colour"=>SALE_COLOR,"text"=>"借款","width"=>2,"values"=>$sale_line_values);

		
		//投资 
		$stat_result = array();
		
		$sql = "SELECT dl.money,dl.create_time as stat_time FROM ".DB_PREFIX."deal_load as dl left join ".DB_PREFIX."deal as d on dl.deal_id = d.id where d.publish_wait = 0 and d.is_effect = 1 and d.is_delete = 0 and d.deal_status in (1,2,4,5) and dl.create_time > '".to_timespan($begin_time_date)."' and dl.create_time <= '".to_timespan($end_time_date)."'";
		
		$stat_result = $GLOBALS['db']->getAll($sql);
		
		$refund_line_values = array();
		for($i=0;$i<=30;$i++)
		{
			$stat_time = to_date($begin_time+$i*24*3600,"Y-m-d");
			$data_row = array("value"=>0,"tip"=>$stat_time."投资0元");
			foreach($stat_result as $row)
			{				
				if(to_date($row['stat_time'],"Y-m-d")==$stat_time)
				{
					$data_row['value'] += floatval($row['money']);
				}				
			}
			if($data_row['value'] > $max_value)$max_value = $data_row['value'];
			$data_row['tip'] = $stat_time."投资".round($data_row['value'],2)."元";
			$refund_line_values[] = $data_row;
		}
		$refund_line_element = array("type"=>"line","colour"=>REFUND_COLOR,"text"=>"投资","width"=>2,"values"=>$refund_line_values);
		
		
		//提现
		//
		$stat_result = array();
		
		$sql = "SELECT money,create_time as stat_time FROM ".DB_PREFIX."user_carry where status = 1 and create_time > '".to_timespan($begin_time_date)."' and create_time <= '".to_timespan($end_time_date)."'";
		
		$stat_result = $GLOBALS['db']->getAll($sql);		

		$verify_line_values = array();
		for($i=0;$i<=30;$i++)
		{
			$stat_time = to_date($begin_time+$i*24*3600,"Y-m-d");
			$data_row = array("value"=>0,"tip"=>$stat_time."提现0元");
			foreach($stat_result as $row)
			{				
				if(to_date($row['stat_time'],"Y-m-d")==$stat_time)
				{
					$data_row['value'] += floatval($row['money']);
				}				
			}
			if($data_row['value'] > $max_value)$max_value = $data_row['value'];
			$data_row['tip'] = $stat_time."提现".round($data_row['value'],2)."元";
			$verify_line_values[] = $data_row;
		}
		$verify_line_element = array("type"=>"line","colour"=>VERIFY_COLOR,"text"=>"提现","width"=>2,"values"=>$verify_line_values);
		
		if(C('LICAI_OPEN') == 1)
		{
			//理财
			//
			$stat_result = array();
			
			$sql = "SELECT money,create_time as stat_time FROM ".DB_PREFIX."licai_order where status> 0 and create_time > '".$begin_time_date."' and create_time <= '".$end_time_date."'";
			
			$stat_result = $GLOBALS['db']->getAll($sql);		
			
			$licai_line_values = array();
			for($i=0;$i<=30;$i++)
			{
				$stat_time = to_date($begin_time+$i*24*3600,"Y-m-d");
				$data_row = array("value"=>0,"tip"=>$stat_time."理财0元");
				foreach($stat_result as $row)
				{				
					if(to_date(to_timespan($row['stat_time']),"Y-m-d")==$stat_time)
					{
						$data_row['value'] += floatval($row['money']);
					}				
				}
				if($data_row['value'] > $max_value)$max_value = $data_row['value'];
				$data_row['tip'] = $stat_time."理财".round($data_row['value'],2)."元";
				$licai_line_values[] = $data_row;
			}
			
			$licai_line_element = array("type"=>"line","colour"=>LICAI_COLOR,"text"=>"理财","width"=>2,"values"=>$licai_line_values);
			
		}
		
		$result['y_axis'] = array("max"=>floatval($max_value));
		
		if(C('LICAI_OPEN') == 1)
		{
			$result['elements'] = array($sale_line_element,$refund_line_element,$verify_line_element,$licai_line_element);
		}
		else
		{
			$result['elements'] = array($sale_line_element,$refund_line_element,$verify_line_element);
		}
		
		$result['bg_colour']	= "#ffffff";
		
		
		ajax_return($result);
	}

}
?>