$(document).ready(function(){
	
	/*投资列表页*/
	$(document).on("pageInit", "#deals", function(e, id, page) {

		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#deals .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#deals .infinite-scroll").attr("all_page");
			now_page = $("#deals .content").attr("now_page");
			now_page = parseInt(now_page);
		   	if (loading || now_page >= all_page) return;
			$("#deals .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.page  =  now_page + 1;
			query.is_ajax = 1;
			
			var parms = get_search_parms();
			var ajaxurl = $("#deals .infinite-scroll").attr("ajaxurl");
		    $.ajax({
		      	url:ajaxurl + parms,
		        data:query,
		        success:function(result){
		        	now_page ++;
					$("#deals .content").attr("now_page",now_page);
		        	loading = false;
		        	$("#deals .infinite-scroll .recommended_nav_2").append(result);
		        	$("#deals .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#deals .infinite-scroll-preloader").hide();
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
			var ajaxurl = $("#deals .infinite-scroll").attr("ajaxurl");
		    $.ajax({
		    	url:ajaxurl + parms,
		        data:query,
		        success:function(result){
		        	now_page=1;
					 $("#deals .content").attr("now_page",now_page);
		        	loading =false;
		       	 	$content.find('.invest').html(result);
		       	 	$("#deals .infinite-scroll").attr("all_page",$("#deals .pull-to-refresh-layer").attr("all_page"));
	       			$.pullToRefreshDone($content);
		       	}
		     });
	    });
	  });
	  
	 
	
	/*投资内容页*/
	$(document).on("pageInit", "#deal", function(e, id, page) {
		var is_submit_lock = false;
		var bid_paypassword = "";
		var bid_calculate = null;
		$("#deal #pay_deal").click(function(){

			if(is_submit_lock==true) return ;
			
			is_submit_lock = true;
			var ajaxurl = WAP_PATH+'/index.php?ctl=deal_dobid';
			var query = newObject();
			
			query.id = $.trim($("#deal #deal_id").val());
			query.bid_money = $.trim($("#deal #pay_inmoney").val());
			query.buy_number = $.trim($("#deal #buy_number").val());
			query.bid_paypassword = $.trim($("#deal #pay_inmoney_password").val());
			query.ecv_id = $.trim($("#deal #ecv_id").val());
			query.use_interestrate = $.trim($("#deal #use_interestrate").val());
			query.interestrate_id = $.trim($("#deal #interestrate_id").val());
		
			
			query.post_type = "json";
			
			$.showIndicator();
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.hideIndicator();
					is_submit_lock = false;
					if(data.status == 2){
						window.location.href = data.app_url;
					}else{
						$.alert(data.show_err);
						if(data.response_code == 1){
							RouterURL(WAP_PATH+'/index.php?ctl=uc_invest','#uc_invest',2);
						}
					}
					
				}
				,error:function(){
					$.hideIndicator();
					$.alert("通信失败");
				}
			});
		});
		
		
		$("#deal #use_interestrate").click(
			  function(){
			  if($("#deal #use_interestrate").is(':checked'))
			  {
				  $('#deal .interestrate_row').show();
				  $('#deal .ecv_row').hide();
			  }
			  else
			  {
				  $('#deal .interestrate_row').hide();
				  $('#deal .ecv_row').show();
			  }
		  });
		
		$("#deal a.c_number").click(function(){
			var rel=$(this).attr("rel");
			var obj = $(this);
			var number = parseInt($("#deal #pay_inmoney").val());
			switch(rel){
				case "-":
					if(number-1 > 1){
						$("#deal #pay_inmoney").val(number-1);
					}
					else{
						$("#deal #pay_inmoney").val(1);
					}
					break;
				case "+":
					var max_portion = $("#deal .c_number-box").attr("max");
					if(number+1 <= max_portion){
						$("#deal #pay_inmoney").val(number+1);
					}
					else{
						$("#deal #pay_inmoney").val(max_portion);
					}
					break;
			}
			
			
			
			loadDealSy();
		});
		
		
		$("#deal #pay_inmoney").change(function(){
			var max_portion = $("#deal .c_number-box").attr("max");
			if(max_portion > $(this).val())
				$("#deal #pay_inmoney").val(max_portion);
			loadDealSy();
		});
				
		loadDealSy();
		
		var changeProcess_act = null;
		var process_num = 0;
		changeProcess();
		function changeProcess(){
			clearTimeout(changeProcess_act);
			var ns = parseFloat($("#deal #detail_process").attr("data"));
			if(process_num ==ns){
				return;
			}
			if(process_num < ns){
				process_num = parseFloat(process_num) + 0.8;
			}
			if(process_num > ns){
				process_num =ns;
			}
			
			process_num =process_num.toFixed(2);
			if(process_num <= ns){
				$("#deal #detail_process .tip em").html(process_num+"%");
				$("#deal #detail_process .tip_bar").css({"padding-left":process_num+"%"});
				$("#deal #detail_process .pros").css({"width":process_num+"%"});
				changeProcess_act = setTimeout(changeProcess,1);
			}
		}
	});
	
	
	 /*投资搜索页*/
	$(document).on("pageInit", "#deals_search", function(e, id, page) {
		$("#deals_search .sideToggle_parent dl dd").click(function(){
			var index_value=$(this).parents("aside").attr("index-value");
			var data_type_name=$(this).attr("data-type-name");
			var data_type_value=$(this).attr("data-type-value");
			
			$(this).addClass("active").siblings().removeClass("active");
			$(this).find("i").html("&#xe61b;");
			$(this).siblings().find("i").html("&#xe63d;");
			$("#deals_search .deals_search_list li").eq(index_value).attr("data-type-name",data_type_name).attr("data-type-value",data_type_value);
			$("#deals_search .deals_search_list li").eq(index_value).find(".conditions").addClass("active").html(data_type_name);
			
			$("#deals_search .d_search_box .reset").css("display","block");
			
		});
		$("#deals_search .d_search_box .reset").click(function(){
			$("#deals_search .deals_search_list li").attr("data-type-name","不限").attr("data-type-value","0").find(".conditions").removeClass("active").html("不限");
			$("#deals_search .sideToggle_parent dl").each(function(){
				$(this).find("dd").eq(0).addClass("active").siblings().removeClass("active");
				$(this).find("dd").eq(0).find("i").html("&#xe61b;");
				$(this).find("dd").eq(0).siblings().find("i").html("&#xe63d;");
				$("#deals_search .d_search_box .reset").css("display","none");
			});
			
		});
		
		$("#deals_search .click_search").click(function(){
			
			var parms = get_search_parms();
			var url = WAP_PATH+"/index.php?ctl=deals"+parms;
			if($("#deals").length > 0){
				$.router.loadPage("#deals");
				reloadpage(url,"#deals",'.invest');
			}
			else{
				$.router.loadPage(url);
			}
		});
	});
	
	$(document).on("pageInit", "#deal_mobile", function(e, id, page) {
		
		$("#deal_mobile .leave_word").click(function(){
			$("#deal_mobile .Leave_Word").toggle();
			$("#deal_mobile .btn_leave_word").hide();
			
		});
		$("#deal_mobile .abolish").click(function(){
			$("#deal_mobile .Leave_Word").hide();
			$("#deal_mobile .btn_leave_word").show();
		});
		
		$("#deal_mobile #add_msg").click(function(){
			var ajaxurl = WAP_PATH+"/index.php?ctl=msg";
			var query = newObject();
			query.rel_table = $.trim($("#deal_mobile #rel_table").val());
			query.rel_id = $.trim($("#deal_mobile #rel_id").val());
			query.title = $.trim($("#deal_mobile #title").val());
			query.content = $.trim($("#deal_mobile #content").val());
			
			query.post_type = "json";
			$.showIndicator();
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.hideIndicator();
					if(data.user_login_status==0){
						RouterURL(WAP_PATH+"/index.php?ctl=login","#login");
					}
					else if(data.response_code == 0){
						$.alert(data.show_err);
					}
					else{
						$.alert(data.show_err);
						var html = '<li class="w_b bb1">';
			          		html += '<div class="img_block"><img src="'+data.avatar+'" style="width:100%; height:100%;"/></div>';
							html += '<div class="d_block w_b_f_1">';
							html += '	<div class="top clearfix">';
							html += '		<span class="name">'+data.user_name+'</span>';
							html += '		<span class="time">'+data.create_time+'</span>';
							html += '	</div>';
							html += '	<p class="middle">'+data.content+'</p>';
							html += '</div>';
			          		html += '</li>';
						$("#deal_mobile .contentblock ul").append(html);
						$("#deal_mobile .Leave_Word").hide();
						$("#deal_mobile .Leave_Word #content").val("");
						$("#deal_mobile .btn_leave_word").show();
					}
				}
				,error:function(){
					$.alert("通信失败");
					$.hideIndicator();
				}
			});
		});
	});
	
	
	$(document).on("pageInit", "#transfer_mobile", function(e, id, page) {
		
		$("#transfer_mobile .leave_word").click(function(){
			$("#transfer_mobile .Leave_Word").toggle();
			$("#transfer_mobile .btn_leave_word").hide();
			
		});
		$("#transfer_mobile .abolish").click(function(){
			$("#transfer_mobile .Leave_Word").hide();
			$("#transfer_mobile .btn_leave_word").show();
		});
		
		$("#transfer_mobile #add_msg").click(function(){
			var ajaxurl = WAP_PATH+"/index.php?ctl=msg";
			var query = newObject();
			query.rel_table = $.trim($("#transfer_mobile #rel_table").val());
			query.rel_id = $.trim($("#transfer_mobile #rel_id").val());
			query.title = $.trim($("#transfer_mobile #title").val());
			query.content = $.trim($("#transfer_mobile #content").val());
			
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					if(data.user_login_status==0){
						RouterURL(WAP_PATH+"/index.php?ctl=login","#login");
					}
					else if(data.response_code == 0){
						$.alert(data.show_err);
					}
					else{
						$.alert(data.show_err);
						var html = '<li class="w_b bb1">';
			          		html += '<div class="img_block"><img src="'+data.avatar+'" style="width:100%; height:100%;"/></div>';
							html += '<div class="d_block w_b_f_1">';
							html += '	<div class="top clearfix">';
							html += '		<span class="name">'+data.user_name+'</span>';
							html += '		<span class="time">'+data.create_time+'</span>';
							html += '	</div>';
							html += '	<p class="middle">'+data.content+'</p>';
							html += '</div>';
			          		html += '</li>';
						$("#transfer_mobile .contentblock ul").append(html);
						$("#transfer_mobile .Leave_Word").hide();
						$("#transfer_mobile .Leave_Word #content").val("");
						$("#transfer_mobile .btn_leave_word").show();
					}
				}
			});
		});
	});
});	



function get_search_parms()
{
	var parms = "";
	if($("#deals_search").length > 0){
		$("#deals_search .deals_search_list li").each(function(){
			parms +="&"+$(this).attr("data-type")+"="+$(this).attr("data-type-value");
		});
	}
	return parms;
}

