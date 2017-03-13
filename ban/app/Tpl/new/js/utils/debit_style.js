/*index*/
$(document).ready(function(){
		$(".select_this").click(function(){
			if(!$(".select_list").hasClass("y"))
			{
				$(".select_list").show();
				$(".select_list").addClass("y");
				$(".select_this").addClass("y");
				
				$(".select_list").hover(
			function(){
				$(this).show();
				$(".select_list li").hover(
			function(){
				$(this).addClass("y");
				$(this).siblings().removeClass("y");
				$(this).click(function(){
					var value_0=$(this).html();
					$(".select_this").html(value_0);
					$(".select_list").hide();
				});
			});
			
			},
			function(){
				$(this).hide();
				$(".select_list").removeClass("y");
				$(".select_this").removeClass("y");
			});
			}
			else{
				$(".select_list").hide();
				$(".select_list").removeClass("y");
				$(".select_this").removeClass("y");
			}
		});
		
			$(".type_choose label").click(function(){
				$(this).addClass("label_y");
				$(this).siblings().removeClass("label_y");
				$("#"+$(this).attr("for")).click();
			});
			
			$(".Process ul li").hover(function(){
				$(this).addClass("y");
				$(this).siblings().removeClass("y");
			},
			function(){
				$(this).removeClass("y");
				
				
			})
	});
	
	/*#scrollDiv轮播*/
	function AutoScroll(obj)
{
	$(obj).find("ul:first").animate(
	{
		marginTop:"-93px"
	},400,function()
	{
		$(this).css({marginTop:"0px"}).find("li:first").appendTo(this);
		set_left($(this).find("li:eq(0)")[0]);
	});
}
function set_left(obj)
{
	$("#left_show").hide(300);	
	$("#left_show").show(300);	
	$("#left_show img").attr("src",$(obj).find("img").attr("src"));
	$("#left_show").find("p.name").html($(obj).find("p.name").attr("alt")+"("+$(obj).find("p.school").html()+")");
	$("#left_show").find("p.for_what").html("<b>\"</b>"+$(obj).find("p.num").attr("alt")+"<b>\"</b>");
	$("#left_show").find("p.time").html("<b class=\"c_f7b652\">"+$(obj).find("p.time").html()+"</b>分钟");				
}
$(document).ready(function(){
	setInterval('AutoScroll("#scrollDiv")',3000)
});

/*help_center.html*/
$(document).ready(function(){
	$(".help_center .nav_list li").click(function(){
		$(this).addClass("y");
		$(this).siblings().removeClass("y");
		var x=$(this).index();
		
		$(".content_list li").eq(x).siblings().hide();
	    $(".content_list li").eq(x).show();
		
		//alert(x);
	});
	
	$("button.type_but").click(
	function(){
		if($("input[type='radio'][name='type']:checked").length == 0)
		{
			alert("请选择白条类型");
			return false;
		};
		
		if($("input[type='radio'][name='debit_money']:checked").length == 0)
		{
			alert("请选择白条金额");
			return false;
		};
		
		$("#repaytime").val($(".select_this").html().replace("个月",""));

		//location.href=$("#form1").attr("action")+'&type='+$("input[type='radio'][name='type']:checked").val()+"&money="+$("input[type='radio'][name='debit_money']:checked").val();
		$("#form1").submit();
		//return true;
	});
});
