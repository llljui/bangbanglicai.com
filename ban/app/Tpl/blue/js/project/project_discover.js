;
$(document).ready(function(){
	bind_event();
	ajax_load();
});

function bind_event()
{
	$("#pin_loading").css("visibility","hidden");
	$(".pages").hide();
	$(window).bind("scroll", function(e){
		ajax_load();
	});
}


var step = 2;
var is_loading = false;

function ajax_load()
{
	var scrolltop = $(window).scrollTop();
	var loadheight = $("#pin_loading").offset().top;
	var windheight = $(window).height();	
	var ajaxurl = $("#pin_loading").attr("rel");
	
	//滚动到位置+分段加载未结束+ajax未在运行
    if(windheight+scrolltop>=loadheight+35&&parseInt(step)>0&&!is_loading)
    {
    	var query = new Object();
    	query.step = step;
    	is_loading = true;

    	$("#pin_loading").css("visibility","visible");    	
    	$.ajax({ 
    		url: ajaxurl,
    		data:query,
    		type: "POST",
    		dataType: "json",
    		success: function(data){
				console.log(step);
    			$("#pin_loading").css("visibility","hidden");
    			$("#pin_box .deal_item_list").append(data.html);
    			step = data.step;
    			is_loading = false;     
    			if(step==0)
    			{
    				$(".pages").show();
    			}
    			if(ajax_callback==1)
    			{
    				ajax_load_callback();
    			}
				leftTimeAct(".left_time");
    		},
    		error:function(ajaxobj)
    		{
				
    		}
    	});	

    	
    }	
};
var leftTimeActInv;
$(function(){
	leftTimeAct(".left_time");
});
// 未开始项目倒计时
function leftTimeAct(left_time){
	if(leftTimeActInv)
		clearTimeout(leftTimeActInv);
	$(left_time).each(function(){
		var leftTime = parseInt($(this).attr("data"));
		//console.log(leftTime);
		if(leftTime > 0)
		{
			var day  =  parseInt(leftTime / 24 /3600);
			var hour = parseInt((leftTime % (24 *3600)) / 3600);
			var min = parseInt((leftTime % 3600) / 60);
			var sec = parseInt((leftTime % 3600) % 60);
			$(this).find(".day").html((day<10?"0"+day:day));
			$(this).find(".hour").html((hour<10?"0"+hour:hour));
			$(this).find(".min").html((min<10?"0"+min:min));
			$(this).find(".sec").html((sec<10?"0"+sec:sec));
			leftTime--;
			$(this).attr("data",leftTime);
		}
		else{
			// // window.location.reload();
			// return false;
		}
	});
	
	leftTimeActInv = setTimeout(function(){
		leftTimeAct(left_time);
	},1000);
	
	if ($.browser.msie && $.browser.version <= 7){
		$(".nav_item").bind('click',function(){
			var $obj=$(this).find("a");
			var p_url=$obj.attr("href");
			window.open(p_url);
		});
	}
}