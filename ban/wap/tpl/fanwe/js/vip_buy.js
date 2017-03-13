$(document).ready(function(){
	$(document).on("pageInit", "#uc_vip_buy", function(e, id, page) {
		var vip_data =$("#uc_vip_buy #vip_data").val();
		vip_data = $.parseJSON(vip_data);
		var JVipBuy_From_Lock = false;
		$("#uc_vip_buy #Jvip_years").change(function(){
				setVipResult(vip_data);
		});
				
		$("#uc_vip_buy #vip_id").change(function(){
			setVipResult(vip_data);
		});
		
		$("#uc_vip_buy  #vip_submitt").click(function(){
			if(JVipBuy_From_Lock==true) return ;
			$.showIndicator();
			JVipBuy_From_Lock = true;
			var ajaxurl = WAP_PATH + '/index.php?ctl=uc_save_vip_buy';
			var years =  $.trim($("#Jvip_years").val());
			var vip_id =  $.trim($("#vip_id").val());
			var paypassword =  $.trim($("#paypassword").val());
			var query = newObject();
			query.years = $.trim($("#Jvip_years").val());
			query.vip_id = $.trim($("#vip_id").val());
			query.paypassword = $.trim($("#paypassword").val());
			
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.hideIndicator();
					JVipBuy_From_Lock = true;
					$.alert(data.show_err);
					if(data.status == 1){
						reloadpage(WAP_PATH + '/index.php?ctl=uc_center','#uc_center','.content',function(){
							RouterURL(WAP_PATH + '/index.php?ctl=uc_vip_buy_log');
						});
					}
					
				}
			
			});
			  
		});
	});
});

function setVipResult(vip_data){
	var buy_fee = 0;
	var vip_id =  parseFloat($("#uc_vip_buy #vip_id").val());
	var site_pirce = 0;
	var original_price = 0;
	
	var cvip_id = 0;
	
	if(vip_data.length > 0){
		if(vip_data.length-3 > 0){
			cvip_id = vip_data[vip_data.length-4].vip_id;
			if(cvip_id == vip_id ){
				site_pirce = vip_data[vip_data.length-4].site_pirce;
				original_price = vip_data[vip_data.length-4].original_price;
			}
		}
		if(vip_data.length-1 > 0){
			cvip_id = vip_data[vip_data.length-2].vip_id;
			if(cvip_id == vip_id ){
				site_pirce = vip_data[vip_data.length-2].site_pirce;
				original_price = vip_data[vip_data.length-2].original_price;
			}
		}
		if(vip_data.length-2 > 0){
			cvip_id = vip_data[vip_data.length-3].vip_id;
			if(cvip_id == vip_id ){
				site_pirce = vip_data[vip_data.length-3].site_pirce;
				original_price = vip_data[vip_data.length-3].original_price;
			}
		}
		if(vip_data.length > 0){
			cvip_id = vip_data[vip_data.length-1].vip_id;
			if(cvip_id == vip_id ){
				site_pirce = vip_data[vip_data.length-1].site_pirce;
				original_price = vip_data[vip_data.length-1].original_price;
			}
		}
		
	}
	
	var years =  parseFloat($("#uc_vip_buy #Jvip_years").val());
	buy_fee = years * site_pirce;
	o_buy_fee = years * original_price; 
		
	$("#uc_vip_buy #Jvip_site_pirce").html((buy_fee)+".00 元");
	$("#uc_vip_buy #Jvip_original_price").html((o_buy_fee)+".00 元");
}	

	