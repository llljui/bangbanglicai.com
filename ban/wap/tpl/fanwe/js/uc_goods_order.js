$(document).ready(function(){
	
	/*投资列表页*/
	$(document).on("pageInit", "#uc_goods_order", function(e, id, page) {
		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#uc_goods_order .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_goods_order .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#uc_goods_order .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.page  =  now_page + 1;
			query.is_ajax = 1;
			
			var parms = get_search_parms();
			var ajaxurl = $("#uc_goods_order .infinite-scroll").attr("ajaxurl");
		    $.ajax({				
		      	url:ajaxurl + parms,
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;
		        	$("#uc_goods_order .infinite-scroll .invest").append(result);
		        	$("#uc_goods_order .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#uc_goods_order .infinite-scroll-preloader").hide();
		        	$.refreshScroller();
		        }
		      });
	       
	    });
	    });
	   });
	 





