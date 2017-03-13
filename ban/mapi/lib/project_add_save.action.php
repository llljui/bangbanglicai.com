<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_add_save
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		$ajax = intval($GLOBALS["request"]['ajax']);	
		if(!check_ipop_limit(get_client_ip(),"project_save",5))
		{
			$root["status"] = 0;
			$root["show_err"] = "提交太频繁";
			output($root);
		}
			
		if(!$user)
		{
			$root['response_code'] = 0;
			$root['info'] ="未登录";
			$root['user_login_status'] = 0;
			output($root);
		}
		$id =  intval($GLOBALS["request"]['id']);
		$item=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."project where id=$id and user_id=".$GLOBALS['user_info']['id']);
		if(!$item&&$id>0){
			$root["status"] = 0;
			$root["show_err"] = "项目不存在";
			output($root);
		}
		$is_edit = $item['is_edit'];
		$is_effect =  $item['is_effect'];
		if($id>0&&$is_effect==1)
		{
			$root["status"] = 0;
			$root["show_err"] = "项目已提交，不能更改";
			output($root);
		}
		
		$data['name'] = strim($GLOBALS["request"]['name']);
		if($data['name']=="")
		{
			$root["status"] = 0;
			$root["show_err"] = "请填写项目名称";
			output($root);
		}
		if(msubstr($data['name'],0,25)!=$data['name'])
		{			
			$root["status"] = 0;
			$root["show_err"] = "项目名称不超过25个字";
			output($root);
		}
		$data['cate_id'] = intval($GLOBALS["request"]['cate_id']);
		if($data['cate_id']==0)
		{
			$root["status"] = 0;
			$root["show_err"] = "请选择项目分类";
			output($root);
		}
		$data['province'] = strim($GLOBALS["request"]['province']);
		if($data['province']=='')
		{
			$root["status"] = 0;
			$root["show_err"] = "请选择省份";
			output($root);
		}
		$data['city'] = strim($GLOBALS["request"]['city']);
		if($data['city']=='')
		{
			$root["status"] = 0;
			$root["show_err"] = "请选择城市";
			output($root);
		}
		$data['brief'] = strim($GLOBALS["request"]['brief']);
		$data['image'] = replace_public(addslashes(trim($GLOBALS["request"]['image_file'])));
		if($data['image']=="" && $id == 0)
		{			
			$root["status"] = 0;
			$root["show_err"] = "上传封面图片";
			output($root);
		}
		elseif($data['image']=="" && $id > 0)
		{
			unset($data['image']);
		}
		
		require_once APP_ROOT_PATH."system/libs/words.php";	
		$data['tags'] = implode(" ",words::segment($data['name']));


		$data['description'] = replace_public(addslashes(trim(valid_tag($GLOBALS["request"]['description']))));	
//		
		$data['vedio'] = strim($GLOBALS["request"]['vedio']);
		
		if($data['vedio']!="")
		{
			require_once APP_ROOT_PATH."system/utils/vedio.php";
			$vedio = fetch_vedio_url($data['vedio']);		
			if($vedio!="")
			{
				$data['source_vedio'] =  $vedio;
			}
			else
			{
				$root["status"] = 0;
				$root["show_err"] = "非法的视频地址";
				output($root);
			}
		}
		
		$data['limit_price'] = floatval($GLOBALS["request"]['limit_price']);
		if($data['limit_price']<=0)
		{
			$root["status"] = 0;
			$root["show_err"] = "请输入正确的目标金额";
			output($root);
		}
		$data['deal_days'] = floatval($GLOBALS["request"]['deal_days']);
		if($data['deal_days']<=0)
		{
			$root["status"] = 0;
			$root["show_err"] = "请输入正确的上线天数";
			output($root);
		}
		$data['is_edit'] = 1;
		
		
		if($id>0)
		{
			$savenext = intval($GLOBALS["request"]['savenext']);
			$GLOBALS['db']->autoExecute(DB_PREFIX."project",$data,"UPDATE","id=".$id,"SILENT");
			
			//追加faq
			$GLOBALS['db']->query("delete from ".DB_PREFIX."project_faq where deal_id = ".$id);
			$sort = 1;
			foreach($GLOBALS["request"]['question'] as $kk=>$question_item)
			{
				if(strim($GLOBALS["request"]['question'][$kk])!=""&&strim($GLOBALS["request"]['answer'][$kk])!=""&&strim($GLOBALS["request"]['question'][$kk])!="请输入问题"&&strim($GLOBALS["request"]['answer'][$kk])!="请输入答案")
				{
					$faq_item['deal_id'] = $id;
					$faq_item['question'] = strim($GLOBALS["request"]['question'][$kk]);
					$faq_item['answer'] = strim($GLOBALS["request"]['answer'][$kk]);
					$faq_item['sort'] = $sort;
					$GLOBALS['db']->autoExecute(DB_PREFIX."project_faq",$faq_item);
					$sort++;
				}
			}
			$GLOBALS['db']->query("update ".DB_PREFIX."project set deal_extra_cache = '' where id = ".$id);

			$root["id"] = $id;
			$root["status"] = 1;
			$root["show_err"] = "操作成功";
			output($root);

		}
		else
		{
			$data['user_id'] = intval($GLOBALS['user_info']['id']);
			$data['user_name'] = $GLOBALS['user_info']['user_name'];
			$data['create_time'] = get_gmtime();
			$savenext = intval($GLOBALS["request"]['savenext']);
			$GLOBALS['db']->autoExecute(DB_PREFIX."project",$data,"INSERT","","SILENT");
			$data_id = intval($GLOBALS['db']->insert_id());
			if($data_id==0)
			{
				$root["status"] = 0;
				$root["show_err"] = "保存失败，请联系管理员";
				output($root);
			}
			else
			{
				$root["id"] = $data_id;
				es_session::delete("deal_image");
				
				//追加faq
				$sort = 1;
				foreach($GLOBALS["request"]['question'] as $kk=>$question_item)
				{
					if(strim($GLOBALS["request"]['question'][$kk])!=""&&strim($GLOBALS["request"]['answer'][$kk])!=""&&strim($GLOBALS["request"]['question'][$kk])!="请输入问题"&&strim($GLOBALS["request"]['answer'][$kk])!="请输入答案")
					{
						$faq_item['deal_id'] = $data_id;
						$faq_item['question'] = strim($GLOBALS["request"]['question'][$kk]);
						$faq_item['answer'] = strim($GLOBALS["request"]['answer'][$kk]);
						$faq_item['sort'] = $sort;
						$GLOBALS['db']->autoExecute(DB_PREFIX."project_faq",$faq_item);
						$sort++;
					}
				}
				if($savenext==0)
				{
					$root["status"] = 1;
					$root["show_err"] = "操作成功";
					output($root);
				}
				else
				{
					$root["status"] = 1;
					$root["show_err"] = "操作成功";
					output($root);	
				}
			}
		}
		output($root);
	}
}
?>
