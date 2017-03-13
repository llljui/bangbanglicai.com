<?php 
return array( 
	"Index,Statistics"	=>	array(
		"name"	=>	"首页", 
		"node"	=>	array( 
			"Index_main"			=>	array("name"=>"快速导航","module"=>"Index","action"=>"main"),
			"Index_statistics"		=>	array("name"=>"网站数据统计","module"=>"Index","action"=>"statistics"),
			"Index_manage_carry"	=>	array("name"=>"管理员提现列表","module"=>"Index","action"=>"manage_carry"),
			"Index_de_manage_carry"	=>	array("name"=>"管理员提现删除执行","module"=>"Index","action"=>"de_manage_carry"),
			"Statistics_index"		=>	array("name"=>"借款统计","module"=>"Statistics","action"=>"index"),
		)
	),
	
	
	"Log"	=>	array(
		"name"	=>	"系统日志", 
		"node"	=>	array( 
			"index"					=>	array("name"=>"系统日志列表","module"=>"Log","action"=>"index"),
			"foreverdelete"			=>	array("name"=>"永久删除","module"=>"Log","action"=>"foreverdelete"),
			
		)
	),
	
	"Deal,DealMsgboard"=>   array(
        "name"  =>  "借贷审核管理", 
        "node"  =>  array(
        	"Deal_publish"			=>	array("name"=>"待审核贷款","module"=>"Deal","action"=>"publish"),
			"Deal_true_publish"		=>	array("name"=>"复审核贷款","module"=>"Deal","action"=>"true_publish"),
			"Deal_full"				=>	array("name"=>"满标的贷款","module"=>"Deal","action"=>"full"),
            "DealMsgboard_index" 	=>  array("name"=>"留言式贷款申请","module"=>"DealMsgboard","action"=>"index"),
            "DealMsgboard_set_effect" =>  array("name"=>"状态操作","module"=>"DealMsgboard","action"=>"set_effect"),
        )
     ),
     
     
	"Deal,Option"	=>	array(
		"name"	=>	"贷款管理", 
		"node"	=>	array(	
			
			"Deal_index"		=>	array("name"=>"全部贷款列表","module"=>"Deal","action"=>"index"),
			"Deal_advance"		=>	array("name"=>"预告中贷款","module"=>"Deal","action"=>"advance"),
			"Deal_news"    		=>  array("name"=>"新手专享贷","module"=>"Deal","action"=>"news"),
			"Deal_inrepay"		=>	array("name"=>"还款中贷款","module"=>"Deal","action"=>"inrepay"),
			"Deal_over"			=>	array("name"=>"已完成贷款","module"=>"Deal","action"=>"over"),
			"Deal_penalty"		=>	array("name"=>"提前还贷款","module"=>"Deal","action"=>"penalty"),
			"Deal_ing"			=>	array("name"=>"未满标贷款","module"=>"Deal","action"=>"ing"),
			"Deal_expire"		=>	array("name"=>"过期的贷款","module"=>"Deal","action"=>"expire"),
			"Deal_flow"			=>	array("name"=>"流标的贷款","module"=>"Deal","action"=>"flow"),
			"Deal_trash"		=>	array("name"=>"贷款回收站","module"=>"Deal","action"=>"trash"),
			/*"wait"	=>	array("name"=>"等待材料贷款","module"=>"Deal","action"=>"wait"),*/
				
			"Deal_three"		=>	array("name"=>"待还款账单","module"=>"Deal","action"=>"three"),	
			"Deal_yuqi"			=>	array("name"=>"逾期待收款","module"=>"Deal","action"=>"yuqi"),
			"Deal_generation_repay"	=>	array("name"=>"网站垫付款","module"=>"Deal","action"=>"generation_repay"),
			"Deal_user_loads_repay"	=>	array("name"=>"收款信息","module"=>"Deal","action"=>"user_loads_repay"),
					
			"Deal_export_csv_three"	=>	array("name"=>"导出待还款账单","module"=>"Deal","action"=>"export_csv_three"),	
			"Deal_three_msg"		=>	array("name"=>"催款短信提醒","module"=>"Deal","action"=>"three_msg"),
			"Deal_export_csv_yuqi"	=>	array("name"=>"导出逾期待收款","module"=>"Deal","action"=>"export_csv_yuqi"),
			"Deal_op_generation_repay_status"	=>	array("name"=>"垫付待收款操作","module"=>"Deal","action"=>"op_generation_repay_status"),
			"Deal_export_csv_generation"		=>	array("name"=>"导出垫付待收款","module"=>"Deal","action"=>"export_csv_generation"),
			"Deal_repay_log"		=>	array("name"=>"借贷记录日志","module"=>"Deal","action"=>"repay_log"),
			"Deal_repay_plan"		=>	array("name"=>"还款计划","module"=>"Deal","action"=>"repay_plan"),
			"Deal_do_site_repay"	=>	array("name"=>"网站代还款","module"=>"Deal","action"=>"do_site_repay"),
			"Deal_show_detail"		=>	array("name"=>"投标详情和操作","module"=>"Deal","action"=>"show_detail"),
			"Deal_apart"			=>	array("name"=>"拆标操作","module"=>"Deal","action"=>"apart"),
			"Deal_insert"			=>	array("name"=>"添加贷款","module"=>"Deal","action"=>"insert"),
			"Deal_update"			=>	array("name"=>"编辑贷款","module"=>"Deal","action"=>"update"),
			"Deal_delete"			=>	array("name"=>"删除","module"=>"Deal","action"=>"delete"),	
			"Deal_restore"			=>	array("name"=>"恢复","module"=>"Deal","action"=>"restore"),
			"Deal_foreverdelete"	=>	array("name"=>"永久删除","module"=>"Deal","action"=>"foreverdelete"),			
			"Deal_set_effect"		=>	array("name"=>"设置生效","module"=>"Deal","action"=>"set_effect"),
			"Deal_set_sort"			=>	array("name"=>"排序","module"=>"Deal","action"=>"set_sort"),			
		)
	),
		
	"Loads"	=>	array(
		"name"	=>	"投标记录", 
		"node"	=>	array(	
			"Loads_index"	=>	array("name"=>"所有投标","module"=>"Loads","action"=>"index"),
			"Loads_hand"	=>	array("name"=>"手动投标","module"=>"Loads","action"=>"hand"),
			"Loads_auto"	=>	array("name"=>"自动投标","module"=>"Loads","action"=>"auto"),
			"Loads_success"	=>	array("name"=>"成功的投标","module"=>"Loads","action"=>"success"),
			"Loads_failed"	=>	array("name"=>"失败的投标","module"=>"Loads","action"=>"failed"),
		)
	),
	
	"Transfer"	=>	array(
		"name"	=>	"债权转让", 
		"node"	=>	array( 
			"index"			=>	array("name"=>"所有转让","module"=>"Transfer","action"=>"index"),
			"ing"			=>	array("name"=>"正在转让","module"=>"Transfer","action"=>"ing"),
			"success"		=>	array("name"=>"成功转让","module"=>"Transfer","action"=>"success"),
			"back"			=>	array("name"=>"撤销转让","module"=>"Transfer","action"=>"back"),	
			"reback"		=>	array("name"=>"撤销操作","module"=>"Transfer","action"=>"reback"),	
		)
	),
	
    
//    'DealMsgboard'=>   array(
//        "name"  =>  "留言式贷款申请", 
//        "node"  =>  array(
//            "index" =>  array("name"=>"留言式贷款申请","module"=>"DealMsgboard","action"=>"index"),
//            "set_effect" =>  array("name"=>"状态操作","module"=>"DealMsgboard","action"=>"set_effect"),
//        )
//     ),
	
	"Message"	=>	array(
		"name"	=>	"留言管理", 
		"node"	=>	array( 			
			"index"			=>	array("name"=>"留言列表","module"=>"Message","action"=>"index"),
			"update"		=>	array("name"=>"回复留言","module"=>"Message","action"=>"update"),
			"foreverdelete"	=>	array("name"=>"永久删除","module"=>"Message","action"=>"foreverdelete"),
		)
	),
	
    "Licai,LicaiHistory,LicaiInterest,LicaiRecommend,LicaiDealshow,LicaiOrder"=>   array(
        "name"  =>  "理财管理", 
        "node"  =>  array(  
            "Licai_verify"    		=>  array("name"=>"待审核理财列表","module"=>"Licai","action"=>"verify"),
            "Licai_verify_edit"    	=>  array("name"=>"审核操作","module"=>"Licai","action"=>"verify_edit"),
            "Licai_index"    		=>  array("name"=>"理财列表","module"=>"Licai","action"=>"index"),
            "Licai_add"    			=>  array("name"=>"理财产品添加","module"=>"Licai","action"=>"add"),
            "Licai_edit"   	 		=>  array("name"=>"理财产品编辑","module"=>"Licai","action"=>"edit"),
            "Licai_export_csv"    	=>  array("name"=>"理财产品导出","module"=>"Licai","action"=>"export_csv"),
            "Licai_foreverdelete"   =>  array("name"=>"理财产品永久删除","module"=>"Licai","action"=>"foreverdelete"),
            "LicaiInterest_index"	=>  array("name"=>"固存产品收益率设置","module"=>"LicaiInterest","action"=>"index"),
            "LicaiHistory_index"	=>  array("name"=>"余额宝收益率设置","module"=>"LicaiHistory","action"=>"index"),
            "LicaiRecommend_add" 	=>  array("name"=>"添加到个性推荐","module"=>"LicaiRecommend","action"=>"add"),
            "LicaiOrder_index"   	=>  array("name"=>"购买记录","module"=>"LicaiOrder","action"=>"index"),
            "LicaiDealshow_add"   	=>  array("name"=>"添加到首页订单展示","module"=>"LicaiDealshow","action"=>"add"),
            "LicaiOrder_order_list" =>  array("name"=>"订单列表","module"=>"LicaiOrder","action"=>"order_list"),
            "LicaiOrder_view" 		=>  array("name"=>"订单详情","module"=>"LicaiOrder","action"=>"view"),
            
            "LicaiOrder_export_q_csv" =>  array("name"=>"订单导出","module"=>"LicaiOrder","action"=>"export_q_csv"),
        )
    ),
    
    "LicaiFundType,LicaiFundBrand,LicaiRecommend,LicaiDealshow"=>   array(
        "name"  =>  "理财配置", 
        "node"  =>  array(  
            "LicaiFundType_index"  	=>  array("name"=>"基金种类","module"=>"LicaiFundType","action"=>"index"),
            "LicaiFundType_add"		=>  array("name"=>"基金种类添加","module"=>"LicaiFundType","action"=>"add"),
            "LicaiFundType_edit"    =>  array("name"=>"基金种类编辑","module"=>"LicaiFundType","action"=>"edit"),
            "LicaiFundType_delete"  =>  array("name"=>"基金种类删除","module"=>"LicaiFundType","action"=>"delete"),
            
            "LicaiFundBrand_index"  =>  array("name"=>"基金品牌","module"=>"LicaiFundBrand","action"=>"index"),
            "LicaiFundBrand_add"    =>  array("name"=>"基金品牌添加","module"=>"LicaiFundBrand","action"=>"add"),
            "LicaiFundBrand_edit"   =>  array("name"=>"基金品牌编辑","module"=>"LicaiFundBrand","action"=>"edit"),
            "LicaiFundBrand_delete" =>  array("name"=>"基金品牌删除","module"=>"LicaiFundBrand","action"=>"delete"),
            
            "LicaiRecommend_index"  =>  array("name"=>"个性推荐列表","module"=>"LicaiRecommend","action"=>"index"),
            "LicaiRecommend_edit"   =>  array("name"=>"个性推荐编辑","module"=>"LicaiRecommend","action"=>"edit"),
            "LicaiRecommend_delete" =>  array("name"=>"个性推荐删除","module"=>"LicaiRecommend","action"=>"delete"),
            
            "LicaiHoliday_index"    =>  array("name"=>"假日列表","module"=>"LicaiHoliday","action"=>"index"),
            "LicaiHoliday_add"    	=>  array("name"=>"假日添加","module"=>"LicaiHoliday","action"=>"add"),
            "LicaiHoliday_edit"    	=>  array("name"=>"假日编辑","module"=>"LicaiHoliday","action"=>"edit"),
            "LicaiHoliday_delete"   =>  array("name"=>"假日删除","module"=>"LicaiHoliday","action"=>"delete"),
            
            "LicaiDealshow_index" 	=>  array("name"=>"首页订单展示列表","module"=>"LicaiDealshow","action"=>"index"),
            "LicaiDealshow_edit"    =>  array("name"=>"首页订单展示编辑","module"=>"LicaiDealshow","action"=>"edit"),
            "LicaiDealshow_delete"  =>  array("name"=>"首页订单展示删除","module"=>"LicaiDealshow","action"=>"delete"),
        )
    ),
    
    "LicaiRedempte"=>   array(
        "name"  =>  "理财配置", 
        "node"  =>  array(  
            "LicaiRedempte_index"    		=>  array("name"=>"理财赎回管理","module"=>"LicaiRedempte","action"=>"index"),
            "LicaiRedempte_status"    		=>  array("name"=>"理财赎回操作","module"=>"LicaiRedempte","action"=>"status"),
            "LicaiRedempte_foreach_pay"    	=>  array("name"=>"批量发放理财","module"=>"LicaiRedempte","action"=>"foreach_pay"),
            "LicaiRedempte_export_csv"    	=>  array("name"=>"导出理财赎回","module"=>"LicaiRedempte","action"=>"export_csv"),
            
            "LicaiRedempte_before_index"    =>  array("name"=>"预热期赎回管理","module"=>"LicaiRedempte","action"=>"before_index"),
            "LicaiRedempte_set_status"    	=>  array("name"=>"预热期赎回操作","module"=>"LicaiRedempte","action"=>"set_status"),
            "LicaiRedempte_foreach_pay_before"    =>  array("name"=>"批量发放预热期理财","module"=>"LicaiRedempte","action"=>"foreach_pay_before"),
            "LicaiRedempte_export_q_csv"    =>  array("name"=>"导出预热期理财赎回","module"=>"LicaiRedempte","action"=>"export_q_csv"),
        )
    ),
    
    "LicaiNear,LicaiSend" =>array(
        "name"  =>  "理财发放", 
        "node"  =>  array(  
            "LicaiNear_index"    		=>  array("name"=>"快到期列表","module"=>"LicaiNear","action"=>"index"),
            "LicaiNear_view"    		=>  array("name"=>"快到期发放详情","module"=>"LicaiNear","action"=>"view"),
            "LicaiNear_set_status"    	=>  array("name"=>"快到期理财发放","module"=>"LicaiNear","action"=>"set_status"),
            "LicaiNear_foreach_pay"    	=>  array("name"=>"快到期理财批量发放","module"=>"LicaiNear","action"=>"foreach_pay"),
            "LicaiNear_foreach_sms"    	=>  array("name"=>"快到期理财批量发放短信提醒","module"=>"LicaiNear","action"=>"foreach_sms"),
            "LicaiNear_export_csv"    	=>  array("name"=>"快到期理财导出","module"=>"LicaiNear","action"=>"export_csv"),
            
            "LicaiSend_index"    		=>  array("name"=>"已发放列表","module"=>"LicaiNear","action"=>"index"),
            "LicaiSend_view"    		=>  array("name"=>"已发放详情","module"=>"LicaiNear","action"=>"view"),
            "LicaiSend_export_csv"    	=>  array("name"=>"已发放理财导出","module"=>"LicaiNear","action"=>"export_csv"),
        )
    ),
    
    "LicaiAdvance" =>array(
        "name"  =>  "理财垫付单", 
        "node"  =>  array(  
            "LicaiAdvance_index"    	=>  array("name"=>"垫付列表","module"=>"LicaiAdvance","action"=>"index"),
            "LicaiAdvance_update"    	=>  array("name"=>"垫付回收","module"=>"LicaiAdvance","action"=>"update"),
            "LicaiAdvance_export_csv"   =>  array("name"=>"垫付导出","module"=>"LicaiAdvance","action"=>"export_csv"),
        )
    ),
    
    "User,CreateRelevance,PromotionHuman"	=>	array(
		"name"	=>	"会员列表", 
		"node"	=>	array( 
			
			"User_index"				=>	array("name"=>"个人会员","module"=>"User","action"=>"index"),
			"User_company_index"		=>	array("name"=>"企业会员","module"=>"User","action"=>"company_index"),
			"User_black"				=>	array("name"=>"个人会员黑名单","module"=>"User","action"=>"black"),
			"User_company_black"		=>	array("name"=>"企业会员黑名单","module"=>"User","action"=>"company_black"),
			"User_trash"				=>	array("name"=>"个人会员回收站","module"=>"User","action"=>"trash"),
			"User_company_trash"		=>	array("name"=>"企业会员回收站","module"=>"User","action"=>"company_trash"),
			
			"CreateRelevance_index"		=>	array("name"=>"建立关联","module"=>"CreateRelevance","action"=>"index"),
			"PromotionHuman_index"		=>	array("name"=>"推广人列表","module"=>"PromotionHuman","action"=>"index"),
										
			"User_de_bank"				=>	array("name"=>"删除银行卡","module"=>"User","action"=>"de_bank"),
			"User_passed"				=>	array("name"=>"认证信息","module"=>"User","action"=>"passed"),
			"User_info_down"			=>	array("name"=>"资料下载","module"=>"User","action"=>"info_down"),
			"User_view_info"			=>	array("name"=>"资料展示","module"=>"User","action"=>"view_info"),
			"User_insert"				=>	array("name"=>"添加执行","module"=>"User","action"=>"insert"),
			"User_update"				=>	array("name"=>"编辑执行","module"=>"User","action"=>"update"),
			"User_delete"				=>	array("name"=>"删除","module"=>"User","action"=>"delete"),
			"User_export_csv"			=>	array("name"=>"导出csv","module"=>"User","action"=>"export_csv"),
			"User_foreverdelete"		=>	array("name"=>"永久删除","module"=>"User","action"=>"foreverdelete"),
			"User_account_detail"		=>	array("name"=>"帐户详情","module"=>"User","action"=>"account_detail"),
			"User_restore"				=>	array("name"=>"恢复","module"=>"User","action"=>"restore"),
			"User_set_effect"			=>	array("name"=>"设置生效","module"=>"User","action"=>"set_effect"),
		)
	),
	
	
	"User,Option1"	=>	array(
		"name"	=>	"会员审核", 
		"node"	=>	array( 

			"User_register"			=>	array("name"=>"待审核个人会员","module"=>"User","action"=>"register"),
			"User_company_register"	=>	array("name"=>"待审核企业会员","module"=>"User","action"=>"company_register"),
		)
	),
	
	"Credit,Option"	=>	array(
		"name"	=>	"认证管理", 
		"node"	=>	array( 
			"Credit_user_wait"	=>	array("name"=>"待审的认证 ","module"=>"Credit","action"=>"user_wait"),
			"Credit_user"		=>	array("name"=>"所有认证 ","module"=>"Credit","action"=>"user"),
			//"Credit_user_success"	=>	array("name"=>"通过的认证 ","module"=>"Credit","action"=>"user_success"),
			//"Credit_user_bad"	=>	array("name"=>"失败的认证 ","module"=>"Credit","action"=>"user_bad"),
			"Credit_op_passed"	=>	array("name"=>"认证操作 ","module"=>"Credit","action"=>"op_passed"),
		)
	),
	
	"DealQuotaSubmit,QuotaSubmit,GenerationRepaySubmit"	=>	array(
		"name"	=>	"额度申请", 
		"node"	=>	array( 
			"DealQuotaSubmit_index"		=>	array("name"=>"授信额度申请 ","module"=>"DealQuotaSubmit","action"=>"index"),
			"DealQuotaSubmit_edit"	 	=>	array("name"=>"查看/处理 ","module"=>"DealQuotaSubmit","action"=>"edit"),
			"DealQuotaSubmit_delete" 	=>	array("name"=>"永久删除","module"=>"DealQuotaSubmit","action"=>"delete"),
			
			"QuotaSubmit_index"			=>	array("name"=>"信用额度申请 ","module"=>"QuotaSubmit","action"=>"index"),
			"QuotaSubmit_edit"			=>	array("name"=>"查看/处理 ","module"=>"QuotaSubmit","action"=>"edit"),
			"QuotaSubmit_delete"		=>	array("name"=>"永久删除","module"=>"QuotaSubmit","action"=>"delete"),
			
			"GenerationRepaySubmit_index"	=>	array("name"=>"续约申请","module"=>"GenerationRepaySubmit","action"=>"index"),
			"GenerationRepaySubmit_edit"	=>	array("name"=>"申请处理","module"=>"GenerationRepaySubmit","action"=>"edit"),	
			
		)
	),
	
	"User,CreateRelevance_rebate,PromotionHuman_rebate"	=>	array(
		"name"	=>	"授权服务机构", 
		"node"	=>	array( 

			"User_agencies_index"				=>	array("name"=>"授权服务机构","module"=>"User","action"=>"agencies_index"),
			"User_agencies_register"			=>	array("name"=>"授权服务机构添加","module"=>"User","action"=>"agencies_register"),
			"User_agencies_delete"				=>	array("name"=>"授权服务机构删除","module"=>"User","action"=>"agencies_delete"),
			"User_agencies_trash"				=>	array("name"=>"授权服务机构回收站","module"=>"User","action"=>"agencies_trash"),
			
			"CreateRelevance_rebate_index"		=>	array("name"=>"建立关联","module"=>"CreateRelevance_rebate","action"=>"index"),
			"PromotionHuman_rebate_index"		=>	array("name"=>"返佣统计","module"=>"PromotionHuman_rebate","action"=>"index"),
		)
	),	
	
	"User,Option2"	=>	array(
		"name"	=>	"信息快速查询", 
		"node"	=>	array( 
			
			"User_info"				=>	array("name"=>"个人会员信息","module"=>"User","action"=>"info"),	
			"User_company_info"		=>	array("name"=>"企业会员信息","module"=>"User","action"=>"company_info"),		
			"User_company_manage"	=>	array("name"=>"公司列表","module"=>"User","action"=>"company_manage"),
			"User_work_manage"		=>	array("name"=>"工作列表","module"=>"User","action"=>"work_manage"),
			"User_work"				=>	array("name"=>"工作信息","module"=>"User","action"=>"work"),
			"User_bank_manage"		=>	array("name"=>"银行卡管理","module"=>"User","action"=>"bank_manage"),
				
		)
	),
		
	"DealAgency"	=>	array(
		"name"	=>	"担保机构",
		"node"	=>	array(
			"index"		=>	array("name"=>"担保机构","module"=>"DealAgency","action"=>"index"),
			"trash"		=>	array("name"=>"担保机构回收站","module"=>"DealAgency","action"=>"trash"),
			"insert"	=>	array("name"=>"添加担保机构","module"=>"DealAgency","action"=>"insert"),
			"update"	=>	array("name"=>"编辑担保机构","module"=>"DealAgency","action"=>"update"),
			"insert"	=>	array("name"=>"永久删除","module"=>"DealAgency","action"=>"foreverdelete"),
		)
	),
	
	
	"VipPrivilege,VipType,VipSetting,VipPrivilege,Customers,VipGift,VipWelfare"=>	array(
		"name"	=>	"VIP会员管理", 
		"node"	=>	array( 
			"VipPrivilege_vip_user"				=>	array("name"=>"VIP会员列表","module"=>"VipPrivilege","action"=>"vip_user"),
			"VipPrivilege_send_gift_all"		=>	array("name"=>"发放生日礼品","module"=>"VipPrivilege","action"=>"send_gift_all"),
			"VipType_index"						=>	array("name"=>"VIP等级","module"=>"VipType","action"=>"index"),
			"VipType_add"						=>	array("name"=>"等级类型增加","module"=>"VipType","action"=>"add"),
			"VipType_edit"						=>	array("name"=>"等级类型修改","module"=>"VipType","action"=>"edit"),
			"VipType_delete"					=>	array("name"=>"等级类型删除","module"=>"VipType","action"=>"delete"),
			"VipSetting_index"					=>	array("name"=>"VIP配置列表","module"=>"VipSetting","action"=>"index"),
			"VipSetting_add"					=>	array("name"=>"新增配置","module"=>"VipSetting","action"=>"add"),
			"VipSetting_edit"					=>	array("name"=>"更新配置","module"=>"VipSetting","action"=>"edit"),
			"VipSetting_setting_trash"			=>	array("name"=>"配置回收站","module"=>"VipSetting","action"=>"setting_trash"),
			"VipSetting_delete"					=>	array("name"=>"删除配置","module"=>"VipSetting","action"=>"delete"),
			"VipSetting_foreverdelete"			=>	array("name"=>"永久删除配置","module"=>"VipSetting","action"=>"foreverdelete"),
			"VipSetting_config"					=>	array("name"=>"提现手续费设置","module"=>"VipSetting","action"=>"config"),
			"VipPrivilege_vip_upgrade_record"	=>	array("name"=>"VIP升级记录","module"=>"VipPrivilege","action"=>"vip_upgrade_record"),
			"VipPrivilege_vip_demotion_record"	=>	array("name"=>"VIP降级记录","module"=>"VipPrivilege","action"=>"vip_demotion_record"),
			"Customers_index"					=>	array("name"=>"客服列表","module"=>"Customers","action"=>"index"),
			"Customers_trash"					=>	array("name"=>"客服回收站","module"=>"Customers","action"=>"trash"),
			"VipGift_vip_gift_record"			=>	array("name"=>"奖励发放列表","module"=>"VipGift","action"=>"vip_gift_record"),
			"VipWelfare_given_record"			=>	array("name"=>"福利发放列表","module"=>"VipWelfare","action"=>"given_record"),
			
		)
	),
		
		
	
	"Reportguy"	=>	array(
		"name"	=>	"举报管理", 
		"node"	=>	array( 
			"index"			=>	array("name"=>"举报列表","module"=>"Reportguy","action"=>"index"),
			"edit"			=>	array("name"=>"举报处理 ","module"=>"Reportguy","action"=>"edit"),
			"foreverdelete"	=>	array("name"=>"永久删除","module"=>"Reportguy","action"=>"foreverdelete"),
		)
	),
	
	"MsgSystem"	=>	array(
		"name"	=>	"站内消息",
		"node"	=>	array(
			"index"			=>	array("name"=>"消息列表","module"=>"MsgSystem","action"=>"index"),
			"insert"		=>	array("name"=>"添加提交","module"=>"MsgSystem","action"=>"insert"),
			"update"		=>	array("name"=>"编辑提交","module"=>"MsgSystem","action"=>"update"),
			"foreverdelete"	=>	array("name"=>"永久删除","module"=>"MsgSystem","action"=>"foreverdelete"),
		)
	),
	
	
	"PaymentNotice,BankReconciliation"	=>	array(
		"name"	=>	"充值管理", 
		"node"	=>	array( 
			"PaymentNotice_index"		=>	array("name"=>"在线充值单","module"=>"PaymentNotice","action"=>"index"),
			"BankReconciliation_index"	=>	array("name"=>"在线充值日账单","module"=>"BankReconciliation","action"=>"index"),
			"PaymentNotice_online"		=>	array("name"=>"线下充值单","module"=>"PaymentNotice","action"=>"online"),
			"PaymentNotice_gathering"	=>	array("name"=>"管理员收款","module"=>"PaymentNotice","action"=>"gathering"),
		)
	),
	
	
	
	"UserCarry"	=>	array(
		"name"	=>	"提现管理", 
		"node"	=>	array( 
			"index"		=>	array("name"=>"所有申请","module"=>"UserCarry","action"=>"index"),
			"wait"		=>	array("name"=>"待审申请","module"=>"UserCarry","action"=>"wait"),
			"waitpay"	=>	array("name"=>"代付申请","module"=>"UserCarry","action"=>"waitpay"),
			"success"	=>	array("name"=>"成功申请","module"=>"UserCarry","action"=>"success"),
			"failed"	=>	array("name"=>"失败申请","module"=>"UserCarry","action"=>"success"),
			"reback"	=>	array("name"=>"撤销的申请","module"=>"UserCarry","action"=>"reback"),
			"edit"	    =>	array("name"=>"查看/处理 ","module"=>"UserCarry","action"=>"edit"),
			"delete"	=>	array("name"=>"永久删除","module"=>"UserCarry","action"=>"delete"),
		)
	),
	
	"User,Deal,Security,ProvisionsRisk"	=>	array(
		"name"	=>	"资金日志", 
		"node"	=>	array( 
			"User_fund_management"		=>	array("name"=>"会员资金日志","module"=>"User","action"=>"fund_management"),
			"Deal_site_money"	    	=>	array("name"=>"网站收支","module"=>"Deal","action"=>"site_money"),
			"Security_index"	    	=>	array("name"=>"保障金","module"=>"Security","action"=>"index"),
			"ProvisionsRisk_index"	    =>	array("name"=>"风险准备金","module"=>"ProvisionsRisk","action"=>"index"),
			"ProvisionsRisk_add"	    =>	array("name"=>"风险准备金新增","module"=>"ProvisionsRisk","action"=>"add"),
		)
	),
	"User,handOption"	=>	array(
		"name"	=>	"人工账户变更", 
		"node"	=>	array( 
			"User_hand_recharge"	=>	array("name"=>"快速充值","module"=>"User","action"=>"hand_recharge"),
			"User_hand_overdue"		=>	array("name"=>"快速扣款","module"=>"User","action"=>"hand_overdue"),
			"User_hand_freeze"		=>	array("name"=>"冻结资金","module"=>"User","action"=>"hand_freeze"),
			"User_hand_integral"	=>	array("name"=>"变更信用积分","module"=>"User","action"=>"hand_integral"),
			"User_hand_integrals"	=>	array("name"=>"变更积分","module"=>"User","action"=>"hand_integrals"),
			"User_hand_quota"     	=>	array("name"=>"变更额度","module"=>"User","action"=>"hand_quota"),
		)
	),
	
	"Ipslog,IpsRelation,IpsFullscale,IpsTransfer,IpsProfit"	=>	array(
		"name"	=>	"IPS托管对账", 
		"node"	=>	array( 			
			"Ipslog_create"			=>	array("name"=>"开户列表","module"=>"Ipslog","action"=>"create"),
			"Ipslog_trade"			=>	array("name"=>"标的登记","module"=>"Ipslog","action"=>"trade"),
			"Ipslog_creditor"		=>	array("name"=>"投标记录","module"=>"Ipslog","action"=>"creditor"),
			//"Ipslog_guarantor"	=>	array("name"=>"担保方记录","module"=>"Ipslog","action"=>"guarantor"),
			"Ipslog_recharge"		=>	array("name"=>"充值记录","module"=>"Ipslog","action"=>"recharge"),
			"Ipslog_transfer"		=>	array("name"=>"提现记录","module"=>"Ipslog","action"=>"transfer"),
			"IpsRelation_repayment"	=>	array("name"=>"还款单","module"=>"IpsRelation","action"=>"repayment"),
			"IpsRelation_back_repayment"	=>	array("name"=>"回款单","module"=>"IpsRelation","action"=>"back_repayment"),
			"IpsFullscale_index"	=>	array("name"=>"满标放款","module"=>"IpsFullscale","action"=>"index"),
			"IpsTransfer_index"     =>	array("name"=>"债权转让","module"=>"IpsTransfer","action"=>"index"),
			//"IpsProfit_index"	    =>	array("name"=>"担保收益","module"=>"IpsProfit","action"=>"index"),
		)
	),
	
					
	"StatisticsBorrow"	=>	array(
		"name"	=>	"投资统计", 
		"node"	=>	array( 			
			"tender_total"				=>	array("name"=>"投资总统计","module"=>"StatisticsBorrow","action"=>"tender_total"),
			"tender_usernum_total"		=>	array("name"=>"投资人数","module"=>"StatisticsBorrow","action"=>"tender_usernum_total"),
			"tender_account_total"		=>	array("name"=>"投资金额","module"=>"StatisticsBorrow","action"=>"tender_account_total"),
			"tender_borrow_type"		=>	array("name"=>"标种投资","module"=>"StatisticsBorrow","action"=>"tender_borrow_type"),
			"tender_hasback_total"		=>	array("name"=>"已回款","module"=>"StatisticsBorrow","action"=>"tender_hasback_total"),
			"tender_tobe_receivables"	=>	array("name"=>"待收款","module"=>"StatisticsBorrow","action"=>"tender_tobe_receivables"),
			"tender_rank_list"			=>	array("name"=>"投资排名","module"=>"StatisticsBorrow","action"=>"tender_rank_list"),
			"tender_account_ratio"		=>	array("name"=>"投资额比例","module"=>"StatisticsBorrow","action"=>"tender_account_ratio"),
			
		)
	),
	
	"StatisticsLoan"	=>	array(
		"name"	=>	"借款统计", 
		"node"	=>	array( 			
			"loan_total"			=>	array("name"=>"借款总统计","module"=>"StatisticsLoan","action"=>"loan_total"),
			"loan_usernum_total"	=>	array("name"=>"借款人数","module"=>"StatisticsLoan","action"=>"loan_usernum_total"),
			"loan_account_total"	=>	array("name"=>"借款金额","module"=>"StatisticsLoan","action"=>"loan_account_total"),
			"loan_borrow_type"		=>	array("name"=>"标种借款","module"=>"StatisticsLoan","action"=>"loan_borrow_type"),
			"loan_hasback_total"	=>	array("name"=>"已还款","module"=>"StatisticsLoan","action"=>"loan_hasback_total"),
			"loan_tobe_receivables"	=>	array("name"=>"待还款","module"=>"StatisticsLoan","action"=>"loan_tobe_receivables"),
			"loan_repay_late_total"	=>	array("name"=>"逾期还款","module"=>"StatisticsLoan","action"=>"loan_repay_late_total"),
		)
	),
	
	"StatisticsClaims"	=>	array(
		"name"	=>	"债权统计", 
		"node"	=>	array( 			
			"change_account_total"	=>	array("name"=>"债权转让","module"=>"StatisticsClaims","action"=>"change_account_total"),
		)
	),
	
	"WebsiteStatistics"	=>	array(
		"name"	=>	"平台统计", 
		"node"	=>	array( 			
			"website_recharge_total"	=>	array("name"=>"充值统计","module"=>"WebsiteStatistics","action"=>"website_recharge_total"),
			"website_extraction_cash"	=>	array("name"=>"提现统计","module"=>"WebsiteStatistics","action"=>"website_extraction_cash"),
			"website_users_total"		=>	array("name"=>"用户统计","module"=>"WebsiteStatistics","action"=>"website_users_total"),
			"website_age_group_total"	=>	array("name"=>"地域分布统计","module"=>"WebsiteStatistics","action"=>"website_region_pie"),
			"website_age_group_total"	=>	array("name"=>"用户属性统计","module"=>"WebsiteStatistics","action"=>"website_age_group_total"),
			"website_advance_total"		=>	array("name"=>"网站垫付统计","module"=>"WebsiteStatistics","action"=>"website_advance_total"),
			"website_cost_total"		=>	array("name"=>"网站费用统计","module"=>"WebsiteStatistics","action"=>"website_cost_total"),
		)
	),
	
	
	
	"Departments,MyManager,MyCustomer,OverdueBillMonth"=>	array(
		"name"	=>	"部门管理",
		"node"	=>	array(
			"Departments_index"				=>	array("name"=>"部门列表","module"=>"Departments","action"=>"index"),
			"Departments_trash"				=>	array("name"=>"部门回收站","module"=>"Departments","action"=>"trash"),
			"Departments_insert"			=>	array("name"=>"新增部门","module"=>"Departments","action"=>"insert"),
			"Departments_update"			=>	array("name"=>"编辑部门","module"=>"Departments","action"=>"update"),
			"Departments_foreverdelete"		=>	array("name"=>"删除部门","module"=>"Departments","action"=>"foreverdelete"),
			"Departments_export_csv"		=>	array("name"=>"部门导出","module"=>"Departments","action"=>"export_csv"),
			"MyManager_index"				=>	array("name"=>"部门成员","module"=>"MyManager","action"=>"index"),
			"MyManager_insert"				=>	array("name"=>"添加成员","module"=>"MyManager","action"=>"insert"),
			"MyManager_update"				=>	array("name"=>"编辑成员","module"=>"MyManager","action"=>"update"),
			"MyManager_export_csv"			=>	array("name"=>"导出成员","module"=>"MyManager","action"=>"export_csv"),
			"MyCustomer_index"				=>	array("name"=>"待分配会员","module"=>"MyCustomer","action"=>"index"),
			"MyCustomer_update"				=>	array("name"=>"分配会员","module"=>"MyCustomer","action"=>"update"),
			"OverdueBillMonth_unallocated_standard"	=>	array("name"=>"待分配借款标","module"=>"OverdueBillMonth","action"=>"unallocated_standard"),
			"MyCustomer_updates"			=>	array("name"=>"分配借款标","module"=>"MyCustomer","action"=>"updates"),
			"OverdueBillMonth_all_loan"		=>	array("name"=>"所有借款标","module"=>"OverdueBillMonth","action"=>"all_loan"),
		)
	),
	
	"Departments,MyManager"=>	array(
		"name"	=>	"提成统计",
		"node"	=>	array(
			"Departments_referrals"			=>	array("name"=>"部门提成统计","module"=>"Departments","action"=>"referrals"),
			"MyManager_referrals"			=>	array("name"=>"成员提成统计","module"=>"MyManager","action"=>"referrals"),
			"MyManager_referrals_log"		=>	array("name"=>"提成统计详情","module"=>"MyManager","action"=>"referrals_log"),
			"Departments_export_log_csv"	=>	array("name"=>"导出提成统计详情","module"=>"MyManager","action"=>"export_log_csv"),
		)
	),
	
	
	"MyMembership,OverdueBillMonth,User"=>	array(
		"name"	=>	"我的会员",
		"node"	=>	array(
			"MyMembership_index"					=>	array("name"=>"我的会员列表","module"=>"MyMembership","action"=>"index"),
			"OverdueBillMonth_index"				=>	array("name"=>"本月到期账单","module"=>"OverdueBillMonth","action"=>"index"),
			"OverdueBillMonth_overdue_bill"			=>	array("name"=>"逾期账单","module"=>"OverdueBillMonth","action"=>"overdue_bill"),
			"OverdueBillMonth_repayment_bill"		=>	array("name"=>"已还款账单","module"=>"OverdueBillMonth","action"=>"repayment_bill"),
			"OverdueBillMonth_repayloan_scale"		=>	array("name"=>"还款中借款标","module"=>"OverdueBillMonth","action"=>"repayloan_scale"),
			"OverdueBillMonth_completedloan_scale"	=>	array("name"=>"已完成借款标","module"=>"OverdueBillMonth","action"=>"completedloan_scale"),
			"OverdueBillMonth_badloan_scale"		=>	array("name"=>"已坏账借款标","module"=>"OverdueBillMonth","action"=>"badloan_scale"),
				
			"OverdueBillMonth_repay_plan_a"			=>	array("name"=>"还款详情","module"=>"OverdueBillMonth","action"=>"repay_plan_a"),
			"OverdueBillMonth_op_status"			=>	array("name"=>"坏账操作","module"=>"OverdueBillMonth","action"=>"op_status"),
			"OverdueBillMonth_repay_log"			=>	array("name"=>"操作明细","module"=>"OverdueBillMonth","action"=>"repay_log"),
			"OverdueBillMonth_preview"				=>	array("name"=>"预览","module"=>"OverdueBillMonth","action"=>"preview"),
			"OverdueBillMonth_show_detail"			=>	array("name"=>"投标详情和操作","module"=>"OverdueBillMonth","action"=>"show_detail"),

			"User_borrowing_member"					=>	array("name"=>"借款会员列表","module"=>"User","action"=>"borrowing_member"),
			"User_bad_member"						=>	array("name"=>"坏账会员","module"=>"User","action"=>"bad_member"),
		)
	),
	
	"Article,ArticleCate"	=>	array(
		"name"	=>	"文章管理", 
		"node"	=>	array( 			
			"Article_index"				=>	array("name"=>"文章列表","module"=>"Article","action"=>"index"),
			"Article_insert"			=>	array("name"=>"添加执行","module"=>"Article","action"=>"insert"),
			"Article_update"			=>	array("name"=>"编辑执行","module"=>"Article","action"=>"update"),
			"Article_set_effect"		=>	array("name"=>"设置生效","module"=>"Article","action"=>"set_effect"),
			"Article_set_sort"			=>	array("name"=>"排序","module"=>"Article","action"=>"set_sort"),			
			"Article_delete"			=>	array("name"=>"删除","module"=>"Article","action"=>"delete"),
			"Article_trash"				=>	array("name"=>"回收站","module"=>"Article","action"=>"trash"),	
			"ArticleCate_index"			=>	array("name"=>"文章分类列表","module"=>"ArticleCate","action"=>"index"),
			"ArticleCate_insert"		=>	array("name"=>"添加执行","module"=>"ArticleCate","action"=>"insert"),
			"ArticleCate_update"		=>	array("name"=>"编辑执行","module"=>"ArticleCate","action"=>"update"),
			"ArticleCate_delete"		=>	array("name"=>"删除","module"=>"ArticleCate","action"=>"delete"),			
			"ArticleCate_set_effect"	=>	array("name"=>"设置生效","module"=>"ArticleCate","action"=>"set_effect"),
			"ArticleCate_set_sort"		=>	array("name"=>"设置排序","module"=>"ArticleCate","action"=>"set_sort"),
			"ArticleCate_trash"			=>	array("name"=>"回收站","module"=>"ArticleCate","action"=>"trash"),
			
		)
	),
	
	"Nav,Vote,Adv"	=>	array(
		"name"	=>	"PC端设置", 
		"node"	=>	array( 			
			"Nav_index"				=>	array("name"=>"导航菜单","module"=>"Nav","action"=>"index"),
			"Nav_insert"			=>	array("name"=>"添加执行","module"=>"Nav","action"=>"insert"),
			"Nav_update"			=>	array("name"=>"编辑执行","module"=>"Nav","action"=>"update"),
			"Nav_set_effect"		=>	array("name"=>"设置生效","module"=>"Nav","action"=>"set_effect"),
			"Nav_set_sort"			=>	array("name"=>"排序","module"=>"Nav","action"=>"set_sort"),
			"Vote_index"			=>	array("name"=>"投票调查列表","module"=>"Vote","action"=>"index"),
			"Vote_insert"			=>	array("name"=>"添加执行","module"=>"Vote","action"=>"insert"),
			"Vote_update"			=>	array("name"=>"编辑执行","module"=>"Vote","action"=>"update"),
			"Vote_add_ask_row"		=>	array("name"=>"添加问题","module"=>"Vote","action"=>"add_ask_row"),
			"Vote_do_vote_ask"		=>	array("name"=>"保存问卷","module"=>"Vote","action"=>"do_vote_ask"),
			"Vote_foreverdelete"	=>	array("name"=>"永久删除","module"=>"Vote","action"=>"foreverdelete"),
			"Vote_set_effect"		=>	array("name"=>"设置生效","module"=>"Vote","action"=>"set_effect"),
			"Vote_set_sort"			=>	array("name"=>"排序","module"=>"Vote","action"=>"set_sort"),
			"Vote_vote_ask"			=>	array("name"=>"编辑问卷","module"=>"Vote","action"=>"vote_ask"),
			"Vote_vote_result"		=>	array("name"=>"查看结果","module"=>"Vote","action"=>"vote_result"),
			"Adv_index"				=>	array("name"=>"广告列表","module"=>"Adv","action"=>"index"),
			"Adv_insert"			=>	array("name"=>"添加","module"=>"Adv","action"=>"insert"),
			"Adv_update"			=>	array("name"=>"编辑","module"=>"Adv","action"=>"update"),
			"Adv_foreverdelete"		=>	array("name"=>"永久删除","module"=>"Adv","action"=>"foreverdelete"),
			"Adv_load_adv_id"		=>	array("name"=>"读取广告位","module"=>"Adv","action"=>"load_adv_id"),
			"Adv_load_file"			=>	array("name"=>"读取页面","module"=>"Adv","action"=>"load_file"),
			"Adv_set_effect"		=>	array("name"=>"设置生效","module"=>"Adv","action"=>"set_effect"),
		)
	),
	
	"Conf,MAdv"	=>	array(
		"name"	=>	"移动端设置", 
		"node"	=>	array( 
			"Conf_mobile"		=>	array("name"=>"手机端配置","module"=>"Conf","action"=>"mobile"),
			"Conf_savemobile"	=>	array("name"=>"保存手机端配置","module"=>"Conf","action"=>"savemobile"),		
			"MAdv_index"		=>	array("name"=>"手机端广告列表","module"=>"MAdv","action"=>"index"),
			"MAdv_insert"		=>	array("name"=>"手机端广告添加","module"=>"MAdv","action"=>"insert"),
			"MAdv_update"		=>	array("name"=>"手机端广告编辑","module"=>"MAdv","action"=>"update"),
			"MAdv_foreverdelete"	=>	array("name"=>"删除手机端广告","module"=>"MAdv","action"=>"foreverdelete"),
		)
	),
	
	"LinkGroup,Link"	=>	array(
		"name"	=>	"友情链接", 
		"node"	=>	array( 		
			"LinkGroup_index"			=>	array("name"=>"友情链接分组","module"=>"LinkGroup","action"=>"index"),
			"LinkGroup_insert"			=>	array("name"=>"添加提交","module"=>"LinkGroup","action"=>"insert"),
			"LinkGroup_update"			=>	array("name"=>"编辑提交","module"=>"LinkGroup","action"=>"update"),
			"LinkGroup_foreverdelete"	=>	array("name"=>"永久删除","module"=>"LinkGroup","action"=>"foreverdelete"),			
			"LinkGroup_set_effect"		=>	array("name"=>"设置有效性","module"=>"LinkGroup","action"=>"set_effect"),
			"LinkGroup_set_sort"		=>	array("name"=>"设置排序","module"=>"LinkGroup","action"=>"set_sort"),
			"Link_index"				=>	array("name"=>"友情链接列表","module"=>"Link","action"=>"index"),
			"Link_insert"				=>	array("name"=>"添加提交","module"=>"Link","action"=>"insert"),
			"Link_update"				=>	array("name"=>"编辑提交","module"=>"Link","action"=>"update"),						
			"Link_foreverdelete"		=>	array("name"=>"永久删除","module"=>"Link","action"=>"foreverdelete"),
			"Link_set_effect"			=>	array("name"=>"设置有效性","module"=>"Link","action"=>"set_effect"),
			"Link_set_sort"				=>	array("name"=>"设置排序","module"=>"Link","action"=>"set_sort"),
		)
	),
	
	
	"WeixinConf,WeixinReply,WeixinTemplate,WeixinUser"	=>	array(
		"name"	=>	"微信平台设置", 
		"node"	=>	array( 		
			"WeixinConf_index"						=>	array("name"=>"第三方平台配置","module"=>"WeixinConf","action"=>"index"),
			"WeixinInfo_index"						=>	array("name"=>"账户管理","module"=>"WeixinInfo","action"=>"index"),
			"WeixinInfo_nav_setting"				=>	array("name"=>"自定义菜单","module"=>"WeixinInfo","action"=>"nav_setting"),
			"WeixinReply_index"						=>	array("name"=>"默认回复设置","module"=>"WeixinReply","action"=>"index"),			
			"WeixinReply_onfocus"					=>	array("name"=>"关注时回复","module"=>"WeixinReply","action"=>"onfocus"),
			"WeixinReply_txt"						=>	array("name"=>"文本回复","module"=>"WeixinReply","action"=>"txt"),
			"WeixinReply_news"						=>	array("name"=>"图文回复","module"=>"WeixinReply","action"=>"news"),
			"WeixinReply_lbs"						=>	array("name"=>"LBS回复","module"=>"WeixinReply","action"=>"lbs"),
			"WeixinReply_edittext"					=>	array("name"=>"文本编辑","module"=>"WeixinReply","action"=>"edittext"),
			"WeixinReply_editnews"					=>	array("name"=>"图文编辑","module"=>"WeixinReply","action"=>"editnews"),
			"WeixinReply_editlbs"					=>	array("name"=>"LBS编辑","module"=>"WeixinReply","action"=>"editlbs"),
			"WeixinReply_delreply"					=>	array("name"=>"删除回复","module"=>"WeixinReply","action"=>"delreply"),
			
			"WeixinTemplate_set_industry"			=>	array("name"=>"设置行业","module"=>"WeixinTemplate","action"=>"set_industry"),						
			"WeixinTemplate_index"					=>	array("name"=>"模板列表","module"=>"WeixinTemplate","action"=>"index"),
			"WeixinTemplate_install_tmpl"			=>	array("name"=>"安装模板","module"=>"WeixinTemplate","action"=>"install_tmpl"),
			"WeixinTemplate_msglist"				=>	array("name"=>"模板消息队列","module"=>"WeixinTemplate","action"=>"msglist"),
			"WeixinTemplate_send"					=>	array("name"=>"消息队列发送","module"=>"WeixinTemplate","action"=>"send"),
			"WeixinTemplate_foreverdelete"			=>	array("name"=>"消息队列删除","module"=>"WeixinTemplate","action"=>"foreverdelete"),
			
			"WeixinUser_groups"						=>	array("name"=>"分组管理","module"=>"WeixinUser","action"=>"groups"),
			"WeixinUser_groups_add"					=>	array("name"=>"添加分组","module"=>"WeixinUser","action"=>"groups_add"),
			"WeixinUser_groups_editor"				=>	array("name"=>"编辑分组","module"=>"WeixinUser","action"=>"groups_editor"),
			"WeixinUser_groups_synch"				=>	array("name"=>"同步分组","module"=>"WeixinUser","action"=>"groups_synch"),
			"WeixinUser_index"						=>	array("name"=>"会员管理","module"=>"WeixinUser","action"=>"index"),
			"WeixinUser_message_send"				=>	array("name"=>"普通消息群发","module"=>"WeixinUser","action"=>"message_send"),
			"WeixinUser_message_send_add"			=>	array("name"=>"普通消息群发编辑","module"=>"WeixinUser","action"=>"message_send_add"),
			"WeixinUser_to_send_message"			=>	array("name"=>"群发推送","module"=>"WeixinUser","action"=>"to_send_message"),
			"WeixinUser_advanced"					=>	array("name"=>"高级群发","module"=>"WeixinUser","action"=>"advanced"),
			"WeixinUser_advanced_add"				=>	array("name"=>"高级群发编辑","module"=>"WeixinUser","action"=>"advanced_add"),
			"WeixinUser_advanced_add"				=>	array("name"=>"高级群发编辑","module"=>"WeixinUser","action"=>"advanced_add"),
			"WeixinUser_message_send_del"			=>	array("name"=>"消息群发删除","module"=>"WeixinUser","action"=>"message_send_del"),
		)
	),
	
	
	"Conf,option"	=>	array(
		"name"	=>	"会员奖励", 
		"node"	=>	array( 
			"Conf_registered"		=>	array("name"=>"注册奖励","module"=>"Conf","action"=>"registered"),
			"Conf_signin"			=>	array("name"=>"签到奖励","module"=>"Conf","action"=>"signin"),	
			"Conf_update_signin"	=>	array("name"=>"更新签到奖励配置","module"=>"Conf","action"=>"update_signin"),	
			
		)
	),
	
	
	"Goods,GoodsCate,GoodsOrder"=>	array(
		"name"	=>	"积分商城",
		"node"	=>	array(
			"Goods_index"			=>	array("name"=>"商品列表","module"=>"Goods","action"=>"index"),
			"Goods_add"				=>	array("name"=>"新增商品","module"=>"Goods","action"=>"add"),
			"Goods_edit"			=>	array("name"=>"编辑商品","module"=>"Goods","action"=>"edit"),
			//"GoodsType_index"		=>	array("name"=>"类型列表","module"=>"GoodsType","action"=>"index"),
			//"GoodsType_insert"		=>	array("name"=>"类型增加","module"=>"GoodsType","action"=>"insert"),
			//"GoodsType_update"		=>	array("name"=>"类型更新","module"=>"GoodsType","action"=>"update"),
			"GoodsCate_index"		=>	array("name"=>"分类列表","module"=>"GoodsCate","action"=>"index"),
			"GoodsCate_add"			=>	array("name"=>"分类增加","module"=>"GoodsCate","action"=>"add"),
			"GoodsCate_edit"		=>	array("name"=>"分类更新","module"=>"GoodsCate","action"=>"edit"),
			"GoodsOrder_index"		=>	array("name"=>"订单列表","module"=>"GoodsOrder","action"=>"index"),
			"GoodsOrder_add"		=>	array("name"=>"订单增加","module"=>"GoodsOrder","action"=>"add"),
			"GoodsOrder_edit"		=>	array("name"=>"订单更新","module"=>"GoodsOrder","action"=>"edit"),
		)
	),
	
	"Referrals,Conf"	=>	array(
		"name"	=>	"会员邀请返利", 
		"node"	=>	array( 
			"Referrals_index"				=>	array("name"=>"邀请返利列表","module"=>"Referrals","action"=>"index"),
			"Referrals_pay"					=>	array("name"=>"发放返利","module"=>"Referrals","action"=>"pay"),
			"Conf_referrals"				=>	array("name"=>"会员邀请返利配置","module"=>"Conf","action"=>"referrals"),
			"Conf_update_referrals"			=>	array("name"=>"更新邀请返利配置","module"=>"Conf","action"=>"update_referrals"),
		)
	),
	
	"Referrals_rebate,CreateRelevance_rebate,PromotionHuman_rebate,Conf" =>array(
		"name"	=>	"授权服务机构及返佣", 
		"node"	=>	array( 	
			"Referrals_rebate_index"		=>	array("name"=>"投资返佣列表","module"=>"Referrals_rebate","action"=>"index"),
			"Referrals_rebate_borrow_index"	=>	array("name"=>"借款返佣列表","module"=>"Referrals_rebate","action"=>"borrow_index"),
			"CreateRelevance_rebate_index"	=>	array("name"=>"建立返佣关联","module"=>"CreateRelevance_rebate","action"=>"index"),
			"PromotionHuman_rebate_index"	=>	array("name"=>"授权服务机构统计","module"=>"PromotionHuman_rebate","action"=>"index"),
			"Conf_commossion"				=>	array("name"=>"授权服务机构返佣配置","module"=>"Conf","action"=>"commossion"),
			"Conf_update_commossion"		=>	array("name"=>"更新授权服务机构返佣配置","module"=>"Conf","action"=>"update_commossion"),
			)
	),
	
	
	"EcvType"	=>	array(
		"name"	=>	"红包管理", 
		"node"	=>	array( 
			"index"			=>	array("name"=>"红包类型","action"=>"index"),
			"insert"		=>	array("name"=>"添加执行 ","action"=>"insert"),
			"update"		=>	array("name"=>"编辑执行","action"=>"update"),
			"send"			=>	array("name"=>"红包发放","action"=>"send"),
			"foreverdelete"	=>	array("name"=>"永久删除","action"=>"foreverdelete"),
		)
	),
	
	"Learn,option" => array(
        "name"  =>  "体验金管理", 
        "node"  =>  array( 
            "Learn_learn_send_list"    	=>  array("name"=>"体验金发放列表","module"=>"Learn","action"=>"learn_send_list"), 
            "Learn_take_back"    		=>  array("name"=>"体验金回收","module"=>"Learn","action"=>"take_back"), 
            "Learn_learn_load" 			=>  array("name"=>"体验金投资记录","module"=>"Learn","action"=>"learn_load"), 
            "Learn_activity_setting"	=>  array("name"=>"体验金活动列表","module"=>"Learn","action"=>"activity_setting"), 
            "Learn_add"					=>  array("name"=>"体验金活动添加","module"=>"Learn","action"=>"add"), 
            "Learn_edit"				=>  array("name"=>"体验金活动编辑","module"=>"Learn","action"=>"edit"), 
            "Learn_delete"				=>  array("name"=>"体验金活动删除","module"=>"Learn","action"=>"delete"), 
            "Learn_learn_list"			=>  array("name"=>"理财产品列表","module"=>"Learn","action"=>"learn_list"), 
            "Learn_add_learn"			=>  array("name"=>"理财产品添加","module"=>"Learn","action"=>"add_learn"), 
            "Learn_edit_learn"			=>  array("name"=>"理财产品编辑","module"=>"Learn","action"=>"edit_learn"), 
            "Learn_foreverdelete"		=>  array("name"=>"理财产品删除","module"=>"Learn","action"=>"foreverdelete"), 
        )
    ),
    
    
    "Conf,UserCarry,Bank,Credit"	=>	array(
		"name"	=>	"系统配置", 
		"node"	=>	array( 
			"Conf_index"			=>	array("name"=>"系统配置","module"=>"Conf","action"=>"index"),
			"Conf_update"			=>	array("name"=>"更新配置","module"=>"Conf","action"=>"update"),
			"Conf_qq"				=>	array("name"=>"QQ客服配置","module"=>"Conf","action"=>"qq"),
			"Conf_update_qq"		=>	array("name"=>"更新QQ客服配置","module"=>"Conf","action"=>"update_qq"),

		)
	),
	
	
	"DealCate,DealLoanType,City,Contract,Conf"	=>	array(
		"name"	=>	"贷款配置", 
		"node"	=>	array( 
			"Conf_loan"				=>	array("name"=>"贷款配置","module"=>"Conf","action"=>"loan"),
			"DealCate_index"		=>	array("name"=>"贷款分类列表","module"=>"DealCate","action"=>"index"),
			"DealCate_insert"		=>	array("name"=>"贷款分类添加执行","module"=>"DealCate","action"=>"insert"),
			"DealCate_update"		=>	array("name"=>"贷款分类编辑执行","module"=>"DealCate","action"=>"update"),
			"DealCate_delete"		=>	array("name"=>"贷款分类删除","module"=>"DealCate","action"=>"delete"),
			"DealCate_set_effect"	=>	array("name"=>"贷款分类设置生效","module"=>"DealCate","action"=>"set_effect"),
			"DealCate_set_sort"		=>	array("name"=>"贷款分类排序","module"=>"DealCate","action"=>"set_sort"),
			"DealCate_trash"		=>	array("name"=>"贷款分类回收站","module"=>"DealCate","action"=>"trash"),	
			
			"DealLoanType_index"	=>	array("name"=>"借款类型列表","module"=>"DealLoanType","action"=>"index"),
			"DealLoanType_insert"	=>	array("name"=>"借款类型添加执行","module"=>"DealLoanType","action"=>"insert"),
			"DealLoanType_update"	=>	array("name"=>"借款类型编辑执行","module"=>"DealLoanType","action"=>"update"),
			"DealLoanType_delete"	=>	array("name"=>"借款类型删除","module"=>"DealLoanType","action"=>"delete"),
			"DealLoanType_set_effect"	=>	array("name"=>"借款类型设置生效","module"=>"DealLoanType","action"=>"set_effect"),
			"DealLoanType_set_sort"	=>	array("name"=>"借款类型排序","module"=>"DealLoanType","action"=>"set_sort"),		
			"DealLoanType_trash"	=>	array("name"=>"借款类型回收站","module"=>"DealLoanType","action"=>"trash"),	
			
			"City_index"			=>	array("name"=>"城市列表","module"=>"City","action"=>"index"),
			"City_insert"			=>	array("name"=>"城市添加执行","module"=>"City","action"=>"insert"),
			"City_update"			=>	array("name"=>"城市编辑执行","module"=>"City","action"=>"update"),
			"City_delete"			=>	array("name"=>"城市删除","module"=>"City","action"=>"delete"),
			"City_foreverdelete"	=>	array("name"=>"城市永久删除","module"=>"City","action"=>"foreverdelete"),	
			"City_set_effect"		=>	array("name"=>"城市设置生效","module"=>"City","action"=>"set_effect"),		
			"City_trash"			=>	array("name"=>"城市回收站","module"=>"City","action"=>"trash"),	
			
			"Contract_index"		=>	array("name"=>"合同范本设置","module"=>"Contract","action"=>"index"),
			"Contract_trash"		=>	array("name"=>"合同范本回收站","module"=>"Contract","action"=>"trash"),
			"Contract_add"			=>	array("name"=>"合同范本添加","module"=>"Contract","action"=>"add"),
			"Contract_edit"			=>	array("name"=>"合同范本编辑","module"=>"Contract","action"=>"edit"),
			"Contract_delete"		=>	array("name"=>"合同范本删除","module"=>"Contract","action"=>"delete"),
			"Contract_foreverdelete"	=>	array("name"=>"合同范本彻底删除","module"=>"Contract","action"=>"foreverdelete"),
			"Contract_set_effect"	=>	array("name"=>"合同范本设置生效","module"=>"Contract","action"=>"set_effect"),
						
		)
	),
	
	"UserField,Conf,Credit,UserLevel,VipType,VipSetting,VipGift,VipFestivals,VipWelfare"	=>	array(
		"name"	=>	"会员配置", 
		"node"	=>	array( 
			"Conf_users"					=>	array("name"=>"会员配置","module"=>"Conf","action"=>"users"),
			"UserField_index"				=>	array("name"=>"会员字段列表","module"=>"UserField","action"=>"index"),
			"UserField_insert"				=>	array("name"=>"添加执行","module"=>"UserField","action"=>"insert"),
			"UserField_update"				=>	array("name"=>"编辑执行","module"=>"UserField","action"=>"update"),
			"UserField_foreverdelete"		=>	array("name"=>"永久删除","module"=>"UserField","action"=>"foreverdelete"),
			"UserField_set_sort"			=>	array("name"=>"排序","module"=>"UserField","action"=>"set_sort"),
			
			"Credit_index"					=>	array("name"=>"认证类型","module"=>"Credit","action"=>"index"),
			"Credit_insert"					=>	array("name"=>"类型添加执行","module"=>"Credit","action"=>"insert"),
			"Credit_update"					=>	array("name"=>"类型编辑执行","module"=>"Credit","action"=>"update"),
			
			"UserLevel_index"				=>	array("name"=>"信用等级列表","module"=>"UserLevel","action"=>"index"),
			"UserLevel_insert"				=>	array("name"=>"添加提交","module"=>"UserLevel","action"=>"insert"),
			"UserLevel_update"				=>	array("name"=>"编辑提交","module"=>"UserLevel","action"=>"update"),
			"UserLevel_foreverdelete"		=>	array("name"=>"永久删除","module"=>"UserLevel","action"=>"foreverdelete"),
			
			"VipType_index"					=>	array("name"=>"VIP等级列表","module"=>"VipType","action"=>"index"),
			"VipSetting_gradesettingconfig"		=>	array("name"=>"等级配置列表","module"=>"VipSetting","action"=>"gradesettingconfig"),
			
			"VipGift_index"					=>	array("name"=>"礼品管理","module"=>"VipGift","action"=>"index"),
			"VipGift_export_csv"			=>	array("name"=>"导出列表","module"=>"VipGift","action"=>"export_csv"),
			"VipGift_insert"				=>	array("name"=>"新增礼品","module"=>"VipGift","action"=>"insert"),
			"VipGift_update"				=>	array("name"=>"编辑礼品","module"=>"VipGift","action"=>"update"),
			"VipFestivals_index"			=>	array("name"=>"节日积分列表","module"=>"VipFestivals","action"=>"index"),
			"VipFestivals_send_gift"		=>	array("name"=>"发放","module"=>"VipFestivals","action"=>"send_gift"),
			"VipFestivals_add"				=>	array("name"=>"新增节日","module"=>"VipFestivals","action"=>"add"),
			"VipFestivals_edit"				=>	array("name"=>"编辑节日","module"=>"VipFestivals","action"=>"edit"),
			"VipFestivals_delete"			=>	array("name"=>"删除节日","module"=>"VipFestivals","action"=>"delete"),
			"VipWelfare_score_exchange"		=>	array("name"=>"积分兑现","module"=>"VipWelfare","action"=>"score_exchange"),
			"VipWelfare_score_export_csv"	=>	array("name"=>"积分兑现导出","module"=>"VipWelfare","action"=>"score_export_csv"),
				
		)
	),
	
	
	"UserCarry,Bank"	=>	array(
		"name"	=>	"提现配置", 
		"node"	=>	array( 
			"UserCarry_config"		=>	array("name"=>"提现手续费配置","module"=>"UserCarry","action"=>"config"),
			"UserCarry_saveconfig"	=>	array("name"=>"更新提现手续费配置","module"=>"UserCarry","action"=>"saveconfig"),
			"Bank_index"			=>	array("name"=>"提现银行设置","module"=>"Bank","action"=>"index"),
			"Bank_add"				=>	array("name"=>"添加提现银行","module"=>"Bank","action"=>"insert"),
			"Bank_edit"				=>	array("name"=>"编辑提现银行","module"=>"Bank","action"=>"update"),
			"Bank_delete"			=>	array("name"=>"删除提现银行","module"=>"Bank","action"=>"delete"),
						
		)
	),
	
	
	"Idcard,Collocation,Payment,ApiLogin,Integrate" =>	array(
		"name"	=>	"接口设置", 
		"node"	=>	array( 
		    "Idcard_index"    		=>  array("name"=>"实名认证接口","module"=>"Idcard","action"=>"index"),
			"Collocation_index"		=>	array("name"=>"资金托管","module"=>"Collocation","action"=>"index"),
			"Payment_index"			=>	array("name"=>"支付接口列表","module"=>"Payment","action"=>"index"),
			"Payment_insert"		=>	array("name"=>"支付接口安装保存","module"=>"Payment","action"=>"insert"),
			"Payment_update"		=>	array("name"=>"支付接口编辑执行","module"=>"Payment","action"=>"update"),
			"Payment_uninstall"		=>	array("name"=>"支付接口卸载","module"=>"Payment","action"=>"uninstall"),
			"ApiLogin_index"		=>	array("name"=>"会员第三方登录列表","module"=>"ApiLogin","action"=>"index"),
			"ApiLogin_insert"		=>	array("name"=>"会员第三方登录插件安装","module"=>"ApiLogin","action"=>"insert"),
			"ApiLogin_update"		=>	array("name"=>"会员第三方登录插件编辑","module"=>"ApiLogin","action"=>"update"),
			"ApiLogin_uninstall"	=>	array("name"=>"会员第三方登录插件卸载","module"=>"ApiLogin","action"=>"uninstall"),
			"Integrate_index"		=>	array("name"=>"会员整合插件","module"=>"Integrate","action"=>"index"),
			"Integrate_install"		=>	array("name"=>"会员整合插件安装","module"=>"Integrate","action"=>"install"),
			"Integrate_save"		=>	array("name"=>"会员整合插件保存","module"=>"Integrate","action"=>"save"),
			"Integrate_uninstall"	=>	array("name"=>"会员整合插件卸载","module"=>"Integrate","action"=>"uninstall"),
		)
	),
	
	
	"Role,Admin"	=>	array(
		"name"	=>	"系统管理员", 
		"node"	=>	array( 
			"Role_index"			=>	array("name"=>"角色管理","module"=>"Role","action"=>"index"),
			"Role_add"				=>	array("name"=>"添加","module"=>"Role","action"=>"add"),
			"Role_edit"				=>	array("name"=>"编辑","module"=>"Role","action"=>"edit"),
			"Role_set_effect"		=>	array("name"=>"设置生效","module"=>"Role","action"=>"set_effect"),
			"Role_delete"			=>	array("name"=>"删除","module"=>"Role","action"=>"delete"),
			"Role_trash"			=>	array("name"=>"回收站","module"=>"Role","action"=>"trash"),
			"Role_foreverdelete"	=>	array("name"=>"永久删除","module"=>"Role","action"=>"foreverdelete"),
			"Admin_index"			=>	array("name"=>"管理员列表","module"=>"Admin","action"=>"index"),
			"Admin_add"				=>	array("name"=>"添加","module"=>"Admin","action"=>"add"),
			"Admin_edit"			=>	array("name"=>"编辑","module"=>"Admin","action"=>"edit"),
			"Admin_edit"			=>	array("name"=>"删除","module"=>"Admin","action"=>"delete"),
			"Admin_set_default"		=>	array("name"=>"设置默认管理员","module"=>"Admin","action"=>"set_default"),
			"Admin_set_effect"		=>	array("name"=>"设置生效","module"=>"Admin","action"=>"set_effect"),	
			
		)
	),
    		    
	"MsgTemplate,MailServer,Sms,PromoteMsg"	=>	array(
		"name"	=>	"短信邮件管理", 
		"node"	=>	array( 
			"MsgTemplate_index"			=>	array("name"=>"消息模板管理","module"=>"MsgTemplate","action"=>"index"),
			"MsgTemplate_update"		=>	array("name"=>"保存","module"=>"MsgTemplate","action"=>"update"),
			"MsgTemplate_load_tpl"		=>	array("name"=>"载入对应模板","module"=>"MsgTemplate","action"=>"load_tpl"),
			"MailServer_index"			=>	array("name"=>"邮件服务器列表","module"=>"MailServer","action"=>"index"),
			"MailServer_insert"			=>	array("name"=>"添加执行","module"=>"MailServer","action"=>"insert"),
			"MailServer_update"			=>	array("name"=>"编辑执行","module"=>"MailServer","action"=>"update"),
			"MailServer_foreverdelete"	=>	array("name"=>"永久删除","module"=>"MailServer","action"=>"foreverdelete"),			
			"MailServer_send_demo"		=>	array("name"=>"发送测试邮件","module"=>"MailServer","action"=>"send_demo"),
			"MailServer_set_effect"		=>	array("name"=>"设置生效","module"=>"MailServer","action"=>"set_effect"),
			"PromoteMsg_mail_index"		=>	array("name"=>"邮件列表","module"=>"PromoteMsg","action"=>"mail_index"),
			"PromoteMsg_add_mail"		=>	array("name"=>"添加邮件页面","module"=>"PromoteMsg","action"=>"add_mail"),
			"PromoteMsg_add_sms"		=>	array("name"=>"添加短信页面","module"=>"PromoteMsg","action"=>"add_sms"),
			"PromoteMsg_edit_mail"		=>	array("name"=>"编辑邮件页面","module"=>"PromoteMsg","action"=>"edit_mail"),
			"PromoteMsg_edit_sms"		=>	array("name"=>"编辑短信页面","module"=>"PromoteMsg","action"=>"edit_sms"),
			"PromoteMsg_foreverdelete"	=>	array("name"=>"永久删除","module"=>"PromoteMsg","action"=>"foreverdelete"),
			"PromoteMsg_insert_mail"	=>	array("name"=>"添加邮件执行","module"=>"PromoteMsg","action"=>"insert_mail"),
			"PromoteMsg_insert_sms"		=>	array("name"=>"添加短信执行","module"=>"PromoteMsg","action"=>"insert_sms"),
			"PromoteMsg_sms_index"		=>	array("name"=>"短信列表","module"=>"PromoteMsg","action"=>"sms_index"),
			"PromoteMsg_update_mail"	=>	array("name"=>"编辑邮件执行","module"=>"PromoteMsg","action"=>"update_mail"),
			"PromoteMsg_update_sms"		=>	array("name"=>"编辑短信执行","module"=>"PromoteMsg","action"=>"update_sms"),
			"Sms_index"					=>	array("name"=>"短信接口列表","module"=>"Sms","action"=>"index"),
			"Sms_insert"				=>	array("name"=>"安装保存","module"=>"Sms","action"=>"insert"),
			"Sms_update"				=>	array("name"=>"编辑执行","module"=>"Sms","action"=>"update"),
			"Sms_uninstall"				=>	array("name"=>"卸载","module"=>"Sms","action"=>"uninstall"),
			"Sms_send_demo"				=>	array("name"=>"发送测试短信","module"=>"Sms","action"=>"send_demo"),
			"Sms_set_effect"			=>	array("name"=>"设置生效","module"=>"Sms","action"=>"set_effect"),
			
		)
	),
	
	
		
	"DealMsgList，PromoteMsgList"	=>	array(
		"name"	=>	"队列管理", 
		"node"	=>	array( 
			"DealMsgList_index"				=>	array("name"=>"业务队列列表","module"=>"DealMsgList","action"=>"index"),
			"DealMsgList_send"				=>	array("name"=>"手动发送","module"=>"DealMsgList","action"=>"send"),
			"DealMsgList_show_content"		=>	array("name"=>"显示内容","module"=>"DealMsgList","action"=>"show_content"),
			"DealMsgList_foreverdelete"		=>	array("name"=>"永久删除","module"=>"DealMsgList","action"=>"foreverdelete"),
			"PromoteMsgList_index"			=>	array("name"=>"推广队列列表","module"=>"PromoteMsgList","action"=>"index"),
			"PromoteMsgList_foreverdelete"	=>	array("name"=>"永久删除","module"=>"PromoteMsgList","action"=>"foreverdelete"),
			"PromoteMsgList_send"			=>	array("name"=>"手动发送","module"=>"PromoteMsgList","action"=>"send"),
			"PromoteMsgList_show_content"	=>	array("name"=>"显示内容","module"=>"PromoteMsgList","action"=>"show_content"),
			
		)
	),
	
	"Database"	=>	array(
		"name"	=>	"数据库", 
		"node"	=>	array( 
			"delete"	=>	array("name"=>"删除备份","module"=>"Database","action"=>"delete"),
			"dump"		=>	array("name"=>"备份数据","module"=>"Database","action"=>"dump"),
			"execute"	=>	array("name"=>"执行SQL语句","module"=>"Database","action"=>"execute"),
			"index"		=>	array("name"=>"数据库备份列表","module"=>"Database","action"=>"index"),
			"restore"	=>	array("name"=>"恢复备份","module"=>"Database","action"=>"restore"),
			"sql"		=>	array("name"=>"SQL操作","module"=>"Database","action"=>"sql"),
		)
	),
	
	
	"Ecv"	=>	array(
		"name"	=>	"代金券管理", 
		"node"	=>	array( 
			"index"	=>	array("name"=>"代金券列表","action"=>"index"),
			"export_csv"	=>	array("name"=>"代金券导出","action"=>"export_csv"),
			"foreverdelete"	=>	array("name"=>"永久删除","action"=>"foreverdelete"),
		)
	),
	
	
	"Cache"	=>	array(
		"name"	=>	"缓存处理", 
		"node"	=>	array( 
			"clear_admin"		=>	array("name"=>"清空后台缓存","module"=>"Cache","action"=>"clear_admin"),
			"clear_data"		=>	array("name"=>"清空数据缓存","module"=>"Cache","action"=>"clear_data"),
			"clear_image"		=>	array("name"=>"清空图片缓存","module"=>"Cache","action"=>"clear_image"),
			"clear_parse_file"	=>	array("name"=>"清空脚本样式缓存","module"=>"Cache","action"=>"clear_parse_file"),
			"index"				=>	array("name"=>"缓存处理","module"=>"Cache","action"=>"index"),
		)
	),
	
	
	"File"	=>	array(
		"name"	=>	"文件管理", 
		"node"	=>	array( 
			"deleteImg"		=>	array("name"=>"删除图片","module"=>"File","action"=>"deleteImg"),
			"do_upload"		=>	array("name"=>"编辑器图片上传","module"=>"File","action"=>"do_upload"),
			"do_upload_img"	=>	array("name"=>"图片控件上传","module"=>"File","action"=>"do_upload_img"),
		)
	),
	
		
);
?>