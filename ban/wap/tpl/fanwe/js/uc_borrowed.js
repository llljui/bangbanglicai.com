$(document).ready(function(){
	
	$(document).on("pageInit", "#uc_inrepay_refund", function(e, id, page) {
		if($(page).find(".content").attr("status")=="0"){
			$.alert($(page).find(".content").attr("show_err"),function(){
				 RouterBack($(page).find(".content").attr("back_url"),$(page).find(".content").attr("back_page"),$(page).find(".content").attr("back_epage"));
			});
		}
		$("#uc_inrepay_refund .list_con .money[price]").each(function(i,o){
	        var price = $(o).attr("price");
	        var price_arr = price.split(".");
	        if(price_arr.length>1)
	        {
	            $(o).html("<h1>"+price_arr[0]+"</h1><h2>."+price_arr[1]+"</h2>");
	        }
	        else
	        {
	            $(o).html("<h1>"+price+"</h1>");
	        }
	    });
	    
	    
		$("#uc_inrepay_refund #submitt").click(function(){
			$.confirm("您确认要提前还款吗？",function(){
				var ajaxurl = WAP_PATH+'/index.php?ctl=uc_do_inrepay_refund';
				var query = newObject();
				query.id = $.trim($("#uc_inrepay_refund #deal_id").val());
				query.paypassword = $.trim($("#uc_inrepay_refund #pay_inmoney_password").val());
				query.post_type = "json";
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"Post",
					dataType:"json",
					success:function(data){
						if(data.user_login_status==0){
							RouterURL(WAP_PATH+'/index.php?ctl=login','#login');
						}
						else if(data.status==2){
							window.location.href = data.jump;
						}
						else if(data.status==1){
							reloadpage(WAP_PATH+'/index.php?ctl=uc_refund','#uc_refund');
						}
						else{
							$.alert(data.show_err);
						}
					}
				});
			});
			
		});
	});
	$(document).on("pageInit", "#uc_quick_refund", function(e, id, page) {
			
		$("#uc_quick_refund #submitt").click(function(){
			$.confirm("您确认要还款吗？",function(){
				var ajaxurl = WAP_PATH+'/index.php?ctl=uc_do_quick_refund';
				var deal_id =  $.trim($("#uc_quick_refund #deal_id").val());
				var paypassword =  $.trim($("#uc_quick_refund #paypassword").val());
				var query = newObject();
				query.ids = $.trim($("#uc_quick_refund #l_key").val());
				query.id = $.trim($("#uc_quick_refund #deal_id").val());
				query.paypassword = $.trim($("#uc_quick_refund #paypassword").val());
				
				query.post_type = "json";
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"Post",
					dataType:"json",
					success:function(data){
						$.alert(data.show_err);
						if(data.status == 2){
							window.location.href = data.app_url;
						}else{
							reloadpage(WAP_PATH+'/index.php?ctl=uc_quick_refund&id='+deal_id,'#uc_quick_refund','.content');
						}
							
					}
				
				});
			});
			
		});
   });        
   
   $(document).on("pageInit", "#uc_borrowed", function(e, id, page) {
		var all_page = 0;
		var loading = false;
		$("#uc_borrowed .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_borrowed .uc_borrowed-box").attr("all_page");
			now_page = $("#uc_borrowed .content").attr("now_page");
			now_page = parseInt(now_page);
			if (loading || now_page >= all_page) return;
			$("#uc_borrowed .infinite-scroll-preloader").show();
			loading = true;
			var query = newObject();
			query.p  =  now_page + 1;
			query.is_ajax = 1;
			query = get_url_params(query);
			delete query.ctl;		
			$.ajax({
				url:$("#uc_borrowed .uc_borrowed-box").attr("ajaxurl"),
				data:query,
				success:function(result){
					now_page ++;
					$("#uc_borrowed .content").attr("now_page",now_page);
					loading = false;
					

					$("#uc_borrowed .infinite-scroll .invest").append(result);
					$("#uc_borrowed .infinite-scroll .pull-to-refresh-layer").last().remove();
					$("#uc_borrowed .infinite-scroll-preloader").hide();
					$.refreshScroller();
				}
			});
		});	   								
	 });
});