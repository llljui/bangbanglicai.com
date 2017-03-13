<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require APP_ROOT_PATH.'app/Lib/project_func.php';
class project_deal
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*new start*/
		$id = intval($GLOBALS["request"]['id']);
		
		$deal_info = $GLOBALS['db']->getRow("select d.*,dl.name as deal_level,dc.name as deal_type from ".DB_PREFIX."project as d left join ".DB_PREFIX."user_level as dl on dl.id=d.user_level left join ".DB_PREFIX."project_cate as dc on dc.id=d.cate_id where d.id = ".$id." and d.is_delete = 0 and (d.is_effect = 1 or (d.is_effect = 0 and d.user_id = ".$user_id."))");
		
 		$access = get_level_access($user,$deal_info);

 		$root["access"]=$access;
 		if(!$deal_info)
		{
			$root['response_code'] = 0;
			$root['show_err'] ="项目不存在，请返回重新操作";
		}		
		
		if($deal_info['is_effect']==1)
		{
			log_deal_visit($deal_info['id']);
		}		
	 
		$deal_info['image'] = get_abs_url_root($deal_info["image"]);
		
		$wx=array();
		$wx['img_url']=$deal_info['image'];
		$wx['title']=$deal_info['name'];
		$wx['desc']=$deal_info['brief'];
		$root['wx']=$wx;
		
		$deal_info = cache_project_extra($deal_info,0);
		
		$deal_info = init_project_page_wap($deal_info);
		
		$root["deal_info"]=$deal_info;
		
		$limit="0,3";
		
		$log_list = $GLOBALS['db']->getAll("select p_l.*,u.user_name as u_user_name from ".DB_PREFIX."project_log p_l left join ".DB_PREFIX."user u on p_l.user_id = u.id where p_l.deal_id = ".$deal_info['id']." order by p_l.create_time desc limit ".$limit);
		
		foreach($log_list as $k => $v)
		{
			//output(array("msg"=>get_user_avatar_wap($v["user_id"],"small")));
			$log_list[$k]["avatar"] = get_abs_wap_avatar_url_root(get_user_avatar_wap($v["user_id"],"small"));
		}
		
		$log_num = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_log where deal_id = ".$deal_info['id'] );		
		
		$root["log_list"]=$log_list;
		$root["log_num"]=intval($log_num);
		
		$comment_list = $GLOBALS['db']->getAll("select p_c.*,u.user_name as u_user_name from ".DB_PREFIX."project_comment p_c left join ".DB_PREFIX."user u on p_c.user_id = u.id where p_c.deal_id = ".$id." and p_c.log_id = 0 and p_c.status=1 order by p_c.create_time desc limit ".$limit);
		
		foreach($comment_list as $c_k => $c_v)
		{
			//output(array("msg"=>get_user_avatar_wap($v["user_id"],"small")));
			$comment_list[$c_k]["avatar"] = get_abs_wap_avatar_url_root(get_user_avatar_wap($c_v["user_id"],"small"));
		}
		
		$comment_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."project_comment where deal_id = ".$id." and log_id = 0 and status=1 ");

		$root["info_url"]=wap_url("index","project_deal#info",array("id"=>$id));
		$root["comment_list"]=$comment_list;
		$root["comment_count"]=intval($comment_count);
		$root["deal_index_url"]=wap_url("index","project_deal#index",array("id"=>$id));
		$root["usermessage_url"]=wap_url("index","project_usermessage",array("id"=>$deal_info['user_id']));
		$root["home_url"]=wap_url("index","project_deal#home",array("id"=>$deal_info['user_id']));
		$root["program_title"]= msubstr($deal_info["name"],0,8);
		
		output($root);
		
	}
}
?>
