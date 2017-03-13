$(document).ready(function(){
 	$(document).on("pageInit", "#uc_bank", function(e, id, page) {
	 	$("#uc_bank .delete").click(function(){
	 		var  id = $.trim($(this).attr("data-id"));
	 		$.confirm("确认要删除吗？",function() {
			   	var ajaxurl = WAP_PATH+'/member.php?ctl=uc_del_bank';
		 		var query = newObject();
		 		query.id = id;
		 		query.post_type = "json";
				$.ajax({
					url:ajaxurl,
		 			data:query,
		 			type:"Post",
		 			dataType:"json",
		 			success:function(data){
		 				$.alert(data.show_err,function(){
		 					reloadpage(WAP_PATH+'/member.php?ctl=uc_bank',"#uc_bank",".content",function(){
			 					RouterBack(WAP_PATH+'/member.php?ctl=uc_bank',"#uc_bank","#uc_bank");
			 				});
		 				});
		 				
		 			}
		 		});
			 },function() {
			   	return false;
			 });
	 		
	 	});
	 	
		$("#uc_bank button.uc_bank").click(function(){
			var ips_money = $("#uc_bank #ips_money").val();
			if(ips_money==0){
				$.alert("可用资金为0,不可进行提现！");
				return false;
			}
			
			var ajaxurl = WAP_PATH+'/member.php?ctl=uc_bank';
			var query = newObject();
			query.pTrdAmt = $.trim($("#uc_bank #pTrdAmt").val());
			
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					if(data.response_code == 1){
						window.location.href = data.dw_url;
					}
					
				}
			});
		});
	
	    $("#uc_bank #pTrdAmt").keyup(function(){
	    	setBkCarryResult();
	    });
	    $("#uc_bank #pTrdAmt").blur(function(){
	        setBkCarryResult();
	    });
    });
    
    $(document).on("pageInit", "#uc_add_bank", function(e, id, page) {
    	$("#uc_add_bank .seclet_but").click(function(){
			$("#uc_add_bank .bank_seclet").toggle();
		});
		$("#uc_add_bank .bank_seclet li").click(function(){
			var value=$(this).html();
			$("#uc_add_bank .this_bank").html(value);
			$(this).parent().hide();
			$(this).addClass("active").siblings().removeClass("active");
		});
		
		$.ajax({
			url:APP_ROOT+"/system/region.js",
			cache:true,
			success:function(result){
				eval(result);
				load_bk_select("1",regionConf);
				$("#uc_add_bank select[name='region_lv2']").live("change",function(){
					load_bk_select("2",regionConf);
				});
				$("#uc_add_bank select[name='region_lv3']").live("change",function(){
					load_bk_select("3",regionConf);
				});
			}
		});
		
		 
	 	$("#uc_add_bank #add_bank").click(function(){
	 		var ajaxurl = WAP_PATH+'/member.php?ctl=uc_save_bank';
	 		var query = newObject();
	 		
	 		query.bank_id = $("#uc_add_bank li.active input").val();
	 		query.bankzone = $.trim($("#uc_add_bank #bankzone").val());
	 		query.bankcard = $.trim($("#uc_add_bank #bankcard").val());
	 		query.region_lv1 = $.trim($("#uc_add_bank #region_lv1").val());
	 		query.region_lv2 = $.trim($("#uc_add_bank #region_lv2").val());
	 		query.region_lv3 = $.trim($("#uc_add_bank #region_lv3").val());
	 		query.region_lv4 = $.trim($("#uc_add_bank #region_lv4").val());
	 		query.post_type = "json";
	 		$.ajax({
	 			url:ajaxurl,
	 			data:query,
	 			type:"Post",
	 			dataType:"json",
	 			success:function(data){
	 				if(data.user_login_status==1){
		 				$.alert(data.show_err,function(){
		 					if(data.response_code==1){
				 				reloadpage(WAP_PATH+'/member.php?ctl=uc_bank','#uc_bank','.content',function(){
				 					RouterBack(WAP_PATH+'/member.php?ctl=uc_bank','#uc_bank','#uc_bank');
				 				});
			 				}
		 				});
		 				
	 				}
	 				else{
	 					RouterURL(WAP_PATH+'/member.php?ctl=login','#login');
	 				}
	 			}
	 		});
	 	});
    });
    
    $(document).on("pageInit", "#uc_carry_money", function(e, id, page) {
	    $("#uc_carry_money .seclet_but").click(function(){
			$("#uc_carry_money .bank_seclet").toggle();
		});
		$("#uc_carry_money .bank_seclet li").click(function(){
			var value=$(this).html();
			$("#uc_carry_money .this_bank").html(value);
			$(this).parent().hide();
			$(this).addClass("active").siblings().removeClass("active");
		});
		
	
	 	$("#uc_carry_money .but_this").click(function(){
			var paypassword = $.trim($("#uc_carry_money #paypassword").val());
	 		var ajaxurl = WAP_PATH+'/member.php?ctl=uc_save_carry';
	 		var query = newObject();
	 		query.bid = $.trim($("#uc_carry_money #band_id").val());
	 		query.amount = $.trim($("#uc_carry_money #Jcarry_amount").val());
	 		query.paypassword = $.trim($("#uc_carry_money #paypassword").val());
	 		query.post_type = "json";
			
	 		$.ajax({
	 			url:ajaxurl,
	 			data:query,
	 			type:"Post",
	 			dataType:"json",
	 			success:function(data){
	 				$.alert(data.show_err,function(){
	 					if(data.response_code==1){
							RouterURL(WAP_PATH+'/member.php?ctl=uc_carry_money_log','#uc_carry_money_log',2);
						}
	 				});
	 				
					
	 			}
	 		});
	 	});
		 
		
		$("#uc_carry_money #Jcarry_amount").blur(function(){
			setNrCarryResult();
		});
	}); 
	
});


function load_bk_select(lv,regionConf)
{
	var name = "region_lv"+lv;
	var next_name = "region_lv"+(parseInt(lv)+1);
	var id = $("#uc_add_bank select[name='"+name+"']").val();
	
	var x = "";
	if(lv==1){
		var evalStr="regionConf.r"+id+".c";
		x="省";
	}
	if(lv==2){
		var evalStr="regionConf.r"+$("#uc_add_bank select[name='region_lv1']").val()+".c.r"+id+".c";
		x="市";
	}
	if(lv==3){
		var evalStr="regionConf.r"+$("#uc_add_bank select[name='region_lv1']").val()+".c.r"+$("#uc_add_bank select[name='region_lv2']").val()+".c.r"+id+".c";
		x="区";
	}
	
	if(id==0)
	{
		var html = "<option value='0'>选择"+x+"</option>";
	}
	else
	{
		var regionConfs=eval(evalStr);
		evalStr+=".";
		var html = "<option value='0'>选择"+x+"</option>";
		for(var key in regionConfs)
		{
			html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
		}
	}
	$("#uc_add_bank select[name='"+next_name+"']").html(html);
	
	if(lv != 4)
	{
		load_bk_select(parseInt(lv)+1);
	}
}

 /*第一种形式 第二种形式 更换显示样式*/
function setBkTabS(sname,cursel,n){
	 for(i=1;i<=n;i++){
		  var menu=$("#uc_bank #"+sname+i);
		  var con=$("#uc_bank #con_"+sname+"_"+i);
		  if(i==cursel){
		  	con.show();
		  	menu.addClass("hover");
		  }
		  else{
		  	con.hide();
		  	menu.removeClass("hover");
		 }
	 }
}

function setBkCarryResult(){
   var json_fee = $("#uc_bank input[name='json_fee']").val();
   json_fee = $.parseJSON(json_fee);
    var carry_amount = 0;
    if ($.trim($("#uc_bank #pTrdAmt").val()).length > 0) {
        if ($("#uc_bank #pTrdAmt").val() == "-") {
            carry_amount = "-0";
        }
        else{
            carry_amount = parseFloat($("#pTrdAmt").val());
        }
    }
    carry_amount=$.trim($("#uc_bank #pTrdAmt").val());
    carry_amount = parseFloat(carry_amount);
    
    var fee = 0;
    var fee_type = 0;
    
    if(json_fee.length > 0){
        if(carry_amount >= json_fee[json_fee.length-1].max_price){
            fee = json_fee[json_fee.length-1].fee;
            fee_type = json_fee[json_fee.length-1].fee_type;
            
        }
        else{
            $.each(json_fee,function(n,data) {
                if(carry_amount >=data.min_price && carry_amount<=data.max_price) { 
                 fee = data.fee;
                 fee_type = data.fee_type;
                 
                }
            }); 
        }
    }
    
    fee = parseFloat(fee);
    if(fee_type == 1){
        fee = carry_amount * fee * 0.01;
    }
    
    $("#uc_bank #Jcarry_fee").html(fee+" 元");
}

function setNrCarryResult(){
	var json_fee = $("#uc_carry_money input[name='json_fee']").val();
  		json_fee = $.parseJSON(json_fee);
	var carry_amount = 0;
	var total_amount =  parseFloat($("#uc_carry_money input[name='money']").val());
	var nmc_amount =  parseFloat($("#uc_carry_money input[name='nmc_amount']").val());
	
	if ($.trim($("#uc_carry_money #Jcarry_amount").val()).length > 0) {
		if ($("#uc_carry_money #Jcarry_amount").val() == "-") {
			carry_amount = "-0";
		}
		else{
			carry_amount = parseFloat($("#uc_carry_money #Jcarry_amount").val());
		}
	}
			
	var fee = 10;
	carry_amount = parseFloat(carry_amount);
	var realAmount = carry_amount+fee;
	
	if(carry_amount < 0){
		$.alert("请输入正确金额");
		
	}
	else if(carry_amount > total_amount - nmc_amount){
		$.alert("您的可提现余额不足");
	}
	else if(carry_amount == 0){
		$.alert("取现最低只能是0.1元");
	}
	else{
		$("#uc_carry_money #Jcarry_balance").html("");
	}
	
	var fee = 0;
	var fee_type = 0;
	
	if(json_fee.length > 0){
		if(carry_amount >= json_fee[json_fee.length-1].max_price){
			fee = json_fee[json_fee.length-1].fee;
			fee_type = json_fee[json_fee.length-1].fee_type;
			
		}
		else{
			$.each(json_fee,function(n,data) {
				if(carry_amount >=data.min_price && carry_amount<=data.max_price) { 
	           	 fee = data.fee;
				 fee_type = data.fee_type;
				 
				}
	        }); 
		}
	}
	
	fee = parseFloat(fee);
	if(fee_type == 1){
		fee = carry_amount * fee * 0.01;
	}
	var nmc_amount = $.trim($("#uc_carry_money #Jcarry_nmc_amount").val());
	var carry_balance = total_amount-carry_amount-fee-nmc_amount;
	
	if(carry_balance<0){
		$.alert("您的账户余额不足");
	}
	
	$("#uc_carry_money #Jcarry_fee").html(fee+" 元");
	
	var realAmount = carry_amount+fee;
	$("#uc_carry_money #Jcarry_realAmount").html(realAmount+" 元");
	var acount_balance = total_amount-carry_amount-fee;
	$("#uc_carry_money #Jcarry_acount_balance_res").val(acount_balance);
	$("#uc_carry_money #Jcarry_acount_balance").html(acount_balance + nmc_amount+" 元");
}