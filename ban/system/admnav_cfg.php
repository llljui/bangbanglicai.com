<?php 
return array( 
	"index"	=>	array(
		"name"	=>	"首页", 
		"key"	=>	"index", 
		"groups"	=>	array( 
			"index"	=>	array(
				"name"	=>	"首页", 
				"key"	=>	"index", 
				"nodes"	=>	array( 
					array("name"=>"快速导航","module"=>"Index","action"=>"main"),
					array("name"=>"网站数据统计","module"=>"Index","action"=>"statistics"),
					array("name"=>"借款统计","module"=>"Statistics","action"=>"index"),
				),
			),
			"syslog"	=>	array(
				"name"	=>	"系统日志", 
				"key"	=>	"syslog", 
				"nodes"	=>	array( 
					array("name"=>"系统日志列表","module"=>"Log","action"=>"index"),
				),
			),
		),
	),
	"deal"	=>	array(
		"name"	=>	"借贷管理", 
		"key"	=>	"deal", 
		"groups"	=>	array( 
			
			"deal_c"	=>	array(
			"name"	=>	"借贷审核管理", 
			"key"	=>	"deal_s", 
			"nodes"	=>	array( 
					array("name"=>"待审核列表","module"=>"Deal","action"=>"publish"),
					array("name"=>"复审核列表","module"=>"Deal","action"=>"true_publish"),
					array("name"=>"满标待放款","module"=>"Deal","action"=>"full"),
					array("name"=>"留言式贷款","module"=>"DealMsgboard","action"=>"index"),
					/*array("name"=>"等材料贷款","module"=>"Deal","action"=>"wait"),*/
					
				),
			),
				
			"deal"	=>	array(
				"name"	=>	"贷款管理", 
				"key"	=>	"deal", 
				"nodes"	=>	array( 
					array("name"=>"全部贷款","module"=>"Deal","action"=>"index"),
					array("name"=>"预告中贷款","module"=>"Deal","action"=>"advance"),
					array("name"=>"新手专享贷款","module"=>"Deal","action"=>"news"),
					array("name"=>"还款中贷款","module"=>"Deal","action"=>"inrepay"),
					array("name"=>"已完成贷款","module"=>"Deal","action"=>"over"),
					array("name"=>"提前还贷款","module"=>"Deal","action"=>"penalty"),
					array("name"=>"未满标贷款","module"=>"Deal","action"=>"ing"),
					array("name"=>"过期的贷款","module"=>"Deal","action"=>"expire"),
					array("name"=>"流标的贷款","module"=>"Deal","action"=>"flow"),
					array("name"=>"贷款回收站","module"=>"Deal","action"=>"trash"),
				),
			),
			
			"deal_money"	=>	array(
			"name"	=>	"借贷记录", 
			"key"	=>	"deal_money", 
			"nodes"	=>	array(
					array("name"=>"待还款账单","module"=>"Deal","action"=>"three"),
					array("name"=>"逾期待收款","module"=>"Deal","action"=>"yuqi"),
					array("name"=>"网站垫付款","module"=>"Deal","action"=>"generation_repay"),
					array("name"=>"收款信息","module"=>"Deal","action"=>"user_loads_repay"),
				),
			),
			
			"loads"	=>	array(
			"name"	=>	"投标信息", 
			"key"	=>	"loads", 
			"nodes"	=>	array( 
					array("name"=>"所有投标","module"=>"Loads","action"=>"index"),
					array("name"=>"手动投标","module"=>"Loads","action"=>"hand"),
					array("name"=>"自动投标","module"=>"Loads","action"=>"auto"),
					array("name"=>"成功投标","module"=>"Loads","action"=>"success"),
					array("name"=>"失败投标","module"=>"Loads","action"=>"failed"),
				),
			),
			
			
			"transfer"	=>	array(
					"name"	=>	"债权转让",
					"key"	=>	"transfer",
					"nodes"	=>	array(
						array("name"=>"所有转让","module"=>"Transfer","action"=>"index"),
						array("name"=>"正在转让","module"=>"Transfer","action"=>"ing"),
						array("name"=>"成功转让","module"=>"Transfer","action"=>"success"),
						array("name"=>"撤销转让","module"=>"Transfer","action"=>"back"),
					),
			),
			
			
			"message"	=>	array(
					"name"	=>	"留言管理",
					"key"	=>	"message",
					"nodes"	=>	array(
							array("name"=>"留言列表","module"=>"Message","action"=>"index"),
					),
			),
			
			
			
//			"deal_s"	=>	array(
//			"name"	=>	"满标管理", 
//			"key"	=>	"deal_s", 
//			"nodes"	=>	array( 
//					
//					
//				),
//			),
//			
//			
//            "deal_msgboard"=> array(
//                "name"  =>  "留言式贷款申请", 
//                "key"   =>  "deal_msgboard", 
//                "nodes" =>  array( 
//                    array("name"=>"留言式贷款申请","module"=>"DealMsgboard","action"=>"index"),
//                ),
//            ),  
	
			
			/*	
			"uplan"	=>	array(
					"name"	=>	"U-计划",
					"key"	=>	"uplan",
					"nodes"	=>	array(
							array("name"=>"计划分类列表","module"=>"PlanCate","action"=>"index"),
							array("name"=>"计划列表","module"=>"Plan","action"=>"index"),
							//array("name"=>"计划参与列表","module"=>"PlanJoin","action"=>"index"),
					),
			),*/
				
			
		),
	),
	
	"licai" => array(
		"name" =>"理财管理",
		"key" =>"licai",
		"groups" =>array(
			
				
				"licai_audit_manage"	=>	array(
						"name"	=>	"理财审核管理",
						"key"	=>	"licai_audit_manage",
						"nodes"	=>	array(
								array("name"=>"待审核理财","module"=>"Licai","action"=>"verify"),
						),
				),
				
				"licai_manage"	=>	array(
					"name"	=>	"理财管理",
					"key"	=>	"licai_manage",
					"nodes"	=>	array(
							array("name"=>"理财列表","module"=>"Licai","action"=>"index"),
							array("name"=>"订单列表","module"=>"LicaiOrder","action"=>"order_list"),
					),
				),
				
				"licai_send"	=>	array(
					"name"	=>	"理财发放",
					"key"	=>	"licai_send",
					"nodes"	=>	array(
							array("name"=>"快到期","module"=>"LicaiNear","action"=>"index"),
							array("name"=>"已发放","module"=>"LicaiSend","action"=>"index"),
					),
				),
				
				"licai_redempte"	=>	array(
						"name"	=>	"赎回管理",
						"key"	=>	"licai_redempte",
						"nodes"	=>	array(
								array("name"=>"理财期赎回管理","module"=>"LicaiRedempte","action"=>"index"),
								array("name"=>"预热期赎回管理","module"=>"LicaiRedempte","action"=>"before_index"),
						),
				),
				
				"licai_advance"	=>	array(
						"name"	=>	"垫付单",
						"key"	=>	"licai_advance",
						"nodes"	=>	array(
							array("name"=>"垫付单管理","module"=>"LicaiAdvance","action"=>"index"),
						),
				),
			
				"licai_setting"	=>	array(
						"name"	=>	"理财配置",
						"key"	=>	"licai_setting",
						"nodes"	=>	array(
								/*array("name"=>"银行列表","module"=>"LicaiBank","action"=>"index"),*/
								array("name"=>"基金种类","module"=>"LicaiFundType","action"=>"index"),
								array("name"=>"基金品牌","module"=>"LicaiFundBrand","action"=>"index"),
								array("name"=>"个性推荐","module"=>"LicaiRecommend","action"=>"index"),
								array("name"=>"假日列表","module"=>"LicaiHoliday","action"=>"index"),
								array("name"=>"首页订单展示","module"=>"LicaiDealshow","action"=>"index"),
						),
				),
			
		),
	),
	
	"user"	=>	array(
			"name"	=>	"会员管理",
			"key"	=>	"user",
			"groups"	=>	array(
					"user"	=>	array(
							"name"	=>	"会员列表",
							"key"	=>	"user",
							"nodes"	=>	array(
									array("name"=>"个人会员","module"=>"User","action"=>"index"),
									array("name"=>"企业会员","module"=>"User","action"=>"company_index"),
									array("name"=>"建立关联","module"=>"CreateRelevance","action"=>"index"),
									array("name"=>"推广人列表","module"=>"PromotionHuman","action"=>"index"),
									
//									array("name"=>"会员黑名单","module"=>"User","action"=>"black"),
//									array("name"=>"会员回收站","module"=>"User","action"=>"trash"),
							),
					),
//					"company"	=>	array(
//							"name"	=>	"企业会员",
//							"key"	=>	"company",
//							"nodes"	=>	array(
//									
//									array("name"=>"会员黑名单","module"=>"User","action"=>"company_black"),
//									array("name"=>"会员回收站","module"=>"User","action"=>"company_trash"),
//							),
//					),
					
					"audit"	=>	array(
							"name"	=>	"会员审核",
							"key"	=>	"audit",
							"nodes"	=>	array(
									array("name"=>"个人会员审核","module"=>"User","action"=>"register"),
									array("name"=>"企业会员审核","module"=>"User","action"=>"company_register"),
							),
					),
					"credit"	=>	array(
					"name"	=>	"认证管理",
					"key"	=>	"credit",
					"nodes"	=>	array(
								array("name"=>"待审认证","module"=>"Credit","action"=>"user_wait"),
								array("name"=>"所有认证","module"=>"Credit","action"=>"user"),
//								array("name"=>"通过的认证","module"=>"Credit","action"=>"user_success"),
//								array("name"=>"失败的认证","module"=>"Credit","action"=>"user_bad"),
							),
					),
					
					
					"quotasubmit"	=>	array(
							"name"	=>	"额度申请",
							"key"	=>	"quotasubmit",
							"nodes"	=>	array(
									array("name"=>"授信额度申请","module"=>"DealQuotaSubmit","action"=>"index"),
									array("name"=>"信用额度申请","module"=>"QuotaSubmit","action"=>"index"),
									array("name"=>"续约申请","module"=>"GenerationRepaySubmit","action"=>"index"),
							),
					),
					"agencies"	=>	array(
							"name"	=>	"授权服务机构",
							"key"	=>	"agencies",
							"nodes"	=>	array(
									array("name"=>"授权服务机构","module"=>"User","action"=>"agencies_index"),
									array("name"=>"建立关联","module"=>"CreateRelevance_rebate","action"=>"index"),
									array("name"=>"返佣统计","module"=>"PromotionHuman_rebate","action"=>"index"),
//									array("name"=>"授权服务机构回收站","module"=>"User","action"=>"agencies_trash"),
							),
					),
					
					"other"	=>	array(
							"name"	=>	"信息快速查询",
							"key"	=>	"other",
							"nodes"	=>	array(
									array("name"=>"个人会员信息","module"=>"User","action"=>"info"),
									array("name"=>"企业会员信息","module"=>"User","action"=>"company_info"),
									array("name"=>"公司列表","module"=>"User","action"=>"company_manage"),
									array("name"=>"工作信息","module"=>"User","action"=>"work_manage"),
									array("name"=>"银行卡列表","module"=>"User","action"=>"bank_manage"),
							),
					),
					
					"agency"	=>	array(
							"name"	=>	"担保机构",
							"key"	=>	"agency",
							"nodes"	=>	array(
									array("name"=>"担保机构","module"=>"DealAgency","action"=>"index"),
//									array("name"=>"担保机构回收站","module"=>"DealAgency","action"=>"trash"),
							),
					),
					
					"privilege"	=>	array(
							"name"	=>	"VIP会员管理",
							"key"	=>	"privilege",
							"nodes"	=>	array(
									array("name"=>"VIP会员表","module"=>"VipPrivilege","action"=>"vip_user"),
									array("name"=>"VIP等级配置","module"=>"VipType","action"=>"index"),
//										array("name"=>"VIP等级回收站","module"=>"VipType","action"=>"vip_type_trash"),
									array("name"=>"VIP特权配置","module"=>"VipSetting","action"=>"index"),
									array("name"=>"VIP升级记录","module"=>"VipPrivilege","action"=>"vip_upgrade_record"),
									array("name"=>"VIP降级记录","module"=>"VipPrivilege","action"=>"vip_demotion_record"),
									array("name"=>"VIP购买日志","module"=>"VipPrivilege","action"=>"vip_buy_log"),
									
									array("name"=>"VIP奖励配置","module"=>"VipGift","action"=>"index"),
									
									array("name"=>"客服列表","module"=>"Customers","action"=>"index"),
									array("name"=>"客服回收站","module"=>"Customers","action"=>"trash"),
									array("name"=>"奖励发放列表","module"=>"VipGift","action"=>"vip_gift_record"),
									array("name"=>"福利发放列表","module"=>"VipWelfare","action"=>"given_record"),
									array("name"=>"积分兑换记录","module"=>"VipWelfare","action"=>"score_exchange"),
							),
						),
					
					"reportguy"	=>	array(
							"name"	=>	"举报管理",
							"key"	=>	"reportguy",
							"nodes"	=>	array(
									array("name"=>"举报列表","module"=>"Reportguy","action"=>"index"),
							),
					),
					
					"notice"	=>	array(
							"name"	=>	"站内消息",
							"key"	=>	"notice",
							"nodes"	=>	array(
									array("name"=>"消息群发","module"=>"MsgSystem","action"=>"index"),
									array("name"=>"消息列表","module"=>"MsgBox","action"=>"index"),
							),
					),
				
				

//					"userconfig"	=>	array(
//							"name"	=>	"相关配置",
//							"key"	=>	"userconfig",
//							"nodes"	=>	array(
//									array("name"=>"会员字段列表","module"=>"UserField","action"=>"index"),
//									//array("name"=>"会员组别列表","module"=>"UserGroup","action"=>"index"),
//									array("name"=>"信用等级列表","module"=>"UserLevel","action"=>"index"),
//							),
//					),
//					
//					
//					
//		
//						"reward"	=>	array(
//								"name"	=>	"投资奖励",
//								"key"	=>	"gift",
//								"nodes"	=>	array(
//										array("name"=>"奖励发放列表","module"=>"VipGift","action"=>"vip_gift_record"),
//										array("name"=>"礼品管理","module"=>"VipGift","action"=>"index"),
//										array("name"=>"非提现金管理","module"=>"VipRedEnvelope","action"=>"index"),
//
//								),
//						),
//		
//						"welfare"	=>	array(
//								"name"	=>	"节日福利",
//								"key"	=>	"welfare",
//								"nodes"	=>	array(
//										array("name"=>"节日积分表","module"=>"VipFestivals","action"=>"index"),
////										array("name"=>"节日积分回收站","module"=>"VipFestivals","action"=>"festivals_trash"),
//										array("name"=>"福利发放列表","module"=>"VipWelfare","action"=>"given_record"),
//										array("name"=>"积分兑现","module"=>"VipWelfare","action"=>"score_exchange"),
//										
//								),
//						),
					
			),
	),	
	
	
	"order"	=>	array(
			"name"	=>	"资金管理",
			"key"	=>	"order",
			"groups"	=>	array(
					
//					"ic_audit"	=>	array(
//							"name"	=>	"充值提现审核",
//							"key"	=>	"ic_audit",
//							"nodes"	=>	array(
//									
//									
//							),
//					),
					"order"	=>	array(
							"name"	=>	"充值管理",
							"key"	=>	"order",
							"nodes"	=>	array(
									array("name"=>"在线充值单","module"=>"PaymentNotice","action"=>"index"),									
									array("name"=>"在线充值日账单","module"=>"BankReconciliation","action"=>"index"),
									array("name"=>"线下充值单","module"=>"PaymentNotice","action"=>"online"),
							),
					),
					
					"usercarry"	=>	array(
							"name"	=>	"提现管理",
							"key"	=>	"usercarry",
							"nodes"	=>	array(
									array("name"=>"提现列表","module"=>"UserCarry","action"=>"index"),
									array("name"=>"提现待审核","module"=>"UserCarry","action"=>"wait"),
									array("name"=>"提现待付款","module"=>"UserCarry","action"=>"waitpay"),
//									array("name"=>"成功申请","module"=>"UserCarry","action"=>"success"),
//									array("name"=>"失败申请","module"=>"UserCarry","action"=>"failed"),
//									array("name"=>"会员撤销","module"=>"UserCarry","action"=>"reback"),
							),
					),
					
					"moneylog"=>array(
							"name"	=>	"资金日志",
							"key"	=>	"moneylog",
							"nodes"	=>	array(
									array("name"=>"会员资金日志","module"=>"User","action"=>"fund_management"),
									array("name"=>"网站收支","module"=>"Deal","action"=>"site_money"),
//									array("name"=>"保障金","module"=>"Security","action"=>"index"),
									array("name"=>"风险准备金","module"=>"ProvisionsRisk","action"=>"index"),
							),
					),
					
					"hand_operated"	=>	array(
							"name"	=>	"人工账户变更",
							"key"	=>	"hand_operated",
							"nodes"	=>	array(
									array("name"=>"充值","module"=>"User","action"=>"hand_recharge"),
									array("name"=>"扣款","module"=>"User","action"=>"hand_overdue"),
									array("name"=>"冻结资金","module"=>"User","action"=>"hand_freeze"),
									array("name"=>"变更信用积分","module"=>"User","action"=>"hand_integral"),
									array("name"=>"变更积分","module"=>"User","action"=>"hand_integrals"),
									array("name"=>"变更额度","module"=>"User","action"=>"hand_quota"),
							),
					),
					
					"ipslog"	=>	array(
							"name"	=>	"托管对账",
							"key"	=>	"ipslog",
							"nodes"	=>	array(
									array("name"=>"开户","module"=>"Ipslog","action"=>"create"),
									array("name"=>"标的登记","module"=>"Ipslog","action"=>"trade"),
									array("name"=>"投标记录","module"=>"Ipslog","action"=>"creditor"),
									//array("name"=>"担保方","module"=>"Ipslog","action"=>"guarantor"),
									array("name"=>"充值","module"=>"Ipslog","action"=>"recharge"),
									array("name"=>"提现","module"=>"Ipslog","action"=>"transfer"),
									array("name"=>"还款单","module"=>"IpsRelation","action"=>"repayment"),
									array("name"=>"回款单","module"=>"IpsRelation","action"=>"back_repayment"),
									array("name"=>"满标放款","module"=>"IpsFullscale","action"=>"index"),
									array("name"=>"债权转让","module"=>"IpsTransfer","action"=>"index"),
									//array("name"=>"担保收益","module"=>"IpsProfit","action"=>"index"),
							),				
					),
			),
	),
	
//	"routine" => array(
//		"name" => "待办事务",
//		"key"  => "routine",
//		"groups" => array(
//			
//			


//		)
//	),
	
	"statistics"	=>	array(
				"name"	=>	"数据统计",
				"key"	=>	"statistics",
				"groups"	=>	array(
						"borrow_statistics"	=>	array(
								"name"	=>	"投资统计",
								"key"	=>	"borrow_statistics",
								"nodes"	=>	array(
										array("name"=>"投资总统计","module"=>"StatisticsBorrow","action"=>"tender_total"),
										array("name"=>"投资人数","module"=>"StatisticsBorrow","action"=>"tender_usernum_total"),
										array("name"=>"投资金额","module"=>"StatisticsBorrow","action"=>"tender_account_total"),
										array("name"=>"标种投资","module"=>"StatisticsBorrow","action"=>"tender_borrow_type"),
										array("name"=>"已回款","module"=>"StatisticsBorrow","action"=>"tender_hasback_total"),
										array("name"=>"待收款","module"=>"StatisticsBorrow","action"=>"tender_tobe_receivables"),
										array("name"=>"投资排名","module"=>"StatisticsBorrow","action"=>"tender_rank_list"),
										array("name"=>"投资额比例","module"=>"StatisticsBorrow","action"=>"tender_account_ratio"),
								),
						),
		
						"loan_statistics"	=>	array(
								"name"	=>	"借款统计",
								"key"	=>	"loan_statistics",
								"nodes"	=>	array(
										array("name"=>"借款总统计","module"=>"StatisticsLoan","action"=>"loan_total"),
										array("name"=>"借款人数","module"=>"StatisticsLoan","action"=>"loan_usernum_total"),
										array("name"=>"借款金额","module"=>"StatisticsLoan","action"=>"loan_account_total"),
										array("name"=>"标种借款","module"=>"StatisticsLoan","action"=>"loan_borrow_type"),
										array("name"=>"已还款","module"=>"StatisticsLoan","action"=>"loan_hasback_total"),
										array("name"=>"待还款","module"=>"StatisticsLoan","action"=>"loan_tobe_receivables"),
										array("name"=>"逾期还款","module"=>"StatisticsLoan","action"=>"loan_repay_late_total"),

								),
						),
		
		
						"claims_statistics"	=>	array(
								"name"	=>	"债权统计",
								"key"	=>	"claims_statistics",
								"nodes"	=>	array(
										array("name"=>"债权转让","module"=>"StatisticsClaims","action"=>"change_account_total"),
								),
						),
							
						"website_statistics"	=>	array(
								"name"	=>	"平台统计",
								"key"	=>	"website_statistics",
								"nodes"	=>	array(
										array("name"=>"充值统计","module"=>"WebsiteStatistics","action"=>"website_recharge_total"),
										array("name"=>"提现统计","module"=>"WebsiteStatistics","action"=>"website_extraction_cash"),
										array("name"=>"用户统计","module"=>"WebsiteStatistics","action"=>"website_users_total"),
										array("name"=>"地域分布统计","module"=>"WebsiteStatistics","action"=>"website_region_pie"),
										array("name"=>"用户属性统计","module"=>"WebsiteStatistics","action"=>"website_age_group_total"),
										array("name"=>"网站垫付统计","module"=>"WebsiteStatistics","action"=>"website_advance_total"),
										array("name"=>"网站费用统计","module"=>"WebsiteStatistics","action"=>"website_cost_total"),
								),
						),
							
				),
		),
		
	
		
	"department"	=>	array(
		"name"	=>	"部门管理",
		"key"	=>	"department",
		"groups"	=>	array(
			"admin_manage"	=>	array(
				"name"	=>	"管理员管理",
				"key"	=>	"admin_manage",
				"nodes"	=>	array(
					array("name"=>"部门列表","module"=>"Departments","action"=>"index"),
//					array("name"=>"部门回收站","module"=>"Departments","action"=>"trash"),
					array("name"=>"部门成员","module"=>"MyManager","action"=>"index"),
					array("name"=>"待分配会员","module"=>"MyCustomer","action"=>"index"),
					array("name"=>"待分配借款标","module"=>"OverdueBillMonth","action"=>"unallocated_standard"),
					array("name"=>"所有借款标","module"=>"OverdueBillMonth","action"=>"all_loan"),
				),
			),
			"department_referrals"	=>	array(
					"name"	=>	"提成统计",
					"key"	=>	"member_bill",
					"nodes"	=>	array(
						array("name"=>"部门提成统计","module"=>"Departments","action"=>"referrals"),
						array("name"=>"成员提成统计","module"=>"MyManager","action"=>"referrals"),
					),
			),	
			"member_bill"	=>	array(
					"name"	=>	"我的会员",
					"key"	=>	"member_bill",
					"nodes"	=>	array(
							array("name"=>"我的会员列表","module"=>"MyMembership","action"=>"index"),
							array("name"=>"本月到期账单","module"=>"OverdueBillMonth","action"=>"index"),
							array("name"=>"逾期账单","module"=>"OverdueBillMonth","action"=>"overdue_bill"),
							array("name"=>"已还款账单","module"=>"OverdueBillMonth","action"=>"repayment_bill"),
							array("name"=>"还款中借款标","module"=>"OverdueBillMonth","action"=>"repayloan_scale"),
							array("name"=>"已完成借款标","module"=>"OverdueBillMonth","action"=>"completedloan_scale"),
							array("name"=>"已坏账借款标","module"=>"OverdueBillMonth","action"=>"badloan_scale"),
							array("name"=>"借款会员列表","module"=>"User","action"=>"borrowing_member"),
							array("name"=>"坏账会员列表","module"=>"User","action"=>"bad_member"),
						
					),
			),
				
				
		),
	),
		
	"front"	=>	array(
			"name"	=>	"前端设置",
			"key"	=>	"front",
			"groups"	=>	array(
				"article"	=>	array(
						"name"	=>	"文章管理",
						"key"	=>	"article",
						"nodes"	=>	array(
								array("name"=>"文章列表","module"=>"Article","action"=>"index"),
								array("name"=>"文章分类","module"=>"ArticleCate","action"=>"index"),
//								array("name"=>"文章回收站","module"=>"Article","action"=>"trash"),
						),
				),					
//				"articlecate"	=>	array(
//						"name"	=>	"文章分类",
//						"key"	=>	"articlecate",
//						"nodes"	=>	array(
//								array("name"=>"分类回收站","module"=>"ArticleCate","action"=>"trash"),
//						),
//				),
				"frontconfig"	=>	array(
						"name"	=>	"PC端设置",
						"key"	=>	"frontconfig",
						"nodes"	=>	array(
								array("name"=>"导航菜单","module"=>"Nav","action"=>"index"),
								array("name"=>"投票调查","module"=>"Vote","action"=>"index"),
								array("name"=>"广告设置","module"=>"Adv","action"=>"index"),
						),
				),
				
				"mobile"	=>	array(
						"name"	=>	"移动端设置", 
						"key"	=>	"mobile", 
						"nodes"	=>	array( 
							array("name"=>"移动端配置","module"=>"Conf","action"=>"mobile"),
							array("name"=>"广告列表","module"=>"MAdv","action"=>"index"),
						),
				),	
					
				"link"	=>	array(
						"name"	=>	"友情链接",
						"key"	=>	"link",
						"nodes"	=>	array(
								array("name"=>"友情链接分组","module"=>"LinkGroup","action"=>"index"),
								array("name"=>"友情链接列表","module"=>"Link","action"=>"index"),
						),
				),
			),
	),
	
	"weixin"	=>	array(
		"name"	=>	"微信平台设置", 
		"key"	=>	"weixin", 
		"groups"	=>	array( 
			"weixinconf"	=>	array(
				"name"	=>	"平台设置", 
				"key"	=>	"WeixinConf", 
				"nodes"	=>	array( 
					array("name"=>"第三方平台配置","module"=>"WeixinConf","action"=>"index"),
  				),
			),
			"weixininfo"	=>	array(
				"name"	=>	"微信配置", 
				"key"	=>	"weixininfo", 
				"nodes"	=>	array( 
					array("name"=>"账户管理","module"=>"WeixinInfo","action"=>"index"),
					array("name"=>"自定义菜单","module"=>"WeixinInfo","action"=>"nav_setting"),
					 
  				),
			),
		   "weixinreply"	=>	array(
				"name"	=>	"微信回复设置", 
				"key"	=>	"weixinreply", 
				"nodes"	=>	array( 
 					array("name"=>"默认回复设置","module"=>"WeixinReply","action"=>"index"),
 					array("name"=>"关注时回复","module"=>"WeixinReply","action"=>"onfocus"),
 					array("name"=>"文本回复","module"=>"WeixinReply","action"=>"txt"),
 					array("name"=>"图文回复","module"=>"WeixinReply","action"=>"news"),
 					array("name"=>"LBS回复","module"=>"WeixinReply","action"=>"lbs"),
   				),
			),
			
			
			"weixintemplate"	=>	array(
				"name"	=>	"微信模板设置", 
				"key"	=>	"weixintemplate", 
				"nodes"	=>	array(
 					array("name"=>"设置行业","module"=>"WeixinTemplate","action"=>"set_industry"),
 					array("name"=>"模板列表","module"=>"WeixinTemplate","action"=>"index"),
 					array("name"=>"模板消息队列","module"=>"WeixinTemplate","action"=>"msglist"),
    				),
			),
			
			"weixinuser"	=>	array(
				"name"	=>	"微信会员管理", 
				"key"	=>	"weixinuser", 
				"nodes"	=>	array(
 					array("name"=>"分组管理","module"=>"WeixinUser","action"=>"groups"),
 					array("name"=>"会员管理","module"=>"WeixinUser","action"=>"index"),
 					array("name"=>"普通消息群发","module"=>"WeixinUser","action"=>"message_send"),
 					array("name"=>"高级群发","module"=>"WeixinUser","action"=>"advanced"),
    				),
			),
 			
		),
	),
		
	"Marketing"	=>	array(
			"name"	=>	"营销推广",
			"key"	=>	"Marketing",
			"groups"	=>	array(
					
					"reward"	=>	array(
								"name"	=>	"会员奖励",
								"key"	=>	"reward",
								"nodes"	=>	array(
										array("name"=>"注册奖励","module"=>"Conf","action"=>"registered"),
										array("name"=>"签到奖励","module"=>"Conf","action"=>"signin"),
										

								),
						),
									
			
					"integral_mall"	=>	array(
							"name"	=>	"积分商城",
							"key"	=>	"integral_mall",
							"nodes"	=>	array(
									array("name"=>"积分商城","module"=>"Goods","action"=>"index"),
									//array("name"=>"商品类型","module"=>"GoodsType","action"=>"index"),
									array("name"=>"商品分类","module"=>"GoodsCate","action"=>"index"),
									array("name"=>"兑换商品","module"=>"GoodsOrder","action"=>"index"),
							),
					),
					
					"referral"	=>	array(
							"name"	=>	"会员邀请返利",
							"key"	=>	"referral",
							"nodes"	=>	array(
									array("name"=>"邀请返利列表","module"=>"Referrals","action"=>"index"),
									array("name"=>"会员邀请返利配置","module"=>"Conf","action"=>"referrals"),
							),
					),				
					"rebate"	=>	array(
							"name"	=>	"授权服务机构及返佣",
							"key"	=>	"referral",
							"nodes"	=>	array(
									array("name"=>"借款返佣列表","module"=>"Referrals_rebate","action"=>"borrow_index"),
									array("name"=>"投资返佣列表","module"=>"Referrals_rebate","action"=>"index"),
									array("name"=>"授权服务机构返佣设置","module"=>"Conf","action"=>"commossion"),
									
							),
					),
					"ecvtype"	=>	array(
							"name"	=>	"红包管理",
							"key"	=>	"ecvtype",
							"nodes"	=>	array(
									array("name"=>"红包类型","module"=>"EcvType","action"=>"index"),
							),
					),
					"learnlist"  =>  array(
			                "name"  =>  "体验金管理", 
			                "key"   =>  "learnlist", 
			                "nodes" =>  array( 
			                    array("name"=>"发放列表","module"=>"Learn","action"=>"learn_send_list"),
			                    array("name"=>"投资记录","module"=>"Learn","action"=>"learn_load"),
			                    array("name"=>"活动设置","module"=>"Learn","action"=>"activity_setting"),
			                    array("name"=>"理财产品列表","module"=>"Learn","action"=>"learn_list"),
			                ),
			           ),
					"interestrateType"	=>	array(
							"name"	=>	"加息券管理",
							"key"	=>	"interestrateType",
							"nodes"	=>	array(
									array("name"=>"加息券类型","module"=>"InterestrateType","action"=>"index"),
							),
					),
	
			),
	),
		

	
	"system"	=>	array(
		"name"	=>	"系统配置", 
		"key"	=>	"system", 
		"groups"	=>	array( 
			"sysconf"	=>	array(
				"name"	=>	"系统设置", 
				"key"	=>	"sysconf", 
				"nodes"	=>	array( 
					array("name"=>"系统配置","module"=>"Conf","action"=>"index"),
					//array("name"=>"QQ客服配置","module"=>"Conf","action"=>"qq"),
					
				),
			),
			
			"dealconfig"	=>	array(
				"name"	=>	"贷款设置", 
				"key"	=>	"dealconfig", 
				"nodes"	=>	array( 
					array("name"=>"贷款配置","module"=>"Conf","action"=>"loan"),
					array("name"=>"贷款分类设置","module"=>"DealCate","action"=>"index"),
					array("name"=>"贷款类型设置","module"=>"DealLoanType","action"=>"index"),
					array("name"=>"贷款城市设置","module"=>"City","action"=>"index"),
					array("name"=>"合同范本设置","module"=>"Contract","action"=>"index"),
				),
			),
			
			"userconfig"	=>	array(
				"name"	=>	"会员配置", 
				"key"	=>	"userconfig", 
				"nodes"	=>	array( 
						array("name"=>"会员配置","module"=>"Conf","action"=>"users"),
						array("name"=>"会员字段列表","module"=>"UserField","action"=>"index"),
						//array("name"=>"会员组别列表","module"=>"UserGroup","action"=>"index"),
						array("name"=>"认证类型设置","module"=>"Credit","action"=>"index"),
						array("name"=>"信用等级列表","module"=>"UserLevel","action"=>"index"),
				),
			),
			
			"carryconfig"	=>	array(
				"name"	=>	"提现配置", 
				"key"	=>	"carryconfig", 
				"nodes"	=>	array( 
						array("name"=>"提现手续费","module"=>"UserCarry","action"=>"config"),
						array("name"=>"提现银行设置","module"=>"Bank","action"=>"index"),
				),
			),
			
			/*"debitconfig"	=>	array(
				"name"	=>	"白条设置", 
				"key"	=>	"debitconfig", 
				"nodes"	=>	array( 
					array("name"=>"白条设置","module"=>"Debit","action"=>"index"),
				),
			),
			"dealshow"	=>	array(
					"name"	=>	"展示订单",
					"key"	=>	"message",
					"nodes"	=>	array(
							array("name"=>"首页展示订单","module"=>"DealShow","action"=>"index"),
					),
			),*/
			
			"interface"	=>	array(
					"name"	=>	"接口设置",
					"key"	=>	"interface",
					"nodes"	=>	array(
							array("name"=>"实名认证接口","module"=>"Idcard","action"=>"index"),
							array("name"=>"资金托管接口","module"=>"Collocation","action"=>"index"),
							array("name"=>"支付接口","module"=>"Payment","action"=>"index"),
							array("name"=>"会员第三方登录接口","module"=>"ApiLogin","action"=>"index"),
							array("name"=>"会员整合接口","module"=>"Integrate","action"=>"index"),
					),
			),
			
				
			"admin"	=>	array(
				"name"	=>	"系统管理员", 
				"key"	=>	"admin", 
				"nodes"	=>	array( 
					array("name"=>"角色管理","module"=>"Role","action"=>"index"),
					//array("name"=>"角色回收站","module"=>"Role","action"=>"trash"),
					array("name"=>"管理员列表","module"=>"Admin","action"=>"index"),
				),
			),
			"msg"	=>	array(
				"name"	=>	"短信邮件管理", 
				"key"	=>	"msg", 
				"nodes"	=>	array( 
					array("name"=>"消息模板管理","module"=>"MsgTemplate","action"=>"index"),
					array("name"=>"邮件服务器列表","module"=>"MailServer","action"=>"index"),
					array("name"=>"邮件列表","module"=>"PromoteMsg","action"=>"mail_index"),
					array("name"=>"短信接口列表","module"=>"Sms","action"=>"index"),
					array("name"=>"短信列表","module"=>"PromoteMsg","action"=>"sms_index"),
				),
			),
			"msglist"	=>	array(
				"name"	=>	"队列管理", 
				"key"	=>	"msglist", 
				"nodes"	=>	array( 
					array("name"=>"业务队列列表","module"=>"DealMsgList","action"=>"index"),
					array("name"=>"推广队列列表","module"=>"PromoteMsgList","action"=>"index"),
				),
			),
			"datebase"	=>	array(
				"name"	=>	"数据库", 
				"key"	=>	"datebase", 
				"nodes"	=>	array( 
					array("name"=>"数据库备份","module"=>"Database","action"=>"index"),
					array("name"=>"SQL操作","module"=>"Database","action"=>"sql"),
				),
			),
			
			
		),
	),
	
	
		
);
?>
