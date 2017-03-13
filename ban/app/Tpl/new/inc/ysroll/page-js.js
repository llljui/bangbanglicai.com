$(function(){

	/*page5的切换效果*/
	$(".page5 .con1 .pic1 a").click(function(event) {
		$(this).parents().siblings('.pic2').children('img').eq($(this).index()).show().siblings('').hide();
		$(this).addClass('yel').siblings('').removeClass('yel');
	});

	//翻页效果	
	$('#fullpage').fullpage({
		verticalCentered:true,
		sectionsColor:['#71baed','#40cbea','#ffc67d','#48cdb5','#939ed7','#f89596'],
		navigation:true,
		scrollingSpeed:1500,
		navigationPosition:'right',
		afterLoad:function(anchorLink,index){
			if(index == 1){
				$('.page .page1 .con1 .pic').addClass('draw');
				$(".big_arr").show();
			}
			if(index == 2){
				$('.page .page2 .con1 ul li,.page2 .con1 .title').addClass('draw');
				$(".big_arr").show();
			}
			
			if(index == 3){
				$('.page .page3 .con1 ul,.page3 .con1 .title').addClass('draw');
				$(".big_arr").show();
			}
			
			if(index == 4){
				$('.page .page4 .con1 .txt div,.page4 .con1 .title,.page4 .con1 .txt li img').addClass('draw');
				$(".big_arr").show();
			}
			
			if(index == 5){
				$('.page5 .con1 .pic div,.page5 .con1 .title').addClass('draw');
				$(".big_arr").show();

			}
			
			if(index == 6){
				$('.page6 .con1 .get_btn,.page6 .con1 div').addClass('draw');
				$(".big_arr").hide();
			}
		},
		
		//翻页动画
		onLeave:function(index,nextIndex,direction){
			if(index == 1 && direction == 'down'){
				$('.page .page1 .con1 .pic').removeClass('draw');
				$('.pc .page_2 .bg div').addClass('draw');
			}else if (index == 2 && direction == 'up'){
				$('.page .page2 .con1 ul li,.page2 .con1 .title').removeClass('draw');
				$('.page .page1 .con1 .pic').addClass('draw');
			}else if(index == 2 && direction == 'down'){
				$('.page .page2 .con1 ul li,.page2 .con1 .title').removeClass('draw');
				$('.page .page3 .con1 ul,.page3 .con1 .title').addClass('draw');
			}else if(index == 3 && direction == 'up'){
				$('.page .page2 .con1 ul li,.page2 .con1 .title').addClass('draw');
				$('.page .page3 .con1 ul,.page3 .con1 .title').removeClass('draw');
			}else if(index == 3 && direction == 'down'){
				$('.page .page3 .con1 ul,.page3 .con1 .title').removeClass('draw');
				$('.page .page4 .con1 .txt div,.page4 .con1 .title,.page4 .con1 .txt li img').addClass('draw');	
			}else if(index == 4 && direction == 'up'){
				$('.page .page3 .con1 ul,.page3 .con1 .title').addClass('draw');
				$('.page .page4 .con1 .txt div,.page4 .con1 .title,.page4 .con1 .txt li img').removeClass('draw');	
			}else if(index == 4 && direction == 'down'){
				$('.page .page4 .con1 .txt div,.page4 .con1 .title,.page4 .con1 .txt li img').removeClass('draw');
				$('.page5 .con1 .pic div,.page5 .con1 .title').addClass('draw');	
			}else if(index == 5 && direction == 'up'){
				$('.page .page4 .con1 .txt div,.page4 .con1 .title,.page4 .con1 .txt li img').addClass('draw');
				$('.page5 .con1 .pic div,.page5 .con1 .title').removeClass('draw').addClass('move_up');
			}else if(index == 5 && direction == 'down'){
				$('.page5 .con1 .pic div,.page5 .con1 .title').removeClass('draw');
				$('.page6 .con1 .get_btn,.page6 .con1 div').addClass('draw');
			}else if(index == 6 && direction == 'up'){
				$('.page5 .con1 .pic div,.page5 .con1 .title').addClass('draw');
				$('.page6 .con1 .get_btn,.page6 .con1 div').removeClass('draw');
			}
		}				
	});
});