<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class project_send
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;
		
		/*old*/
		/*
		$today = to_date(TIME_UTC,"Y-m-d");	
		
		$root['today'] = $today;
		
		require_once APP_ROOT_PATH.'app/libs/licai.php';
		//require_once APP_ROOT_PATH.'app/Lib/page.php';
		$filter_parms =array();
		
		$filter_parms['type'] = $type = isset($GLOBALS['request']['type']) ? intval($GLOBALS['request']['type']) : 0;
		//起购金额
		$filter_parms['money'] = $money = isset($GLOBALS['request']['money']) ? intval($GLOBALS['request']['money']) : 0;
		//年化收益
		$filter_parms['rate'] = $rate = isset($GLOBALS['request']['rate']) ? intval($GLOBALS['request']['rate']) : 0;
		
		$filter_parms['sortby'] = $sortby = isset($GLOBALS['request']['sortby']) ? strim($GLOBALS['request']['sortby']) : "";
		$filter_parms['descby'] = $descby = isset($GLOBALS['request']['descby']) ?strtoupper(strim($GLOBALS['request']['descby'])) : "DESC";
		
		$page = intval($GLOBALS['request']['page']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
		$condition = " status = 1 ";
		if($type!=""){
			$condition .= " AND `type` = $type  ";
		}
		
		if($money != 0){
			switch($money){
				case 1:
					$condition.=" AND min_money <= 1000 ";
					break;
				case 2:
					$condition.=" AND min_money >= 1000 AND min_money <=10000  ";
					break;
				case 3:
					$condition.=" AND min_money >= 10000 AND min_money <=30000  ";
					break;
				case 4:
					$condition.=" AND min_money >= 30000 AND min_money <=50000  ";
					break;
				case 5:
					$condition.=" AND min_money >= 50000 AND min_money <=100000  ";
					break;
				case 6:
					$condition.=" AND min_money >= 100000 AND min_money <=150000  ";
					break;
				case 7:
					$condition.=" AND min_money >= 150000 AND min_money <=200000  ";
					break;
				case 8:
					$condition.=" AND min_money >= 200000 ";
					break;
			}
		}
		
		if($rate != 0){
			switch($rate){
				case 1:
					$condition.=" AND average_income_rate <= 4.5 ";
					break;
				case 2:
					$condition.=" AND average_income_rate between 4.5 AND  5.6  ";
					break;
				case 3:
					$condition.=" AND average_income_rate between 5.6 AND 6  ";
					break;
				case 4:
					$condition.=" AND average_income_rate between 6 AND  7  ";
					break;
				case 5:
					$condition.=" AND average_income_rate between 7 AND  8  ";
					break;
				case 6:
					$condition.=" AND average_income_rate between 8 AND 9  ";
					break;
				case 7:
					$condition.=" AND average_income_rate >= 9  ";
					break;
			}
		}
		
		$orderBy = "`sort` DESC,id DESC";
		if($sortby!=""){
			$orderBy = $sortby." ".$descby.", `sort` DESC,id DESC ";
		}


		$result = get_licai_list($condition,$orderBy,$limit);

		$root['list'] =  $result['list'];
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($result['rs_count']/app_conf("PAGE_SIZE")),"page_size"=>app_conf("PAGE_SIZE"));
		
		//为客户创造收益
		//$user_income = doubleval($GLOBALS['db']->getOne("select sum(earn_money) from ".DB_PREFIX."user_log WHERE `type`=9 "));
		$user_income = doubleval($GLOBALS['db']->getOne("select sum(earn_money) from ".DB_PREFIX."licai_redempte"));
		*/
		/*end old*/
		
		/*new*/
		$root = array();
		$root['response_code'] = 1;

		$root['kf_phone'] = $GLOBALS['m_config']['kf_phone'];//客服电话
		$root['kf_email'] = $GLOBALS['m_config']['kf_email'];//客服邮箱
		
 		//关于我们(填文章ID)
		$root['about_info'] = intval($GLOBALS['m_config']['about_info']);
		$root['version'] = VERSION; //接口版本号int
		$root['page_size'] = PAGE_SIZE;//默认分页大小
		$root['program_title'] = $GLOBALS['m_config']['program_title'];
		$root['site_domain'] = str_replace("/mapi", "", SITE_DOMAIN.APP_ROOT);//站点域名;
		$root['site_domain'] = str_replace("http://", "", $root['site_domain']);//站点域名;
		$root['site_domain'] = str_replace("https://", "", $root['site_domain']);//站点域名;
		
		/*虚拟的累计项目总个数，支持总人数，项目支持总金额*/ 
	 	$virtual_effect = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal where is_effect = 1 and is_delete=0");
	 	$virtual_person =  $GLOBALS['db']->getOne("select sum((support_count+virtual_person)) from ".DB_PREFIX."deal_item");
	 	$virtual_money =  $GLOBALS['db']->getOne("select sum((support_count+virtual_person)*price) from ".DB_PREFIX."deal_item");

	 	$root['virtual_effect'] = $virtual_effect;//项目总个数
		$root['virtual_person'] = $virtual_person;//累计支持人
		$root['virtual_money'] =number_format($virtual_money,2);//筹资总金额
	
	    /*虚拟的累计项目总个数，支持总人数，项目支持总金额 结束*/
	    /*首页广告*/
	    $adv_num=intval($GLOBALS['m_config']['adv_num'])?$GLOBALS['m_config']['adv_num']:5;
		$index_list = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_adv where status = 1  order by sort asc limit 0,$adv_num");
		
		$adv_list = array();
		foreach($index_list as $k=>$v)
		{
			if($v['page'] == 'top'){
				if ($v['img'] != '')
						$v['img'] = get_abs_img_root_wap(get_spec_image($v['img'],640,250,1));	
				if($v['type']==1){
					$v['url']=url_wap("article#index",array("id"=>$v['data']));
				}elseif($v['type']==2){
					$v['url']=$v['data'];
				}
				$adv_list[] = $v;	
			}
		}
 		$GLOBALS['tmpl']->assign('adv_list',$adv_list);
		
		/*项目显示以及权限控制*/
		//===============首页项目列表START===================
		$page_size =  $GLOBALS['m_config']['page_size'];
		$page = intval($_REQUEST['p']);

		$limit="";
		$index_pro_num=$GLOBALS['m_config']['index_pro_num'];
		if($index_pro_num>0){
			$limit="  0,$index_pro_num";
		}
		
		$GLOBALS['tmpl']->assign("current_page",$page);
 		//权限控制
		$new_condition='';
		$hot_conditon='';
		if(app_conf("INVEST_STATUS")==1){
			$new_condition='type=0';
			$hot_conditon='type=0';
		}elseif(app_conf("INVEST_STATUS")==2){
			$new_condition='type=1';
			$hot_conditon='type=1';
		}else{
			$new_condition='type=0';
			$hot_conditon='type=1';
		}
		$hot_conditon.=' and is_hot=1 ';
		//最新的项目
		$deal_new_result = get_project_list('0,4',$new_condition,'sort asc,id desc');
		$GLOBALS['tmpl']->assign("deal_new_list",$deal_new_result['list']);
		//热门的项目
		$deal_hot_result = get_project_list('0,4',$hot_conditon,'support_count desc');
		$GLOBALS['tmpl']->assign("deal_hot_list",$deal_hot_result['list']);


 		
		$condition = " d.is_recommend=1 ";
 		$now_time = get_gmtime();
		$deal_result=get_project_list($limit,$condition);
 		$deal_list = $deal_result['list'];
		$deal_count =  $deal_result['rs_count'];
		
		$wx=array();
		$wx['img_url']=get_domain().$m_config['logo']?$m_config['logo']:app_conf("SITE_LOGO");
		$wx['title']=app_conf("SEO_TITLE");
		$wx['desc']=app_conf("SEO_DESCRIPTION");
		$GLOBALS['tmpl']->assign('wx',$wx);
		
		//获取当前项目列表下的所有子项目
 		$GLOBALS['tmpl']->assign("deal_count",$deal_count);
		$GLOBALS['tmpl']->assign("deal_list",$deal_list);
		$invest_status=app_conf("INVEST_STATUS");
		$GLOBALS['tmpl']->assign("invest_status",$invest_status);
		$cate_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_cate where pid =0 order by sort asc");
	
		$GLOBALS['tmpl']->assign("cates_list",$cate_list);
  		$GLOBALS['tmpl']->display("index.html");
		/*end new*/
		 
		$root['user_income'] = $user_income;
		$root['program_title'] = "理财列表";
		
		output($root);		
	}
}
?>
