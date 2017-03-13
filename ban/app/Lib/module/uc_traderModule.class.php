<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require_once APP_ROOT_PATH.'app/Lib/uc.php';
require_once APP_ROOT_PATH.'system/libs/peizi.php';
require_once APP_ROOT_PATH.'app/Lib/page.php';

class uc_traderModule extends SiteBaseModule
{
	private $creditsettings;
	private $allow_exchange = false;

	public function __construct()
	{
		if(in_array(ACTION_NAME,array("carry","savecarry"))){
			$is_ajax = intval($_REQUEST['is_ajax']);
			//判断是否是黑名单会员
	    	if($GLOBALS['user_info']['is_black']==1){
	    		showErr("您当前无权限提现，具体联系网站客服",$is_ajax,url("index","uc_center"));
	    	}
		}
		if(file_exists(APP_ROOT_PATH."public/uc_config.php"))
		{
			require_once APP_ROOT_PATH."public/uc_config.php";
		}
		if(app_conf("INTEGRATE_CODE")=='Ucenter'&&UC_CONNECT=='mysql')
		{
			if(file_exists(APP_ROOT_PATH."public/uc_data/creditsettings.php"))
			{
				require_once APP_ROOT_PATH."public/uc_data/creditsettings.php";
				$this->creditsettings = $_CACHE['creditsettings'];
				if(count($this->creditsettings)>0)
				{
					foreach($this->creditsettings as $k=>$v)
					{
						$this->creditsettings[$k]['srctitle'] = $this->credits_CFG[$v['creditsrc']]['title'];
					}
					$this->allow_exchange = true;
					$GLOBALS['tmpl']->assign("allow_exchange",$this->allow_exchange);
				}
			}
		}
		parent::__construct();
	}
	
	public function index()
	{		
		$user_info = $GLOBALS['user_info'];
		
		$user_info["total_money"] = number_format(floatval($user_info["money"]) + floatval($user_info["lock_money"]), 2);
		
		$GLOBALS['tmpl']->assign('user_data',$user_info);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$trader_list = $GLOBALS['db']->getAll("select po.*,pc.name as conf_type_name from ".DB_PREFIX."peizi_order po left join ".DB_PREFIX."peizi_conf pc on po.peizi_conf_id = pc.id where po.status = 6 and  po.user_id = ".intval($GLOBALS['user_info']['id'])." order by order_sn desc  limit ".$limit);		
		$trader_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where status = 6 and  user_id = ".intval($GLOBALS['user_info']['id']));		
		foreach($trader_list as $k => $v)
		{
			$trader_list[$k] = 	get_peizi_order_fromat($trader_list[$k]);
		}

		$page = new Page($trader_count,app_conf("PAGE_SIZE"));   //初始化分页对象 
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("trader_list",$trader_list);

		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_EXCHANGE']);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_trader_index.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function verify()
	{
		$user_info = $GLOBALS['user_info'];
		
		$user_info["total_money"] = number_format(floatval($user_info["money"]) + floatval($user_info["lock_money"]), 2);
		
		$GLOBALS['tmpl']->assign('user_data',$user_info);
		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$trader_list = $GLOBALS['db']->getAll("select po.*,pc.name as conf_type_name from ".DB_PREFIX."peizi_order po left join ".DB_PREFIX."peizi_conf pc on po.peizi_conf_id = pc.id where po.status in (0,1,2,4,9) and  po.user_id = ".intval($GLOBALS['user_info']['id'])." order by order_sn desc limit ".$limit);		
		
		$trader_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where status in (0,1,2,4,9) and  user_id = ".intval($GLOBALS['user_info']['id']));		
		
		foreach($trader_list as $k => $v)
		{
			$trader_list[$k] = 	get_peizi_order_fromat($trader_list[$k]);
			if($v["status"] != 4 && $v["status"] != 6)
			{
				$trader_list[$k]["loss_money_format"] = "￥0.00";
			}
		}

		$page = new Page($trader_count,app_conf("PAGE_SIZE"));   //初始化分页对象 
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("trader_list",$trader_list);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_trader_verify.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function cancel()
	{
		$id = intval($_REQUEST["id"]);
		if(!$id)
		{
			$return = array();
			$return["status"] = 0;
			$return["info"] = "操作失败，请重试";
			return ajax_return($return);
		}
		$info = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."peizi_order where id =".$id." and status = 1 and user_id = ".$GLOBALS["user_info"]["id"]);
		if(!$info)
		{
			$return = array();
			$return["status"] = 0;
			$return["info"] = "操作失败，请重试";
			return ajax_return($return);
		}
		else
		{
			//更改状态
			$update_date = array();
			$update_date["status"] = "9";
			$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order",$update_date,"UPDATE","id=".$id);
			$return["status"] = 1;
			$return["info"] = "操作成功";
			
			//更新数据
			$cost_money = $info['cost_money'];
			$first_rate_money = $info['first_rate_money'];
			$manage_money = $info['manage_money'];
			$user_id = $info["user_id"];
			$order_id = $info["id"];
			
			require_once APP_ROOT_PATH.'system/libs/user.php';
			//退冻结：本金cost_money,首次付款 first_rate_money, 业务审核费manage_money
			//退 冻结 投资人的: 投资资金  borrow_money			
			//解冻：本金 cost_money
			modify_account(array('money'=>$cost_money,'lock_money'=>-$cost_money), $user_id,'配资申请失败解冻配资本金,配资编号:'.$order_id,30);

			//解冻：首次付款  first_rate_money
			modify_account(array('money'=>$first_rate_money,'lock_money'=>-$first_rate_money), $user_id,'配资申请失败解冻预交款,配资编号:'.$order_id,31);
			
			//解冻：业务审核费  manage_money
			if ($manage_money > 0)
				modify_account(array('money'=>$manage_money,'lock_money'=>-$manage_money), $user_id,'配资申请失败解冻服务费,配资编号:'.$order_id,32);
		
			return ajax_return($return);
		}
	}
	public function history_trader()
	{		
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$trader_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order where status in(3,5,7,8) and  user_id = ".intval($GLOBALS['user_info']['id'])."  order by order_sn desc limit ".$limit);		
		
		$trader_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where status in(3,5,7,8) and  user_id = ".intval($GLOBALS['user_info']['id']));		
		
		foreach($trader_list as $k => $v)
		{
			$trader_list[$k]["trader_money"] = $v["cost_money"] + $v["borrow_money"];
			$trader_list[$k]["loss_money"] = $v["stock_money"] - ($v["cost_money"] + $v["borrow_money"]);
			$trader_list[$k]["loss_money_format"] = format_price($trader_list[$k]["loss_money"]);
			$trader_list[$k]["loss_ratio"] = $v["stock_money"]/($v["cost_money"] + $v["borrow_money"]);
			$trader_list[$k]["status"] = get_peizi_status($v["status"]);
		}

		$page = new Page($trader_count,app_conf("PAGE_SIZE"));   //初始化分页对象 
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("trader_list",$trader_list);
		
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_trader_history.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function detail()
	{
		$id = intval($_REQUEST["id"]);
		if($id>0)
		{
			$this->detail_action($id);
		}
		else
		{
			showErr("访问错误，请重试");
		}
	}
	public function history_detail()
	{
		$id = intval($_REQUEST["id"]);
		if($id>0)
		{
			$this->detail_action($id);
		}
		else
		{
			showErr("访问错误，请重试");
		}
	}
	public function verify_detail()
	{
		$id = intval($_REQUEST["id"]);
		if($id>0)
		{
			$this->detail_action($id);
		}
		else
		{
			showErr("访问错误，请重试");
		}
	}
	function detail_action($id)
	{
		$trader_info = $GLOBALS['db']->getRow("select po.*,AES_DECRYPT(po.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd,pc.name as conf_type_name from ".DB_PREFIX."peizi_order po left join ".DB_PREFIX."peizi_conf as pc on po.peizi_conf_id = pc.id where  po.user_id = ".intval($GLOBALS['user_info']['id'])." and po.id=".$id);		
		
		$trader_info = get_peizi_order_fromat($trader_info);
		
		if($trader_info["status"] != 6 && $trader_info["status"] != 8)
		{
			$trader_info["loss_money_format"] = "￥0.00";
		}
		
		//总标志 6全部禁用 4启用
		$main_flag = true;
		//0:追加保证金;1:申请延期;2:申请增资;3:申请减资;4:提取赢余;5:申请结束配资'
		$trader_info["flag_0"] = true;
		$trader_info["flag_1"] = true;
		$trader_info["flag_2"] = true;
		$trader_info["flag_3"] = true;
		$trader_info["flag_4"] = true;
		$trader_info["flag_5"] = true;
		 
		if($trader_info["status"]==8 || $trader_info["status"]== 3|| $trader_info["status"]== 5|| $trader_info["status"]==7)
		{
			$main_flag = false;
		}
		elseif($trader_info["status"]==6)
		{
			$main_flag = true;
		}
		else
		{
			$main_flag = false;
		}
		$order_op = $GLOBALS["db"] -> getAll("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$id);
	
		foreach($order_op as $k => $v)
		{
			/*if( $v["op_status"] != 3 || $v["op_status"] != 5)
			{
				$trader_info["flag_".$v["op_type"]] = false;
			}*/
			if( $v["op_type"] == 0 && $v["op_status"] != 1 && $v["op_status"] != 5 )
			{
				$trader_info["flag_2"] = false;
				$trader_info["flag_3"] = false;
			}
		}
		if($trader_info["type"] == 1)
		{
			$trader_info["flag_1"] = false;
			$trader_info["flag_2"] = false;
			$trader_info["flag_3"] = false;
		}
		if($main_flag == false)
		{
			$trader_info["flag_0"] = false;
			$trader_info["flag_1"] = false;
			$trader_info["flag_2"] = false;
			$trader_info["flag_3"] = false;
			$trader_info["flag_4"] = false;
			$trader_info["flag_5"] = false;
		}
		
		/*操盘列表*/
		$op_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_op where user_id = ".intval($GLOBALS['user_info']['id'])." and peizi_order_id=".$id." order by id desc ");		
		
		foreach($op_list as $k => $v)
		{
			
			$op_list[$k]["op_type_format"] = get_peizi_op_type($v["op_type"]);
			$op_list[$k]["op_status_format"] = get_peizi_op_status($v["op_status"],$v["op_type"]);
			
			if($v["op_status"] == 3 || $v["op_status"] == 4)
			{
				$op_list[$k]["op_date"] = $v["op_date2"];
			}
			else
			{
				$op_list[$k]["op_date"] = $v["op_date1"];
			}
		}
		/*资金列表*/
		$fee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_fee_list where user_id = ".intval($GLOBALS['user_info']['id'])." and peizi_order_id=".$id." order by id desc");		

		/*历史金额*/
		$history_list = $GLOBALS['db']->getAll("select m.stock_date,m.stock_money from ".DB_PREFIX."peizi_order_stock_money m left join ".DB_PREFIX."peizi_order po on m.peizi_order_id = po.id where po.user_id = ".intval($GLOBALS['user_info']['id'])." and peizi_order_id=".$id." order by m.id asc");		
		
		$GLOBALS['tmpl']->assign("history_list",$history_list);
		
		$GLOBALS['tmpl']->assign("fee_list",$fee_list);
		
		$GLOBALS['tmpl']->assign("op_list",$op_list);
		
		$GLOBALS['tmpl']->assign("vo",$trader_info);
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_trader_detail.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function add_op()
	{
		$id = intval($_REQUEST["id"]);
		$type = intval($_REQUEST["type"]);
		$return =  array();
		$info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order where id = ".$id." and user_id = ".$GLOBALS["user_info"]["id"]);
		if(!$info)
		{
			$return["status"] = 0;
			$return["msg"] = "操作失败请重试";
			ajax_return($return);
			return;
		}
		
		
		$op_info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$id." and user_id = ".$GLOBALS["user_info"]["id"]." and op_status in (0,1) ");
		if($op_info)
		{
			$return["status"] = 0;
			$return["msg"] = "您还有申请未审核通过，请等待申请通过后操作";
			ajax_return($return);
			return;
		}
		
		//有成功追加：保证金 外,不让：增资，减资 了
		$op_type_1 = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$id." and op_type = 0 and user_id = ".$GLOBALS["user_info"]["id"]." and op_status = 3 ");
		if($op_type_1 && ($type==2||$type==3))
		{
			$return["status"] = 0;
			$return["msg"] = "提交错误，请刷新重试";
			ajax_return($return);
			return;
		}
		switch($type)
		{
			case 0:
			case 4:
			case 5:
				$return["status"] = 1;
				$return["title"] = "金额";
				if($type == 5)
				{
					$return["title"] = "股票账户余额";
				}
				$return["title_val"] = "<input name='op_val' id='op_val' class='f-input' value='1'/>";
				break;
			case 1:
				if($info["type"]==1)
				{
					$return["status"] = 0;
					$return["msg"] = "操作失败请重试";
					ajax_return($return);
					break;
				}
				$return["status"] = 1;
				$return["title"] = "时间";
				if($info["type"] == 0)
				{
					$return["title_val"] = "<input name='op_val' id='op_val' class='f-input' value='1'/>天";
				}
				elseif($info["type"] == 2)
				{
					$return["title_val"] = "<input name='op_val' id='op_val' class='f-input' value='1'/>月";
				}
				;
				break;
			case 2:
			case 3:
				if($info["type"]==1)
				{
					$return["status"] = 0;
					$return["msg"] = "操作失败请重试";
					ajax_return($return);
					break;
				}
				$parma = get_peizi_conf(1,$info["borrow_money"],$info["lever"],0,1);
				$lever_list = $parma["peizi_conf"]["lever_list"][0]["lever_array"];
				$max = -1;
				$min = -1;
				foreach($lever_list as $k=>$v)
				{
					if($max == -1 && $min ==-1)
					{
						$max = $min = $v["lever"];
					}
					if($v["lever"]>$max)
					{
						$max = $v["lever"];
					}
					if($v["lever"]<$min)
					{
						$min = $v["lever"];
					}
				};
				$return["status"] = 1;
				$return["title"] = "倍率";
				$return["title_val"] = "<select name='op_val' id='op_val' class='ui-select w120 select-w120 m10' value='0'>";
				if($type == 2)
				{
					$i = $info["lever"]+1;
					if($min == $max || $info["lever"] >= $max)
					{
						$return["status"] = 0;
						$return["msg"] = "当前值已经不能调整";
						ajax_return($return);
						break;
						//$return["title_val"] .= "<option value='".$info["lever"]."'>".$info["lever"]."</option>";
					}
					else
					{
						while($i <= $max)
						{
							$return["title_val"] .= "<option value='".$i."'>".$i."</option>";
							$i ++ ;
						}
					};
				}
				else
				{
					$i = $info["lever"]-1;
					if($min == $max || $info["lever"] <= $min)
					{
						$return["title_val"] .= "<option value='".$info["lever"]."'>".$info["lever"]."</option>";
					}
					else
					{
						while($i >= $min)
						{
							$return["title_val"] .= "<option value='".$i."'>".$i."</option>";
							$i -- ;
						}
					};
				};
				$return["title_val"] .= "</select>";
				break;
		}
		
		ajax_return($return);
	}
	public function save_op()
	{
		$peizi_order_id = intval($_REQUEST["id"]);
		$op_type = intval($_REQUEST["type"]);
		$op_val = strim($_REQUEST["op_val"]);
		$memo = strim($_REQUEST["memo"]);
		
		if($peizi_order_id > 0)
		{
			$info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order where id = ".$peizi_order_id." and user_id = ".$GLOBALS["user_info"]["id"]);
			if($info)
			{
				$op_info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$peizi_order_id." and user_id = ".$GLOBALS["user_info"]["id"]." and op_status in (0,1)");
				if($op_info)
				{
					$return["status"] = 0;
					$return["msg"] = "您还有申请未审核通过，请等待申请通过后操作";
					ajax_return($return);
					return;
				}
				//$op_type_1 = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$peizi_order_id." and user_id = ".$GLOBALS["user_info"]["id"]." and op_status not in (2,5) ");
			
				//有成功追加：保证金 外,不让：增资，减资 了
				$op_type_1 = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$peizi_order_id." and op_type = 0 and user_id = ".$GLOBALS["user_info"]["id"]." and op_status = 3 ");
				if($op_type_1 && ($op_type==2||$op_type==3))
				{
					$return["status"] = 0;
					$return["msg"] = "提交错误，请刷新重试";
					ajax_return($return);
					return;
				}
				else
				{
					$data = array();
					$data["peizi_order_id"] = $peizi_order_id;
					$data["op_type"] = $op_type;
					$data["create_date"] = to_date(TIME_UTC);
					$data["op_val"] = $op_val;
					$data["memo"] = $memo;										
					$data["user_id"] = $GLOBALS["user_info"]["id"];
					
					$data["lever"] = $info["lever"];
					$data['cost_money'] = $info["cost_money"];
					
					$data["change_memo"] = get_peizi_op_val_info($data,get_peizi_type($info['type'],true));
					
					$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_op",$data,"INSERT");
					$return["status"] = 1;
					$return["msg"] = "提交成功，请等待管理员审核";
				}
			}
			else
			{
				$return["status"] = 0;
				$return["msg"] = "操作失败请重试";
			}
		}
		else
		{
			$return["status"] = 0;
			$return["title"] = "保存失败，请刷新重新操作";
		}
		ajax_return($return);
	}
	public function cancel_op()
	{
		$id = $_REQUEST["id"];
		$info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where id = ".$id." and op_status = 0 and user_id = ".$GLOBALS["user_info"]["id"]);
		if($info)
		{
			$update_date = array();
			$update_date["op_status"] = 5;
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_op",$update_date,"UPDATE","id=".$id);
			$return["status"] = 1;
			$return["msg"] = "操作成功";
		}
		else
		{
			$return["status"] = 0;
			$return["msg"] = "保存失败，请刷新重新操作";
		}
		ajax_return($return);
	}
	//电子合同
	public function contract(){
		$id = intval($_REQUEST['id']);
		if($id == 0){
			showErr("操作失败！");
		}
		$peizi_order = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."peizi_order WHERE id=".$id." and user_id=".$GLOBALS['user_info']['id']." ORDER BY create_time ASC");
		if(!$peizi_order){
			showErr("操作失败！");
		}
		$peizi_order = get_peizi_order_fromat($peizi_order);
		$GLOBALS['tmpl']->assign('vo',$peizi_order);
		
		$u_info = get_user("*",$peizi_order['user_id']);
		
		$invite_info = get_user("*",$peizi_order['invest_user_id']);
		
		$GLOBALS['tmpl']->assign('user_info',$u_info);
		
		$GLOBALS['tmpl']->assign('invest_user_info',$invite_info);
		
		$GLOBALS['tmpl']->assign('SITE_URL',str_replace(array("https://","http://"),"",SITE_DOMAIN));
		$GLOBALS['tmpl']->assign('SITE_TITLE',app_conf("SITE_TITLE"));
		$GLOBALS['tmpl']->assign('CURRENCY_UNIT',app_conf("CURRENCY_UNIT"));
		
		
		$contract = $GLOBALS['tmpl']->fetch("str:".get_contract($peizi_order['contract_id']));
		
		$GLOBALS['tmpl']->assign('contract',$contract);
		
		$GLOBALS['tmpl']->display("inc/uc/uc_trader_contract.html");	
	}
	
	
	//电子合同
	public function dcontract(){
		$id = intval($_REQUEST['id']);
		if($id == 0){
			showErr("操作失败！");
		}
		$peizi_order = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."peizi_order WHERE id=".$id." and user_id=".$GLOBALS['user_info']['id']." ORDER BY create_time ASC");
		if(!$peizi_order){
			showErr("操作失败！");
		}
		$peizi_order = get_peizi_order_fromat($peizi_order);
		$GLOBALS['tmpl']->assign('vo',$peizi_order);
		
		$u_info = get_user("*",$peizi_order['user_id']);
		
		$invite_info = get_user("*",$peizi_order['invest_user_id']);
		
		$GLOBALS['tmpl']->assign('user_info',$u_info);
		
		$GLOBALS['tmpl']->assign('invest_user_info',$invite_info);
		
		$GLOBALS['tmpl']->assign('SITE_URL',str_replace(array("https://","http://"),"",SITE_DOMAIN));
		$GLOBALS['tmpl']->assign('SITE_TITLE',app_conf("SITE_TITLE"));
		$GLOBALS['tmpl']->assign('CURRENCY_UNIT',app_conf("CURRENCY_UNIT"));
		
		
		$contract = $GLOBALS['tmpl']->fetch("str:".get_contract($peizi_order['contract_id']));
	
	
		$GLOBALS['tmpl']->assign('contract',$contract);

		require APP_ROOT_PATH."/system/utils/word.php";
    	$word = new word(); 
   		$word->start(); 
   		$wordname = "借款协议.doc"; 
   		echo  $GLOBALS['tmpl']->fetch("inc/uc/uc_trader_contract.html");
   		$word->save($wordname); 
		
	}
	//投资金额返利
	public function invest_money()
	{

		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where p_invest_user_id = ".$GLOBALS["user_info"]["id"]." and status in (6,8)");

		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("SELECT o.*,c.name as conf_type_name ,u.user_name
			FROM ".DB_PREFIX."peizi_order o 
			left join ".DB_PREFIX."peizi_conf c on o.peizi_conf_id = c.id 
			left join ".DB_PREFIX."user u on o.p_invest_user_id = u.id
			where o.p_invest_user_id=".$GLOBALS["user_info"]["id"]." and o.status in (6,8)");
		}
		foreach($result['list'] as $k => $v)
		{
			$result['list'][$k] = get_peizi_order_fromat($v);
		}
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_invest_money.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	//利息与佣金收益返利
	public function invest_fee_money()
	{

		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order_fee_list  where p_invest_user_id =".$GLOBALS["user_info"]["id"]." and has_pay = 1");
		if($result['count'] > 0){
			$sql = 	"select fl.*,u.user_name,i_u.user_name as invest_user_name,o.order_sn from ".DB_PREFIX."peizi_order_fee_list fl left join ".DB_PREFIX."user u on fl.user_id = u.id
			left join ".DB_PREFIX."user i_u on fl.invest_user_id = i_u.id left join ".DB_PREFIX."peizi_order o on fl.peizi_order_id = o.id where fl.p_invest_user_id =".$GLOBALS["user_info"]["id"]." and fl.has_pay = 1" ;
			$result['list'] = $GLOBALS['db']->getAll($sql);
		}
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_invest_fee_money.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	//借款金额返利
	public function invite_borrow_money()
	{

		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where p_user_id = ".$GLOBALS["user_info"]["id"]." and status in (6,8)");

		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("SELECT o.*,c.name as conf_type_name FROM ".DB_PREFIX."peizi_order o left join ".DB_PREFIX."peizi_conf c on o.peizi_conf_id = c.id where o.p_user_id=".$GLOBALS["user_info"]["id"]." and o.status in (6,8)");
		}
		foreach($result['list'] as $k => $v)
		{
			$result['list'][$k] = get_peizi_order_fromat($v);
		}
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_borrow_money.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	//平台利息收益返利
	public function borrow_interest_money()
	{

		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order_fee_list  where p_user_id =".$GLOBALS["user_info"]["id"]." and has_pay = 1");
		if($result['count'] > 0){
			$sql = 	"select fl.*,u.user_name,i_u.user_name as invest_user_name,o.order_sn from ".DB_PREFIX."peizi_order_fee_list fl left join ".DB_PREFIX."user u on fl.user_id = u.id
			left join ".DB_PREFIX."user i_u on fl.invest_user_id = i_u.id left join ".DB_PREFIX."peizi_order o on fl.peizi_order_id = o.id where fl.p_user_id =".$GLOBALS["user_info"]["id"]." and fl.has_pay = 1" ;
			$result['list'] = $GLOBALS['db']->getAll($sql);
		}
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_borrow_interest_money.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	//平台佣金收益返利
	public function borrow_commission_money()
	{

		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order_fee_list  where p_user_id =".$GLOBALS["user_info"]["id"]." and has_pay = 1");
		if($result['count'] > 0){
			$sql = 	"select fl.*,u.user_name,i_u.user_name as invest_user_name,o.order_sn from ".DB_PREFIX."peizi_order_fee_list fl left join ".DB_PREFIX."user u on fl.user_id = u.id
			left join ".DB_PREFIX."user i_u on fl.invest_user_id = i_u.id left join ".DB_PREFIX."peizi_order o on fl.peizi_order_id = o.id where fl.p_user_id =".$GLOBALS["user_info"]["id"]." and fl.has_pay = 1" ;
			$result['list'] = $GLOBALS['db']->getAll($sql);
		}
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_borrow_commission_money.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	//投资单
	public function investment()
	{
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where invest_user_id = ".$GLOBALS['user_info']["id"]." ORDER BY id DESC");

		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("SELECT o.*,u.user_name FROM ".DB_PREFIX."peizi_order o left join ".DB_PREFIX."user u on o.user_id= u.id WHERE o.invest_user_id=".$GLOBALS['user_info']["id"]." ORDER BY o.id DESC");
		}
		foreach($result['list'] as $k => $v)
		{
			$result['list'][$k]["trader_money"] = $v["cost_money"] + $v["borrow_money"];
			$result['list'][$k]["loss_money"] = $v["stock_money"] - ($v["cost_money"] + $v["borrow_money"]);
			$result['list'][$k]["loss_money_format"] = format_price($v["loss_money"]);
			$result['list'][$k]["loss_ratio"] = $v["stock_money"]/($v["cost_money"] + $v["borrow_money"]);
			$result['list'][$k]["status"] = get_peizi_status($v["status"]);
		}
		
		$GLOBALS['tmpl']->assign("trader_list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_investment.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function peizi_invest_detail()
	{
		$id = intval($_REQUEST["id"]);
		if($id>0)
		{
			$trader_info = $GLOBALS['db']->getRow("select po.*,AES_DECRYPT(po.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd,pc.name as conf_type_name,u.user_name from ".DB_PREFIX."peizi_order po left join ".DB_PREFIX."peizi_conf as pc on po.peizi_conf_id = pc.id left join ".DB_PREFIX."user u on  po.user_id = u.id where  po.invest_user_id = ".intval($GLOBALS['user_info']['id'])." and po.id=".$id);		
		
			$trader_info = get_peizi_order_fromat($trader_info);
			
			if($trader_info["status"] != 6 && $trader_info["status"] != 8)
			{
				$trader_info["loss_money_format"] = "￥0.00";
			}

			/*操盘列表*/
			$op_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_op where user_id = ".intval($trader_info['user_id'])." and peizi_order_id=".$id." order by id desc ");		
			
			foreach($op_list as $k => $v)
			{
				
				$op_list[$k]["op_type_format"] = get_peizi_op_type($v["op_type"]);
				$op_list[$k]["op_status_format"] = get_peizi_op_status($v["op_status"],$v["op_type"]);
				
				if($v["op_status"] == 3 || $v["op_status"] == 4)
				{
					$op_list[$k]["op_date"] = $v["op_date2"];
				}
				else
				{
					$op_list[$k]["op_date"] = $v["op_date1"];
				}
			}
			/*资金列表*/
			$fee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_order_fee_list where user_id = ".intval($trader_info['user_id'])." and peizi_order_id=".$id." order by id desc");		
			
			/*历史金额*/
			$history_list = $GLOBALS['db']->getAll("select m.stock_date,m.stock_money from ".DB_PREFIX."peizi_order_stock_money m left join ".DB_PREFIX."peizi_order po on m.peizi_order_id = po.id where po.user_id = ".intval($trader_info['user_id'])." and peizi_order_id=".$id." order by m.id asc");		
			
			$GLOBALS['tmpl']->assign("history_list",$history_list);
			
			$GLOBALS['tmpl']->assign("fee_list",$fee_list);
			
			$GLOBALS['tmpl']->assign("op_list",$op_list);
			
			$GLOBALS['tmpl']->assign("vo",$trader_info);
			
			$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_invest_detail.html");
			
			$GLOBALS['tmpl']->display("page/uc.html");
		}
		else
		{
			showErr("访问错误，请重试");
		}
	}
	//待投资列表
	public function wait_investment()
	{
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order where status = 2 and invest_user_id = 0 ");

		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("SELECT o.*,u.user_name FROM ".DB_PREFIX."peizi_order o left join ".DB_PREFIX."user u on o.user_id= u.id WHERE status = 2 and invest_user_id = 0 and o.user_id <>".$GLOBALS["user_info"]["id"]." ORDER BY o.invest_begin_time DESC");
		}
		foreach($result['list'] as $k => $v)
		{
			$result['list'][$k] = get_peizi_order_fromat($v);
		}
		
		$GLOBALS['tmpl']->assign("trader_list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_wait_investment.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	public function peizi_wait_invest_detail()
	{
		$id = intval($_REQUEST["id"]);
		if($id>0)
		{
			$trader_info = $GLOBALS['db']->getRow("select po.*,AES_DECRYPT(po.stock_pwd_encrypt,'".AES_DECRYPT_KEY."') as stock_pwd,pc.name as conf_type_name,u.user_name from ".DB_PREFIX."peizi_order po left join ".DB_PREFIX."peizi_conf as pc on po.peizi_conf_id = pc.id left join ".DB_PREFIX."user u on  po.user_id = u.id where po.id=".$id);		
		
			$trader_info = get_peizi_order_fromat($trader_info);
			
			if($trader_info["status"] != 6 && $trader_info["status"] != 8)
			{
				$trader_info["loss_money_format"] = "￥0.00";
			}
			
			$GLOBALS['tmpl']->assign("vo",$trader_info);
			
			$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_wait_invest_detail.html");
			
			$GLOBALS['tmpl']->display("page/uc.html");
		}
		else
		{
			showErr("访问错误，请重试");
		}
	}
	//配资单变更审核
	public function investment_change()
	{

		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order_op op left join ".DB_PREFIX."peizi_order o on op.peizi_order_id = o.id  where op.op_type in (1,2) and op.op_status = 0 and o.invest_user_id = ".intval($GLOBALS["user_info"]["id"]));

		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("select op.*,o.order_sn from ".DB_PREFIX."peizi_order_op op left join ".DB_PREFIX."peizi_order o on op.peizi_order_id = o.id  where op.op_type in (1,2) and op.op_status = 0 and o.invest_user_id = ".intval($GLOBALS["user_info"]["id"]));
		}
		
		foreach($result['list'] as $k => $v)
		{
			$result['list'][$k]["op_type_format"] = get_peizi_op_type($v["op_type"]);
			$result['list'][$k]["op_status_format"] = get_peizi_op_status($v["op_status"]);
		}
		
		$GLOBALS['tmpl']->assign("op_list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_investment_change.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	//配资单变更审核记录
	public function investment_change_log()
	{

		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = array();
		$result['count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."peizi_order_op op left join ".DB_PREFIX."peizi_order o on op.peizi_order_id = o.id  where op.op_type not in (1,2) and op.op_status = 0 and o.invest_user_id = ".intval($GLOBALS["user_info"]["id"]));

		if($result['count'] > 0){
			
			$result['list'] = $GLOBALS['db']->getAll("select op.*,o.order_sn from ".DB_PREFIX."peizi_order_op op left join ".DB_PREFIX."peizi_order o on op.peizi_order_id = o.id  where op.op_type not in (1,2) and op.op_status = 0 and o.invest_user_id = ".intval($GLOBALS["user_info"]["id"]));
		}
		
		foreach($result['list'] as $k => $v)
		{
			$result['list'][$k]["op_type_format"] = get_peizi_op_type($v["op_type"]);
			$result['list'][$k]["op_status_format"] = get_peizi_op_status($v["op_status"]);
		}
		
		$GLOBALS['tmpl']->assign("op_list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("inc_file","peizi/peizi_investment_change_log.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	//配资单更新
	public function update_invest_op()
	{
		$id = intval($_REQUEST["id"]);
		$type = intval($_REQUEST["type"])==1?1:2; //1为同意 2为不同意
		
		$info = $GLOBALS['db']->getRow("select op.* from ".DB_PREFIX."peizi_order_op op left join ".DB_PREFIX."peizi_order o on op.peizi_order_id = o.id  where op.op_type in (1,2) and op.op_status = 0 and o.invest_user_id = ".intval($GLOBALS["user_info"]["id"]))." and id =".$id;
		if($info)
		{
			$update_date = array();
			$update_date["op_status"] = $type;
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order_op",$update_date,"UPDATE","id=".$id);
			$result = array();
			$result["status"] = 1;
			$result["info"] = "操作成功";
			ajax_return($result);
		}
		else
		{
			$result = array();
			$result["status"] = 0;
			$result["info"] = "操作失败，请稍后重试";
			ajax_return($result);
		}
	}
	//投资
	public function do_investment()
	{
		$id = intval($_REQUEST["id"]);		
		$bid_paypassword = strim(FW_DESPWD($_REQUEST['bid_paypassword']));
		
		if($bid_paypassword==""){
			$root['status'] = 0;
			$root["info"] = $GLOBALS['lang']['PAYPASSWORD_EMPTY'];
			ajax_return($root);
		}
		
		if(md5($bid_paypassword)!=$GLOBALS['user_info']['paypassword']){
			$root['status'] = 0;
			$root["info"] = $GLOBALS['lang']['PAYPASSWORD_ERROR'];
			ajax_return($root);
		}
		
		$order_info = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."peizi_order where status = 2 and invest_user_id = 0");
		if(!$order_info)
		{
			$root['status'] = 0;
			$root["info"] = "已经有人投资";
			ajax_return($root);
		}
		if($order_info["user_id"]==$GLOBALS["user_info"]["id"])
		{
			$root['status'] = 0;
			$root["info"] = "不能投资自己的配资";
			ajax_return($root);
		}
		if($order_info["borrow_money"] > $GLOBALS['user_info']['money']){
			$root['status'] = 2;
			$root["show_err"] = $GLOBALS['lang']['MONEY_NOT_ENOUGHT'];
			$root["jump"] = url("index","uc_money#incharge");
			ajax_return($root);
		}
		
		$update = array();
		$update["invest_user_id"] = $GLOBALS["user_info"]["id"];
		$update["status"] = 4;
		$update['invest_end_time'] = to_date(TIME_UTC);
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."peizi_order",$update,"UPDATE","id=".$id." and  status = 2  and invest_user_id = 0");
		if($GLOBALS['db']->affected_rows())
		{
			require_once APP_ROOT_PATH."system/libs/user.php";
			modify_account(array('money'=>-$order_info['borrow_money'],'lock_money'=>$order_info['borrow_money']), $GLOBALS["user_info"]["id"],'配资投资冻结,配资编号:'.$order_info["id"],36);
	
			$root['status'] = 1;
			$root["info"] = "操作成功";
			$root["jump"] = url("index","uc_trader#wait_investment");
			ajax_return($root);
		}
		else
		{
			$root['status'] = 0;
			$root["info"] = "已经有人投资";
			ajax_return($root);
		}
	}
	public function makedeals()
	{
		//检查是否有发布的但是未确认投标的标
    	if($GLOBALS['db']->getOne("SELECT * FROM ".DB_PREFIX."deal WHERE is_delete=0 and publish_wait=1 and user_id=".$GLOBALS['user_info']['id']) > 0){
    		app_redirect(url("index","borrow#steptwo"));
    	}
		
		$GLOBALS['tmpl']->assign('page_title',"生成理财包");
		
		$user_statics = sys_user_status($GLOBALS['user_info']['id']);
		
		$GLOBALS['tmpl']->assign("user_statics",$user_statics);
		
		$GLOBALS['tmpl']->assign("typeid",$typeid);
		
		$agreement = app_conf('BORROW_AGREEMENT');
		$GLOBALS['tmpl']->assign("agreement",$agreement);
		
		$loan_type_list = load_auto_cache("deal_loan_type_list");
		$GLOBALS['tmpl']->assign("loan_type_list",$loan_type_list);
		
		$loantype_list = load_auto_cache("loantype_list");
    	$GLOBALS['tmpl']->assign("loantype_list",$loantype_list);
		
		$level_list = load_auto_cache("level");
		$GLOBALS['tmpl']->assign("level_list",$level_list);
		
		$has_day_type = 0;
		foreach($level_list['repaytime_list'][$GLOBALS['user_info']['level_id']] as $k=>$v){
			if($v[1] ==0)
				$has_day_type = 1;
		}
		$GLOBALS['tmpl']->assign("has_day_type",$has_day_type);
		
		/*-----*/
		$vo = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal WHERE (is_delete=2 or is_delete=3) AND user_id=".$GLOBALS['user_info']['id']);

		$ids  = $_REQUEST['ids'];
		
		if(!$ids)
		{
			$ids = explode(',',$vo["peizi_order_ids"]);
		}
		
		if(count($ids)==0){
			showErr("请选择配资");
			return;
		}
		
		$ids = implode(',',$ids);
		
		$ids = strim($ids);
		
		$GLOBALS['tmpl']->assign('peizi_ids',$ids);
		
		$has_deal = $GLOBALS["db"]->getOne("select group_concat(id) as ids  from ".DB_PREFIX."peizi_order where id in (".$ids.") and deal_id >0 and status in (6,8) and invest_user_id = ".intval($GLOBALS['user_info']["id"]));
		
		if($has_deal !="")
		{
			showErr("配资编号:“".$has_deal."”已分配理财包");
			return;
		}
		
		$condition = " o.id in (".$ids.")";
		$condition .= " and o.deal_id = 0 ";
		$condition .= " and o.status in (6,8) ";
		$condition .= " and o.invest_user_id = ".$GLOBALS["user_info"]["id"];
		
		$order_info = $GLOBALS["db"]->getAll("select o.*,pc.name as conf_type_name from ".DB_PREFIX."peizi_order o LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = o.peizi_conf_id where ".$condition);
		if(!$order_info){
			showErr("操作失败，请刷新重试");
			return;
		}
		
		
		foreach($order_info as $k=> $v)
		{
			$order_info[$k] = get_peizi_order_fromat($v);
		}
		$GLOBALS['tmpl']->assign('peizi_list',$order_info);
				
		$vo['user_id'] = $GLOBALS["user_info"]["id"]; //用户id
		
		$level = $GLOBALS['db']->getOne("SELECT name FROM ".DB_PREFIX."user_level WHERE id=".intval($GLOBALS['user_info']['level_id']));
		$GLOBALS['tmpl']->assign("level",$level);
		
		
		
		$user_view_infos = $GLOBALS['user_info']['view_info'];
	    $user_view_infos = unserialize($user_view_infos);
	    $user_view_infoss = array();
	    
	    $deal_view_info = unserialize($deal['view_info']);
	    foreach($user_view_infos as $k=>$v){
	    	//会员自己传的或者管理员勾选的 才会显示
	    	if(intval($v['is_user'])==1 || isset($deal_view_info[$k])){
	    		$user_view_infoss[$k] = $v;
	    		$user_view_infoss[$k]['key'] = $k;
	    		
	    		if(isset($deal_view_info[$k])){
	    			$user_view_infoss[$k]['is_selected'] = 1;
	    		}
	    	}
	    }	
	    $GLOBALS['tmpl']->assign("user_view_info",$user_view_infoss);
		
		//VIP会员 借款管理费
		$load_mfee = $GLOBALS['db']->getOne("SELECT v.load_mfee FROM ".DB_PREFIX."vip_setting v LEFT JOIN ".DB_PREFIX."user u ON u.vip_id=v.vip_id where u.vip_state=1 and v.is_effect=1 and u.id='".$GLOBALS['user_info']['id']."'");
		
		if($load_mfee){
			$manage_fee=$load_mfee;
		}else{
			$manage_fee = app_conf("MANAGE_FEE");
		}
    	$GLOBALS['tmpl']->assign('manage_fee',$manage_fee);
		
		//计算平均多少利率
		$vo['rate'] = 12;
		
		$vo['repay_time_type'] = 1;
		
		//计算贷款多少钱
		$vo['borrow_amount'] = $GLOBALS["db"]->getOne("select sum(borrow_money) as borrow_amount from ".DB_PREFIX."peizi_order o where ".$condition);
		
		/******/	
		$GLOBALS['tmpl']->assign("deal",$vo);
	    
	    //担保机构
	    $agency_list = get_user_info("*","is_effect = 1 AND user_type=2 ","ALL");
	    
	    $GLOBALS['tmpl']->assign("agency_list",$agency_list);
	    
	    $json_data = $level_list['repaytime_list'][$GLOBALS['user_info']['level_id']];
		$GLOBALS['tmpl']->assign('json_data',json_encode($json_data));
	   
    	$inc_file =  "peizi/peizi_borrow_stepone.html";
    	$GLOBALS['tmpl']->assign('inc_file',$inc_file);
    	$GLOBALS['tmpl']->display("page/borrow_step.html");
		
		/******/
	}
	function domakedeals(){
		$is_ajax = intval($_REQUEST['is_ajax']);
    	
    	if(!$GLOBALS['user_info']){
    		showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$is_ajax);
    	}
    	$t = trim($_REQUEST['t']);
    	
    	if(!in_array($t,array("save","publish"))){
    		showErr($GLOBALS['lang']['ERROR_TITLE'],$is_ajax);
    	}
    	
    	if($t=="save")
    		$data['is_delete'] = 2;
    	else
    		$data['is_delete'] = 0;
    	
    	$data['name'] = strim($_REQUEST['borrowtitle']);
    	if(empty($data['name'])){
    		showErr("请输入借款标题",$is_ajax);
    	}
    	$data['publish_wait'] = 1;
    	$icon_type = strim($_REQUEST['imgtype']);
    	if($icon_type==""){
    		showErr("请选择借款图片类型",$is_ajax);
    	}
    	$icon_type_arr = array(
    		'upload' =>1,
    		'userImg' =>2,
    		'systemImg' =>3,
    	);
    	$data['icon_type'] = $icon_type_arr[$icon_type];
    	
    	if(intval($data['icon_type'])==0)
    	{
    		showErr("请选择借款图片类型",$is_ajax);
    	}
    	
    	switch($data['icon_type']){
    		case 1 :
    			if(strim($_REQUEST['icon'])==''){
    				showErr("请上传图片",$is_ajax);
    			}
    			else{
    				$data['icon'] = replace_public(strim($_REQUEST['icon']));
    			}
    			break;
    		case 2 :
    			$data['icon'] = replace_public(get_user_avatar($GLOBALS['user_info']['id'],'big'));
    			break;
    		case 3 :
    			if(intval($_REQUEST['systemimgpath'])==0){
    				showErr("请选择系统图片",$is_ajax);
    			}
    			else{
    				$data['icon'] = $GLOBALS['db']->getOne("SELECT icon FROM ".DB_PREFIX."deal_loan_type WHERE id=".intval($_REQUEST['systemimgpath']));
    			}
    			break;
    	}
    	
    	$data['type_id'] = intval($_REQUEST['borrowtype']);
    	if($data['type_id']==0){
    		showErr("请选择借款用途",$is_ajax);
    	}
    	
    	$data['borrow_amount'] = floatval($_REQUEST['borrowamount']);
    	
    	if($data['borrow_amount'] < (int)trim(app_conf('MIN_BORROW_QUOTA')) || $data['borrow_amount'] > (int)trim(app_conf('MAX_BORROW_QUOTA')) || $data['borrow_amount'] %50 != 0){
    		showErr("请正确输入借款金额",$is_ajax);
    	}
    	
    	/*//判断是否需要额度
    	if($GLOBALS['db']->getOne("SELECT is_quota FROM ".DB_PREFIX."deal_loan_type WHERE id=".$data['type_id']) == 1){
	    	if(intval($GLOBALS['user_info']['quota']) != 0){
	    		$can_use_quota = get_can_use_quota($GLOBALS['user_info']['id']);
	    		if($data['borrow_amount'] > intval($can_use_quota)){
	    			showErr("输入借款的借款金额超过您的可用额度<br>您当前可用额度为：".$can_use_quota,$is_ajax);
	    		}
	    	}
    	}*/
    	
    	$data['repay_time'] = intval($_REQUEST['repaytime']);
    	if($data['repay_time']==0){
    		showErr("借款期限",$is_ajax);
    	}
    	$data['rate'] = floatval($_REQUEST['apr']);
    	$data['repay_time_type'] = intval($_REQUEST['repaytime_type']);
    	$level_list = load_auto_cache("level");
    	$min_rate = 0;
    	$max_rate = 0;
    	$is_rate_lock = false;
    	
    	foreach($level_list['repaytime_list'][$GLOBALS['user_info']['level_id']] as $kkk=>$vvv){
    		if($data['repay_time_type']==1){
	    		if($data['repay_time'] == intval($vvv[0]) && $vvv[1]==$data['repay_time_type']){
	    			$min_rate = $vvv[2];
	    			$max_rate = $vvv[3];
	    		}
    		}
    		else{
    			if($data['repay_time'] <= intval($vvv[0]) && intval($vvv[1]) == $data['repay_time_type'] && $is_rate_lock == false){
					$min_rate = $vvv[2];
					$max_rate = $vvv[3];
					$is_rate_lock = true;
				}
				elseif($data['repay_time'] > intval($vvv[0])  && intval($vvv[1]) == $data['repay_time_type']){
					$min_rate = $vvv[2];
					$max_rate = $vvv[3];
				}
    		}
    	}
    	
    	if(floatval($data['rate']) <= 0 || floatval($data['rate']) > $max_rate || floatval($data['rate']) < $min_rate){
    		showErr("请正确输入借款利率",$is_ajax);
    	}
    	
    	$data['enddate'] = intval($_REQUEST['enddate']);
    	
    	$data['description'] = replace_public(btrim($_REQUEST['borrowdesc']));
    	$data['description'] = valid_tag($data['description']);
    	
    	if(trim($data['description'])==''){
    		showErr("请输入项目描述",$is_ajax);
    	}
    	
    	$user_view_info = $GLOBALS['user_info']['view_info'];
    	$user_view_info = unserialize($user_view_info);
    	
    	$new_view_info_arr = array();	
    	for($i=1;$i<=intval($_REQUEST['file_upload_count']);$i++){
    		$img_info = array();
    		$img = replace_public(strim($_REQUEST['file_'.$i]));
    		if($img!=""){
    			$img_info['name'] = strim($_REQUEST['file_name_'.$i]);
    			$img_info['img'] = $img;
    			$img_info['is_user'] = 1;
    			
    			$user_view_info[] = $img_info;
    			$ss = $user_view_info;
				end($ss);
				$key = key($ss);
    			$new_view_info_arr[$key] = $img_info;
    		}
    	}
    	    	
    	$datas['view_info'] = serialize($user_view_info);
    	
    	$GLOBALS['db']->autoExecute(DB_PREFIX."user",$datas,"UPDATE","id=".$GLOBALS['user_info']['id']);

    	
    	$data['view_info'] = array();
    	foreach($_REQUEST['file_key'] as $k=>$v){
    		if(isset($user_view_info[$v])){
    			$data['view_info'][$v] = $user_view_info[$v];
    		}
    	}
    	
    	foreach($new_view_info_arr as $k=>$v){
    		$data['view_info'][$k] = $v;
    	}
    	
    	$data['view_info'] = serialize($data['view_info']);
    	
    	
    	//资金运转
    	$data['remark_1'] = strim(replace_public($_REQUEST['remark_1']));
    	$data['remark_1'] = valid_tag($data['remark_1']);
    	//风险控制措施
    	$data['remark_2'] = strim(replace_public($_REQUEST['remark_2']));
    	$data['remark_2'] = valid_tag($data['remark_2']);
    	//政策及市场分析
    	$data['remark_3'] = strim(replace_public($_REQUEST['remark_3']));
    	$data['remark_3'] = valid_tag($data['remark_3']);
    	//企业背景
    	$data['remark_4'] = strim(replace_public($_REQUEST['remark_4']));
    	$data['remark_4'] = valid_tag($data['remark_4']);
    	//企业信息
    	$data['remark_5'] = strim(replace_public($_REQUEST['remark_5']));
    	$data['remark_5'] = valid_tag($data['remark_5']);
    	//项目相关资料
    	$data['remark_6'] = strim(replace_public($_REQUEST['remark_6']));
    	$data['remark_6'] = valid_tag($data['remark_6']);
    	
    	//$data['voffice'] = intval($_REQUEST['voffice']);
    	//$data['vposition'] = intval($_REQUEST['vposition']);
    	$data['voffice'] = 1;
    	$data['vposition'] = 1;
    	
    	$data['is_effect'] = 1;
    	$data['deal_status'] = 0;
    	
    	$data['agency_id'] = intval($_REQUEST['agency_id']);
    	$data['agency_status'] = 1;
    	$data['warrant'] = intval($_REQUEST['warrant']);
    	
    	
    	$data['guarantor_margin_amt'] = floatval($_REQUEST['guarantor_margin_amt']);
    	$data['guarantor_pro_fit_amt'] = floatval($_REQUEST['guarantor_pro_fit_amt']);
    	
    	$data['user_id'] = intval($GLOBALS['user_info']['id']);
    	
    	$data['loantype'] = intval($_REQUEST['loantype']);
    	if($data['repay_time_type'] == 0){
    		$data['loantype'] = 2;
    	}
    	
    	//当为天的时候
		if($data['repay_time_type'] == 0){
			$true_repay_time = 1;
		}
		else{
			$true_repay_time = $data['repay_time'];
		}
    	
    	//本金担保
    	if($data['warrant'] == 1){
    		$data['guarantor_amt'] = $data['borrow_amount'];
    	}
    		
    	//本息担保
    	elseif($data['warrant'] == 2){
    		//等额本息
    		if($data['loantype']==0)
    			$data['guarantor_amt'] = pl_it_formula($data['borrow_amount'],$data['rate']/12/100,$true_repay_time) * $true_repay_time;
    		//付息还本
    		elseif($data['loantype']==1)
    			$data['guarantor_amt'] = av_it_formula($data['borrow_amount'],$data['rate']/12/100) * $true_repay_time  +  $data['borrow_amount'];
    		//到期本息
    		elseif($data['loantype']==2)
    			$data['guarantor_amt'] = $data['borrow_amount'] * $data['rate']/12/100 * $true_repay_time +  $data['borrow_amount'];
    	}
    	
    	$data['create_time'] = TIME_UTC;
    	
    	$module = "INSERT";
    	$jumpurl = url("index","borrow#steptwo");
    	$condition = "";
    	
    	$deal_id = $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."deal WHERE ((is_delete=2 or is_delete=3) or (is_delete=0 and publish_wait=1)) AND user_id=".$GLOBALS['user_info']['id']);
    	if($deal_id > 0){
    		$module = "UPDATE";
    		if($t=="save")
    			$jumpurl = url("index","borrow#stepone");
    		$condition = "id = $deal_id";
    	}
    	else{
    		if($t=="save"){
    			$jumpurl = url("index","borrow#stepone");
    		}
    	}
		$data["peizi_order_ids"] = "";
    	$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$data,$module,$condition);
    	if($module == "INSERT"){
    		$deal_id = $GLOBALS['db']->insert_id();
    	}
    	
		require_once APP_ROOT_PATH.'app/Lib/deal.php';
		$deal = get_deal($deal_id);
    	//发送验证通知
    	if(
    		$t!="save" &&
    		trim(app_conf('CUSTOM_SERVICE'))!=''&&
    		($GLOBALS['user_info']['idcardpassed']==0 || 
    		 $GLOBALS['user_info']['incomepassed']==0 || 
    		 $GLOBALS['user_info']['creditpassed']==0 || 
    		 $GLOBALS['user_info']['workpassed']==0
    		)
    		
    	){
    		$ulist = explode(",",trim(app_conf('CUSTOM_SERVICE')));
			$ulist = array_filter($ulist);
			
			if($ulist){
				$uuid = $ulist[array_rand($ulist)];
				if($uuid > 0){
		    		$content = app_conf("SHOP_TITLE")."用户您好，请尽快上传必要信用认证材料（包括身份证认证、工作认证、收入认证、信用报告认证）。另外，多上传一些可选信用认证，有助于您提高借款额度，也有利于出借人更多的了解您的情况，以便让您更快的筹集到所需的资金。请您点击'我要贷款'，之后点击相应的审核项目，进入后，可先阅读该项信用认证所需材料及要求，然后按要求上传资料即可。 如果您有任何问题请您拨打客服电话 ".app_conf('SHOP_TEL')." 或给客服邮箱发邮件 ".app_conf("REPLY_ADDRESS")." 我们会及时给您回复。";
		    		require_once APP_ROOT_PATH.'app/Lib/message.php';
		    		
		    		//添加留言
					$message['title'] = $content;
					$message['content'] = htmlspecialchars(addslashes(valid_str($content)));
					$message['title'] = valid_str($message['title']);
						
					$message['create_time'] = TIME_UTC;
					$message['rel_table'] = "deal";
					$message['rel_id'] = $deal_id;
					$message['user_id'] = $uuid;
					
					$message['is_effect'] = 1;		
					$GLOBALS['db']->autoExecute(DB_PREFIX."message",$message);
					
					//添加到动态
					insert_topic("message",$message['rel_id'],$message['user_id'],get_user_name($message['user_id'],false),$GLOBALS['user_info']['id']);
					
					//自己给自己留言不执行操作
					if($deal['user_id']!=$message['user_id']){
						$msg_conf = get_user_msg_conf($deal['user_id']);
						//站内信
						if($msg_conf['sms_asked']==1){
							$notices['shop_title'] = app_conf("SHOP_TITLE");
							$notices['shop_tel'] = app_conf('SHOP_TEL');
							$notices['shop_address'] = app_conf("REPLY_ADDRESS");
							
							/*{$notice.shop_title}用户您好，请尽快上传必要信用认证材料（包括身份证认证、工作认证、收入认证、信用报告认证）。另外，多上传一些可选信用认证，有助于您提高借款额度，也有利于出借人更多的了解您的情况，以便让您更快的筹集到所需的资金。请您点击'我要贷款'，之后点击相应的审核项目，进入后，可先阅读该项信用认证所需材料及要求，然后按要求上传资料即可。 如果您有任何问题请您拨打客服电话{$notice.shop_tel}或给客服邮箱发邮件{$notice.shop_address}我们会及时给您回复。*/
							$notices['url'] = "“<a href=\"".$deal['url']."\">".$deal['name']."</a>”";
							$notices['user_name'] = get_user_name($message['user_id']);
							$notices['msg'] = $message['content'];
							
							$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_WORDS_MSG'",false);
							$GLOBALS['tmpl']->assign("notice",$notices);
							$contents = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
							
							send_user_msg("",$contents,0,$deal['user_id'],TIME_UTC,0,true,13,$message['rel_id']);
						}
						//邮件
						if($msg_conf['mail_asked']==1 && app_conf('MAIL_ON')==1){
							$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_MAIL_DEAL_MSG'");
							$tmpl_content = $tmpl['content'];
							
							$notice['user_name'] = $GLOBALS['user_info']['user_name'];
							$notice['msg_user_name'] = get_user_name($message['user_id'],false);
							$notice['deal_name'] = $deal['name'];
							$notice['deal_url'] = SITE_DOMAIN.url("index","deal",array("id"=>$deal['id']));
							$notice['message'] = $message['content'];
							$notice['site_name'] = app_conf("SHOP_TITLE");
							$notice['site_url'] = SITE_DOMAIN.APP_ROOT;
							$notice['help_url'] = SITE_DOMAIN.url("index","helpcenter");
							
							
							$GLOBALS['tmpl']->assign("notice",$notice);
							
							$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
							$msg_data['dest'] = $GLOBALS['user_info']['email'];
							$msg_data['send_type'] = 1;
							$msg_data['title'] = get_user_name($message['user_id'],false)."给您的标留言！";
							$msg_data['content'] = addslashes($msg);
							$msg_data['send_time'] = 0;
							$msg_data['is_send'] = 0;
							$msg_data['create_time'] = TIME_UTC;
							$msg_data['user_id'] = $GLOBALS['user_info']['id'];
							$msg_data['is_html'] = $tmpl['is_html'];
							$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
						}
					}
				}
			}
    	}

    	$GLOBALS['db']->query("UPDATE ".DB_PREFIX."peizi_order SET deal_id=".$deal_id." WHERE id in (".strim($_REQUEST['peizi_ids']).") ");
		if($is_ajax==1){
    		showSuccess($GLOBALS['lang']['SUCCESS_TITLE'],$is_ajax,$jumpurl);
    	}
    	else{
	    	app_redirect($jumpurl);
    	}	
	}
}
?>