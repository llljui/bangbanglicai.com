<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
require_once  APP_ROOT_PATH.'app/Lib/project_func.php';

class project_deals
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		$root['user_id'] = $user_id;

		$root['program_title'] = "综合推荐";
        
        $param = array();//参数集合
           
         //数据来源参数
		$r = strim($GLOBALS['request']['r']);   //推荐类型
        $param['r'] = $r?$r:'';
		$root['p_r'] = $r;
                
		$id = intval($GLOBALS['request']['id']);  //分类id
		$param['id'] = $id;
		$root['p_id'] = $id;
		
		$loc = strim($GLOBALS['request']['loc']);  //地区
		$param['loc'] = $loc;
		$root['p_loc'] = $loc;
        
        $state = intval($GLOBALS['request']['state']);  //状态

        $param['state'] = $state;
		$root['p_state'] = $state;
                
		$tag = strim($GLOBALS['request']['tag']);  //标签
		$param['tag'] = $tag;
		$root['p_tag'] = $tag;
                
		$kw = strim($GLOBALS['request']['k']);    //关键词
		$param['k'] = $kw;
		$root['p_k'] = $kw;
		        
        $type = intval($GLOBALS['request']['type']);   //推荐类型
        $param['type'] = $type;
		$root['p_type'] = $type; 

		if(intval($GLOBALS['request']['redirect'])==1)
		{
			$param = array();
			if($r!="")
			{
				$param = array_merge($param,array("r"=>$r));
			}
			if($id>0)
			{
				$param = array_merge($param,array("id"=>$id));
			}	
            if($loc!="")
			{
				$param = array_merge($param,array("loc"=>$loc));
			}
            if($state!="")
			{
				$param = array_merge($param,array("state"=>$state));
			}           
			if($tag!="")
			{
				$param = array_merge($param,array("tag"=>$tag));
			}
			if($kw!="")
			{
				$param = array_merge($param,array("k"=>$kw));
			}
			if($type!="")
			{
				$param = array_merge($param,array("type"=>$type));
			}
			
			app_redirect(wap_url("index","project_deals",$param));
		}
		
		$cate_list = load_dynamic_cache("PROJECT_CATE_LIST");
		
		if(!$cate_list)
		{
			$cate_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."project_cate order by sort asc");
			set_dynamic_cache("PROJECT_CATE_LIST",$cate_list);
		}
		
		$cate_result = array();
 		foreach($cate_list as $k=>$v){
			if($v['pid'] == 0){
				$temp_param = $param;
				$cate_result[$v['id']]['id'] = $v['id'];
				$cate_result[$v['id']]['name'] = $v['name'];
				$temp_param['id'] = $v['id'];
				$cate_result[$v['id']]['url'] = wap_url("index","project_deals",$temp_param);
			}else{
				if($v['pid']>0){
				$temp_param['id'] = $v['id'];
				$cate_result[$v['pid']]['child'][]=array('id'=>$v['id'],'name'=>$v['name'],'url'=>wap_url("index","project_deals",$temp_param));
				}
			}
			if($v['id']==$id){
				$root['cate_name'] = $v['name'];
			}
		}
		
 		$root['cate_list'] = $cate_result;
		
		$pid = $id;
		//获取父类id
		
		if($cate_list){
			$pid = $this->get_child($cate_list,$pid);
		}
		/*子分类 start*/
		$cate_ids = array();
		$is_child = false;
		$temp_cate_ids = array();
		
		if($cate_list){
			$child_cate_result= array();
			foreach($cate_list as $k=>$v)
			{
				if($v['pid'] == $pid){
					if($v['id'] > 0){
						$temp_param = $param;
						$child_cate_result[$v['id']]['id'] = $v['id'];
						$child_cate_result[$v['id']]['name'] = $v['name'];
						$temp_param['id'] = $v['id'];
						$child_cate_result[$v['id']]['url'] = wap_url("index","project_deals",$temp_param);
						 if($id==$v['id']){
						 	$is_child = true;
						 }
						
					}
				}
				if($v['pid'] == $pid || $pid==0){
					$temp_cate_ids[] = $v['id'];
				}
			}		
		}
		
		//假如选择了子类 那么使用子类ID  否则使用 父类和其子类
		if($is_child){
			$cate_ids[] = $id;
		}
		else{
			$cate_ids[] = $pid;
			$cate_ids = array_merge($cate_ids,$temp_cate_ids);
		}
 		$cate_ids=array_filter($cate_ids);
		$root['child_cate_list'] = $child_cate_result;
		$root['pid'] = $pid;
		
		/*子分类 end*/
       $city_list = load_dynamic_cache("PROJECT_CITY_LIST"); 

       if($type ==0){
       		if(!$city_list)
			{
				$city_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf order by id asc");
				set_dynamic_cache("PROJECT_CITY_LIST",$city_list);
			}
       }
		
		$city_list_array = array();
 		foreach($city_list as $k=>$v){
			if($v['region_level'] <= 2)
			{
				$temp_param = $param;
				$temp_param['loc'] = $v['name'];
				if($v['region_level']==2)
				{
					$city_list[$k]['url'] = $v['url'] = wap_url("index","project_deals",$temp_param);
				}
				else
				{
					$city_list[$k]['url'] = $v['url'] = wap_url("index","project_deals");
				}
				$city_list_array[$v["id"]."_"] = $v;
			}
		}
		
		foreach($city_list as $k => $v ){
			if($v['region_level'] == 2 || $v['region_level'] == 3)
			{
				if($city_list_array[$v['pid']."_"])
				{
					$temp_param = array();
					$temp_param['loc'] = $v['name'];
					$v['url']=wap_url("index","project_deals",$temp_param);
					$city_list_array[$v['pid']."_"]['child'][] = $v ;
				}
			}
		};
		
		//$root["msg"] = print_r($city_list_array,true);
		$root['city_list'] = $city_list_array;
		//=================region_conf==============
		
		$state_list = array(
			1=>array("name"=>"筹资成功"),
			2=>array("name"=>"筹资失败"),
			3=>array("name"=>"筹资中"),
		);
		foreach($state_list as $k=>$v){
			$temp_param = $param;
			$temp_param['state'] = $k;
			$state_list[$k]['url'] = wap_url("index","project_deals",$temp_param);
		}
		if($state==0){
			$root['state_name'] = "所有项目";
		}else{
			$root['state_name'] = $state_list[$state]['name'];
		}
		$root['state_list'] = $state_list;
		
		$page_size = app_conf("DEAL_PAGE_SIZE");
		$step_size = app_conf("DEAL_PAGE_SIZE");
		
		$step = intval($GLOBALS['request']['step']);
		if($step==0)$step = 1;
		$page = intval($GLOBALS['request']['page']);
		if($page==0)$page = 1;		
		$limit = (($page-1)*$page_size+($step-1)*$step_size).",".$step_size	;
		
		$root['current_page'] = $page;

		$condition = " d.is_delete = 0 and d.is_effect = 1 "; 
		if($r!="")
		{if($r=="new")
			{
				$condition.=" and ".TIME_UTC." - d.begin_time < ".(7*24*3600)." and ".TIME_UTC." - d.begin_time > 0 ";  //上线不超过一天
				$root['program_title'] = "最新上线";
			}
			elseif($r=="rec")
			{
				$condition.=" and d.is_recommend = 1 ";
				$root['program_title'] = "推荐项目";
			}
            elseif($r=="yure")
			{
				$condition.="   and ".TIME_UTC." <  d.begin_time ";   
				$root['program_title'] = "正在预热";
			}
			elseif($r=="nend")
			{
				$condition.=" and d.end_time - ".TIME_UTC." < ".(7*24*3600)." and d.end_time - ".TIME_UTC." > 0 ";  //三天就要结束
				$root['program_title'] = "即将结束";
			}
			elseif($r=="classic")
			{
				$condition.=" and d.is_classic = 1 ";
				$root['program_title'] = "经典项目";
				$root["is_classic"]=true;
				$root["is_classic"]=true;
			}
			elseif($r=="limit_price")
			{
				$condition.=" and max(d.limit_price) ";
				$root['program_title'] = "最高目标金额";
			}
		}
		switch($state)
		{
			//筹资成功
			case 1 : 
				$condition.=" and d.is_success=1  and d.end_time < ".TIME_UTC; 
				$root['program_title'] = "筹资成功";
				break;
			//筹资失败
			case 2 : 
				$condition.=" and d.end_time < ".TIME_UTC." and d.end_time!=0  and d.is_success=0  "; 
				$root['program_title'] = "筹资失败";
				break;
			//筹资中
			case 3 : 
				$condition.=" and (d.end_time > ".TIME_UTC." or d.end_time=0 ) and d.begin_time < ".TIME_UTC."   ";  
				$root['program_title'] = "筹资中";
			break;
		}

		if(count($cate_ids)>0)
		{
			$condition.= " and d.cate_id in (".implode(",",$cate_ids).")";          
		}
		if($loc!="")
         {
            $condition.=" and (d.province = '".$loc."' or city = '".$loc."') ";
			$root["program_title"]=$loc;            
		}
		if($tag!="")
		{
			$unicode_tag = str_to_unicode_string($tag);
			$condition.=" and match(d.tags_match) against('".$unicode_tag."'  IN BOOLEAN MODE) ";
			$root["program_title"]=$tag;
		}
		if($type!=="")
        {
        	$type=intval($type);
            $condition.=" and type=$type ";
 		}
		if($kw!="")
		{		
			$kws_div = div_str($kw);
			foreach($kws_div as $k=>$item)
			{
				
				$kws[$k] = str_to_unicode_string($item);
			}
			$ukeyword = implode(" ",$kws);
			$condition.=" and (match(d.name_match) against('".$ukeyword."'  IN BOOLEAN MODE) or match(d.tags_match) against('".$ukeyword."'  IN BOOLEAN MODE)  or d.name like '%".$kw."%') ";

			$root["program_title"]=$kw;
		}
		$result = get_project_list($limit,$condition," d.sort asc ","project",0);
		
		$root["deal_list"]=$result['list'];
		 
		$root["deal_count"]=intval($result['rs_count']);
		
		$root['page'] = array("page"=>$page,"page_total"=>ceil($result['rs_count']/app_conf("DEAL_PAGE_SIZE")),"page_size"=>app_conf("DEAL_PAGE_SIZE"));

		
		output($root);
	
	}
	public function get_child($cate_list,$pid){
 			foreach($cate_list as $k=>$v)
			{
				if($v['id'] ==  $pid){
					if($v['pid'] > 0){
						$pid =$this->get_child($cate_list,$v['pid']) ;
						if($pid==$v['pid']){
							return $pid;
						}
					}
					else{
						return $pid;
					}
				}
			}
	}
}
?>
