$(document).ready(
		function() {
			$('.menu li a').hover(function() {
				$(this).parent('.menu li').find('div').show();
				$(this).parent('.menu li').addClass('on');
			});
			$('.menu li').mouseleave(function() {
				$(this).find('div').hide();
				$(this).removeClass('on');
			});
			$('.menu dl dt').hover(function() {
				$(this).parent('.menu dl').find("dd").show();
				$(this).addClass("on");
			});
			$('.menu dl').mouseleave(function() {
				$(this).find("dd").hide();
				$(this).find("dt").removeClass("on");
			});
			$('.tab span').click(function() {
				$(this).next('b').css('display', 'block');
				$(this).siblings().next('b').hide();
			});
			$('.code_Android').hover(function() {
				$(this).find('span').show();
			}).mouseleave(function() {
				$(this).find('span').hide();
			});
			$('.code_IOS').hover(function() {
				$(this).find('span').show();
			}).mouseleave(function() {
				$(this).find('span').hide();
			});
			$('.footer_code a').hover(function() {
				$(this).find('b').show(300);
			}).mouseleave(function() {
				$(this).find('b').hide(300);
			});
			$('.invest_box_Info div.fleft').hover(function() {
				$(this).find('div.user').show();
			}).mouseleave(function() {
				$(this).find('div.user').hide();
			});
			$('.borrow_tab a').click(
					function() {
						$('.borrow_tab span').siblings().css(
								'background-position', '-104px -100px');
						$('.borrow_tab span.last').css('background-position',
								'-214px -100px');
						$(this).next('span').css('background-position',
								'-104px 0');
						$(this).prev('span').css('background-position',
								'-104px -50px');
					});
			$('.borrow_tab a:last').click(
					function() {
						$(this).next('span').css('background-position',
								'-214px -148px');
					});
			$('.title_tab span:first').click(function() {
				$(this).addClass('on');
				$('.title_tab span').removeClass('on2');
				$('#info_tab1').show(300);
				$('#info_tab2').hide(300);
				$('#info_tab3').hide(300);
			});
			$('.title_tab span:eq(1)').click(function() {
				$(this).addClass('on2');
				$(this).next('span').removeClass('on2');
				$(this).prev('span').removeClass('on');
				$('#info_tab1').hide(300);
				$('#info_tab2').show(300);
				$('#info_tab3').hide(300);
			});
			$('.title_tab span:last').click(function() {
				$(this).addClass('on2');
				$('.title_tab span:first').removeClass('on');
				$(this).prev('span').removeClass('on2');
				$('#info_tab1').hide(300);
				$('#info_tab2').hide(300);
				$('#info_tab3').show(300);
			});
			$('.bao_tab span:first').click(function() {
				$(this).addClass('on');
				$(this).siblings().removeClass('on');
				$('#info_tab1').show(300);
				$('#info_tab2').hide(300);
			});
			$('.bao_tab span:eq(1)').click(function() {
				$(this).addClass('on');
				$(this).siblings().removeClass('on');
				$('#info_tab1').hide(300);
				$('#info_tab2').show(300);
			});
			$('a.close').click(function() {
				$(this).parent('.member_tips').hide(300);
			});
			$('.memberLeft dt').click(function() {
				$(this).addClass("on");
				$(this).siblings().removeClass("on");
				$('.memberLeft dd').hide(200);
				$(this).next("dd").show(200);
			});
			$('.icon_publish').hover(function() {
				$('div.div').show(300);
			});
			$('.bbs_tt').mouseleave(function() {
				$(this).find('div.div').hide(300);
			});
			$('.bank label').click(function() {
				$(this).addClass('on');
				$(this).siblings().removeClass('on');
				$('.zhifu label').siblings().removeClass('on');
			});
			$('.zhifu label').click(function() {
				$(this).addClass('on');
				$(this).siblings().removeClass('on');
				$('.bank label').siblings().removeClass('on');
			});
			$(".rightIcon span").hover(function() {
				$(this).find('em').stop().show(100);
			}).mouseleave(function() {
				$(".rightIcon span em").stop().hide(100);
			});
			$(".rightIcon span").hover(function() {
				$(this).find('font').show();
			}).mouseleave(function() {
				$(".rightIcon span font").hide();
			});
			$(window).scroll(function() {
				if ($(window).scrollTop() > 100) {
					$("#back-to-top").fadeIn(1000);
					$("rightIcon").css('right', '5%');
				}

				else {
				}
			});
			$("#back-to-top,#back-to-top span").on('click', function() {
				$("#rightIcon").fadeOut(1000);
			});
		});
$(function() {
	var sw = 1;
	$("#banner .num a").mouseover(function() {
		sw = $(".num a").index(this);
		myShow(sw);
	});

	function myShow(i) {
		$("#banner .num a").eq(i).addClass("cur").siblings("a").removeClass(
				"cur");
		$("#banner ul li").eq(i).stop(true, true).fadeIn(600).siblings("li")
				.fadeOut(600);
		$("#banner ol li").eq(i).stop(true, true).fadeIn(600).siblings("li")
				.fadeOut(600);
	}
	$("#banner").hover(function() {
		if (myTime) {
			clearInterval(myTime);
		}
	}, function() {
		myTime = setInterval(function() {
			myShow(sw)
			sw++;
			if (sw == 5) {
				sw = 0;
			}
		}, 5000);
	});

	var myTime = setInterval(function() {
		myShow(sw)
		sw++;
		if (sw == 5) {
			sw = 0;
		}
	}, 3500);
})
function tab(num) {
	for ( var id = 0; id <= 9; id++) {
		if (id == num) {
			document.getElementById("on" + id).style.display = "block";
			document.getElementById("myon" + id).className = "on";
		}

		else {
			document.getElementById("on" + id).style.display = "none";
			document.getElementById("myon" + id).className = "";
		}
	}
}

function index_tab(num) {
	for ( var id = 0; id <= 9; id++) {
		if (id == num) {
			document.getElementById("non" + id).style.display = "block";
			document.getElementById("nmyon" + id).className = "on";
		}

		else {
			document.getElementById("non" + id).style.display = "none";
			document.getElementById("nmyon" + id).className = "";
		}
	}
}

function tab2(num) {
	for ( var id = 0; id <= 9; id++) {
		if (id == num) {
			document.getElementById("o2n" + id).style.display = "block";
			document.getElementById("my2on" + id).className = "on";
		}

		else {
			document.getElementById("o2n" + id).style.display = "none";
			document.getElementById("my2on" + id).className = "";
		}
	}
}

function ShowDiv(show_div, bg_div) {
	document.getElementById(show_div).style.display = 'block';
	document.getElementById(bg_div).style.display = 'block';
	var bgdiv = document.getElementById(bg_div);
	bgdiv.style.width = document.body.scrollWidth;
	$("#" + bg_div).height($(document).height());
};

function CloseDiv(show_div, bg_div) {
	document.getElementById(show_div).style.display = 'none';
	document.getElementById(bg_div).style.display = 'none';
};

function tabArticle(num) {
	for ( var id = 1; id <= 10; id++) {
		if (id == num) {
			document.getElementById("item" + id).className = "on";
		}

		else {
			document.getElementById("item" + id).className = "";
		}
	}
}

function fixedSidebar() {
	var side = $("#test2");
	var sideTop = side.offset() + side.height();
	var h = $("#test").height() - side.height();
	var timer = 300;
	var fixStatus = function() {
		var h = $("#test").height() - side.height();
		var winHeight = $(window).height();
		var scrollTop = $(document).scrollTop();
		var tempBottom = scrollTop + winHeight - sideTop;
		if (tempBottom > h) {
			tempBottom = h
		}

		if (scrollTop + winHeight > sideTop) {
			side.css({
				position : "absolute",
				right : "0"
			}).stop(true).animate({
				top : (tempBottom - 21)
			}, timer)
		}

		else {
			side.css({
				position : "static",
				right : "0"
			}).stop(true).animate({
				top : 0
			}, timer)
		}
	};
	$(window).scroll(function() {
		fixStatus()
	});
	$(window).resize(function() {
		fixStatus()
	})
}
$(function() {
	fixedSidebar()
});

function reloads(target) {
	var _this = $("#refresh" + target);
	var soffset = _this.offset().top;
	window.location.reload();
	setTimeout(function() {
		$("body,html").animate({
			scrollTop : soffset
		}, 10)
	}, 200)
};