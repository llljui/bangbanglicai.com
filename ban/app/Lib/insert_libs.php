<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

/*以下为动态载入的函数库*/

//动态加载今日团购
function insert_load_today_deal()
{
	require_once APP_ROOT_PATH.'app/Lib/deal.php';
	//输出今日团购
	$today_deal = get_deal_show_shop();
	$GLOBALS['tmpl']->assign("today_deal",$today_deal);
	return $GLOBALS['tmpl']->fetch("inc/insert/load_today_deal.html");
}
//动态加载用户提示
function insert_load_user_tip()
{
	if(intval($GLOBALS['user_info']['id']) > 0){
		//输出未读的消息数
		$msg_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."msg_box where to_user_id = ".intval($GLOBALS['user_info']['id'])." and is_read = 0 and is_delete = 0 and type = 0");
		$GLOBALS['tmpl']->assign("msg_count",intval($msg_count));
		$expire = array();
		if($GLOBALS['user_info']){
			$credit_file = get_user_credit_file($GLOBALS['user_info']['id'],$GLOBALS['user_info']);
	    	$GLOBALS['tmpl']->assign("credit_file",$credit_file);
		}
	}
	return $GLOBALS['tmpl']->fetch("inc/insert/load_user_tip.html");
}
//动态加载白条用户提示
function insert_load_debit_user_tip()
{
	if(intval($GLOBALS['user_info']['id']) > 0){
		//输出未读的消息数
		$msg_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."msg_box where to_user_id = ".intval($GLOBALS['user_info']['id'])." and is_read = 0 and is_delete = 0 and type = 0");
		$GLOBALS['tmpl']->assign("msg_count",intval($msg_count));
		$expire = array();
		if($GLOBALS['user_info']){
			$credit_file = get_user_credit_file($GLOBALS['user_info']['id'],$GLOBALS['user_info']);
	    	$GLOBALS['tmpl']->assign("credit_file",$credit_file);
		}
	}
	return $GLOBALS['tmpl']->fetch("debit/debit_load_user_tip.html");
}

//动态加载用户提示
function insert_load_user_tip_index()
{

	//输出未读的消息数
	$msg_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."msg_box where to_user_id = ".intval($GLOBALS['user_info']['id'])." and is_read = 0 and is_delete = 0 and type = 0");
	$GLOBALS['tmpl']->assign("msg_count",intval($msg_count));
	$expire = array();
	if($GLOBALS['user_info']){
		$credit_file = get_user_credit_file($GLOBALS['user_info']['id'],$GLOBALS['user_info']);
	    $GLOBALS['tmpl']->assign("credit_file",$credit_file);
	}
	return $GLOBALS['tmpl']->fetch("inc/insert/load_user_tip_index.html");
}

/**
 * 动态输出成功案例， 不受缓存限制
 */
function insert_success_deal_list(){
	//输出成功案例
	$GLOBALS['tmpl']->caching = true;
	$GLOBALS['tmpl']->cache_lifetime = 120;  //首页缓存10分钟
	$cache_id  = md5("success_deal_list");	
	if (!$GLOBALS['tmpl']->is_cached("inc/insert/success_deal_list.html", $cache_id))
	{	
		$suc_deal_list =  get_deal_list(11,0,"deal_status in(4,5) "," success_time DESC,sort DESC,id DESC");
		$GLOBALS['tmpl']->assign("succuess_deal_list",$suc_deal_list['list']);
	}
	return $GLOBALS['tmpl']->fetch("inc/insert/success_deal_list.html",$cache_id);
}



//载入文章点击数
function insert_load_article_click($para)
{
	if(check_ipop_limit(CLIENT_IP,"article",60,intval($para['article_id'])))
	{
					//每一分钟访问更新一次点击数
		$GLOBALS['db']->query("update ".DB_PREFIX."article set click_count = click_count + 1 where id =".intval($para['article_id']));
	}
	return intval($GLOBALS['db']->getOne("select click_count from ".DB_PREFIX."article where id = ".intval($para['article_id'])));
}


function insert_load_msg_list()
{
	$rel_table = strim($_REQUEST['act']);
			$message_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."message_type where type_name='".$rel_table."' ");
			if(!$message_type||$message_type['is_fix']==0)
			{
				$message_type_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."message_type where is_fix = 0 order by sort desc");
				if(!$message_type_list)
				{
					showErr($GLOBALS['lang']['INVALID_MESSAGE_TYPE']);
				}
				else
				{
					if(!$message_type)
					$message_type = $message_type_list[0];
					foreach($message_type_list as $k=>$v)
					{
						if($v['type_name'] == $message_type['type_name'])
						{
							$message_type_list[$k]['current'] = 1;
						}
						else
						{
							$message_type_list[$k]['current'] = 0;
						}
					}
					$GLOBALS['tmpl']->assign("message_type_list",$message_type_list);
				}
			}
			$rel_table = $message_type['type_name'];
			$condition = '';	
			$id = intval($_REQUEST['id']);
			if($rel_table == 'deal')
			{
				$deal = get_deal($id);
				if($deal['buy_type']!=1)
				$GLOBALS['tmpl']->assign("deal",$deal);
				$id = $deal['id'];
			}
			//require './app/Lib/side.php'; 
			if($id>0)
			$condition = "rel_table = '".$rel_table."' and rel_id = ".$id;
			else
			$condition = "rel_table = '".$rel_table."'";
		
			if(app_conf("USER_MESSAGE_AUTO_EFFECT")==0)
			{
				$condition.= " and user_id = ".intval($GLOBALS['user_info']['id']);
			}
			else 
			{
				if($message_type['is_effect']==0)
				{
					$condition.= " and user_id = ".intval($GLOBALS['user_info']['id']);
				}
			}
			
			$condition.=" and is_buy = ".intval($_REQUEST['is_buy']);
			//message_form 变量输出
			
			//开始输出当前的site_nav					
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$message_type['show_name'],'url'=>url("shop","msg#".$message_type['type_name']));
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			//输出当前的site_nav
					
					
			$GLOBALS['tmpl']->assign("post_title",$message_type['show_name']);
			$GLOBALS['tmpl']->assign("page_title",$message_type['show_name']);
			$GLOBALS['tmpl']->assign('rel_id',$id);
			$GLOBALS['tmpl']->assign('rel_table',$rel_table);
			$GLOBALS['tmpl']->assign('is_buy',intval($_REQUEST['is_buy']));
			
			if(intval($_REQUEST['is_buy'])==1)
			{
				$GLOBALS['tmpl']->assign("post_title",$GLOBALS['lang']['AFTER_BUY']);
				$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['AFTER_BUY']);		
			}
			
			if(!$GLOBALS['user_info'])
			{
				$GLOBALS['tmpl']->assign("message_login_tip",sprintf($GLOBALS['lang']['MESSAGE_LOGIN_TIP'],url("shop","user#login"),url("shop","user#register")));
			}
			
			//分页
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
			
			$message = get_message_list_shop($limit,$condition);
			
			$page = new Page($message['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign("user_auth",get_user_auth());
			$GLOBALS['tmpl']->assign("message_list",$message['list']);
			return $GLOBALS['tmpl']->fetch("inc/insert/load_msg_list.html");
}

function insert_load_login_form()
{
	return $GLOBALS['tmpl']->fetch("inc/page_login_form.html");
}
function insert_load_debit_login_form()
{
	return $GLOBALS['tmpl']->fetch("debit/debit_login_form.html");
}
function insert_load_unit_login_form()
{
	return $GLOBALS['tmpl']->fetch("inc/page_unit_login_form.html");
}

function insert_load_authorized_login_form()
{
	return $GLOBALS['tmpl']->fetch("inc/page_authorized_login_form.html");
}

function insert_agency_info()
{
	$agency_info  = es_session::get("manageagency_info");
	$GLOBALS['tmpl']->assign('agency_info',$agency_info);
	return $GLOBALS['tmpl']->fetch("manageagency/agency_login_info.html");
}
function insert_authorized_info()
{
	$authorized_info  = es_session::get("authorized_info");
	$GLOBALS['tmpl']->assign('authorized_info',$authorized_info);
	return $GLOBALS['tmpl']->fetch("authorized/authorized_login_info.html");
}
//动态获取可同步登录的API大图
function insert_get_app_login($type)
{
		//0:小登录图标 1:大登录图标 2:绑定图标
		if(!isset($type['v'])){
			$type['v'] = 1;
		}
		$apis = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."api_login");
		if(intval($type["r"])==1)
			$str = "<h3>或使用这些帐号登录</h3>";
		else
			$str = "<h3>合作网站账号登录</h3>";
		foreach($apis as $k=>$api)
		{					
			$str .= $url."<span id='api_".$api['class_name']."_".$type['v']."'><script type='text/javascript'>load_api_url('".$api['class_name']."',".$type['v'].");</script></span>";
			
		}
		return $str;

}


//动态加载不同模块的点评
function insert_load_comment($param)
{
	
	require_once APP_ROOT_PATH."app/Lib/message.php";
	require_once APP_ROOT_PATH.'app/Lib/page.php';
	$rel_id = intval($_REQUEST['id']); //关联数据的ID
	$rel_table = $param['rel_table'];
	$is_effect = $param['is_effect'];
	$is_image = $param['is_image'];
	$width = $param['width'];
	$height = $param['height'];
	
	$GLOBALS['tmpl']->assign("height",$height);
	$GLOBALS['tmpl']->assign("width",$width);
	$GLOBALS['tmpl']->assign("rel_id",$rel_id);
	$GLOBALS['tmpl']->assign("rel_table",$rel_table);
	$GLOBALS['tmpl']->assign("is_effect",$is_effect);
	$GLOBALS['tmpl']->assign("is_image",$is_image);
	
	//分页
	$page = intval($_REQUEST['p']);
	if($page==0)
	$page = 1;
	$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");			
	$result = get_message_list_shop($limit," rel_table='".$rel_table."' and rel_id = ".$rel_id." and is_effect = 1");		

	$GLOBALS['tmpl']->assign("message_list",$result['list']);
	$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
	$p  =  $page->show();
	$GLOBALS['tmpl']->assign('pages',$p);
	$GLOBALS['tmpl']->assign("user_auth",get_user_auth());
			
	if(!$GLOBALS['user_info'])
	{
			$GLOBALS['tmpl']->assign("message_login_tip",sprintf($GLOBALS['lang']['MESSAGE_LOGIN_TIP'],url("shop","user#login"),url("shop","user#register")));
	}
	
	return $GLOBALS['tmpl']->fetch("inc/inc_comment_list.html");
}

function insert_load_keyword()
{
	$keyword = addslashes(htmlspecialchars(trim($_REQUEST['keyword'])));
	if($keyword=='')
	$keyword = $GLOBALS['lang']['HEAD_KEYWORD_EMPTY_TIP'];
	return $keyword;
}

function insert_get_syn_class()
{
	$apis = $GLOBALS['db']->getAll("select class_name from ".DB_PREFIX."api_login where is_weibo = 1");
	$str = "";
	foreach($apis as $k=>$v)
	{
		if($GLOBALS['user_info']['is_syn_'.strtolower($v['class_name'])]==1)
		{
			$str.="<input type='hidden' class='syn_class' value='".$v['class_name']."' />";
		}
	}
	return $str;
}

function insert_artile_list($param){
	if($param['cate']=="" || $param['tpl']=="")
		return "";
	if($param['limit']=="")
		$param['limit']= 5;
		
	$cate_id =  $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."article_cate where title='".$param['cate']."'",false);
	
	if($cate_id > 0){
		$article_list  = get_article_list($param['limit'],$cate_id);
		if($article_list){
			$GLOBALS['tmpl']->assign($param['datakey']."_id",$cate_id);
			$GLOBALS['tmpl']->assign($param['datakey']."_list",$article_list['list']);	
		}
	}
	return $GLOBALS['tmpl']->fetch($param['tpl']);
}


function insert_get_login_key(){
	return LOGIN_DES_KEY();
}

function insert_get_hash_key(){
	return HASH_KEY();
}

function insert_is_mobile(){
	if(isMobile()){
		return 1;
	}
	else{
		return 0;
	}
}
?>
