$(document).ready(function(){
	
	$(document).on("pageInit", "#uc_interestrate_exchange", function(e, id, page) {
		var is_submit_lock;
		$("#uc_interestrate_exchange .exchange").bind("click",function(){
			var obj = $(this);
			$.confirm("确定要兑换吗？",function(){		
				Interrate_exchange(obj);
			});
			
		});		
		
		$("#uc_interestrate_exchange  #sn_exchange").bind("submit",function(){
			if(is_submit_lock) return ;
			if($.trim($(this).find("input[name='sn']").val())=="")
			{
				$.alert("请输入序列号");
				return false;
			}
			
			is_submit_lock = true;
			var ajaxurl = $(this).attr("action");
			var query = newObject();
			query.post_type = "json";
			query.sn = $("#uc_interestrate_exchange input[name='sn']").val();
			$.ajax({ 
				url: ajaxurl,
				data:query,
				dataType: "json",
				type: "POST",
				success: function(data){
					is_submit_lock = false;
					if(data.user_login_status==2){
						$.alert(data.show_err,function(){
							RouterURL(WAP_PATH+'/index.php?ctl=login','#login');	
						});
						
					}else if(data.status==1){
						$.alert(data.show_err,function(){
							if($("#uc_interestrate").length > 0){
								$.router.loadPage("#uc_interestrate");
								reloadpage(WAP_PATH+'/index.php?ctl=uc_interestrate','#uc_interestrate','.content');		
							}
							else{
								$.router.loadPage(WAP_PATH+'/index.php?ctl=uc_interestrate',"#uc_interestrate");
							}
						});
						
					}else{
						$.alert(data.show_err);
					}
				},
				error:function(ajaxobj)
				{
					is_submit_lock = false;
				}
			});		
			return false;
		});
	});
	$(document).on("pageInit", "#uc_interestrate", function(e, id, page) {
		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#uc_interestrate .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_interestrate .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#uc_interestrate .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.page  =  now_page + 1;
			query.is_ajax = 1;
		    $.ajax({
		      	url:$("#uc_interestrate .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;
		        	$("#uc_interestrate .infinite-scroll .invest").append(result);
		        	$("#uc_interestrate .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#uc_interestrate .infinite-scroll-preloader").hide();
		        	$.refreshScroller();
		        }
		      });
	       
	    });
	    
	    
	});
	
});

var is_ItRexsubmit_lock = false;

function ItRexChageview(obj){
	var obj = $(obj);
	if(obj.css("display")=="block"){
		obj.hide();
		return false;
	}
	else{
		obj.show();
		obj.find(".ir_item").show();
		obj.find(".ir_send").hide();
		return false;
	}
}

function ItRexChageview1(obj){
	$(obj).find(".ir_item").hide();
	$(obj).find(".ir_send").show();
}

function ItRexChageview2(obj){
	$(obj).find(".ir_item").show();
	$(obj).find(".ir_send").hide();
}


function ItRexChageview3(obj,rel){
	
	var obj = $(obj);
	$.confirm("确认转让？",function(){
		if(is_ItRexsubmit_lock==true) return ;
	
		var ajaxurl = WAP_PATH+'/index.php?ctl=uc_interestrate_do_send';
		var query = newObject();
		
		query.id = obj.parent().find("input[name=id]").val();
		query.user_name = obj.parent().find("input[name=user_name]").val();
		
		if($.trim(query.user_name)=="")
		{
			$.alert("请填写会员名");
			return;
		}
		
		query.post_type = "json";
		
		is_ItRexsubmit_lock = true;
		
		$.ajax({
			url:ajaxurl,
			data:query,
			type:"Post",
			dataType:"json",
			success:function(data){
				is_ItRexsubmit_lock = false;
				if(data.status == 1){
					$.alert(data.show_err,function(){
						$("#ir_block_"+rel).remove();
						$("#ir_detail_"+rel).remove();
					});
				}else{
					$.alert(data.show_err);
				}
			}
		});
	});
	
}

function Interrate_exchange(obj){	
	var ajaxurl = obj.attr("url");
	var query = newObject();
	query.id=obj.attr("data-id");
	query.post_type = "json";
	$.ajax({ 
		url: ajaxurl,
		data:query,
		type:"Post",
		dataType:"json",
		success: function(data){
			if(data.user_login_status==0){
				$.alert(obj.show_err,
					function(){RouterURL(WAP_PATH+'/index.php?ctl=login','#login');}
				);
			}else if(data.status==1){
				$.alert(data.show_err,
					function(){
						if($("#uc_interestrate").length > 0){
							reloadpage(WAP_PATH+'/index.php?ctl=uc_interestrate','#uc_interestrate','.content',function(){
								RouterBack(WAP_PATH+'/index.php?ctl=uc_interestrate',"#uc_interestrate","#uc_interestrate");
							});		
						}
						else{
							$.router.loadPage(WAP_PATH+'/index.php?ctl=uc_interestrate',"#uc_interestrate");
						}
					}
				);
			}else{
				$.alert(data.show_err);
			}
		},
		error:function(ajaxobj)
		{
			
		}
	});		

}