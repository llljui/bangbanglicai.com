
$(document).ready(function(){
	if($.browser.msie) {			
		var SH=$(window).height();
		$('.contactusdiyou').css('height',SH+50);	
	}
	
});

$(function(){

	$(".hoverbtn").bind('click',function(){
     		$(".hoverbtn").toggleClass("v"); 
     		if ($(".hoverbtn").hasClass("v")) {
     			$(".hoverimg").attr("src",TMPL+"/images/hoverbtnbg1.gif");
     			if($.browser.msie) {
     			
				    // 此浏览器为 IE
				    
				} else {
				    $('.diyoumask').fadeIn();
				}
				$('.contactusdiyou').animate({right:'0'},300);		
     		}
     		else{
     			$(".hoverimg").attr("src",TMPL+"/images/hoverbtnbg.gif");
     			$('.contactusdiyou').animate({right:'-230px'},300,function(){});
     			if($.browser.msie) {
				    // 此浏览器为 IE
				} else {
				    $('.diyoumask').fadeOut();
				}
     		}
  });

});


/*鼠标上移显示*/



function aqln_hover(){
	$(".contactusdiyou").mouseover(function(){
		$(".contactusdiyou").oneTime(50,function(){  
			$('.diyoumask').show();
			$('.contactusdiyou').animate({right:'0'},1000);			
		 });
	});
}
function aqln_leave(){
	$(".contactusdiyou").mouseleave(function(){
		$(".contactusdiyou").stopTime(); 
		$('.contactusdiyou').animate({right:'-230px'},1000,function(){$('.diyoumask').hide();});
	});
}
