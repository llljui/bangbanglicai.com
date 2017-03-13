<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class MyCustomerAction extends CommonAction{
	private function auth(){
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		return $adm_session;
	}
	public function index()
	{	
		$adm_session = $this->auth();
		if($adm_session['pid'] > 0 || $adm_session['is_department'] == 1){
			$this->error (l("NO_AUTH"));
		}
		
		$this->assign("main_title",L("DEAL_THREE"));
		
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		
		switch($sorder){
			case "adm_name":
					$order ="u.admin_id";
				break;
			
			default : 
				$order =$sorder;
				break;
		}
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
		
		//开始加载搜索条件
		$condition =" 1=1 ";
		$condition .= " and u.is_effect = 1 and u.is_delete = 0 and (user_type = 0 || user_type = 1) ";
		
		$admin_names  = !isset($_REQUEST['adm_names'])? -2 : intval($_REQUEST['adm_names']);
		$_REQUEST['adm_names'] = $admin_names;
		$name = strim($_REQUEST['name']) =="" ? "" : strim($_REQUEST['name']);
		
		// -2：未分配 ，  -1：已分配
		if($admin_names==-2){
			$condition .=" AND u.admin_id =  0 "; 
		}
		elseif($admin_names == -1){
			$condition .=" AND u.admin_id >  0 ";
		}
		elseif($admin_names > 0){
			$condition .=" AND u.admin_id =  $admin_names  ";
		}
		
		if($name!=""){
			$condition .=" AND u.user_name =  '".$name."'  ";
		}
		
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."user u left join ".DB_PREFIX."admin a on u.admin_id = a.id  WHERE $condition ";

		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		
		if($rs_count > 0){
			
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
			
			$sql_list =  " SELECT u.*,AES_DECRYPT(u.real_name_encrypt,'".AES_DECRYPT_KEY."') as real_name,AES_DECRYPT(u.email_encrypt,'".AES_DECRYPT_KEY."') as email,AES_DECRYPT(u.idno_encrypt,'".AES_DECRYPT_KEY."') as idno,AES_DECRYPT(u.money_encrypt,'".AES_DECRYPT_KEY."') as money,AES_DECRYPT(u.mobile_encrypt,'".AES_DECRYPT_KEY."') as mobile,a.adm_name,c.name as cname FROM ".DB_PREFIX."user u left join ".DB_PREFIX."admin a on u.admin_id = a.id left join ".DB_PREFIX."customer c on c.id = u.customer_id WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
			
			$list = $GLOBALS['db']->getAll($sql_list);
			$page = $p->show();
			$this->assign ( "page", $page );
			
		}

		$admin_cate = $GLOBALS['db']->getAll("select id,adm_name from ".DB_PREFIX."admin where is_effect = 1 and is_delete = 0 and is_department = 0 and pid > 0 ");
		$this->assign ( 'admin_cate', $admin_cate );

		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		
		require_once APP_ROOT_PATH."app/Lib/common.php";
		$user_info = get_user_info( "*","id=".$id);
		$this->assign ( 'user_info', $user_info );
		
		//管理员列表
		$adm_sql =  " SELECT * FROM ".DB_PREFIX."admin WHERE is_delete= 0 and is_effect=1 and is_department = 0 and pid > 0 ";
		$adm_list = $GLOBALS['db']->getAll($adm_sql);
		$this->assign ( 'admins', $adm_list );
		
		$this->display ();
		
	}
	
	public function update()
	{
		$id = intval($_REQUEST ['id']);
		$admin_id = intval($_REQUEST ['admin_id']);
		//$customer_id = intval($_REQUEST ['customer_id']);
		
		$user_info = array();
		$user_info['admin_id'] = $admin_id;
		//$user_info['customer_id'] = $customer_id;
		$old_admin_id =$GLOBALS['db']->getOne("SELECT admin_id FROM ".DB_PREFIX."user WHERE id=".$id);
		$list = $GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info,"UPDATE","id=".$id);

		if (false !== $list) {
			if($old_admin_id > 0){
				$total = M("User")->where("admin_id=".$old_admin_id)->count();
				M("Admin")->where("id=".$old_admin_id)->setField("referrals_count",$total);
			}
			if($admin_id > 0){
				$total = M("User")->where("admin_id=".$admin_id)->count();
				M("Admin")->where("id=".$admin_id)->setField("referrals_count",$total);
			}
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function updates()  //更新标所属的客服/管理员
	{
		$id = intval($_REQUEST ['id']);
		$admin_id = intval($_REQUEST ['admin_id']);
		$customers_id = intval($_REQUEST ['customers_id']);
	
		$deal_info = array();
		if($admin_id)
		{$deal_info['admin_id'] = $admin_id;}
		if($customers_id)
		{$deal_info['customers_id'] = $customers_id;}
		
		$list = $GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal_info,"UPDATE","id=".$id);
	
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	
	
	
}
?>