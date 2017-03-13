$(document).on("pageInit", "#uc_learn", function(e, id, page) {
    var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#uc_learn .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_learn .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#uc_learn .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.p  =  now_page + 1;
			query.is_ajax = 1;
			query = get_url_params(query);
			delete query.ctl;		
		    $.ajax({
		      	url:$("#uc_learn .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;

		        	$("#uc_learn .infinite-scroll .invest").append(result);
					$("#uc_learn .infinite-scroll .top_menu_list").last().remove();
		        	$("#uc_learn .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#uc_learn .infinite-scroll-preloader").hide();
		        	$.refreshScroller();
				}
		    });
	    });
							
			$("#uc_learn .j_do_interest").click(function(){
				if(confirm("确定领取收益？"))
				{
				var ajaxurl = WAP_PATH+'/index.php?ctl=uc_learn_do_interest';
				var query = newObject();
				query.post_type = "json";
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"Post",
					dataType:"json",
					success:function(data){
						$.alert(data.show_err,function(){
							reloadpage(WAP_PATH+'/index.php?ctl=uc_learn','#uc_learn','.content');
							RouterURL(WAP_PATH+'/index.php?ctl=uc_learn','#uc_learn');
						});
					}
				
					});
				}	
		});
		
		
		$("#uc_learn .J_do_invest").click(function(){
			$.confirm("确定投资？",function(){
				var ajaxurl = WAP_PATH+'/index.php?ctl=uc_learn_do_invest';
				var learn_id =  $.trim($("#learn_id").val());
				var money =  $.trim($("#money").val());
				var query = newObject();
				query.learn_id = $.trim($("#learn_id").val());
				query.money = $.trim($("#money").val());
				query.post_type = "json";
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"Post",
					dataType:"json",
					success:function(data){
						$.alert(data.show_err,function(){
							reloadpage(WAP_PATH+'/index.php?ctl=uc_learn&t=load','#uc_learn','.content');
							RouterURL(WAP_PATH+'/index.php?ctl=uc_learn&t=load','#uc_learn');
						});					
					}
				
				});
			},function(){
		    	return false;
		    });	

		});
});	


$(document).on("pageInit", "#uc_learn_deal_invest", function(e, id, page) {
	
	var now_page = 1;
	var all_page = 0;
	var loading = false;
	$("#uc_learn_deal_invest .infinite-scroll-preloader").hide();
	$(page).on('infinite', function() {
		all_page=$("#uc_learn_deal_invest .detail_content").attr("all_page");
		now_page = $("#uc_learn_deal_invest .detail_content").attr("now_page");
		now_page = parseInt(now_page);
	   	if (loading || now_page >= all_page) return;
		$("#uc_learn_deal_invest .infinite-scroll-preloader").show();
	    loading = true;
		var query = newObject();
		query.page  =  now_page + 1;
		query.is_ajax = 1;
		
		var parms = get_search_parms();
		var ajaxurl = $("#uc_learn_deal_invest .infinite-scroll").attr("ajaxurl");
	    $.ajax({
	      	url:ajaxurl + parms,
	        data:query,
	        success:function(result){
	        	now_page ++;
				$("#uc_learn_deal_invest .detail_content").attr("now_page",now_page);
	        	loading = false;
	        	$("#uc_learn_deal_invest .infinite-scroll .detail_content").append(result);
	        	$("#uc_learn_deal_invest .infinite-scroll-preloader").hide();
	        	$.refreshScroller();
	        }
	      });
       
    });
	
	$("#uc_learn_deal_invest .tz_link_btn").click(function(){
	   
	    var ajaxurl = WAP_PATH+'/index.php?ctl=uc_learn_bid';
		var id = $(this).attr("data-id");
	    var query = newObject();
	    query.id = $(this).attr("data-id");
		
	    query.post_type = "json";
	    $.ajax({
	        url:ajaxurl,
	        data:query,
	        type:"Post",
	        dataType:"json",
	        success:function(data){
	            if(data.status == 0){
	               $.alert(data.show_err);
	               reploadpage(WAP_PATH+'/index.php?ctl=uc_learn_deal_invest',"#uc_learn_deal_invest",'.content');
	               RouterURL(WAP_PATH+'/index.php?ctl=uc_learn_deal_invest',"#uc_learn_deal_invest");
	            }else{
	                RouterURL(WAP_PATH+'/index.php?ctl=uc_learn_bid&id='+id,"#uc_learn_bid",2);
	            }
	            
	        }
	    
	    });
	    
	});
});

$(document).on("pageInit", "#uc_learn_bid", function(e, id, page) {
	$("#uc_learn_bid #submitt").click(function(){
	    var ajaxurl = WAP_PATH+'/index.php?ctl=uc_learn_dobid';
		var deal_id =  $.trim($("#uc_learn_bid #deal_id").val());
	    var learn_id =  $.trim($("#uc_learn_bid #learn_id").val());
	    var bid_money =  $.trim($("#uc_learn_bid #bid_money").val());
	    var bid_paypassword =  $.trim($("#uc_learn_bid #bid_paypassword").val());
	    var query = newObject();
	    query.deal_id = deal_id;
	    query.learn_id = learn_id;
	    query.bid_paypassword = bid_paypassword;
	    query.bid_money = bid_money;
		
	    query.post_type = "json";
	    $.ajax({
	        url:ajaxurl,
	        data:query,
	        type:"Post",
	        dataType:"json",
	        success:function(data){
	            $.alert(data.show_err);
				if(data.status==1){
					RouterURL(WAP_PATH+'/index.php?ctl=uc_invest','#uc_invest',2);
				}else{
					
					reloadpage(WAP_PATH+'/index.php?ctl=uc_learn_bid&id='+deal_id,"#uc_learn_bid",".content");
					RouterURL(WAP_PATH+'/index.php?ctl=uc_learn_bid&id='+deal_id,'#uc_learn_bid');
				}
	           
	        }
	    
	    });
	      
	    $(this).parents(".float_block").hide();
	});
});