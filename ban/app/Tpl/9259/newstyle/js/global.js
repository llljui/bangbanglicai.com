
function gotoLogin(){
	window.location.href = logoutforlogin();
}
function logoutforlogin(){
	rlturl = getLocalhostPath()+"/MemberCenter/login.html";
	return rlturl;
}
//页头判断是否登录：如果传入的值为“1”说明是所有页面进行判段，
//如果传入的值为“2”说明是需要登录的页面，如果是其它值说明是需要登录并且有特殊字段需要处理
function loginandonlogin(str){
	var url ="";
	var urls = window.location.href;
	if(urls.split("login").length>1||urls.split("register").length>1||urls.split("find_0").length>1||urls.split("find_2").length>1||urls.split("Register").length>1){
		url=getHpoleFrontURLhs()+"SY810S" ;
	}else{
		url=getHpoleFrontURL()+"SY810S";
	}
	var backUrl = "";
//	if(urls.split("login").length>1){
//		url=getHpoleFrontURLhs()+"SY810S" ;
//		 backUrl =getHpoleURL()+"/payfor/httpsfor.jsp"
//	}
	$.ajax({
		type : "post",
		dataType : "json",
		url : url,
		data:{'temp':1},
		success : function(datas) {
			if(datas.code=="Succeeded"){
//				if(str==1||str==7){//页头判断是否登录
					$("#islogin").removeClass('disNone');
					$("#noislogin").addClass('disNone');
					var isdiv=" <a class=\"btn btn-warning banner-btn\" href=\"/investment/investlist_0.html\">去投资</a>";
					$("#isdiv").html(isdiv);
					var variihtml="";
					var enrolval ="";
					if(datas.data.nickname == '')
					{
						enrolval=datas.data.mobilephone;
					}
					else
					{
						enrolval=datas.data.nickname;
					}	
					if(urls.split("login").length>1||urls.split("register").length>1||urls.split("find_0").length>1||urls.split("find_2").length>1){
						variihtml+="您好，<a class=\"col-b3\"href='javascript:;' onclick='openCenter(1)' id=ezname>"+enrolval+"</a><a class=\"col-b3\"href='javascript:;' style='display:none'  id=membername>"+datas.data.enrolname+"</a><span class='message' onclick=mymessage()><span>0</span></span><a href=javascript:void(0); onclick=onlogin(1)>[退出]</a>";
					}else{
						variihtml+="您好，<a class=\"col-b3\"href='javascript:;' onclick='openCenter(1)' id=ezname>"+enrolval+"</a><a class=\"col-b3\"href='javascript:;' style='display:none'  id=membername>"+datas.data.enrolname+"</a><span class='message' onclick=mymessage()><span>0</span></span><a href=javascript:void(0); onclick=onlogin()>[退出]</a>";
					}
					$("#islogin").html(variihtml);
					wdmessage(0);
//					if(backUrl!=""){
//						location.href=backUrl+";jsessionid="+datas.JSESSIONID;
//					}
//				}else 
				if(str==3){
					$("#relaname").html(datas.data.realname);
				}else if(str==4){ 
					$("span[name^=mycode]").each(function() {
						$(this).html(datas.data.recomcode);
					});
					$("#recomed").val(datas.data.recomcode);
					$("#enmae").html(datas.data.enrolname);
				}
//			}
		}else if(datas.code!="Succeeded"){
			$("a[name^=zhuImg]").each(function() {
				$(this).attr({'href':'/help/help.html'});
			});
			if(str!=1){//如果没有登录则进入登录页面
				var sl="";
				if(str=="4")
				{
					sl=	"#share";
				}
				location.href=logoutforlogin()+sl;
			}
		}
	}
});
}


//未读消息
function wdmessage(str){
        var url ="";
	var urls = window.location.href;
	if(urls.split("find_0").length>1||urls.split("find_2").length>1){
		url =getHpoleFrontURLhs()+"SY811S"
	}else{
		url =getHpoleFrontURL()+"SY811S"
	}
	$.ajax({
		type : "post",
		dataType : "json",
		url : url,
		success : function(datas) {
			var count=datas.data.count;
			var htmlv="";
			var html1="";
			if(count==0){
				htmlv="";
				html1=0;
			}else if(count>0&&count<=99){
				htmlv="<span>"+datas.data.count+"</span>";
				html1=datas.data.count;
			}else if(count>99){
				htmlv="<span>"+99+"+</span>";
				html1=datas.data.count;
			}
			if(str==0){
				
				$(".message").html(htmlv);
			}else if(str==1){
				$("#vd").text(html1);
			}else if(str==2){
				$("#vd").text(html1);
				$(".message").html(htmlv);
			}
			
			}
		});
}

function mymessage(falg){
	if(falg==1){
		location.href=getHpoleURLhs()+"/MemberCenter/news.html"
	}else{
		location.href=getHpoleURL()+"/MemberCenter/news.html"
	}
	
}
/*下拉菜单end*/
function openCenter(str){
	if(str==1){
		window.location.href=getHpoleURL()+"/MemberCenter/member.html";
	}else if(str==2){
		window.location.href=getHpoleURL()+"/help/help.html";
	}else if(str==3){
		window.location.href=getHpoleURL()+"/about/aboutUs.html";
	}
	
}
//会员中心左边菜单点击样式

$(".vipNav ul li a").click(function(){
	$(".vipNav ul li a").removeClass("active");
	$(this).addClass("active");
});

function cleft(str){
	$(".vipNav ul li a").removeClass("active");
	$(".vipNav ul li a").eq(str).addClass("active");
	$(".subli>a").css("color","#3482da");
	$(".subli").hover(function(){
		$(".subli span").removeClass().addClass("sup");
		$(".subnav").slideDown(300);
	},function(){
		$(".subli span").removeClass().addClass("sdown");
		$(".subnav").hide();
	});	
}

function topleft(str){
	$(".top-Nav ul li .m").eq(str).addClass("cur");
	$(".top-Nav ul li .m").mouseover( function(){
		$("#nav-list li .m").removeClass("cur");			
	})
	$(".top-Nav ul li .m").mouseout( function(){
		$(".top-Nav ul li .m").eq(str).addClass("cur");	
	})
	$(".subli").hover(function(){
		$(".subli span").removeClass().addClass("sup");
		$(".subnav").slideDown(300);
	},function(){
		$(".subli span").removeClass().addClass("sdown");
		$(".subnav").hide();
	});	
}

function aboutleft(str){
	$(".aboutNav-con  li a").removeClass("act");
	$(".aboutNav-con  li a").eq(str).addClass("act");
}
//帮助中心左边菜单
function set_tab(tab, id, num) {
    for ( var i = 1; i <= num; i++ ) {
       jQuery("#" + tab + '_' + i).removeAttr('class');
         }
       jQuery("#" + tab + '_' + id).attr('class', 'act');
 }

//全站右侧浮层代码
$(function(){
	
	var tophtml="<div id=\"izl_rmenu\" class=\"izl-rmenu\"><a href=\"http://wpa.b.qq.com/cgi/wpa.php?ln=1&key=XzkzODAwMDA0NV8zMTAwNzBfNDAwNjYwNTYwM18yXw\"  target=\"_blank\" class=\"fubtn fubtn-qq\"></a><div class=\"fubtn fubtn-phone\"><div class=\"phone\">400-6605-603</div></div><div class=\"fubtn fubtn-top\"></div></div>";
	$("#top").html(tophtml);
	$("#izl_rmenu").each(function(){
		$(this).find(".fubtn-phone").mouseenter(function(){
			$(this).find(".phone").fadeIn("fast");
		});
		$(this).find(".fubtn-phone").mouseleave(function(){
			$(this).find(".phone").fadeOut("fast");
		});		
		$(this).find(".fubtn-top").click(function(){
			$("html, body").animate({
				"scroll-top":0
			},"fast");
		});
	});
	var lastRmenuStatus=false;
	$(window).scroll(function(){//bug
		var _top=$(window).scrollTop();
		if(_top>200){
			$("#izl_rmenu").data("expanded",true);
		}else{
			$("#izl_rmenu").data("expanded",false);
		}
		if($("#izl_rmenu").data("expanded")!=lastRmenuStatus){
			lastRmenuStatus=$("#izl_rmenu").data("expanded");
			if(lastRmenuStatus){
				$("#izl_rmenu .fubtn-top").slideDown();
			}else{
				$("#izl_rmenu .fubtn-top").slideUp();
			}
		}
	});
});
