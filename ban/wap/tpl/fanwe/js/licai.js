$(document).ready(function(){
	var licai_leftTimeActInv = null;
	$(document).on("pageInit", "#licai_deals", function(e, id, page) {
		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#transfer .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#licai_deals .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#licai_deals .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.page  =  now_page + 1;
			query.is_ajax = 1;
		    $.ajax({
		      	url:$("#licai_deals .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;
		        	$("#licai_deals .infinite-scroll .invest").append(result);
		        	$("#licai_deals .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#licai_deals .infinite-scroll-preloader").hide();
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
		    $.ajax({
		    	url:$("#licai_deals .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page=1;
		        	loading =false;
		       	 	$content.find('.invest').html(result);
		       	 	$("#licai_deals .infinite-scroll").attr("all_page",$("#licai_deals .pull-to-refresh-layer").attr("all_page"));
	       			$.pullToRefreshDone($content);
		       	}
		     });
	    });
	 });
	 
	 $(document).on("pageInit", "#licai_uc_record_lc", function(e, id, page) {
		 
		$(page).on("click","#submitt",function(){
			var id = $.trim($("#id").val());
			var b_time = $.trim($("#begin_time").val());
			var e_time = $.trim($("#end_time").val());
			var b_b_time = $.trim($("#buy_begin_time").val());
			var b_e_time = $.trim($("#buy_end_time").val());
			
			var url_str = $("#url_str").val();
			url_str = url_str.replace("b_time",b_time);
			url_str = url_str.replace("e_time",e_time);
			url_str = url_str.replace("b_b_time",b_b_time);
			url_str = url_str.replace("b_e_time",b_e_time);
			url_str = url_str+'&id='+id;
			RouterURL(url_str,'#licai_uc_record_lc',1);

		});
	 });
	 
	 $(document).on("pageInit", "#licai_uc_expire_status", function(e, id, page) {
		 
		$(page).on("click","#submittss",function(){
			var ajaxurl = $("#ajax_url").val();
			var url_str = $("#url_str").val();
			var id =  $.trim($("#id").val());
			var earn_money =  $.trim($("#earn_money").val());
			var fee =  $.trim($("#fee").val());
			var query = newObject();
			query.id = $.trim($("#id").val());
			query.earn_money = $.trim($("#earn_money").val());
			query.fee = $.trim($("#fee").val());
			
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.alert(data.show_err,function(){
						RouterURL(url_str,'#licai_uc_record_lc',1);
					});
				}
			});
			$(this).parents(".float_block").hide();	  		
		});
	 });
	 
	$(document).on("pageInit", "#licai_uc_buyed_deal_lc", function(e, id, page) {
		 
		$(page).on("click",".s_cancel",function(){
			
			item_id = $(this).attr("rel");
			$.confirm("您确定要取消赎回吗？",function(){
				
				var ajaxurl = $("#ajax_url").val();
				var url_str = $("#url_str").val();
				var id =  $.trim($("#id").val());
				var redempte_id =  $.trim($("#redempte_id_"+item_id).val());
				var query = newObject();
				query.redempte_id = $.trim($("#redempte_id_"+item_id).val());
				query.status = 0;
				query.post_type = "json";
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"Post",
					dataType:"json",
					success:function(data){
						$.alert(data.info,function(){
							RouterURL(url_str+'&id='+id,"#licai_uc_buyed_deal_lc",1);	
						});
					}
				}); 
			},function(){
		    	return false;
		    });
			
			$(this).parents(".float_block").hide();	  		
		});
	 });
	 
	$(document).on("pageInit", "#licai_uc_redeem_lc", function(e, id, page) {
		 
		$(page).on("click","#submitt",function(){
			var ajaxurl = $("#ajax_url").val();
			var deal_name = $.trim($("#deal_name").val());
			var b_time = $.trim($("#begin_time").val());
			var e_time = $.trim($("#end_time").val());
			var user_name = $.trim($("#user_name").val());
			
			var query = newObject();
			query.deal_name = $.trim($("#deal_name").val());
			query.b_time = $.trim($("#begin_time").val());
			query.e_time = $.trim($("#end_time").val());
			query.user_name = $.trim($("#user_name").val());
			
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					RouterURL(ajaxurl+'&deal_name='+deal_name,"#licai_uc_redeem_lc",1);
				}
			});
			$(this).parents(".float_block").hide();
		});
	 }); 
	 
	 $(document).on("pageInit", "#licai_uc_redeem_lc_status", function(e, id, page) {
		 
		$(page).on("click","#submitt",function(){
			var ajaxurl = $("#ajax_url").val();
			var url_str = $("#url_str").val();
			var redempte_id =  $.trim($("#redempte_id").val());
			var earn_money =  $.trim($("#earn_money").val());
			var fee =  $.trim($("#fee").val());
			var query = newObject();
			query.redempte_id = $.trim($("#redempte_id").val());
			query.earn_money = $.trim($("#earn_money").val());
			query.fee = $.trim($("#fee").val());
			
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.alert(data.show_err,function(){
						RouterURL(url_str,'#licai_uc_record_lc',1);
					});
				}
			});
			$(this).parents(".float_block").hide();
		});
	 });
	 
	 $(document).on("pageInit", "#licai_deal", function(e, id, page) {
	 	 var system_time = $("#h_system_time").html();
	 	 var licai_type =  $("#h_licai_type").html();
		var licai_interest_json = $("#h_licai_interest_json").html();
		licai_interest_json =  JSON.parse(licai_interest_json);
		var income_money_val = 0;
		var before_day = parseInt($("#h_before_day").html());
		var buy_day = parseInt($("#h_buy_day").html());
		var before_money_val = 0;
		var site_buy_fee_rate= 0;
		var redemption_fee_rate = 0;
		// 预期一天收益
		var $deal_top_r_bd=$("#deal_top_r_bd"),
			$min_money=$deal_top_r_bd.find("input[name='min_money']"),
			$money=$deal_top_r_bd.find("input[name='money']"),
			$income_money=$deal_top_r_bd.find("input[name='income_money']"),
			endTime = parseInt($("#left_time").attr("data")),
			leftTime = endTime - system_time;
			
			
		 BindLiCaiDeal();
		 function BindLiCaiDeal(){
		 	if ($("#h_show_pic").length > 0){
				if ($("#h_show_pic").html() != "")
				{
					var numbs = $("#h_show_pic").html().split(",_");
					var myData = new Array();
					for(i = 0 ; i<numbs.length ;i++)
					{
						var tmp = numbs[i].split(',');
						myData[i] = [tmp[0],parseInt(tmp[1])];
					}
					var myChart = new JSChart('data_table', 'line');
					myChart.setAxisNameX("");
					myChart.setAxisNameY("");
					myChart.setIntervalStartY(0);
					myChart.setAxisPaddingTop(10);
					myChart.setDataArray(myData);
					myChart.setTitle('');
					myChart.setSize(300, 200);
					myChart.setBarColor('#39a1ea');
					myChart.draw();
				}
			}
				 
			if(!($money.val())){
				$income_money.attr("value",0);
			}
			
	       	licai_leftTimeAct("#left_time");
		 }
		
		$(page).on('click','#pay_deal',function(){
			reloadpage($("#load_url").val(),'#licai_deal','.content',function(){
										  BindLiCaiDeal();
									});
			return false;
		   	  var id= $.trim($("#id").val());
			  var money_val= $.trim($("#money").val());
			  var min_money= $.trim($("#min_money").val());  
			  var tc_money= $.trim($("#tc_money").val()); 
			  
              if(endTime!=0&&leftTime<=0){
                  $.alert("项目已结束！");
                  return false;
              }
              if($deal_top_r_bd.find("input[name='own_pro']").length){
                  $.alert("不能购买自己发布的理财产品");
                  return false;
              }
              if(parseFloat(tc_money) < parseFloat(money_val)){
                  $.alert("您的账户余额不足！");
                  return false;
              }
              if(!(money_val)){
                  $.alert("请输入金额");
                  return false;
              }
				if((money_val)<=0){
                  $.alert("请输入正确的金额");
                  return false;
              }
              else if(parseFloat(money_val) < parseFloat(min_money)){
                  $.alert("最低金额不能低于"+ min_money +"元");
                  return false;
              }
              else{
                  var ajaxurl = $("#ajax_url").val();
			        var query = newObject();
			        
			        query.id = $.trim($("#id").val());
			        query.money = $.trim($("#money").val());
			        query.paypassword = $.trim($("#pay_inmoney_password").val());
			        query.post_type = "json";
			        $.ajax({
			            url:ajaxurl,
			            data:query,
			            type:"Post",
			            dataType:"json",
			            success:function(data){
		                    $.alert(data.show_err,function(){
								if(data.response_code == 1){
									reloadpage($("#load_url").val(),'#licai_deal','.content',function(){
										  BindLiCaiDeal();
									});
								}
							});
			            }
			        });
              }
          });
		  
        $(page).on('keyup','#money',function(){		
			  var money_val= $.trim($("#money").val());	
			  if(parseFloat($("#user_left_money").attr("data")) < parseFloat(money_val)){
				  $("#user_left_money_tip").show();
			  }
			  else{
				  $("#user_left_money_tip").hide();
			  }
			   
			  if(licai_type > 0){
				  if(parseInt(licai_interest_json[licai_interest_json.length - 1].max_money) <= money_val){
					  income_money_val = parseFloat(licai_interest_json[licai_interest_json.length - 1].interest_rate);
					  before_money_val = parseFloat(licai_interest_json[licai_interest_json.length - 1].before_rate);
					  site_buy_fee_rate= parseFloat(licai_interest_json[licai_interest_json.length - 1].site_buy_fee_rate);
					  redemption_fee_rate= parseFloat(licai_interest_json[licai_interest_json.length - 1].redemption_fee_rate);
				  }
				  else{
					  $.each(licai_interest_json,function(i,v){
						  if(parseInt(v['min_money']) <= money_val && parseInt(v.max_money) > money_val){
							  income_money_val = parseFloat(v.interest_rate);
							  before_money_val = parseFloat(v.before_rate);
							  site_buy_fee_rate= parseFloat(v.site_buy_fee_rate);
							  redemption_fee_rate= parseFloat(v.redemption_fee_rate);
						  }
					  });
				  }
			  }
			  else{
				  income_money_val = licai_interest_json;
			  }
	  		  
			  $("#verify_money").html(money_val);
			  if(money_val){
				  
				  if(licai_type > 0){
					  var normal_rate=income_money_val/100;  // 正常利率
					  var preheat_rate=before_money_val/100;  // 预热利率
					  var procedures_rate=site_buy_fee_rate/100;  // 网站手续费率
					  var redemption_rate=redemption_fee_rate/100;  // 赎回手续费率
					  var new_money_val=money_val-money_val*procedures_rate;  // 扣除手续费后金额
					  
					  // 收益
					  var income_money=(new_money_val*normal_rate*buy_day)/365 + (new_money_val*preheat_rate*before_day)/365;
					  var redemption_money=((new_money_val)*redemption_rate*(buy_day+before_day))/365; // 赎回手续费
					  var new_income_money=(income_money-redemption_money).toFixed(2);
					  $income_money.attr("value",new_income_money);
					  $(".J_u_money_sy").html(isNaN(new_income_money)?0:new_income_money);  
				  }
				  else
				  {
					  var redemption_fee_rate = income_money_val.redemption_fee_rate;
					  var site_buy_fee_rate = income_money_val.site_buy_fee_rate;
					  var platform_rate = income_money_val.platform_rate;
					  var average_income_rate = income_money_val.average_income_rate;

					  var procedures_rate=site_buy_fee_rate/100;  // 网站手续费率
					  var redemption_rate=redemption_fee_rate/100;  // 赎回手续费率
					  var preheat_rate = average_income_rate/100; //收益
					  var new_money_val=money_val-money_val*procedures_rate;  // 扣除手续费后金额

					  //收益
					  var income_money= (new_money_val*preheat_rate*buy_day)/365;
					  var redemption_money=(new_money_val)*redemption_rate*buy_day/365;  // 赎回手续费
					  var new_income_money=(income_money-redemption_money).toFixed(2);
					  $income_money.attr("value",new_income_money);
					  $(".J_u_money_sy").html(isNaN(new_income_money)?0.00:new_income_money);  
				  }
			  }
		  });
	  
	  function licai_leftTimeAct(left_time){
		  if(licai_leftTimeActInv)
   			 clearTimeout(licai_leftTimeActInv);
			$(left_time).each(function(){
				var endTime = parseInt($(this).attr("data"));
				var leftTime = endTime - system_time + 3600*24 ;
				if(endTime){
					if(leftTime > 0){
						var day  =  parseInt(leftTime / 24 /3600);
						var hour = parseInt((leftTime % (24 *3600)) / 3600);
						var min = parseInt((leftTime % 3600) / 60);
						var sec = parseInt((leftTime % 3600) % 60);
						$(this).find(".day").html((day<10?"0"+day:day));
						$(this).find(".hour").html((hour<10?"0"+hour:hour));
						$(this).find(".min").html((min<10?"0"+min:min));
						$(this).find(".sec").html((sec<10?"0"+sec:sec));
						system_time++;
						
						//$(this).attr("data",leftTime);
					}
					else{
						$(this).html("已结束");
					}
				}
				else{
					$(this).html("永久有效");
				}
			});
			licai_leftTimeActInv = setTimeout(function(){
				licai_leftTimeAct(left_time);
			},1000);
		}
			
	 });
	 
	 $(document).on("pageInit", "#licai_uc_redeem", function(e, id, page) {
		
		 $(page).on("keyup","#redeem_money",function(event){
            code = event.keyCode;
            if(parseFloat($("#redeem_money").val())>parseFloat($("#have_money").attr("title")))
            {
                $("#redeem_money").val($("#have_money").attr("title"));
                $.alert("赎回的金额不能大于持有本金");
            }
            money = $("#back_rate").html() * $("#redeem_money").val();
            if(isNaN(money))
            {
                money = 0;
            }
            fun_money();
        });
		
		var licai_type =  parseInt($("#h_licai_type").html());
		var licai_interest_json = $("#h_licai_interest_json").html();
		licai_interest_json = JSON.parse(licai_interest_json);
		var before_rate = 0;
		var before_breach_rate = 0;
		var breach_rate = 0;
		var interest_rate = 0;
		var licai_status = $("#h_licai_status").html();
		var before_days = parseInt($("#before_days").html());
		var days = parseInt($("#days").html());
		var before_arrival_earn = 0;
		var arrival_earn  = 0;

		function fun_money(){
        
            $money = $("#redeem_money");
            var money_val=$money.val();
            
            if(licai_type > 0){
                if(licai_interest_json[licai_interest_json.length - 1].max_money < money_val){
                    before_rate = licai_interest_json[licai_interest_json.length - 1].before_rate;
                    before_breach_rate = licai_interest_json[licai_interest_json.length - 1].before_breach_rate;
                    breach_rate = licai_interest_json[licai_interest_json.length - 1].breach_rate;
                    interest_rate = licai_interest_json[licai_interest_json.length - 1].interest_rate;
                }
                else{
                    $.each(licai_interest_json,function(i,v){
                        
                        if( parseFloat(v.min_money) < parseFloat(money_val) && parseFloat(v.max_money) > parseFloat(money_val)){
                            before_rate = v.before_rate;
                            before_breach_rate = v.before_breach_rate;
                            breach_rate = v.breach_rate;
                            interest_rate = v.interest_rate;
                        }
                    });
                }
            }
            else{
                income_money_val = licai_interest_json;
            }
            if(money_val){
            	if(licai_type > 0){
                   if(licai_status == 0)
                   {  //预热期违约收益
                      before_arrival_earn = parseFloat($("#redeem_money").val()) * before_breach_rate / 365 / 100 * (before_days);
                      //理财期收益
                      arrival_earn = 0;
                      $("#q_rate").html(before_breach_rate+"%");
				   }
                   else if (licai_status == 1)
                   {   //预热期完成收益
                      before_arrival_earn = parseFloat($("#redeem_money").val()) * before_rate / 365 / 100 * (before_days);
                      //理财期违约收益
                      arrival_earn = parseFloat($("#redeem_money").val()) * breach_rate / 365 / 100 * (days);
                      $("#q_rate").html(breach_rate+"%");
				   }
                   else if (licai_status == 2)
                   {    //预热期完成收益
                      before_arrival_earn = parseFloat($("#redeem_money").val()) * before_rate / 365 / 100 * (before_days);
                      //理财期完成收益
                      arrival_earn = parseFloat($("#redeem_money").val()) * interest_rate / 365 / 100 * (days);
                      $("#q_rate").html(interest_rate+"%");
				   }
				}
				else
				{
					before_arrival_earn = 0;
					arrival_earn = income_money_val*money_val/365/100;
				}
                  //预计收益
                  arrival_amount = parseFloat($("#redeem_money").val())+ before_arrival_earn + arrival_earn;
                  $("#redeem_interest_money").html(arrival_earn.toFixed(2) +"元");
                  $("#expect_amount").html(arrival_amount.toFixed(2));   //预计到账金额
                  $("#expect_before_earn").html(before_arrival_earn.toFixed(2));     //预计收益
                  $("#expect_earn").html(arrival_earn.toFixed(2));   //预计理财收益
            }
		}
		
		$(page).on('click','#redeem_btn',function(){
		
			var ajaxurl = $("#ajax_url").html();
			if($("#redeem_money").val() <= 0)
			{
				$.alert("请输入要赎回的金额");
				return;
			}
			if($("input[name=password]").val() == "")
			{
				$.alert("请输入支付密码");
				return;
			}
			var id =  $.trim($("#id").val());
			var redeem_money =  $.trim($("#redeem_money").val());
			var paypassword =  $.trim($("#paypassword").val());
			var query = newObject();
			query.id = $.trim($("#id").val());
			query.redeem_money = $.trim($("#redeem_money").val());
			query.paypassword = $.trim($("#paypassword").val());
			
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.alert(data.show_err,function(){
						if(data.status == 1){
							var url_str = $("#url_str").html();
							RouterURL(url_str+'&id='+id,'#licai_uc_redeem',1);
						}		
					});
				}
			
			});
		 });
	  });
});