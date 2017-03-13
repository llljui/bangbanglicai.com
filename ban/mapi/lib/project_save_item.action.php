<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_save_item
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		if(!$user)
		{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		
		$id = intval($GLOBALS["request"]['id']);
		$deal_id=intval($GLOBALS["request"]['deal_id']);
		$item=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."project where id=$deal_id and user_id=".$user_id);
		if(!$item){
			$root["status"] = 0;
			$root["show_err"] = "项目不存在";
			output($root);
		}
		if($id>0){
			$count=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_item where id=$id and deal_id=$deal_id ");
			if(!$count){
				$root["status"] = 0;
				$root["show_err"] = "该项目不存在这个子项目";
				output($root);
			}
		}
		$root["id"] = $deal_id;
		
		$data['price'] = floatval($GLOBALS["request"]['price']);
		if($data['price']<=0)
		{
			$root["status"] = 0;
			$root["show_err"] = "请输入正确的价格";
			output($root);
		}
		
		$data['description'] = strim($GLOBALS["request"]['description']);
		$data['is_delivery'] = intval($GLOBALS["request"]['is_delivery']);
		$data['delivery_fee'] = floatval($GLOBALS["request"]['delivery_fee']);
		$data['is_limit_user'] = intval($GLOBALS["request"]['is_limit_user']);
		$data['limit_user'] = intval($GLOBALS["request"]['limit_user']);
		$data['repaid_day'] = intval($GLOBALS["request"]['repaid_day']);
		$data['deal_id'] = intval($GLOBALS["request"]['deal_id']);
		$data['is_share'] = intval($GLOBALS["request"]['is_share']);
		$data['share_fee'] = floatval($GLOBALS["request"]['share_fee']);
		
		if($data['is_limit_user']>0 && $data['limit_user'] == 0)
		{
			$root["status"] = 0;
			$root["show_err"] = "请输入正确的限购人数";
			output($root);
		}
		
		if($data['is_share']>0 && $data['share_fee'] == 0)
		{
			$root["status"] = 0;
			$root["show_err"] = "请输入正确的分红金额";
			output($root);
		}
		
		if($data['is_share'] == 0)
			$data['share_fee'] = 0;
		
		if(count($GLOBALS["request"]['file'])>4)
		{
			$root["status"] = 0;
			$root["show_err"] = "图片不能超过四张";		
			output($root);
		}
		
		if($id==0)
		{
			$GLOBALS['db']->autoExecute(DB_PREFIX."project_item",$data,"INSERT","","SILENT");
			$result_id = intval($GLOBALS['db']->insert_id());
			if($result_id>0)
			{
				if(count($GLOBALS["request"]['file'])>=0)
				{
					foreach($GLOBALS["request"]['file'] as $k=>$v)
					{
						$image_data['deal_id'] = $data['deal_id'];
						$image_data['deal_item_id'] = $result_id;
						$image_data['image'] = replace_public($v);
						$GLOBALS['db']->autoExecute(DB_PREFIX."project_item_image",$image_data);
					}					
				}
				$GLOBALS['db']->query("update ".DB_PREFIX."project set deal_extra_cache = '' where id = ".$data['deal_id']);
				
				$root['show_err'] = "保存成功";
				$root['status'] = 1;
				output($root);
			}
			else
			{
				$root["status"] = 0;
				$root["show_err"] = "保存失败";
				output($root);
			}
		}
		else
		{
			$GLOBALS['db']->autoExecute(DB_PREFIX."project_item",$data,"UPDATE","id=".$id,"SILENT");
			
			//旧图
			$old_images = '0';
			if(count($GLOBALS['request']['image_names'])>0)
			{
				foreach($GLOBALS['request']['image_names'] as $i_k => $i_v)
				{
					$old_images .= ",".intval($i_v);
				}
			}	
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_item_image where deal_item_id = '".$id."' and deal_id = '".$deal_id."' and  id not in (".$old_images.")");	

			if(count($GLOBALS["request"]['file'])>=0)
			{				
				foreach($GLOBALS["request"]['file'] as $k=>$v)
				{
					$image_data['deal_id'] = $data['deal_id'];
					$image_data['deal_item_id'] = $id;
					$image_data['image'] = strim($v);
					$GLOBALS['db']->autoExecute(DB_PREFIX."project_item_image",$image_data);
				}					
			}
			$GLOBALS['db']->query("update ".DB_PREFIX."project set deal_extra_cache = '' where id = ".$data['deal_id']);
 			$root['show_err'] = "保存成功";
			$root['status'] = 1;
			output($root);
		}
		
		
		output($root);
	
	}
}
?>
