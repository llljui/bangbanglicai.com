 
 =============================================
 Router.prototype.getPage
	this.xhr = $.ajax({
            url: url,
            timeout:10000,//加的
 
 =============================================
 
 Router.prototype.loadPage = function(url) {
 
             var id = this.genStateID();
            this.setCurrentStateID(id);
			url = url.replace("&hasleftpanel=1","");//加的
			url = url.replace("?hasleftpanel=1","");//加的
            this.pushState(url, id);
 
 =============================================
 
Router.prototype.animatePages = function(leftPage, rightPage, leftToRight) {
    	$(".pull-to-refresh-layer .pull-to-refresh-arrow").css({"opacity":0});//加的
        var curPageClass = 'page-current';
        var animPageClasses = [
            'page-from-center-to-left',
            'page-from-center-to-right',
            'page-from-right-to-center',
            'page-from-left-to-center'].join(' ');

        if (!leftToRight) {
            // 新页面从右侧切入
            rightPage.trigger("pageAnimationStart", [rightPage[0].id, rightPage]);
            leftPage.removeClass(animPageClasses).removeClass(curPageClass).addClass('page-from-center-to-left');
            rightPage.removeClass(animPageClasses).addClass(curPageClass).addClass('page-from-right-to-center');
            leftPage.animationEnd(function() {
                leftPage.removeClass(animPageClasses);
                $(".pull-to-refresh-layer .pull-to-refresh-arrow").css({"opacity":1});//加的
            });
            rightPage.animationEnd(function() {
                rightPage.removeClass(animPageClasses);
                rightPage.trigger("pageAnimationEnd", [rightPage[0].id, rightPage]);
                rightPage.trigger("pageInitInternal", [rightPage[0].id, rightPage]);
                 $(".pull-to-refresh-layer .pull-to-refresh-arrow").css({"opacity":1});//加的
            });
        } else {
            leftPage.trigger("pageAnimationStart", [rightPage[0].id, rightPage]);
            leftPage.removeClass(animPageClasses).addClass(curPageClass).addClass('page-from-left-to-center');
            rightPage.removeClass(animPageClasses).removeClass(curPageClass).addClass('page-from-center-to-right');
            leftPage.animationEnd(function() {
                leftPage.removeClass(animPageClasses);
                leftPage.trigger("pageAnimationEnd", [leftPage[0].id, leftPage]);
                leftPage.trigger("pageReinit", [leftPage[0].id, leftPage]);
                 $(".pull-to-refresh-layer .pull-to-refresh-arrow").css({"opacity":1});//加的
            });
            rightPage.animationEnd(function() {
                rightPage.removeClass(animPageClasses);
                $(".pull-to-refresh-layer .pull-to-refresh-arrow").css({"opacity":1});//加的
            });
        }

    };
 =============================================