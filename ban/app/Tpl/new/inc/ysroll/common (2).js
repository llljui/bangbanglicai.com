
$(function() {
	
	// 首页微信

	$(".header_L .weixin").hover(function() {
		$(this).children('.weixin_pic').stop().slideDown(500);
	}, function() {
		$(this).children('.weixin_pic').stop().slideUp(500);
	});

	// 首页nav
	$(".nav li").click(function(event) {
		$(this).addClass('current').siblings('').removeClass('current');
	});

    //公用-输入框提示
	$('.input input').focus(function(){
		$(this).addClass('col');
		if($(this).val() ==this.defaultValue){  
			$(this).val("");           
		} 
	}).blur(function(){
		$(this).removeClass('col');
		if ($(this).val() == '') {
			$(this).val(this.defaultValue);
		}
	});

	// 项目详情页tap
	$(".plan_del .title a").click(function(event) {
		$(this).parents().siblings('.detail_con').children('.pro1').eq($(this).index()).show().siblings('.pro1').hide();
		$(this).addClass('onchoice').siblings('').removeClass('onchoice');

	});

	// 账户中心-消息中心tap
	$(".xx_con .title a").click(function(event) {
		$(this).addClass('tap').siblings('').removeClass('tap');
		$(this).parents().siblings('.xx_txt').children('ul').eq($(this).index()).show().siblings('ul').hide();
	});
	$(".xx_con .xx_txt li").click(function(event) {
		$(this).toggleClass('read');
	});

	// 账户中心-密码设置tap
	$(".mm_con .title a").click(function(event) {
		$(this).addClass('tap').siblings('').removeClass('tap');
		$(this).parents().siblings('.mm_txt').children('.code_num').eq($(this).index()).show().siblings('.code_num').hide();
	});
	
});