$(document).ready(function(){
	
	$(document).on("pageInit", "#uc_transfer", function(e, id, page) {
		var all_page = 0;
		var loading = false;
		$("#uc_transfer .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_transfer .uc_transfer-box").attr("all_page");
			now_page = $("#uc_transfer .content").attr("now_page");
			now_page = parseInt(now_page);
			if (loading || now_page >= all_page) return;
			$("#uc_transfer .infinite-scroll-preloader").show();
			loading = true;
			var query = newObject();
			query.p  =  now_page + 1;
			query.is_ajax = 1;
			query = get_url_params(query);
			delete query.ctl;
			$.ajax({
				url:$("#uc_transfer .uc_transfer-box").attr("ajaxurl"),
				data:query,
				success:function(result){
					now_page ++;
					$("#uc_transfer .content").attr("now_page",now_page);
					loading = false;
					

					$("#uc_transfer .infinite-scroll .invest").append(result);
					$("#uc_transfer .infinite-scroll .pull-to-refresh-layer").last().remove();
					$("#uc_transfer .infinite-scroll-preloader").hide();
					$.refreshScroller();
				}
			});
		});	


		$("#uc_transfer .block").click(function(){			
				var ajaxurl = WAP_PATH + '/index.php?ctl=uc_do_reback';
				var query = newObject();
				query.dltid = $.trim($("#dltida").val());
				query.post_type = "json";
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"Post",
					dataType:"json",
					success:function(data){
						$.alert(data.show_err);
						if(data.response_code == 1){
							reloadpage(WAP_PATH+'/index.php?ctl=uc_transfer',"#uc_transfer",'.content',function(){
								$("#uc_transfer .content .infinite-scroll-preloader").hide();
							});
						}
					}
				});
			});		
	 });
	 
	 
	$(document).on("pageInit", "#uc_to_transfer", function(e, id, page) {
		$("#do_transfer").click(function(){
			
			var ajaxurl = WAP_PATH+'/index.php?ctl=uc_do_transfer';
			var query = newObject();
			query.dlid = $.trim($("#uc_to_transfer #dlid").val());
			query.dltid = $.trim($("#uc_to_transfer #dltid").val());
			query.transfer_money = $.trim($("#uc_to_transfer #transfer_money").val());
			query.paypassword = $.trim($("#uc_to_transfer #pass_word").val());
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					
					$.alert(data.show_err);
					
					if(data.response_code == 1){
						reloadpage(WAP_PATH+'/index.php?ctl=uc_transfer','#uc_transfer','.content',function(){
							RouterBack(WAP_PATH+'/index.php?ctl=uc_transfer','#uc_transfer','#uc_transfer');
						});
					}
				
				}
				
			});
			
		
		});
	});
});
 