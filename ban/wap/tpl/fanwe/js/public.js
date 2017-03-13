var bid_calculate = false;
$(function(){
	'use strict';
	$("#uc_collect_editor").click(function(){
		var this_value=$("#uc_collect_editor").html();
		if(this_value=="编辑")
		{
			$(".invest_tit .delete").show();
		   $(this).html("完成");
		}
		else
		{
			$(".invest_tit .delete").hide();
			$(this).html("编辑");
		}
	});
	
	/*首页绑定*/
	$(document).on("pageInit", "#init", function(e, id, page) {
		var loading = false;
		var post_act = null;
		var max_pos = 0;
        if($("#category_slidebox .hd").find("li").length <=1){
            $("#category_slidebox .hd").hide();
        }
        
        var $content = $(page).find(".content").on('refresh', function(e) {
	      	if (loading) return;
	      	loading =true;
	      	var query = newObject();
			query.is_ajax = 1;
		    $.ajax({
		    	url:$("#init .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	loading =false;
		       	 	$content.find('.content').html(result);
	       			$.pullToRefreshDone($content);
		       	}
		       	,error:function(){
					$.toast("通信失败");
				}
		     });
	    });
	    InitPostBar();
	    function InitPostBar(){
	    	clearTimeout(post_act);
	    	 $("#init .init-bar-statics .posbar .col").each(function(){
		    	var pos = $(this).attr("pos");
		    	var nowpos = $(this).attr("nowpos");
		    	nowpos = parseInt(nowpos)+1;
		    	if(nowpos <= pos){
		    		$(this).find(".point").css({height:nowpos+"%"});
		    		$(this).attr("nowpos",nowpos);
		    		if(nowpos >= max_pos)
		    			max_pos = nowpos;
		    	}
		    });
		    
		    if(max_pos <100)
		    	post_act = setTimeout(InitPostBar,50);
	    }
		
	});
	
	
	 $(document).on("pageInit", "#learn_activity", function(e, id, page) {
		 $("#learn_activity .tz_link_btn").click(function(){
		 	
		 	var obj = $(this);
		    $.confirm("确定投资？",function(){
		        var ajaxurl = WAP_PATH + '/index.php?ctl=uc_learn_do_invest';
		        var query = newObject();
		        query.learn_id = obj.attr("data-id");
		        query.money = $.trim($("#learn_activity #money").val());
		        query.status = 0;
		        query.post_type = "json";
		        $.showIndicator();
		        $.ajax({
		            url:ajaxurl,
		            data:query,
		            type:"Post",
		            dataType:"json",
		            success:function(data){
		                $.alert(data.show_err,function(){
							RouterURL(WAP_PATH + '/index.php?ctl=learn_activity');
		                });
		            }
			        ,error:function(){
						$.hideIndicator();
						$.toast("通信失败");
					}
		        });
		    },function(){
		    	return false;
		    });  
			    
		});
	});
	 	/*充值日志列表*/
	$(document).on("pageInit", "#uc_incharge_log", function(e, id, page) {
		var now_page = 1;
		var all_page = 0;
		var loading = false;
		$("#uc_incharge_log .infinite-scroll-preloader").hide();
		$(page).on('infinite', function() {
			all_page=$("#uc_incharge_log .infinite-scroll").attr("all_page");
		   	if (loading || now_page >= all_page) return;
			$("#uc_incharge_log .infinite-scroll-preloader").show();
		    loading = true;
			var query = newObject();
			query.p  =  now_page + 1;
			query.is_ajax = 1;
			query = get_url_params(query);
			delete query.ctl;		
		    $.ajax({
		      	url:$("#uc_incharge_log .infinite-scroll").attr("ajaxurl"),
		        data:query,
		        success:function(result){
		        	now_page ++;
		        	loading = false;

		        	$("#uc_incharge_log .infinite-scroll .invest").append(result);
					$("#uc_incharge_log .infinite-scroll .pull-to-refresh-layer").last().remove();
		        	$("#uc_incharge_log .infinite-scroll-preloader").hide();
		        	$.refreshScroller();
				}
				,error:function(){
					$.toast("通信失败");
				}
		    });
	    });	   		
		
		var $content = $(page).find(".content").on('refresh', function(e) {
	    	
	      	if (loading) return;
	      	loading =true;
	      	var query = newObject();
			query.page  =  1;
			query.is_ajax = 1;
			var parms = get_search_parms();
			var ajaxurl = $("#uc_incharge_log .infinite-scroll").attr("ajaxurl");
		    $.ajax({
		    	url:ajaxurl + parms,
		        data:query,
		        success:function(result){
		        	now_page=1;
		        	loading =false;
		       	 	$content.find('.invest').html(result);
		       	 	$("#uc_incharge_log .infinite-scroll").attr("all_page",$("#uc_incharge_log .pull-to-refresh-layer").attr("all_page"));
	       			$.pullToRefreshDone($content);
		       	}
		       	,error:function(){
					$.toast("通信失败");
				}
		     });
	    });
		
	 });
	  /*登录页面*/
	$(document).on("pageInit", "#login", function(e, id, page) {
		
		$("#login #email").blur(function(){
			if($.trim($(this).val())=="")
			{
				$.toast('用户名不能为空');
				return false;
			}
		});
	
		$("#login #pwd").blur(function(){
			if($.trim($(this).val())=="")
			{
				$.toast('密码不能为空');
				return false;
			}
		});
	
	
	
		$("#login .ui-button_login").click(function(){
				var email_val=$.trim($("#login #email").val());
				var pwd=$.trim($("#login #pwd").val());
				if(!email_val)
				{
					$.toast("请填写用户名");
					return false;
				}
				if(!pwd)
				{
					$.toast("请填写密码");
					return false;
				}
				var ajaxurl = WAP_PATH + '/index.php?ctl=login';
				var query = newObject();
				query.email = email_val;
				query.pwd = pwd;
				query.post_type = "json";
				
				$.showIndicator();
				$.ajax({
					url:ajaxurl,
					data:query,
					type:"POST",
					dataType:"json",
					success:function(data){
						$.hideIndicator();
						$.toast(data.show_err);
						if(data.user_login_status == 1)
						{
							try{
								var json = '{"id":"'+data.id+'","user_name":"'+data.user_name+'"}';
								App.login_success(json);
							}
							catch(e){
								
							}
							if(document.referrer.indexOf("login_out") > 0 || document.referrer.indexOf("register") > 0 ){
								RouterURL(WAP_PATH+'/index.php?ctl=init','#init',2);
						    }else{
								location.replace(document.referrer);
						    }
						}
					}
					,error:function(){
						$.hideIndicator();
						$.toast("通信失败");
					}
				});
				
			
			});
		});

	/*注册页面*/
	$(document).on("pageInit", "#register", function(e, id, page) {
		var is_lock_send_vy = null;
		$("#register #signup-submit").click(function(){
			var sname;
			sname=$.trim($("#register #user_pwd").val());
			var reg=/^[a-zA-z0-9]{6,}$/;  
			var regs=/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/;
			if(reg.test(sname)){	
				if(regs.test(sname)){	
				}
				else{
					$.alert("密码安全等级低，请用数字+字母");
					return false;
				}
			}
			else{
				$.alert("密码长度在6~16之间，只能包含字符、数字和下划线。");
				return false;
			}
			$.showIndicator();
			var ajaxurl = WAP_PATH +'/index.php?ctl=register';
			var query = new Object();
			query.user_name = $.trim($("#register #user_name").val());
			query.user_pwd = $.trim($("#register #user_pwd").val());
			query.user_pwd_confirm = $.trim($("#register #user_pwd_confirm").val());
			query.mobile = $.trim($("#register #phone").val());
			query.mobile_code = $.trim($("#register #mobile_code").val());
			query.referer = $.trim($("#register #referer").val());
			query.post_type = "json";
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.hideIndicator();
					if(data.user_login_status)
					{	
						$.alert(data.show_err,function(){
							RouterURL(WAP_PATH +'/index.php?ctl=register_idno','register_idno',1);
						});
					}else{
						$.alert(data.show_err);
					}
				}
				,error:function(){
					$.hideIndicator();
					$.toast("通信失败");
				}
			});
		});
		
		
		$("#register #getcode").click(function(){
			if(localStorage.sendtime !="undefined" &&  parseInt(new Date().getTime()/1000) - localStorage.sendtime < 60){
				$.alert("发送中，请稍后");
				return false;
			}
			if(is_lock_send_vy || $(this).hasClass(".btn_disable")){
				return false;
			}
			var ajaxurl = WAP_PATH +'/index.php?ctl=send_register_code';
			var query = newObject();
			query.mobile = $.trim($("#register #phone").val());
			query.post_type = "json";
			$.showIndicator();
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.hideIndicator();
					if(data.response_code == 1){
						localStorage.sendtime = parseInt(new Date().getTime()/1000);
						left_rg_time = 60;
						left_time_to_send_regvy($("#register #getcode"));
					}
					$.alert(data.show_err);
				},error:function(){
					$.hideIndicator();
					$.toast("通信失败");
				}
			});
		});
		
		$("#register #user_name").bind("blur",function(){
			var obj = $(this);
			var ajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=check_field";
			var query = new Object();
			query.field_name = "user_name";
			query.field_data = $.trim(obj.val());
			query.is_pc = 1;
			$.ajax({ 
				url: ajaxurl,
				data:query,
				type: "POST",
				dataType: "json",
				success: function(data){
					if(data.status!=1)
					{
						$.alert(data.info);
					}
				}
			});	
		}); /*用户名验证*/
	});
	
	/*重置支付密码*/
	$(document).on("pageInit", "#reset_pay_pwd", function(e, id, page) {

		var regsiter_vy_time = null;  	//定义时间
		var is_lock_send_vy = false;	//解除锁定
		var left_rg_time = 0;			//开始时间
		function left_time_to_send_regvy(){
			clearTimeout(regsiter_vy_time);
			if(left_rg_time > 0){
				regsiter_vy_time = setTimeout(left_time_to_send_regvy,1000);
				$("#reset_pay_pwd #getcode").val(left_rg_time+"秒后重获验证码");
				$("#reset_pay_pwd #getcode").addClass("btn_disable");
				left_rg_time -- ;
			}
			else{
				is_lock_send_vy = false;
				$("#reset_pay_pwd #getcode").removeClass("btn_disable");
				$("#reset_pay_pwd #getcode").val("重新获取验证码");
				
				left_rg_time = 0;
			}
		}


		$("#reset_pay_pwd #signup-submit").click(function(){
			var pay_pwd = $.trim($("#reset_pay_pwd #user_pwd").val());
			var pay_pwd_confirm = $.trim($("#reset_pay_pwd #user_pwd_confirm").val());
			if(pay_pwd!=pay_pwd_confirm){
				$.alert("新密码与确认密码不一致！");
				return false;
			}
			var ajaxurl = WAP_PATH +'/index.php?ctl=save_pay_pwd';			
			var query = newObject();
			query.mobile_code= $.trim($("#reset_pay_pwd #mobile_code").val());
			query.pay_pwd= $.trim($("#reset_pay_pwd #user_pwd").val());
			query.pay_pwd_confirm= $.trim($("#reset_pay_pwd #user_pwd_confirm").val());
			query.post_type='json';
			$.showIndicator();
			$.ajax({ 
				url: ajaxurl,
				data:query,
				type: "POST",
				dataType: "json",
				success: function(result){
					$.hideIndicator();
					if(result.response_code)
					{
						$.alert(result.show_err);
						RouterURL(WAP_PATH +'/index.php?ctl=uc_center','#uc_center');
					}
					else
					{	
						$.alert(result.show_err);
					}
				}
				,error:function(){
					$.hideIndicator();
					$.toast("通信失败");
				}
			});	
		});


		$("#reset_pay_pwd #getcode").click(function(){
			if(localStorage.sendtime !="undefined" &&  parseInt(new Date().getTime()/1000) - localStorage.sendtime < 60){
				$.toast("发送中，请稍后");
				return false;
			}
			if(is_lock_send_vy || $(this).hasClass(".btn_disable")){
				return false;
			}
			var ajaxurl = WAP_PATH +'/index.php?ctl=send_reset_pay_code';			
			var query = newObject();
			query.post_type='json';
			$.showIndicator();
			$.ajax({ 
				url: ajaxurl,
				data:query,
				type: "POST",
				dataType: "json",
				success: function(result){
					$.hideIndicator();
					if(result.response_code)
					{
						localStorage.sendtime = parseInt(new Date().getTime()/1000);
						left_rg_time = 60;
						left_time_to_send_regvy();
					}
					$.alert(result.show_err);
				}
				,error:function(){
					$.hideIndicator();
					$.toast("通信失败");
				}
			});	
		});
	});
	 
	
	/**
	 *注册  STEP2 
	 */
	$(document).on("pageInit", "#register_idno", function(e, id, page) {
		$("#register_idno #idno_submit").click(function(){
			var ajaxurl = WAP_PATH +'/index.php?ctl=register_idno';;
			var query = newObject();
			query.real_name = $.trim($("#register_idno #real_name").val());
			query.idno = $.trim($("#register_idno #idno").val());
			query.post_type = "json";
			$.showIndicator();
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.hideIndicator();
					if(data.status==1)
					{
						$.alert(data.show_err);
						if(data.acct_url)
							RouterURL(data.acct_url,"none",1);
						else
							RouterURL(WAP_PATH + '/index.php?ctl=uc_center',"#uc_center",2);
						
					}
					else{
						$.alert(data.show_err);
					}
				}
				,error:function(){
					$.hideIndicator();
					$.toast("通信失败");
				}
			});
		});
	});
	
	/**
	 * 忘记密码页
	 */
	$(document).on("pageInit", "#save_reset_pwd", function(e, id, page) {
		$("#save_reset_pwd #signup-submit").click(function(){
			var sname;
			sname=$.trim($("#save_reset_pwd #user_pwd").val());
			var reg=/^[a-zA-z0-9]{6,}$/;  
			var regs=/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/;
			if(reg.test(sname)){	
				if(regs.test(sname)){	
				}
				else{
					$.alert("安全等级低，请用数字+字母");
					return false;
				}
			}
			else{
				$.alert("长度在6~16之间，只能包含字符、数字和下划线。");
				return false;
			}
			var ajaxurl = WAP_PATH + '/index.php?ctl=save_reset_pwd';
			var query = newObject();
			query.mobile = $.trim($("#save_reset_pwd #mobile").val());
			query.mobile_code = $.trim($("#save_reset_pwd #mobile_code").val());
			query.user_pwd = $.trim($("#save_reset_pwd #user_pwd").val());
			query.user_pwd_confirm = $.trim($("#save_reset_pwd #user_pwd_confirm").val());
			query.post_type = "json";
			$.showIndicator();
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.hideIndicator();
					$.alert(data.show_err);
					if(data.response_code){
						RouterURL(WAP_PATH + '/index.php?ctl=login',"#login");
					}
				}
				,error:function(){
					$.hideIndicator();
					$.toast("通信失败");
				}
			});
		});
		
		$("#save_reset_pwd #getcode").click(function(){
			
			if(localStorage.sendtime !="undefined" &&  parseInt(new Date().getTime()/1000) - localStorage.sendtime < 60){
				$.alert("发送中，请稍后");
				return false;
			}
			if(is_lock_send_vy || $(this).hasClass(".btn_disable")){
				return false;
			}
			var ajaxurl = WAP_PATH +'/index.php?ctl=send_reset_pwd_code';;
			var query = newObject();
			query.mobile = $.trim($("#save_reset_pwd #mobile").val());
			query.post_type = "json";
			$.showIndicator();
			$.ajax({
				url:ajaxurl,
				data:query,
				type:"Post",
				dataType:"json",
				success:function(data){
					$.hideIndicator();
					if(data.response_code == 1){
						localStorage.sendtime = parseInt(new Date().getTime()/1000);
						left_rg_time = 60;
						left_time_to_send_regvy($("#save_reset_pwd #getcode"));
					}
					$.alert(data.show_err);
				}
				,error:function(){
					$.hideIndicator();
					$.toast("通信失败");
				}
			});
		});
	});
	
	$.init();
});
/*未开始项目倒计时*/
function project_leftTimeAct_f(left_time,project_leftTimeAct){
	clearTimeout(project_leftTimeAct);
	$(left_time).each(function(){
		var leftTime = parseInt($(this).attr("data"));
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
			leftTime = leftTime-1;
			$(this).attr("data",leftTime);
		}
		else{
			$(this).parent(".div_dt").hide();
			$(this).parent().next().show();
			return false;
		}
	});
	project_leftTimeAct = setTimeout(function(){
		project_leftTimeAct_f(left_time,project_leftTimeAct);
	},1000);
};
function project_deals_bind_menu()
{
	/*筛选分类*/ 
	var hideList_height = $(document).height();
	$(".hide_list").css("height",hideList_height+"px");
	$(".mall-cate>li").each(function(index){
		var $this = $(this);
		$this.bind({
			click:function(e){
				e.stopPropagation();
				/*初始化*/ 
				$(".abbr").removeClass("webkit-box");
				$(".hide_list").hide();
				$("#second_list>ul").hide();
				if(!($this.hasClass("cur"))){
					$this.addClass("cur").siblings().removeClass("cur");
					$(".hide_list").show().find(".abbr").eq(index).addClass("webkit-box").find("#second_list>ul").eq(index).show();
					$("#first_list li").each(function(index){
						var $this = $(this);
						$this.click(function(e){
							e.stopPropagation();
							$(".second_list>ul").hide();
							$this.addClass("select").siblings().removeClass("select");
							$(".second_list>ul").eq(index).show();
						});
					});
					$("#second_list>ul").hide();
				}
				else{
					$this.removeClass("cur");
					$(".abbr").eq(index).removeClass("webkit-box");
				}
			} ,
			change:function(){
				
			}
		});
	});
	
	$(".abbr").bind("click",function(e){
		e.stopPropagation();
	});
	
	$(document).click(function(){
		$(".mall-cate>li").removeClass("cur");
		$(".abbr").removeClass("webkit-box");
		$(".hide_list").hide();
		$("#second_list>ul").hide();
	});
}

/*截取url参数*/
function get_url_params(query)
{
   var url = location.search; /*获取url中"?"符后的字串*/
   var theRequest = query;
   if (url.indexOf("?") != -1) {
      var str = url.substr(1);
      strs = str.split("&");
      for(var i = 0; i < strs.length; i ++) {
         theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
      }
   }
   return theRequest;
}