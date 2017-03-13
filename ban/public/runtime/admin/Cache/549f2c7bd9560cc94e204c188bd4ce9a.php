<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo conf("APP_NAME");?><?php echo l("ADMIN_PLATFORM");?></title>
<script type="text/javascript" src="__ROOT__/public/runtime/admin/lang.js"></script>
<script type="text/javascript">
	var version = '<?php echo app_conf("DB_VERSION");?>';
	var VAR_MODULE = "<?php echo conf("VAR_MODULE");?>";
	var VAR_ACTION = "<?php echo conf("VAR_ACTION");?>";
	var ROOT = '__APP__';
	var ROOT_PATH = '<?php echo APP_ROOT; ?>';
	var app_type = '<?php echo ($apptype); ?>';
	var ofc_swf = '__TMPL__Common/js/open-flash-chart.swf';
	var sale_line_data_url = '<?php echo urlencode(u("Ofc/sale_line"));?>';
	var sale_refund_data_url = '<?php echo urlencode(u("Ofc/sale_refund"));?>';
</script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/style.css" />
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/main.css" />
<script type="text/javascript" src="__TMPL__Common/js/swfobject.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/main.js"></script>
</head>

<body>


<!--
	新版开始
-->
<div class="main">
	<div class="main_title">
		<!--<?php echo conf("APP_NAME");?><?php echo l("ADMIN_PLATFORM");?> <?php echo L("HOME");?>--> 
		<table width=100%>
			<tr>
				<td align=left><?php echo ($greet); ?>，<?php echo ($adm_session["adm_name"]); ?></td>
				<td align=right>你的最后登录时间为:<?php echo ($adm_session["login_time"]); ?><?php echo ($login_time); ?></td>
			</tr>
		</table>
	</div>
	<div class="notify_box">
		<table>
			<tr>
				<td class="statbox tuan_box">
					<table>
						<tr>
							<th>借贷审核</th>
						</tr>
						<tr>
							<td>
								<div class="row">
		                    		<span class="t">待审核标：</span><span class="bx"><a href="<?php echo u("Deal/publish");?>" <?php if($wait_deal_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($wait_deal_count); ?></a>&nbsp;</span>
									<div class="blank0"></div>
								</div>
								<div class="row">
                                    <span class="t">复审核标：</span><span class="bx"><a href="<?php echo u("Deal/true_publish");?>" <?php if($wait_deal_review_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($wait_deal_review_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
								
								<div class="row">
		                    		<span class="t">待放款标：</span><span class="bx"><a href="<?php echo u("Deal/full");?>" <?php if($suc_deal_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($suc_deal_count); ?></a></span>
									<div class="blank0"></div>
								</div>
					            <div class="row">
                                    <span class="t">待审核留言标：</span><span class="bx"><a href="<?php echo u("DealMsgboard/index");?>" <?php if($msgboard_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($msgboard_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>   
							</td>
						</tr>
					</table>
				</td>
				<!--
				<td class="statbox tuan_box">
                    <table>
                        <tr>
                            <th>理财审核</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">待审核标：</span>
									<span class="bx">
                                        <a href="<?php echo u("Licai/verify");?>" <?php if($licai_verify_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_verify_count); ?></a>
                                    </span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                	<span class="t">快到期：</span>
                                    <span class="bx">
                                        <a href="<?php echo u("LicaiNear/index");?>" <?php if($licai_near_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_near_count); ?></a>
                                    </span>
                                    <div class="blank0"></div>
                                </div>
                    
                            </td>
                        </tr>
                    </table>
                </td>-->
				<td class="statbox tuan_box">
					<table>
						<tr>
							<th>额度申请</th>
						</tr>
						<tr>
							<td>
								<div class="row">
                                    <span class="t">授信额度申请：</span><span class="bx"><a href="<?php echo u("DealQuotaSubmit/index",array("status"=>0));?>" <?php if($deal_quota_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($deal_quota_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
		                        <div class="row">
                                    <span class="t">信用额度申请：</span><span class="bx"><a href="<?php echo u("QuotaSubmit/index",array("status"=>0));?>" <?php if($quota_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($quota_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
		                       <div class="row">
                                    <span class="t">续约申请：</span><span class="bx"><a href="<?php echo u("GenerationRepaySubmit/index");?>" <?php if($generation_repay_submit == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($generation_repay_submit); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
						
							</td>
						</tr>
					</table>
				</td>
				
				<td class="statbox tuan_box">
					<table>
						<tr>
							<th>会员审核</th>
						</tr>
						<tr>
							<td>
				                <div class="row">
				                	<span class="t">待审核认证:</span><span class="bx"><a href="<?php echo u("Credit/user_wait");?>" <?php if($auth_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($auth_count); ?></a></span>
									<div class="blank0"></div>
								</div>
								<div class="row">
					                <span class="t">个人会员审核：</span>
									<span class="bx"><a href="<?php echo u("User/register");?>" <?php if($register_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($register_count); ?></a></span>
									<div class="blank0"></div>
								</div>
								<div class="row">
					                <span class="t">企业会员审核：</span>
									<span class="bx"><a href="<?php echo u("User/company_register");?>" <?php if($company_register_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($company_register_count); ?></a></span>
									<div class="blank0"></div>
								</div>
							</td>
						</tr>
					</table>
				</td>
			
			</tr>
			
			<tr>
                
                <td class="statbox tuan_box">
                    <table>
                        <tr>
                            <th>逾期借贷</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">逾期待收款：</span>
									<span class="bx"><a href="<?php echo u("Deal/yuqi");?>" <?php if($yq_repay_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($yq_repay_count); ?></a>&nbsp;</span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">网站垫付款：</span><span class="bx"><a href="<?php echo u("Deal/generation_repay");?>" <?php if($website_pay_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($website_pay_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                    
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="statbox tuan_box">
                    <table>
                        <tr>
                            <th>充值提现</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">线下充值单：</span>
                                    <span class="bx">
                                        <a href="<?php echo u("PaymentNotice/online");?>" <?php if($offline_pay_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($offline_pay_count); ?></a>
                                    </span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">提现待审核：</span>
                                    <span class="bx"><a href="<?php echo u("UserCarry/index",array("status"=>0));?>" <?php if($carry_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($carry_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">提现待付款：</span>
                                    <span class="bx">
                                        <a href="<?php echo u("UserCarry/waitpay");?>" <?php if($waitpay_carry_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($waitpay_carry_count); ?></a>
                                    </span>
                                    <div class="blank0"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="statbox tuan_box">
                    <table>
                        <tr>
                            <th>消息处理</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">待处理举报：</span>
									<span class="bx"><a href="<?php echo u("Reportguy/index",array("status"=>0));?>" <?php if($reportguy_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($reportguy_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">待回复留言：</span><span class="bx"><a href="<?php echo u("Message/index");?>" <?php if($noreply_message_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($noreply_message_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                        
                            </td>
                        </tr>
                    </table>
                </td>
            
            </tr>
			
            <tr>
                <?php if(C('LICAI_OPEN') == 1): ?><td class="statbox event_box">
					<table>
						<tr>
							<th>理财统计</th>
						</tr>
						<tr>
							<td>
								<div class="row">
				                	<span class="t">待审核理财：</span>
                                    <span class="bx">
                                    	<a href="<?php echo u("Licai/verify");?>" <?php if($licai_verify_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_verify_count); ?></a>
                                    </span>
									<div class="blank0"></div>
								</div>    
                                <div class="row">
				                	<span class="t">理财统计：</span>
                                    <span class="bx">
                                    	<a href="<?php echo u("Licai/index");?>" <?php if($licai_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_count); ?></a>
                                    </span>
									<div class="blank0"></div>
								</div>    
                                <div class="row">
				                	<span class="t">订单统计：</span>
                                    <span class="bx">
                                    	<a href="<?php echo u("LicaiOrder/order_list");?>" <?php if($licai_order_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_order_count); ?></a>
                                    </span>
									<div class="blank0"></div>
								</div>  
                                <div class="row">
				                	<span class="t">垫付单：</span>
                                    <span class="bx">
                                    	<a href="<?php echo u("LicaiAdvance/index");?>" <?php if($licai_advance_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_advance_count); ?></a>
                                    </span>
									<div class="blank0"></div>
								</div>                                 
							</td>
						</tr>
					</table>
				</td>
                <td class="statbox event_box">
					<table>
						<tr>
							<th>赎回统计</th>
						</tr>
						<tr>
							<td>
                            	

								<div class="row">
				                	<span class="t">理财期赎回：</span>
                                    <span class="bx">
                                    	<a href="<?php echo u("LicaiRedempte/index");?>" <?php if($licai_redempte_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_redempte_count); ?></a>
                                    </span>
									<div class="blank0"></div>
								</div>    
                                <div class="row">
				                	<span class="t">预热期赎回：</span>
                                    <span class="bx">
                                    	<a href="<?php echo u("LicaiRedempte/before_index");?>" <?php if($licai_wait_redempte_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_wait_redempte_count); ?></a>
                                    </span>
									<div class="blank0"></div>
								</div>                                    
							</td>
						</tr>
					</table>
				</td>
                <td class="statbox event_box">
					<table>
						<tr>
							<th>理财发放统计</th>
						</tr>
						<tr>
							<td>
                            	

								<div class="row">
				                	<span class="t">快到期：</span>
                                    <span class="bx">
                                    	<a href="<?php echo u("LicaiNear/index");?>" <?php if($licai_near_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_near_count); ?></a>
                                    </span>
									<div class="blank0"></div>
								</div>    
                                <div class="row">
				                	<span class="t">已发放：</span>
                                    <span class="bx">
                                    	<a href="<?php echo u("LicaiSend/index");?>" <?php if($licai_send_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($licai_send_count); ?></a>
                                    </span>
									<div class="blank0"></div>
								</div>                                    
							</td>
						</tr>
					</table>
				</td><?php endif; ?>
            </tr>
			<tr>
                <td align="center"  colspan ="4">
                    <div style="margin-bottom:-10px;padding-top:10px; font-size:20px; background-color:#BDDBEF; height:40px; ">
                        
                        <span class="bx"><?php echo to_date(get_gmtime()); ?></span> 数据实时指标
                    </div>
                    
                </td>
            </tr>
            <tr>
                
                <td class="statbox tuan_box">
                    <table>
                        <tr>
                            <th>会员统计</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">今日新个人会员：</span>
                                    <span class="bx"><a href="<?php echo u("User/index");?>" <?php if($total_today_user == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($total_today_user); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">今日新企业会员：</span>
                                    <span class="bx"><a href="<?php echo u("User/company_index");?>" <?php if($total_today_company_user == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($total_today_company_user); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                
                                <div class="row">
                                    <span class="t">会员总数：</span>
                                    <span class="bx"><a href="<?php echo u("User/index");?>" <?php if($total_user == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($total_user); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                    
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="statbox event_box">
                    <table>
                        <tr>
                            <th>借贷统计</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">筹款中标数：</span><span class="bx"><a href="<?php echo u("Deal/ing");?>" <?php if($fundraising_deal_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($fundraising_deal_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">债权转让中标数：</span><span class="bx"><a href="<?php echo u("Transfer/ing");?>" <?php if($transfer_deal_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($transfer_deal_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                
                            </td>
                        </tr>
                    </table>
                </td>
            
                <td class="statbox youhui_box">
                    <table>
                        <tr>
                            <th>资金统计</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">今日投资金额：</span><span class="bx"><a href="<?php echo u("Loads/index");?>" <?php if($today_invest_amount == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($today_invest_amount); ?><?php if($today_invest_amount == 0): ?>0<?php endif; ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">今日借款金额：</span><span class="bx"><a href="<?php echo u("Deal/index");?>" <?php if($today_borrow_amount == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($today_borrow_amount); ?><?php if($today_borrow_amount == 0): ?>0<?php endif; ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">今日债权转让：</span><span class="bx"><a href="<?php echo u("Transfer/index");?>" <?php if($today_transfer_amount == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($today_transfer_amount); ?><?php if($today_transfer_amount == 0): ?>0<?php endif; ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                
            </tr>
		</table>
	</div>	
	<div class="blank5"></div>
	<div class="blank5"></div>
	<div class="blank5"></div>
	<div class="blank5"></div>
	
	<div class="main_title">最近30天运营数据</div>
	<table width=100%>
		
		<tr>
			<td width=10>&nbsp;</td>
			<td width=90%>
				<div id="sale_line_data_chart"></div>
			</td>
			<td width=10>&nbsp;</td>
		</tr>
	</table>
	
    <script type="text/javascript">
	var nav_json_data = <?php echo json_encode($navs); ?>;
	loadModule();
	$("#J_nav").change(function(){
		loadModule();
	});
	$("#J_m").change(function(){
		loadAction();
	});
	function loadModule(){
		var nav =$("#J_nav").val();
		var html = "";
		$.each(nav_json_data,function(i,v){
			if(i==nav){
				$.each(v.groups,function(ii,vv){
					html += '<option value="'+ii+'">'+vv.name+'</option>';
				});
			}
		});
		
		$("#J_m").html(html);
		loadAction();
	}
	
	function loadAction(){
		var nav =$("#J_nav").val();
		var m =  $("#J_m").val();
		var a_html = '<option value="">请选择</option>';
		$.each(nav_json_data,function(i,v){
			if(i==nav){
				$.each(v.groups,function(ii,vv){
					if(ii==m){
						$.each(vv.nodes,function(iii,vvv){
							a_html += '<option value="'+vvv.action+'" module="'+vvv.module+'">'+vvv.name+'</option>';
						});
					}
				});
			}
		});
	
		$("#J_a").html(a_html);
	}
	
	$("#J_a").change(function(){
		if($.trim($(this).val())!=""){
			location.href = ROOT + '?m='+$(this).find("option:selected").attr("module")+'&a='+$(this).val();
		}
	})
</script>
</body>
</html>