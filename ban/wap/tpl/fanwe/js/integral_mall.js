$(document).ready(function(){
	
	/*����̳�*/
	$(document).on("pageInit", "#integral_mall", function(e, id, page) {

		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#integral_mall .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#integral_mall .infinite-scroll").attr("all_page");
			now_page = $("#integral_mall .content").attr("now_page");
			now_page = parseInt(now_page);
		   	if (loading || now_page >= all_page) return;
			$("#integral_mall .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.page  =  now_page + 1;
			query.is_ajax = 1;
			
			var parms = get_search_parms();
			var ajaxurl = $("#integral_mall .infinite-scroll").attr("ajaxurl");
		    $.ajax({
		      	url:ajaxurl + parms,
		        data:query,
		        success:function(result){
		        	now_page ++;
					 $("#integral_mall .content").attr("now_page",now_page);
		        	loading = false;
		        	$("#integral_mall .infinite-scroll .integral_goods").append(result);
		        	$("#integral_mall .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#integral_mall .infinite-scroll-preloader").hide();
		        	$.refreshScroller();
		        }
		      });
	       
	    });
	    
	    var height=$("body").height();
		$("#integral_mall .integral_mall_nav").height(height);
		$("#integral_mall  .float_background").height(height);
      
		        
	  });
	  
	$(document).on("pageInit", "#goods_information", function(e, id, page) {

			
			$("#goods_information .Minus").click(function(){
				var x=$("#goods_information .nun_choose #number").val();
				if(x>1)
				{
					x-=1;
					$("#goods_information .nun_choose #number").val(x);
				}
				else
				{
					$.alert("购买数量不能小于1");
					$("#goods_information .nun_choose #number").val(1);
				}
				
			});
			$("#goods_information .Plus").click(function(){
				var x=$("#goods_information .nun_choose #number").val();
				var y=$("#goods_information .nun_choose #number").attr("max");;//20暂代库存
			
				if(x>(y-1))
				{
					
					$.alert("购买数量不能大于"+y);
					$("#goods_information .nun_choose #number").val(y);
				}
				else
				{
					x=parseInt(x) + 1;
					$("#goods_information .nun_choose #number").val(x);
				}
			});
			
			$("#goods_information #submitt").click(function(){
				var id =  $.trim($("#goods_information #goods_id").val());
				var number =  $.trim($("#goods_information #number").val());
				var uscore =  $.trim($("#goods_information #uscore").val());
				var score =  $.trim($("#goods_information #score").val());
				var user_id =  $.trim($("#goods_information #user_id").val());
				if(user_id==0||user_id==""){
					RouterURL(WAP_PATH+'/index.php?ctl=login','#login');
					return false;
				}else if(uscore<(number*score)){
					$.alert("积分不足");
					return false;
				}
				
				
				var ajaxurl = WAP_PATH+'/index.php?ctl=goods_exchange';
				var query = newObject();
				query.number = number;
				query.id = id;
				query.post_type = "json";
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"Post",
					dataType:"json",
					success:function(data){
						if(data.response_code == 0){
							$.alert(data.show_err);
							RouterURL(WAP_PATH+'/index.php?ctl=login','#login');
							return false;
						}else{
							RouterURL(WAP_PATH+'/index.php?ctl=goods_exchange&id='+id+'&number='+number,'#goods_exchange',2);
							return false;
						}
						
					}
				
				});
				
			});
	});
		
		
		
	
	$(document).on("pageInit", "#goods_exchange", function(e, id, page) {
		
		$("#goods_exchange #submitt").click(function(){
			var is_delivery =  $.trim($("#goods_exchange #is_delivery").val());
			if(is_delivery == 1){
				if(!address_id)
				{
					$.alert("请填写收货信息");
					return false;
				}
			}
			
			
			var ajaxurl = WAP_PATH+'/index.php?ctl=doexchange';
			
			var query = newObject();
			query.number =  $.trim($("#goods_exchange #number").val());
			query.goods_id = $.trim($("#goods_exchange #goods_id").val());
			query.address_id = $.trim($("#goods_exchange #address_id").val());
			query.post_type = "json";
			
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.alert(data.show_err);
					RouterURL(WAP_PATH+'/index.php?ctl=uc_goods_order','#uc_goods_order',2);
				}
			
			});
			
		});

	});
});	

function closeIntegalNav(obj){
	$(obj).parents(".float_block").hide();
}



