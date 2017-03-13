<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class ProjectConfAction extends CommonAction{
	public function index(){
		$conf_res = M("Conf")->where("is_effect = 1 and `name` in ('PAY_RADIO','BUY_PRESEND_POINT_MULTIPLE','REPAY_MAKE','SCORE_TRADE_NUMBER','BUY_PRESEND_SCORE_MULTIPLE','BUY_INVITE_REFERRALS','REFERRAL_LIMIT')")->order("group_id asc,sort asc")->findAll();

		foreach($conf_res as $k=>$v)
		{
			if(($v['name']=="USER_LOGIN_MONEY" && intval(M("Conf")->where("`name`='USER_LOGIN_MONEY_TYPE'")->getField("value")) == 1) || ($v['name']=="USER_LOGIN_KEEP_MONEY" && intval(M("Conf")->where("`name`='USER_LOGIN_KEEP_MONEY_TYPE'")->getField("value")) == 1)){
				$v['value'] = unserialize($v['value']);
			}
			else{
				$v['value'] = htmlspecialchars($v['value']);
				if($v['name']=='TEMPLATE')
				{
					
					//输出现有模板文件夹
					$directory = APP_ROOT_PATH."app/Tpl/";
					$dir = @opendir($directory);
				    $tmpls     = array();
				
				    while (false !== ($file = @readdir($dir)))
				    {
				    	if($file!='.'&&$file!='..')
				        $tmpls[] = $file;
				    }
				    @closedir($dir);
					//end
					
					$v['input_type'] = 1;
					$v['value_scope'] = $tmpls;
				}
				elseif($v['name']=='SHOP_LANG')
				{
					//输出现有语言包文件夹
					$directory = APP_ROOT_PATH."app/Lang/";
					$dir = @opendir($directory);
				    $tmpls     = array();
				
				    while (false !== ($file = @readdir($dir)))
				    {
				    	if($file!='.'&&$file!='..')
				        $tmpls[] = $file;
				    }
				    @closedir($dir);
					//end
					
					$v['input_type'] = 1;
					$v['value_scope'] = $tmpls;
				}
				else
				$v['value_scope'] = explode(",",$v['value_scope']);
			}
			
			$conf[$v['group_id']][] = $v;
		}
		$this->assign("conf",$conf);
		$this->assign("main_title","众筹参数配置");
		$this->display();
	}
	public function update(){
		$conf_res = M("Conf")->where("is_effect = 1 and name in ('PAY_RADIO','BUY_PRESEND_POINT_MULTIPLE','REPAY_MAKE','SCORE_TRADE_NUMBER','BUY_PRESEND_SCORE_MULTIPLE','BUY_INVITE_REFERRALS','REFERRAL_LIMIT')")->findAll();
		foreach($conf_res as $k=>$v)
		{
			if(($v['name']=='USER_LOGIN_MONEY' && intval($_REQUEST['USER_LOGIN_MONEY_TYPE']) == 1) || $v['name']=='USER_LOGIN_KEEP_MONEY' && intval($_REQUEST['USER_LOGIN_KEEP_MONEY_TYPE']) == 1){
				$list = array();
				foreach($_REQUEST[$v['name']] as $km=>$vm){
					$list[] = $vm;
				}
				conf($v['name'],serialize($list));
			}
			else{
				conf($v['name'],floatval(strim($_REQUEST[$v['name']])));
			}
			if($v['name']=='URL_MODEL'&&$v['value']!=$_REQUEST[$v['name']])
			{
				
				clear_auto_cache("cache_shop_acate_tree");
				clear_auto_cache("cache_shop_cate_tree");
				
				clear_auto_cache("city_list_result");
				
				clear_auto_cache("get_help_cache");
				clear_auto_cache("page_image");
				
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_caches/");	
				clear_dir_file(get_real_path()."public/runtime/app/tpl_compiled/");	
				
				clear_dir_file(get_real_path()."public/runtime/app/data_caches/");	
				clear_dir_file(get_real_path()."public/runtime/data/page_static_cache/");
				clear_dir_file(get_real_path()."public/runtime/data/dynamic_avatar_cache/");	
			}
		}
		
		//开始写入配置文件
		$result = write_sys_conf();
		
		if($result['status'] == 0){
			$this->error($result['info']);
		}
			

			
		save_log(l("CONF_UPDATED"),1);		
		//clear_cache();
		write_timezone();
		$this->success(L("UPDATE_SUCCESS"));
	}
}
?>