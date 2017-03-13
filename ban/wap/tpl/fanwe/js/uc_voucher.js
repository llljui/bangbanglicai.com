$(document).ready(function(){
	
	/*我的红包*/
	$(document).on("pageInit", "#uc_voucher", function(e, id, page) {
		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#uc_voucher .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_voucher .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#uc_voucher .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.page  =  now_page + 1;
			query.is_ajax = 1;
			
			var parms = get_search_parms();
			var ajaxurl = $("#uc_voucher .infinite-scroll").attr("ajaxurl");
		    $.ajax({				
		      	url:ajaxurl + parms,
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;
		        	$("#uc_voucher .infinite-scroll .invest").append(result);
		        	$("#uc_voucher .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#uc_voucher .infinite-scroll-preloader").hide();
		        	$.refreshScroller();
		        }
		      });
	       
	    });
	 });
	 $(document).on("pageInit", "#uc_voucher_exchange", function(e, id, page) {
	 	$(page).find(".exchange").bind("click",function(){
	 		var obj = $(this);
			$.confirm("确定要兑换吗？",function(){
				exchangeVoucher(obj);
			});	
		});		
		
		$(page).find("#sn_exchange").bind("submit",function(){
			if($.trim($(this).find("input[name='sn']").val())=="")
			{
				$.alert("请输入序列号");
				return false;
			}
			var ajaxurl = WAP_PATH+'/index.php?ctl=uc_voucher_do_snexchange';
			var query = newObject();
			query.sn = $.trim($(page).find("#sn").val());
			query.post_type = "json";
			$.showIndicator();
			$.ajax({ 
				url: ajaxurl,
				data:query,
				dataType: "json",
				type: "POST",
				success: function(data){
					$.hideIndicator();
					if(data.user_login_status==2){
						$.alert(data.show_err,function(){
							RouterURL(WAP_PATH+'/index.php?ctl=login','#login');
						});
					}else if(data.response_code==1){
						$.alert(data.show_err,function(){
							reloadpage(WAP_PATH+'/member.php?ctl=uc_voucher','#uc_voucher','.content',function(){
								RouterBack(WAP_PATH+'/member.php?ctl=uc_voucher',"#uc_voucher","#uc_voucher");
							});	
						});
					}else{
						$.alert(data.show_err);
					}
				},error:function(){
					$.toast("通信失败");
					$.hideIndicator();
				}	
			});		
			return false;
		});
	 });
});
	 

function exchangeVoucher(obj){	
		var ajaxurl = obj.attr("url");
		var query = newObject();
		query.id=obj.attr("data-id");
		query.post_type = "json";
		$.showIndicator();
		$.ajax({ 
			url: ajaxurl,
			data:query,
			type:"Post",
			dataType:"json",
			success: function(data){
				$.hideIndicator();
				if(data.user_login_status==2){
					$.alert(data.show_err,function(){
						RouterURL(WAP_PATH+'/index.php?ctl=login','#login');
					});
				}else if(data.response_code==1){
					$.alert(data.show_err,function(){
						reloadpage(WAP_PATH+'/member.php?ctl=uc_voucher','#uc_voucher','.content',function(){
							RouterBack(WAP_PATH+'/member.php?ctl=uc_voucher',"#uc_voucher","#uc_voucher");
						});	
					});
				}else{
					$.alert(data.show_err);
				}
			},error:function(){
				$.toast("通信失败");
				$.hideIndicator();
			}	
		});		

	}



