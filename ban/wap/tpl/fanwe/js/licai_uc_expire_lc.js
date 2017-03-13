$(document).on("pageInit", "#licai_uc_expire_lc", function(e, id, page) {
    var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#licai_uc_expire_lc .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#licai_uc_expire_lc .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#licai_uc_expire_lc .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.p  =  now_page + 1;
			query.is_ajax = 1;
			query = get_url_params(query);
			delete query.ctl;		
		    $.ajax({
		      	url:$("#licai_uc_expire_lc .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;

		        	$("#licai_uc_expire_lc .infinite-scroll .invest").append(result);
		        	$("#licai_uc_expire_lc .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#licai_uc_expire_lc .infinite-scroll-preloader").hide();
		        	$.refreshScroller();
				}
		    });
	    });
		
		$("#submitt").click(function(){
        var ajaxurl = '{wap_url a="index" r="licai_uc_expire_lc"}';
        var deal_name = $.trim($("#deal_name").val());
        var b_time = $.trim($("#begin_time").val());
        var e_time = $.trim($("#end_time").val());
        var user_name = $.trim($("#user_name").val());
        if(b_time>e_time && e_time>'0000-00-00'){
			$.alert("到期开始时间不能大于到期结束时间!");
			return false;
		}
        
        var  url = WAP_PATH+'/index.php?ctl=licai_uc_expire_lc&begin_time='+b_time+'&end_time='+e_time+'&user_name='+user_name+'&deal_name='+deal_name;
        reloadpage(url,"#licai_uc_expire_lc",".show_list ul");      
			
        
       
              
    });
   

});	