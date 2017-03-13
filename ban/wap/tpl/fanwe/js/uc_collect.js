$(document).ready(function(){
$(document).on("pageInit", "#uc_collect", function(e, id, page) {
		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#uc_collect .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_collect .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#uc_collect .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.p  =  now_page + 1;
			query.is_ajax = 1;
			query = get_url_params(query);
			delete query.ctl;		
		    $.ajax({
		      	url:$("#uc_collect .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;

		        	$("#uc_collect .infinite-scroll .invest").append(result);
					$("#uc_collect .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#uc_collect .infinite-scroll-preloader").hide();
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
			var ajaxurl = $("#uc_collect .infinite-scroll").attr("ajaxurl");
		    $.ajax({
		    	url:ajaxurl + parms,
		        data:query,
		        success:function(result){
		        	now_page=1;
		        	loading =false;
		       	 	$content.find('.invest').html(result);
		       	 	$("#uc_collect .infinite-scroll").attr("all_page",$("#uc_collect .pull-to-refresh-layer").attr("all_page"));
	       			$.pullToRefreshDone($content);
		       	}
		     });
	    });
		
		$("#uc_collect .delete").click(function(){
			var obj = $(this);
			$.confirm("确认要取消关注吗？",function(){
				var ajaxurl = WAP_PATH + '/index.php?ctl=uc_del_collect';
				var query = newObject();
				query.id = $.trim(obj.attr("rel"));
				query.post_type = "json";
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"Post",
					dataType:"json",
					success:function(data){
						$.alert(data.show_err); 				
						reloadpage(WAP_PATH+'/index.php?ctl=uc_collect',"#uc_collect",'.content',function(){
							$("#uc_collect .content .infinite-scroll-preloader").hide();
						});
					}
				});
			});
		});
		
		
	 });		 
});