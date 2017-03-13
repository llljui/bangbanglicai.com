$(document).ready(function(){
/*转让列表页*/
	$(document).on("pageInit", "#transfer", function(e, id, page) {
		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#transfer .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#transfer .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#transfer .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.page  =  now_page + 1;
			query.is_ajax = 1;
		    $.ajax({
		      	url:$("#transfer .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;
		        	$("#transfer .infinite-scroll .transfer").append(result);
		        	$("#transfer .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#transfer .infinite-scroll-preloader").hide();
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
		    $.ajax({
		    	url:$("#transfer .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page=1;
		        	loading =false;
		       	 	$content.find('.transfer').html(result);
		       	 	$("#transfer .infinite-scroll").attr("all_page",$("#transfer .pull-to-refresh-layer").attr("all_page"));
	       			$.pullToRefreshDone($content);
		       	}
		     });
	    });
	});
	
	/*转让详情页*/
	$(document).on("pageInit", "#transfer_show", function(e, id, page) {
		$("#pay_transfer").click(function(){
			var ajaxurl = WAP_PATH + '/index.php?ctl=transfer_dobid';
			var query = newObject();
			
			query.id = $.trim($("#trans_id").val());
			query.paypassword = $.trim($("#pay_password").val());
			
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					if(data.status == 2){
						RouterURL(data.app_url,'none',1);
					}else{
						$.alert(data.show_err);
						if(data.response_code == 1){
							RouterURL(WAP_PATH + '/index.php?ctl=transfer','#transfer');
						}
					}
				}
			});
		});
	});
});  