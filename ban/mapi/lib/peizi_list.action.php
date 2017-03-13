<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class peizi_list
{
	public function index(){
		
		$root = get_baseroot();

		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 60;  //首页缓存10分钟
		$cache_id  = md5(MODULE_NAME."peizi_list".intval($GLOBALS['request']['p']));
		if (!$GLOBALS['tmpl']->is_cached('peizi/peizi_list.html', $cache_id) || true)
		{		
			//订单状态 status:0:正在申请;1:支付成功;2:审核通过;3:审核失败;4:筹款成功;5:筹款失败;6:开户成功;7:开户失败;8:平仓结束;9:已撤消
			require_once  APP_ROOT_PATH.'app/Lib/page.php';
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			
			$sql_str = "select pc.name as conf_type_name,a.*,u.user_name  from ".DB_PREFIX."peizi_order a
						LEFT JOIN ".DB_PREFIX."user u on u.id = a.user_id
						LEFT JOIN ".DB_PREFIX."peizi_conf pc on pc.id = a.peizi_conf_id
						where  a.status in (2,4,6,8) ";
			
			//输出投标列表
			$page = intval($GLOBALS['request']['p']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");
			
			
			$count_sql = "select count(*) from (".$sql_str.") a ";		
			$count = $GLOBALS['db']->getOne($count_sql);
			
			$sql_str .= ' order by a.sort,a.invest_begin_time desc limit '.$limit;
			
			//echo $sql_str;
			$list = $GLOBALS['db']->getAll($sql_str);
			
			$volist = array();
			foreach ($list as $k => $v) {
				//$list[$k] = get_peizi_order_fromat($v);

				$data = array();
				$data['id'] = $v['id'];
				
				$data['peizi_name'] = $v['peizi_name'];
				
				if ($v['type'] == 2){
					//借款时间
					$data['borrow_time'] = $v['time_limit_num'].'个月';					
					//借款年利率
					$data['year_rate_format'] = ($v['rate'] * 12 * 100).'%';
				}else{
					$data['borrow_time'] = '01-'.$v['time_limit_num'].'起';
					$data['year_rate_format'] = ($v['rate'] * 365 * 100).'%';
				}
				
				//配资杠杆
				$data['lever'] = $v['lever'];
				$data['type'] = $v['type'];
				
				$data['type_fromat'] = get_peizi_type($v['type']);
				
				//投标开始时间
				$data['invest_begin_time'] = $v['invest_begin_time'];
				
				$data['before_time'] = getBeforeTimelag(to_timespan($v['invest_begin_time']));
				
				//状态
				$data['status'] = $v['status'];
				$data['status_fromat'] = get_peizi_status($v['status']);
				
				$data['user'] = get_user("*",$v['user_id']);
				$volist[] = $data;
			}
			
			//print_r($volist);exit;
			//$GLOBALS['tmpl']->assign("list",$volist);
			$root['list'] = $volist;
			
			$page = new Page($count,app_conf("DEAL_PAGE_SIZE"));   //初始化分页对象
			$p  =  $page->show();			
			$GLOBALS['tmpl']->assign('pages',$p);
			
			$root['pages'] = $p;
		}
			
		$root['program_title'] = "配资列表";
		output($root);		
	}
}
?>
