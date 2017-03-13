<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class ProjectOrderAction extends CommonAction{
	public function index()
	{
		if(intval($_REQUEST['type'])=='-1'){
			unset($_REQUEST['type']);
		}
		if(intval($_REQUEST['order_status'])=='-1'){
			unset($_REQUEST['order_status']);
		}
		if(intval($_REQUEST['is_refund'])=='-1'){
			unset($_REQUEST['is_refund']);
		}
		$order_list = $GLOBALS['db']->getAll("select po.*,pi.repaid_day from ".DB_PREFIX."project_order po left join  ".DB_PREFIX."project_item pi on po.deal_item_id = pi.id where po.repay_make_time=0 and po.repay_time>0  ");
		foreach($order_list as $k=>$v){
				$left_date= intval($v['repaid_day']) > 0 ? intval($v['repaid_day']) : intval(app_conf("REPAY_MAKE"));
				$repay_make_date=$v['repay_time']+$left_date*24*3600;
				if($repay_make_date<= TIME_UTC){
 					$GLOBALS['db']->query("update ".DB_PREFIX."project_order set repay_make_time =  ".TIME_UTC." where id = ".$v['id'] );
				}
 		}
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();

		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		if(trim($_REQUEST['deal_name'])!='')
		{
			$map['deal_name'] = array('like','%'.trim($_REQUEST['deal_name']).'%');
		}
		if(trim($_REQUEST['repay_time'])!='')
		{
			$repay_time = to_timespan($_REQUEST['repay_time']);
			$map['repay_time'] = array('between',array($repay_time,$repay_time+3600*24));
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
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
					$info[] = "[".$data['deal_name'].$data['deal_price']."支持人:".$data['user_name']."状态:".$data['order_status']."]";						
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();		
						
				if ($list!==false) {
//					$deal_id=$GLOBALS['db']->getOne("select deal_id from  ".DB_PREFIX."deal_order where id=$id");
//					syn_deal($deal_id);
					save_log($info."成功删除",1);
					$this->success ("成功删除",$ajax);
				} else {
					save_log($info."删除出错",0);					
					$this->error ("删除出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function view()
	{
		$order_info = $GLOBALS['db']->getRow("select po.*,pi.repaid_day,pi.is_delivery,pi.type as item_type from ".DB_PREFIX."project_order po left join  ".DB_PREFIX."project_item pi on po.deal_item_id = pi.id where po.id =".intval($_REQUEST['id']));

		if(!$order_info)$this->error("没有该项目的支持");
		
		
		$payment_notice_list = M("PaymentNotice")->where("order_id=".$order_info['id']." and is_paid = 1")->findAll();
		$this->assign("payment_notice_list",$payment_notice_list);
		
		$this->assign("order_info",$order_info);
		$this->assign("back_list",u("ProjectOrder/get_pay_list",array("deal_id"=>$order_info['deal_id'])));		
		$this->display();
	}
	
	public function refund()
	{
		$id = intval($_REQUEST['id']);
		$order_info = M("ProjectOrder")->getById($id);
		if($order_info)
		{
			if($order_info['is_success'] == 1)
			{
				$this->error("项目已成功，不能退款");
			}
			
			$count_pay_log = M("ProjectPayLog")->where("deal_id=".intval($order_info['deal_id']))->count();
			if($count_pay_log >0)
				$this->error("筹款已发，不能退款");
				
			if($order_info['is_refund']==0)
			{ 
				$GLOBALS['db']->query("update ".DB_PREFIX."project_order set is_success = 0, is_refund = 1 where id = ".$id." and is_refund = 0");
				
				if($GLOBALS['db']->affected_rows()>0)
				{
					require_once APP_ROOT_PATH."system/libs/user.php";									
					modify_account(array("money"=>($order_info['online_pay']+$order_info['credit_pay'])),$order_info['user_id'],$order_info['deal_name']."退款",51);
					//退回积分
					if($order_info['score'] >0)
	 				{
						$log_info=$order_info['deal_name']."退款，退回".$order_info['score']."积分";
						modify_account(array("score"=>$order_info['score']),$order_info['user_id'],$log_info,51);
	 				}
					
					//扣掉购买时送的积分和信用值
					$sp_multiple=unserialize($order_info['sp_multiple']);
					if($sp_multiple['score_multiple']>0)
					{
						$score=intval($order_info['total_price']*$sp_multiple['score_multiple']);
						$log_info=$order_info['deal_name']."退款，扣掉".$score."积分";
						modify_account(array("score"=>"-".$score),$order_info['user_id'],$log_info,51);
					}	
					if($sp_multiple['point_multiple']>0)
					{
						$point=intval($order_info['total_price']*$sp_multiple['point_multiple']);
						$log_info=$order_info['deal_name']."退款，扣掉".$point."信用值";
						modify_account(array("point"=>"-".$point),$order_info['user_id'],$log_info,51);
					}	
					
					//红包
					$GLOBALS['db']->query("UPDATE ".DB_PREFIX."ecv SET use_count = use_count - 1 WHERE id=".$order_info["ecv_id"]." AND user_id=".$order_info['user_id']);
					
					
					require_once APP_ROOT_PATH."app/Lib/project_func.php";				
					syn_project($order_info['deal_id']);
					
					$deal_item['support_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_order where deal_id = ".$order_info['deal_id']." and order_status=3 and is_refund=0 and deal_item_id=".intval($order_info['deal_item_id'])));
					$deal_item['support_amount'] = floatval($GLOBALS['db']->getOne("select sum(deal_price) from ".DB_PREFIX."project_order where deal_id = ".$order_info['deal_id']." and order_status=3 and is_refund=0 and deal_item_id=".intval($order_info['deal_item_id'])));
					$GLOBALS['db']->autoExecute(DB_PREFIX."project_item", $deal_item, $mode = 'UPDATE', "id=".intval($order_info['deal_item_id']), $querymode = 'SILENT');	
					
					//同步deal_log中的deal_info_cache
				
					$GLOBALS['db']->query("update ".DB_PREFIX."project_log set deal_info_cache = '' where deal_id = ".$order_info['deal_id']);
					
					$GLOBALS['db']->query("update ".DB_PREFIX."project set deal_extra_cache = '' where id = ".$order_info['deal_id']);
					
					$GLOBALS['db']->query("delete from ".DB_PREFIX."project_support_log where deal_item_id = ".$order_info['deal_item_id']." and user_id = ".$order_info['user_id']." and deal_id = ".$order_info['deal_id']." and price=".$order_info['deal_price']." limit 1");
					
				}
				$this->success("成功退款到会员余额");
			}
			else
			{
				$this->error("已经退款");
			}
		}
		else
		{
			$this->error("没有该项目的支持");
		}
	}
	
	public function incharge()
	{
		$id = intval($_REQUEST['id']);
		$order_info = M("ProjectOrder")->getById($id);
		if($order_info)
		{
			if($order_info['order_status']==0)
			{
				require_once APP_ROOT_PATH."app/Lib/project_func.php";						
				$result = pay_order($order_info['id']);				
				/*$money = $result['money'];
				$payment_notice['create_time'] = TIME_UTC;
				$payment_notice['user_id'] = $order_info['user_id'];
				$payment_notice['payment_id'] = 0;
				$payment_notice['money'] = $money;
				$payment_notice['bank_id'] = "";
				$payment_notice['order_id'] = $order_info['id'];
				$payment_notice['memo'] = "管理员收款";
				$payment_notice['deal_id'] = $order_info['deal_id'];
				$payment_notice['deal_item_id'] = $order_info['deal_item_id'];
				$payment_notice['deal_name'] = $order_info['deal_name'];
				
				do{
					$payment_notice['notice_sn'] = to_date(TIME_UTC,"Ymd").rand(100,999);
					$GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$payment_notice,"INSERT","","SILENT");
					$notice_id = $GLOBALS['db']->insert_id();
				}while($notice_id==0);
				
				require_once APP_ROOT_PATH."system/libs/cart.php";
				$rs = payment_paid($payment_notice['notice_sn'],"");	*/
				$this->success("收款完成");
			}
			else
			{
				$this->error("已经付过款");
			}
		}
		else
		{
			$this->error("没有该项目的支持");
		}
	}
	
	//导出电子表
	public function export_csv($page = 1)
	{
		$pagesize = 10;
		set_time_limit(0);
		$limit = (($page - 1)*intval($pagesize)).",".(intval($pagesize));
	//	$limit=((0).",".(10));
		//echo $limit;exit;
		$where = " 1=1 ";
		//定义条件
		if(intval($_REQUEST['type'])!='-1'){
			$where.= " and ".DB_PREFIX."project_order.type = ".intval($_REQUEST['type']);
		}
		if(intval($_REQUEST['order_status'])!='-1'){
			$where.= " and ".DB_PREFIX."project_order.order_status = ".intval($_REQUEST['order_status']);
		}
		if(intval($_REQUEST['is_refund'])!='-1'){
			$where.= " and ".DB_PREFIX."project_order.is_refund = ".intval($_REQUEST['is_refund']);
		}
		if(strim($_REQUEST['id'])!='')
		{
			$where.= " and ".DB_PREFIX."project_order.id = ".strim($_REQUEST['id']);
		}
		if(strim($_REQUEST['deal_name'])!='')
		{
			$where.= " and ".DB_PREFIX."project_order.deal_name like '%".strim($_REQUEST['deal_name'])."%'";
		}
		if(strim($_REQUEST['user_name'])!='')
		{
			$where.= " and ".DB_PREFIX."project_order.user_name = '".strim($_REQUEST['user_name'])."'";
		}
		if(strim($_REQUEST['repay_time'])!='')
		{
			$where.= " and FROM_UNIXTIME(".DB_PREFIX."project_order.repay_time,'%Y-%m-%d') = '".strim($_REQUEST['repay_time'])."'";
		}
		$list = M("ProjectOrder")
				->where($where)
				->field(DB_PREFIX.'project_order.*')
				->limit($limit)->findAll();
		
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
			
			$order_value = array( 'user_name'=>'""', 'deal_name'=>'""','deal_type'=>'""', 'total_price'=>'""','mobile'=>'""','consignee'=>'""','address'=>'""');
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","参与者,项目名称,类型,应付总额,手机,收货人,配送地址");	    		    	
		    	$content = $content . "\n";
	    	}
	    	
			foreach($list as $k=>$v)
			{
				$order_value = array();
				$order_value['user_name'] = '"' . iconv('utf-8','gbk',$v['user_name']) . '"';
				$order_value['deal_name'] = '"' . iconv('utf-8','gbk',$v['deal_name']) . '"';
				if($v['type'] == 0)
				{
					$order_value['type'] = '"' . iconv('utf-8','gbk','产品众筹'). '"' ;
				}
				elseif($v['type'] == 2)
				{
					$order_value['type'] = '"' . iconv('utf-8','gbk','无私奉献'). '"' ;
				}
				$order_value['total_price'] = '"' . iconv('utf-8','gbk',$v['total_price']) . '"';
				$order_value['mobile'] = '"' . iconv('utf-8','gbk',$v['mobile']) . '"';
				$order_value['consignee'] = '"' . iconv('utf-8','gbk',$v['consignee']). '"' ;
				$order_value['address'] = '"' . iconv('utf-8','gbk',$v['address']).'"';
				$content .= implode(",", $order_value) . "\n";
			}	
			
			//
			header("Content-Disposition: attachment; filename=order_list.csv");
	    	echo $content ;
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}	
		
	}
	
	
	public function get_pay_list()
	{
		if(intval($_REQUEST['is_refund'])=='-1')
		{
			unset($_REQUEST['is_refund']);
		}
		if(intval($_REQUEST['order_status'])=='-1')
		{
			unset($_REQUEST['order_status']);
		}
		
		$deal_id=$_REQUEST['deal_id'];
		if($deal_id>0){
			$deal_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."project where id=$deal_id ");
			$this->assign("deal_info",$deal_info);
			$order_list = $GLOBALS['db']->getAll("select po.*,pi.repaid_day from ".DB_PREFIX."project_order po left join ".DB_PREFIX."project_item pi on po.deal_item_id = pi.id where po.repay_make_time=0 and po.repay_time>0 and po.deal_id=$deal_id  ");
			
			
		}else{
			$order_list = $GLOBALS['db']->getAll("select dorder.*,pi.repaid_day from ".DB_PREFIX."project_order as dorder left join ".DB_PREFIX."project as d on d.id=dorder.deal_id left join ".DB_PREFIX."project_item pi on dorder.deal_item_id = pi.id where d.type=1 and dorder.repay_make_time=0 and dorder.repay_time>0 ");
		}
		foreach($order_list as $k=>$v){
				
				$left_date= intval($v['repaid_day']) >0 ? intval($v['repaid_day']):intval(app_conf("REPAY_MAKE"));
				$repay_make_date=$v['repay_time']+$left_date*24*3600;
				if($repay_make_date<=TIME_UTC){
 					$GLOBALS['db']->query("update ".DB_PREFIX."project_order set repay_make_time =  ".TIME_UTC." where id = ".$v['id'] );
				}
 		}
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		if(trim($_REQUEST['deal_name'])!='')
		{
			$map['deal_name'] = array('like','%'.trim($_REQUEST['deal_name']).'%');
		}
		if(trim($_REQUEST['repay_time'])!='')
		{
			$repay_time = to_timespan($_REQUEST['repay_time']);
			$map['repay_time'] = array('between',array($repay_time,$repay_time+3600*24));
		}
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
}
?>