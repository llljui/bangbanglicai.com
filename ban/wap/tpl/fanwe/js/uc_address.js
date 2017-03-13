$(document).ready(function(){
	
	$(document).on("pageInit", "#uc_address", function(e, id, page) {
		$("#uc_address #submitt").click(function(){
			var ajaxurl = WAP_PATH+'/index.php?ctl=uc_do_address';
			var id =  $.trim($("#id_val").val());
			var user_name = $.trim($("#name").val());
			var user_phone = $.trim($("#phone").val());
			var user_provinces_cities = $.trim($("#provinces_cities").val());
			var user_address = $.trim($("#address").val());
			var user_zip_code = $.trim($("#zip_code").val());
			if(!user_name)
			{
				$.alert("请填写姓名");
				return false;
			}
			if(!user_phone)
			{
				$.alert("请填写手机号");
				return false;
			}
			if(!user_address)
			{
				$.alert("请填写详细地址");
				return false;
			}
			if(!user_zip_code)
			{
				$.alert("请填写邮编");
				return false;
			}
	
			var query = newObject();
			query.user_name = user_name;
			query.user_phone = user_phone;
			query.user_provinces_cities = user_provinces_cities;
			query.user_address = user_address;
			query.id = id;
			query.user_zip_code = user_zip_code;
			query.is_default = $.trim($("#is_default:checked").val());
			
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.alert(data.show_err,function(){
						if(data.response_code==1){
							reloadpage(WAP_PATH+'/index.php?ctl=goods_address','#goods_address','.content');
							RouterURL(WAP_PATH+'/index.php?ctl=goods_address',"#goods_address",2);
						}
					});
				}
			
			});
			  
		});
			
		$("#uc_address #del_submitt").click(function(){
		   $.confirm("确认删除？",function(){
				var ajaxurl = WAP_PATH+'/index.php?ctl=uc_del_address';
				var id =  $.trim($("#id_val").val());
				var query = newObject();
				query.id = $.trim($("#id_val").val());
				query.post_type = "json";
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"Post",
					dataType:"json",
					success:function(data){
						$.alert(data.show_err);
						if(data.response_code==1){
							$("#goods_address .address-box-"+id).remove();
							RouterURL(WAP_PATH+'/index.php?ctl=goods_address',"#goods_address",2);
						}
					}
				
				});
			});
	          
		});
	});
	
});