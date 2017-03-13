jQuery(function($) {
	$('.nav-1').addClass('li_border');
	$('#thutree[usemap]').rwdImageMaps();
	
	//上下轮播
    $(".topLoop").slide({
        mainCell:".bd ul",
        effect:"topLoop",
        autoPlay:true,
        vis:7,
        scroll:1,
        trigger:"click"
    });
	
	$(window).scroll(function() {
		if ($(window).scrollTop() > 1000)
			$('.gotop').show();
		else
			$('.gotop').hide();
	});
	$('.gotop').click(function() {
		$('html, body').animate({
			scrollTop : 0
		}, 1000);
	});
});
/*立即投资*/
$('.info-div').mouseenter(function(){

	  $(this).children('.sumbit').children('.bound').slideDown("1000");
	});
	$('.info-div').mouseleave(function(){
		$(this).children('.sumbit').children('.bound').slideUp("1000");
	});


 
//圆形进度条
var radialObj4 = $('.indicatorContainer4').radialIndicator({
    barColor: {
        0: '#ff7040',
        33: '#ff7040',
        66: '#ff7040',
        100: '#ff7040'
    },
    percentage: true
}).data('radialIndicator');
//Using Instance
radialObj4.animate($($('.indicatorContainer4').children("input")).val());
/**/

