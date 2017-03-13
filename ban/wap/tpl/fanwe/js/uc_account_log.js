$(document).ready(function(){
	
	$(document).on("pageInit", "#uc_account_log", function(e, id, page) {
		var all_page = 0;
		var loading = false;
		$("#uc_account_log .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_account_log .uc_account_log-box").attr("all_page");
			now_page = $("#uc_account_log .content").attr("now_page");
			now_page = parseInt(now_page);
			if (loading || now_page >= all_page) return;
			$("#uc_account_log .infinite-scroll-preloader").show();
			loading = true;
			var query = newObject();
			query.p  =  now_page + 1;
			query.is_ajax = 1;
			query = get_url_params(query);
			delete query.ctl;		
			$.ajax({
				url:$("#uc_account_log .uc_account_log-box").attr("ajaxurl"),
				data:query,
				success:function(result){
					now_page ++;
					$("#uc_account_log .content").attr("now_page",now_page);
					loading = false;
					

					$("#uc_account_log .infinite-scroll .invest").append(result);
					$("#uc_account_log .infinite-scroll .pull-to-refresh-layer").last().remove();
					$("#uc_account_log .infinite-scroll-preloader").hide();
					$.refreshScroller();
				}
			});
		});	   								
	 });
});
 