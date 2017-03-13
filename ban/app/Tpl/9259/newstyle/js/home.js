// JavaScript Document

$(function(){
$(".i-hezuo-btn").click(function(){
$("#more-hezuo").slideToggle("slow");
//$(this).toggleClass("active"); return false;//触发后然后改变小图标方向，css在.active定义的
});
});

$(function(){	
	$('.title-list li').mouseover(function(){
		var liindex = $('.title-list li').index(this);
		$(this).addClass('on').siblings().removeClass('on');
		$('.product-wrap div.product').eq(liindex).fadeIn(150).siblings('div.product').hide();
		var liWidth = $('.title-list li').width();
		$('.title-list span').stop(false,true).animate({'left' : liindex * liWidth + 'px'},500);
	});	
});