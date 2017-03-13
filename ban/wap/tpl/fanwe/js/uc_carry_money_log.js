$(document).on("pageInit", "#uc_carry_money_log", function(e, id, page) {
		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#uc_carry_money_log .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_carry_money_log .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#uc_carry_money_log .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.p  =  now_page + 1;
			query.is_ajax = 1;
			query = get_url_params(query);
			delete query.ctl;		
		    $.ajax({
		      	url:$("#uc_carry_money_log .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;

		        	$("#uc_carry_money_log .infinite-scroll .invest").append(result);
					$("#uc_carry_money_log .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#uc_carry_money_log .infinite-scroll-preloader").hide();
		        	$.refreshScroller();
				}
		    });
	    });	   		
		
		var $content = $(page).find(".content").on('refresh', function(e) {
	    	
	      	if (loading) return;
	      	loading =true;
	      	var query = newObject();
			query.page  =  1;
			query.is_ajax = 1;
			var parms = get_search_parms();
			var ajaxurl = $("#uc_carry_money_log .infinite-scroll").attr("ajaxurl");
		    $.ajax({
		    	url:ajaxurl + parms,
		        data:query,
		        success:function(result){
		        	now_page=1;
		        	loading =false;
		       	 	$content.find('.invest').html(result);
		       	 	$("#uc_carry_money_log .infinite-scroll").attr("all_page",$("#uc_carry_money_log .pull-to-refresh-layer").attr("all_page"));
	       			$.pullToRefreshDone($content);
		       	}
		     });
	    });
		
	 });