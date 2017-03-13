$(document).ready(function(){
	bind_ajax_form();
});

// 绑定ajax_form
function bind_ajax_form()
{
	$(".ajax_form").find(".ui-button").bind("click",function(){
 		$(".ajax_form").submit();
	});
	$(".ajax_form").bind("submit",function(){
		var ajaxurl = $(this).attr("action");
		var query = $(this).serialize() ;
		$.ajax({ 
			url: ajaxurl,
			dataType: "json",
			data:query,
			type: "POST",
			success: function(ajaxobj){
				if(ajaxobj.status==1)
				{
					if(ajaxobj.info!="")
					{
						$.showSuccess(ajaxobj.info,function(){
							if(ajaxobj.jump!="")
							{
								location.href = ajaxobj.jump;
							}
							else
							{
								location.href = location.href;
							}
						});	
					}
					else
					{
						if(ajaxobj.jump!="")
						{
							location.href = ajaxobj.jump;
						}
					}
				}
				else
				{
					if(ajaxobj.info!="")
					{
						$.showErr(ajaxobj.info,function(){
							if(ajaxobj.jump!="")
							{
								location.href = ajaxobj.jump;
							}
						});	
					}
					else
					{
						if(ajaxobj.jump!="")
						{
							location.href = ajaxobj.jump;
						}
					}							
				}
			},
			error:function(ajaxobj)
			{
				if(ajaxobj.responseText!='')
				alert(ajaxobj.responseText);
			}
		});
		return false;
	});
}


