$(document).ready(function(){
	 
	$(".interestrate_item").hover(function(){
		$(this).find(".mask").show();
	},function(){
		$(this).find(".mask").hide();
	});
	 
	$(".exchange").bind("click",function(){
		var url=$(this).attr("url");
		$.showCfm("确定要兑换吗？",function(){				
			exchange(url);
		});			
	});
	
	$("#sn_exchange").bind("submit",function(){
		if($.trim($(this).find("input[name='sn']").val())=="")
		{
			$.showErr("请输入序列号");
			return false;
		}
		var ajaxurl = $(this).attr("action");
		var query = $(this).serialize();
		$.ajax({ 
			url: ajaxurl,
			data:query,
			dataType: "json",
			type: "POST",
			success: function(obj){
				if(obj.status==2){
					ajax_login();
				}else if(obj.status==1){
					$.showSuccess("兑换成功",function(){
						location.href = obj.jump;
					});				
				}else{
					$.showErr(obj.info);
				}
			},
			error:function(ajaxobj)
			{
				
			}
		});		
		return false;
	});
	
	show_detail();
	send_interestrate();
});



function exchange(url){	
		var ajaxurl = url;
		$.ajax({ 
			url: ajaxurl,
			dataType: "json",
			type: "GET",
			success: function(obj){
				if(obj.status==2){
					ajax_login();
				}else if(obj.status==1){
					$.showSuccess("兑换成功",function(){
						location.href = obj.jump;
					});				
				}else{
					$.showErr(obj.info);
				}
			},
			error:function(ajaxobj)
			{
				
			}
		});		

}
/*加息券*/
function show_detail()
{
	$(".detail").bind("click",function(){
		$.weeboxs.open($(this).attr("href"), {contentType:'ajax',showButton:false,title:"加息券",width:520,height:340,type:'wee'});	
		return false;
	});
}
function send_interestrate()
{
	$(".send").bind("click",function(){
		$.weeboxs.open($(this).attr("href"), {contentType:'ajax',showButton:false,title:"转赠",width:320,height:140,type:'wee',onopen:function(){bind_i_sumbit()}});	
		return false;
	});	
}
function bind_i_sumbit()
{

	$("#i_submit").bind("click",function(){
		var ajaxurl=$("#form1").attr("action");
		var query = new Object();
		query.user_name = $("#user_name").val();
		query.id = $("#id").val();
		
		$.ajax({ 
			url: ajaxurl,
			data:query,
			dataType: "json",
			type: "POST",
			success: function(obj){
				if(obj.status==2){
					ajax_login();
				}else if(obj.status==1){
					$.showSuccess(obj.info,function(){
						location.href = obj.jump;
					});				
				}else{
					$.showErr(obj.info);
				}
			},
			error:function(ajaxobj)
			{
				
			}
		});		
		return false;
	});
}