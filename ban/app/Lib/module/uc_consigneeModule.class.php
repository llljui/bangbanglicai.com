<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_consigneeModule extends SiteBaseModule
{
	public function index()
	{
		$consignee_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_address where user_id = ".intval($GLOBALS['user_info']['id']));
		$GLOBALS['tmpl']->assign("user_id",intval($GLOBALS['user_info']['id']));
		$GLOBALS['tmpl']->assign("consignee_list",$consignee_list);
		$GLOBALS['tmpl']->assign("page_title","收货地址");
		$GLOBALS['tmpl']->assign("inc_file","inc/uc/uc_consignee.html");
		$GLOBALS['tmpl']->display("page/uc.html");
	}
	
	public function add_consignee()
	{
		if(!$GLOBALS['user_info'])
		{
			$data['html'] = $GLOBALS['tmpl']->display("inc/login_form.html","",true);			
			$data['status'] = 0;
		}
		else
		{
			$GLOBALS['tmpl']->caching = true;
			$cache_id  = md5(MODULE_NAME.ACTION_NAME);		
			if (!$GLOBALS['tmpl']->is_cached('inc/uc/uc_add_consignee.html', $cache_id))
			{		
				$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by id asc");  //二级地址
				$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
			}			
			$data['html'] = $GLOBALS['tmpl']->fetch("inc/uc/uc_add_consignee.html",$cache_id,true);			
			$data['status'] = 1;
		}
		ajax_return($data);
	}
	
	public function save_consignee()
	{		
		$ajax = intval($_REQUEST['ajax']);
		
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user_address where user_id = ".intval($GLOBALS['user_info']['id']))>10)
		{
			showErr("每个会员只能预设10个配送地址",$ajax,"");
		}
		
		$id = intval($_REQUEST['id']);
		$consignee = strim($_REQUEST['consignee']);
		$address = strim($_REQUEST['address']);
		$mobile = strim($_REQUEST['mobile']);
		if($consignee=="")
		{
			showErr("请填写收货人姓名",$ajax,"");	
		}
		if($address=="")
		{
			showErr("请填写详细地址",$ajax,"");	
		}
		if($mobile=="")
		{
			showErr("请填写收货人手机号码",$ajax,"");	
		}
		if(!check_mobile($mobile))
		{
			showErr("请填写正确的手机号码",$ajax,"");	
		}
		
		$data = array();
		$data['name'] = $consignee;
		$data['address'] = $address;
		$data['phone'] = $mobile;
		$data['user_id'] = intval($GLOBALS['user_info']['id']);
		
		
		
		if(!check_ipop_limit(get_client_ip(),"setting_save_consignee",5))
		showErr("提交太频繁",$ajax,"");	
		
	
		if($id>0)
		$GLOBALS['db']->autoExecute(DB_PREFIX."user_address",$data,"UPDATE","id=".$id);
		else
		$GLOBALS['db']->autoExecute(DB_PREFIX."user_address",$data);
		
		showSuccess("保存成功",$ajax);
		//$res = save_user($user_data);
	}
	public function set_default_consignee(){
		$data=array('status'=>0,'info'=>'');
		$id=intval($_POST['id']);
		$user_id=intval($_POST['user_id']);
		if(!$id){
			$data['info']="信息错误";
		}else{
			if($GLOBALS['db']->getOne("select count(*) from  ".DB_PREFIX."user_address where id=$id")>0){
				$consignee_all['is_default']=0;
				$consignee['is_default']=1;
				$GLOBALS['db']->autoExecute(DB_PREFIX.'user_address',$consignee_all,"UPDATE","user_id=".$user_id);//全部设置为0
				$GLOBALS['db']->autoExecute(DB_PREFIX.'user_address',$consignee,"UPDATE","id=".$id);//设置对应的为默认
				if($GLOBALS['db']->affected_rows()){
					$data['status']=1;
				}else{
					$data['status']=2;//表示更新数据失败，让用户重新提交
					$data['info']="设置失败,请重新设置";
				}
			}else{
				$data['info']="没有该地址";
			}
		}
		ajax_return($data);
	}
	
	public function edit_consignee()
	{

		if(!$GLOBALS['user_info'])
		{
			$data['html'] = $GLOBALS['tmpl']->display("inc/login_form.html","",true);			
			$data['status'] = 0;
		}
		else
		{
			$id = intval($_REQUEST['id']);
			$consignee_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_address where id = ".$id." and user_id = ".intval($GLOBALS['user_info']['id']));
			
			$region_pid = 0;
			$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by id asc");  //二级地址
			foreach($region_lv2 as $k=>$v)
			{
				if($v['name'] == $consignee_info['province'])
				{
					$region_lv2[$k]['selected'] = 1;
					$region_pid = $region_lv2[$k]['id'];
					break;
				}
			}
			$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
			
			
			if($region_pid>0)
			{
				$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$region_pid." order by id asc");  //三级地址
				foreach($region_lv3 as $k=>$v)
				{
					if($v['name'] == $consignee_info['city'])
					{
						$region_lv3[$k]['selected'] = 1;
						break;
					}
				}
				$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
			}
						
			$GLOBALS['tmpl']->assign("consignee_info",$consignee_info);
			$data['html'] = $GLOBALS['tmpl']->fetch("inc/uc/uc_add_consignee.html","",true);			
			$data['status'] = 1;
		}
		ajax_return($data);
	}
	
	public function del_consignee()
	{
		  $id = intval($_REQUEST['id']);
		  $GLOBALS['db']->query("delete from ".DB_PREFIX."user_address where id = ".$id." and user_id = ".intval($GLOBALS['user_info']['id']));
		  
		  showSuccess("",1,"");
	}
}
?>