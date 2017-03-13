/************zhaoyu 20150303***************/

/*
 * 首页加载时数据
 */
$(document).ready(function () {
	topleft(0);
	//loginandonlogin(1);
	onloand1();
	//initInvestment(1);
	//initInvestmentzqzr(1);
	ggchuli();//公告处理
	hottest();//最热认购
	meitichuli();//媒体
//	changeBannerbg()//banner背景图片处理
	$("#publicdebtday").html(publicdebtday);
});
	var receivedbenefits=1;//累计收益--已收
	var accruedincome=1;//累计收益--未收
	var totalrece=1;//累计收益--总收入
	var avgloanrate=1;//平均年利率
	var avgloanterm=1;//借款周期
	var avgmanbiaotime=1;//标满时间
	var bidamount=1;//投资总额
	var totalcountbid=1;//投资总条数
	var havemoneyback=1;//已归还用户本金
	var totalcountloan=1;//成功借款笔数
	var bidUNIT="";
	var totalUNIT="";
//首页借款投资笔数金额
function onloand1(){
			totalUNIT="位";
			bidUNIT="元";
    		avgloanrate=formatMoney("12.8",1);//平均年利率

			$("#totalrece").html(totalrece);
			$("#bidUNIT").html(bidUNIT);
			$("#totalUNIT").html(totalUNIT);
			$("#bidamount").html(bidamount);
			$("#totalcountloan").html("<span class='intnum' data-to="+1+"></span>");
			$("#avgloanrate").html(avgloanrate);			
			var d = new Date();
			var y=d.getFullYear();
			var m=(d.getMonth()+1);			
			var de=d.getDate();
			var de1=d.getDate()-1;
			if(m<10){
				m="0"+m;
			}
			if(de<10){
				de="0"+de;
			}
			if(de1<10){
				de1="0"+de1;
			}
			var str = d.getFullYear()+"-"+m+"-"+de;
			var totalrece=0;
			var bidamount=0;
			var longTime=DateDiff('2015-09-07',str);
//			$("#pd").html("(数据截至"+d.getFullYear()+"年"+m+"月"+de1+"日)");
			$("#dp").html("<span class='intnum' data-to="+longTime+"></span>");
			numberAdd("timer",1);

}

function ggchuli(){
	var titleid = $("#gonggaoid").val();
	if(!isEmpty(titleid)){ 
		$("#ggtime").text(titleid.split("#")[1]);
	}
}

function numberAdd(nclass,n) {
    $.fn.countTo = function(options) {
        options = options || {};
        return $(this).each(function() {
            var settings = $.extend({},
            $.fn.countTo.defaults, {
                from: $(this).data('from'),
                to: $(this).data('to'),
                speed: $(this).data('speed'),
                refreshInterval: $(this).data('refresh-interval'),
                decimals: $(this).data('decimals')
            },
            options);
            var loops = Math.ceil(settings.speed / settings.refreshInterval),
            increment = (settings.to - settings.from) / loops;
            var self = this,
            $self = $(this),
            loopCount = 0,
            value = settings.from,
            data = $self.data('countTo') || {};
            $self.data('countTo', data);
            if (data.interval) {
                clearInterval(data.interval)
            }
            data.interval = setInterval(updateTimer, settings.refreshInterval);
            render(value);
            function updateTimer() {
                value += increment;
                loopCount++;
                render(value);
                if (typeof(settings.onUpdate) == 'function') {
                    settings.onUpdate.call(self, value)
                }
                if (loopCount >= loops) {
                    $self.removeData('countTo');
                    clearInterval(data.interval);
                    value = settings.to;
                    if (typeof(settings.onComplete) == 'function') {
                        settings.onComplete.call(self, value)
                    }
                }
            }
            function render(value) {
                var formattedValue = settings.formatter.call(self, value, settings);
                $self.html(formattedValue)
            }
        })
    };
    $.fn.countTo.defaults = {
        from: 0,
        to: 0,
        speed: 500,
        refreshInterval: 100,
        decimals: 0,
        formatter: formatter,
        onUpdate: null,
        onComplete: null
    };
    function formatter(value,settings) {
    	if(n==1){
    		return formatMoney(value,0);
    	}else if(n==2){
    		return formatMoney(value.toFixed(settings.decimals),0);
    	}
    }
    $('.'+nclass).each(count);
    function count(options) {
        var $this = $(this);
        options = $.extend({},
        options || {},
        $this.data('countToOptions') || {});
        $this.countTo(options)
    }
};

//投资专区
function initInvestment(prdType){
	var url =getHpoleFrontPageURL()+"SY860L" ; 
		$.ajax({
			type : "post",
			dataType : "json",
			url : url,
			data:{'loantype':3,'maxrows':8}, //3正常  1债转  0 both 
			success : function(datas) {
				 var varithml="";
				 $.each(datas.data,function(i, n) {
						 varithml+="<tr>";
						 varithml+="<td class=\"td1\"><span>"+changeitemimg(n.title.substring(0,3))+"</span></td>"
						 varithml+="<td class='td2 itemname'><div class=\"clear\"><h6 class=\"itemstyle\"><a href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+")>"+n.title.substring(0,3)+"</a></h6>";
						 if(n.status==STATUS_03&&n.istop=='1'){
							 varithml+="<span class='itemlabel label-di'>置</span>" ;
						 }
						 if(1==4){
							 varithml+="<span class='itemlabel label-xin'>信</span>" ;
						 }
						 if(1==2){
							 varithml+="<span class='itemlabel label-new'>新</span>" ;
						 }
						 if(1==3){
							 varithml+="<span class='itemlabel label-jiang'>奖</span>" ;
						 }
						 varithml+="<p><a href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+")>"+n.title.substring(3)+"</a></p></td>";
						 if(n.status==STATUS_03){
							 if(n.sbidtime.split("-").length != 2){
								 varithml+="<td class=\"td3\"><span>"+formatMoney(n.loanrate,1)+"</span>%</td>";
						    }else{
						    	if(n.comingrate=="") varithml+="<td class=\"td3\"><span>"+formatMoney(n.loanrate,1)+"</span>%</td>";
						    	else if(n.comingrate<=0) varithml+="<td class=\"td3\"><span>"+formatMoney(n.loanrate,1)+"</span>%</td>";
						    	else varithml+=" <td><span class='fsz12'>不低于</span><span class=\" txt-b col-red2\"><span class='fsz22 par3'>"+formatMoney(n.comingrate,1)+"</span>%</span></td>";
						    }	
						 }else{
							 varithml+="<td class=\"td3\"><span>"+formatMoney(n.loanrate,1)+"</span>%</td>";
						 }
						 if(n.repaymentcycle==UNIT01){
						 		varithml+="<td class=\"td4\"><span>"+n.loanterm+"</span>个月</td>";
							}else if(n.repaymentcycle==UNIT02){
								varithml+="<td class=\"td4\"><span>"+n.loanterm*3+"</span>个月</td>";
							}else if(n.repaymentcycle==UNIT03){
								varithml+="<td class=\"td4\"><span>"+n.loanterm+"</span>天</td>";
							}else if(n.repaymentcycle==UNIT04){
								varithml+="<td class=\"td4\"><span>"+n.loanterm*7+"</span>天</td>";
							}
						 varithml+="<td class=\"td5\"><span>"+formatMoney(n.loanamount/10000,0)+"</span>万</td>";
						 varithml+="<td class=\"td6\"><p>"+n.repaymentwayname+"</p><div class=\"pat8 clear\"><div class=\"progress jindu-w\"><div class=\"progress-bar\" style=\"width:"+formatMoney(n.bidprocess,1)+"%\"></div></div><em>"+formatMoney(n.bidprocess,1)+"%</em></td>";
						 if(n.status==STATUS_03){
								if(n.sbidtime.split("-").length != 2){
									 if(n.bidprocess>=100){
											varithml+="<td><a class=\"btn btn-default tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >已满标</a></td>";
										}else{
											varithml+="<td><a class=\"btn btn-danger tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >我要投资</a></td>";
										}
							    }else{
									varithml+="<td><a class=\"btn btn-primary tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >即将发布</a></td>";
								}
							}else if(n.status==STATUS_04||n.status==STATUS_05){
								varithml+="<td><a class=\"btn btn-default tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >已满标</a></td>";
							}else if(n.status==STATUS_06){
								varithml+="<td><a class=\"btn btn-green tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >还款中</a></td>";
							}else if(n.status==STATUS_07){
								varithml+="<td><a class=\"btn btn-gray tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >已完成</a></td>";
							}
						 varithml+="</tr>";
						 
						 //alert(n.title.substring(0,6));	 				 
				 }); 
				 $("#investment_tab").html(varithml);
				 //changeitemimg("#investment_tab"); 
			}
		});
}



//债权转让  guofu 2015-05-12
function initInvestmentzqzr(prdType){
	var url =getHpoleFrontPageURL()+"SY860L" ; 
		$.ajax({
			type : "post",
			dataType : "json",
			url : url,
			data:{'loantype':1,'maxrows':4},
			success : function(datas) {
				 var varithml="";
				 $.each(datas.data,function(i, n) {
					 if(n.repaymentcycle=="0"){
						 ncycle = "月";
					 }else if(n.repaymentcycle=="1"){
						 ncycle = "季";
					 }else if(n.repaymentcycle=="2"){
						 ncycle = "天";
					 }else if(n.repaymentcycle=="4"){
						 ncycle = "周";
					 }
					 if(n.loantype=='1'){
						 varithml+="<tr>";
						 varithml+="<td class=\"td1\"><span class=\"yang\">"+changeitemimg(n.title.substring(0,3))+"</span></td>";
						 varithml+="<td class='td2 itemname'><div class=\"clear\"><h6 class=\"itemstyle\"><a href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+")>"+n.title.substring(0,3)+"</a></h6>";
						 varithml+="<span class='itemlabel label-zhuan'>转让</span>"; 
						 varithml+="<p><a href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+")>"+n.title.substring(3)+"</a></p></td>";			
						 varithml+="<td class=\"td3\"><span>"+formatMoney(n.loanrate,1)+"</span>%</td>";
						 varithml+="<td class=\"td4\"><span>"+n.resttime+"</span>天</td>";
						 varithml+="<td class=\"td5\"><span>"+formatMoney(n.loanamount/10000,1)+"</span>万</td>"; 
						 varithml+="<td class=\"td6\">";
						 if(n.repaymentway=="01"){
							 varithml+="<p>按"+ncycle+"等额本息</p>";
						 }else if(n.repaymentway=="03"){
							 varithml+="<p>按"+ncycle+"付息到期还本</p>";
						 }else if(n.repaymentway=="40"){
							 varithml+="<p>自定义还款</p>";
						 }else if(n.repaymentway=="41"){
							 varithml+="<p>一次性还款</p>";
						 } 
						 varithml+="<div class=\"pat8 clear\"><div class=\"progress jindu-w\"><div class=\"progress-bar\" style=\"width:"+formatMoney(n.bidprocess,1)+"%\"></div></div><em>"+formatMoney(n.bidprocess,1)+"%</em></td>";
						 if(n.status==STATUS_01){
							varithml+="<td class=\"td7\"><a class=\"btn btn-danger tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >立即认购</a></td>";
						}else if(n.status==STATUS_02){
							varithml+="<td class=\"td7\"><a class=\"btn btn-gray tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >未转让</a></td>";
						}else if(n.status==STATUS_03){
							varithml+="<td class=\"td7\"><a class=\"btn btn-gray tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >已转让</a></td>";
						}else if(n.status==STATUS_99){
							varithml+="<td class=\"td7\"><a class=\"btn btn-gray tb-btn\" href=javascript:void(0) onclick=openUrl('"+n.id+"',"+n.loantype+") >已终止</a></td>";
						}
						varithml+="</tr>";						
					 }
				 });

				 $("#investment_tab_zz").html(varithml);
			}
		});
}






//借款项目 
function lnNmFormatter(value, row, index){
	return '<a href=javascript:void(0) onclick=openUrl("'+row.id+'")>'+ row.title + '</a>';	
}

//年利率
function lnRateFormatter(value, row, index){
	return formatMoney(row.loanrate,1) + '%';
}

//借款金额 
function lnAmtFormatter(value, row, index){
	return '￥'+formatMoney(row.loanamount,1);
}

//借款期限 
function lnTermUnitFormatter(value, row, index){ 
	var unit = "" ;
	var lnUint = row.repaymentcycle ;
	var lnterm=row.loanterm;
	if(lnUint==UNIT01){
		unit = '月';
	}else if(lnUint==UNIT02){
		lnterm=lnterm*3;
		unit= '月';
	}else if(lnUint==UNIT03){
		unit= '天';
	}else if(lnUint==UNIT04){
		unit= '周';
	}
	return lnterm + unit ;
}

//进度
function lnStsFormatter(value, row, index){
	var jdt=formatMoney(row.bidprocess,1);
//	if(row.status=="04" && row.bidprocess=="100"){
//		jdt=99.99;
//	}else{ 
//		jdt=row.bidprocess;
//	}
	var variihtml="<p>"+jdt+"%</p><div class='progress jindu-w'><div class='progress-bar' style='width:"+jdt+"%'></div> </div> "
	return variihtml ;
}

//状态
function redLnStsformater(value, row, index){
	var variihtml="<button class='btn btn-primary tb-btn' type='button'><a href=javascript:void(0) onclick=openUrl('"+row.id+"')>";
	var variihtml1="<button class='btn btn-default tb-btn' type='button'><a href=javascript:void(0) onclick=openUrl('"+row.id+"')>";
	if(row.status==STATUS_02){
		if(row.sbidtime>0){
			variihtml+="我要投资</a></button>";
		}else{
			variihtml+="即将发布</a></button>";
		}
	}else if(row.status==STATUS_03){
		variihtml=variihtml1+"已满标</a></button>";
	}else if(row.status==STATUS_04){
		variihtml=variihtml1+"还款中</a></button>";
	}else if(row.status==STATUS_05){
		variihtml=variihtml1+"已完成</a></button>";
	}
	
	return variihtml ;
}


/**
 * 
 */
function changeBannerbg() {
	var name = "a[name='bga']";	
	$(name).each(function() {
		var id = $(this).attr("id");
		$(this).css("background-image","url("+id+")");
	});
}

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-83696527-1', 'auto');
	  ga('send', 'pageview');





