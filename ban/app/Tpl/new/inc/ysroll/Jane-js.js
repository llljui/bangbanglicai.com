// JavaScript Document
//日期切换效果
 $(function(){
	 $('.choicetime a').click(function(){
		 var index = $(this).index();
		 $(this).addClass('active').siblings().removeClass('active');
		 $('.J-notice2-con ul').eq(index).show().siblings().hide();
		 });
	 });
//二维码显示
$(function(){
	$('.fc-conp-bg1').mouseover(function(){
		$('.fc-conp-ewm').show(500);
		});
	$('.fc-conp-bg1').mouseleave(function(){
		$('.fc-conp-ewm').hide();
		});
});
//app的显示
$(function(){
	$('.J-add-app span').hover(function(){
		$('.J-add-appimg').stop().slideDown(500);
		},function(){
		$('.J-add-appimg').stop().slideUp(500);
		});
	});
//底部app显示
$(function(){
	$('.fc-conp-bg3').hover(function(){
		$('.fc-conp-appimg').stop().show(500);
		},function(){
		$('.fc-conp-appimg').stop().hide(500);	
			});
	});
//私募基金的切换
/*$(function(){
	$('.J-smjjc-title li').click(function(){
		var index = $(this).index();
		$(this).addClass('J-active').siblings().removeClass('J-active');
		$('.J-act-con').eq(index).show().siblings().hide();
		});
});*/
//积分商城
$(function(){
	$('.J-Imc-top-condi ul:first').css('margin-bottom','10px');
	});
	
	
//积分明细切换
$(function(){
	$('.J-Imxcr-top li').on('click',function(){
		var index = $(this).index();
		$(this).addClass('J-Imccrt-select').siblings().removeClass('J-Imccrt-select');
		$('.J-Imxc-wen-r').eq(index).show().siblings().hide();
		});
	});
//向下无缝走动
$(function(){
	var $container = $('.J-Imxcw-div1 ul');
	var pages = $container.height();
	var liHeight = $
	});
//投资管理中的切换
$(function(){
	$('.J-mainv-click dd').on('click',function(){
		var index = $(this).index();
		$(this).addClass('J-mainvlist-current').siblings().removeClass('J-mainvlist-current');
		$('.J-mainvl-wen').eq(index).show().siblings().hide();
		});
	});
//债权转让
$(function(){
	$('.J-ma-zqzrl-wen dl').children('dd:first').css('width','18%');
	})
//充值
$(function(){
	$('.J-matu-toggle').on('click',function( event){
		$('.J-matu-morebank').slideToggle();
		});
});
//首页 banner
$(function(){
        var $banner=$('.banner');
        var $banner_ul=$('.banner-img');
        var $btn=$('.banner-btn');
        var $btn_a=$btn.find('a')
        var v_width=1920;
        
        var page=1;
        
        var timer=null;
        var btnClass=null;

        var page_count=$banner_ul.find('li').length;//把这个值赋给小圆点的个数
        
        var banner_cir="<li class='selected' href='#'><a></a></li>";
        for(var i=1;i<page_count;i++){
                //动态添加小圆点
                banner_cir+="<li><a href='#'></a></li>";
                }
        $('.banner-circle').append(banner_cir);
        
        var cirLeft=$('.banner-circle').width()*(-0.5);
        $('.banner-circle').css({'marginLeft':cirLeft});
        
        $banner_ul.width(page_count*v_width);
        
        function move(obj,classname){
                //手动及自动播放
        if(!$banner_ul.is(':animated')){
                if(classname=='prevBtn'){
                        if(page==1){
                                        $banner_ul.animate({left:-v_width*(page_count-1)});
                                        page=page_count; 
                                        cirMove();
                        }
                        else{
                                        $banner_ul.animate({left:'+='+v_width},"slow");
                                        page--;
                                        cirMove();
                        }        
                }
                else{
                        if(page==page_count){
                                        $banner_ul.animate({left:0});
                                        page=1;
                                        cirMove();
                                }
                        else{
                                        $banner_ul.animate({left:'-='+v_width},"slow");
                                        page++;
                                        cirMove();
                                }
                        }
                }
        }
        
        function cirMove(){
                //检测page的值，使当前的page与selected的小圆点一致
                $('.banner-circle li').eq(page-1).addClass('selected').siblings().removeClass('selected');
        }
        
        $banner.mouseover(function(){
                $btn.css({'display':'block'});
                clearInterval(timer);
                                }).mouseout(function(){
                $btn.css({'display':'none'});                
                clearInterval(timer);
                timer=setInterval(move,3000);
                                }).trigger("mouseout");//激活自动播放

        $btn_a.mouseover(function(){
                //实现透明渐变，阻止冒泡
                        $(this).animate({opacity:0.6},'fast');
                        $btn.css({'display':'block'});
                         return false;
                }).mouseleave(function(){
                        $(this).animate({opacity:0.3},'fast');
                        $btn.css({'display':'none'});
                         return false;
                }).click(function(){
                        //手动点击清除计时器
                        btnClass=this.className;
                        clearInterval(timer);
                        timer=setInterval(move,3000);
                        move($(this),this.className);
                });
                
        $('.banner-circle li').live('click',function(){
                        var index=$('.banner-circle li').index(this);
                        $banner_ul.animate({left:-v_width*index},'slow');
                        page=index+1;
                        cirMove();
                });
});
//侧边栏滑出电话效果
$(function(){
	$('#J-phonepic').mouseenter(function(){
		$('.J-phonenumb').show().addClass('J-addwidth');
		});
	$('#J-phonepic').mouseleave(function(){
		$('.J-phonenumb').hide().removeClass('J-addwidth');
	 });
});
//我要借款切换
$(function(){
	$('.J-jkcc-title li').click(function(){
		var index = $(this).index();
		$(this).addClass('J-jkc-select').siblings().removeClass('J-jkc-select');
		$('.J-jkcc-list').eq(index).show().siblings().hide();
		});
	});
//我要投资项目列表中的切换
$(function(){
	
	$('.J-liemu span').click(function(){
		$(this).addClass('J-chose').siblings().removeClass('J-chose');
		
	});

});

//债券转让项目列表中的切换
$(function(){
	$('.Z-liemu span').click(function(){
		$(this).addClass('J-chose').siblings().removeClass('J-chose');
		});
	});
//弹窗
$(function(){
	$('.big-link').click(function(){
		$('#myModal').show(500);
		});
	});

//红包页和好友页
$(function(){
	 $('.jhb_num').children('ul:last').css({
		 'border-bottom-color':'#ddd',
		 'border-bottom-width':'1px',
		 'border-bottom-style':'solid',
		 });
	  $('.jhbn-two ul').children('li:last-child').css('color','#ec4b5e');
	 });

	