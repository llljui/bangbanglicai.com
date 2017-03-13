<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';

class uc_interestrateModule extends SiteBaseModule
{
	public function index()
	{
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = get_interestrate_list($limit,$GLOBALS['user_info']['id']);

		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("page_title","我的加息券");
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_interestrate_index.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function exchange()
	{
		 
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$result = get_exchange_interestrate_list($limit);
		
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
	
		
		$GLOBALS['tmpl']->assign("page_title","我的加息券");
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_interestrate_exchange.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function do_exchange()
	{
		$id = intval($_REQUEST['id']);
		$ecv_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."interestrate_type where id = ".$id."  and (begin_time = 0 or begin_time <= ".to_timespan(to_date(TIME_UTC,"Y-m-d")).") and (end_time = 0 or end_time >= ".to_timespan(to_date(TIME_UTC,"Y-m-d")).")");
		if(!$ecv_type)
		{
			showErr("非法的请求",1);
		}
		else
		{
			$exchange_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."interestrate where ecv_type_id = ".$id." and user_id = ".intval($GLOBALS['user_info']['id']));
			if($ecv_type['exchange_limit']>0&&$exchange_count>=$ecv_type['exchange_limit'])
			{
				$msg = sprintf($GLOBALS['lang']['EXCHANGE_VOUCHER_LIMIT'],$ecv_type['exchange_limit']);
				showErr($msg,1);
			}
			elseif($ecv_type['exchange_score']>intval($GLOBALS['db']->getOne("select score from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']))))
			{
				showErr($GLOBALS['lang']['INSUFFCIENT_SCORE'],1);
			}
			else
			{
				require_once APP_ROOT_PATH."system/libs/voucher.php";
				$rs = send_interestrate($ecv_type['id'],$GLOBALS['user_info']['id'],1);
				if($rs)
				{
					require_once APP_ROOT_PATH."system/libs/user.php";
					$msg = sprintf($GLOBALS['lang']['EXCHANGE_VOUCHER_USE_SCORE'],$ecv_type['name'],$ecv_type['exchange_score']);
					modify_account(array('score'=>"-".$ecv_type['exchange_score']),$GLOBALS['user_info']['id'],$msg,'22');
					showSuccess($GLOBALS['lang']['EXCHANGE_SUCCESS'],1,url('index','uc_interestrate'));
				}
				else
				{
					showErr($GLOBALS['lang']['EXCHANGE_FAILED'],1);
				}
			}
		}
	}
	
	public function do_snexchange()
	{
		$sn = strim($_REQUEST['sn']);
		$ecv_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."interestrate_type where exchange_sn = '".$sn."' and (end_time = 0 or end_time >= ".to_timespan(to_date(TIME_UTC,"Y-m-d")).")");
		if(!$ecv_type)
		{
			showErr("非法的请求",1);
		}
		else
		{
			$exchange_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."interestrate where ecv_type_id = ".$ecv_type['id']." and user_id = ".intval($GLOBALS['user_info']['id']));
			if($ecv_type['exchange_limit']>0&&$exchange_count>=$ecv_type['exchange_limit'])
			{
				$msg = sprintf($GLOBALS['lang']['EXCHANGE_VOUCHER_LIMIT'],$ecv_type['exchange_limit']);
				showErr($msg,1);
			}
			else
			{
				require_once APP_ROOT_PATH."system/libs/voucher.php";
				$rs = send_interestrate($ecv_type['id'],$GLOBALS['user_info']['id'],1);
				if($rs)
				{
					showSuccess($GLOBALS['lang']['EXCHANGE_SUCCESS'],1,url('index','uc_interestrate'));
				}
				else
				{
					showErr($GLOBALS['lang']['EXCHANGE_FAILED'],1);
				}
			}
		}
	}
	
	public function detail()
	{
		$id = intval($_REQUEST["id"]);
		
		$sql = "select * from ".DB_PREFIX."interestrate as e left join ".DB_PREFIX."interestrate_type as et on e.ecv_type_id = et.id where et.id = ".$id;
		
		$item = $GLOBALS["db"]->getRow($sql);
		
		if($item)
		{
			$item["rate_format"] = number_format($item["rate"],2)."%";
			$GLOBALS['tmpl']->assign("item",$item);
		}
		$GLOBALS['tmpl']->display("inc/uc/uc_interestrate_detail.html");
	}
	public function send_interestrate()
	{
		$id = $_REQUEST["id"];
		$GLOBALS['tmpl']->assign("id",$id);
		$GLOBALS['tmpl']->display("inc/uc/uc_interestrate_send.html");
	}
	public function do_send_interestrate()
	{
		$id = intval($_REQUEST["id"]);
		$user_name = strim($_REQUEST["user_name"]);
		$to_user_id = get_user_info("id","user_name = '".$user_name."'","ONE");
		if(!$to_user_id)
		{
			showErr("转赠失败",1);
			return;
		}
		elseif($to_user_id == $GLOBALS["user_info"]["id"])
		{
			showErr("不能转赠给自己",1);
			return;
		}
		
		$result = $GLOBALS["db"]->getRow("select * from ".DB_PREFIX."interestrate where id=".$id." and user_id=".$GLOBALS["user_info"]["id"]." and to_user_id = 0 and (begin_time =0 OR (begin_time > 0 AND begin_time < ".TIME_UTC.")) AND (end_time = 0 OR (end_time > 0  AND (end_time +24*3600 - 1) > ".TIME_UTC.")) AND (use_limit =0  OR (use_limit >0 AND use_limit - use_count > 0))");

		if($result)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."interestrate set to_user_id = ".$to_user_id." where id = ".$id);
			showSuccess("转赠成功",1,url('index','uc_interestrate'));
		}
		else
		{
			showErr("转赠失败",1);
		}
	}
}
?>