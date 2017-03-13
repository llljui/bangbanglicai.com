$(document).ready(function(){
	bind_buy_link();
	bind_zoom();
	bind_focus();
	attention_bind_focus();
	bind_faq();
});

function bind_faq(){
	$(".faq_question").bind("click",function(){
		var id=$(this).attr("rel");
 		$(".faq_answer[rel="+id+"]").toggle("slow");
	});	
}

//定义复制文本
$.copyText = function(id)
{
	var txt = $(id).val();
	if(window.clipboardData)
	{
		window.clipboardData.clearData();
		var judge = window.clipboardData.setData("Text", txt);
		if(judge === true)
			$.showSuccess("已经拷贝到剪切板");
		else
			$.showErr("拷贝失败");
	}
	else if(navigator.userAgent.indexOf("Opera") != -1)
	{
		window.location = txt;
	} 
	else if (window.netscape) 
	{
		try
		{
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
		}
		catch(e)
		{
			$.showErr("非IE内核，无法拷贝");
		}
		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
		if (!clip)
			return;
		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		if (!trans)
			return;
		trans.addDataFlavor('text/unicode');
		var str = new Object();
		var len = new Object();
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		var copytext = txt;
		str.data = copytext;
		trans.setTransferData("text/unicode",str,copytext.length*2);
		var clipid = Components.interfaces.nsIClipboard;
		if (!clip)
			return false;
		clip.setData(trans,null,clipid.kGlobalClipboard);
		$.showSuccess("已经拷贝到剪切板");
	}
};

function bind_buy_link()
{
	$(".buy_deal_item").bind("click",function(){
		location.href = $(this).attr("url");
	});
}

function bind_zoom()
{
	$(".image_item").bind("click",function(){
		var img = $(this).find("img").attr("rel");
		$.fancybox.open(img);
	});
	
}

function bind_focus()
{
	$(".focus_deal").bind("click",function(){
		focus_deal($(this).attr("id"));
	});
}

function focus_deal(id)
{
	var ajaxurl = APP_ROOT+"/index.php?ctl=deal&act=focus&id="+id;
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		type: "POST",
		success: function(ajaxobj){
			if(ajaxobj.status==1)
			{
				$(".focus_deal").removeClass("blue");
				$(".focus_deal").removeClass("gray");
				$(".focus_deal").removeClass("blue_hover");
				$(".focus_deal").removeClass("gray_hover");
				$(".focus_deal").removeClass("blue_active");
				$(".focus_deal").removeClass("gray_active");
				$(".focus_deal").addClass("gray");
				$(".focus_deal").attr("rel","gray");
				$(".focus_deal").find("div span").html("取消关注");
				
			}
			else if(ajaxobj.status==2)
			{
				$(".focus_deal").removeClass("blue");
				$(".focus_deal").removeClass("gray");
				$(".focus_deal").removeClass("blue_hover");
				$(".focus_deal").removeClass("gray_hover");
				$(".focus_deal").removeClass("blue_active");
				$(".focus_deal").removeClass("gray_active");
				$(".focus_deal").addClass("blue");
				$(".focus_deal").attr("rel","blue");
				$(".focus_deal").find("div span").html("立即关注");							
			}
			else if(ajaxobj.status==3)
			{
				$.showErr(ajaxobj.info);							
			}
			else
			{
				ajax_login();
			}
		},
		error:function(ajaxobj)
		{
//			if(ajaxobj.responseText!='')
//			alert(ajaxobj.responseText);
		}
	});
}
function attention_bind_focus()
{
	$(".attention_focus_deal").bind("click",function(){
		attention_focus_deal($(this).attr("id"));
	});
}

function attention_focus_deal(id)
{
	var ajaxurl = APP_ROOT+"/index.php?ctl=project&act=focus&id="+id;
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		type: "POST",
		success: function(ajaxobj){
			if(ajaxobj.status==1)
			{
				$(".attention_focus_deal").removeClass("gz");
				$(".attention_focus_deal").addClass("qxgz");
				$(".attention_focus_deal").html('<i></i>取消关注');
			}
			else if(ajaxobj.status==2)
			{
				$(".attention_focus_deal").removeClass("qxgz");
				$(".attention_focus_deal").addClass("gz");
				$(".attention_focus_deal").html('<i></i>关注');
			}
			else if(ajaxobj.status==3)
			{
				$.showErr(ajaxobj.info);							
			}
			else
			{
				ajax_login();
			}
		},
		error:function(ajaxobj)
		{
//			if(ajaxobj.responseText!='')
//			alert(ajaxobj.responseText);
		}
	});
}


// 股权发起项目统计盈亏
function total_price(table_class){
    var total_income=0.00;
    var total_out=0.00;
    $(table_class).each(function(i){
        var item_income=0.00;
        var item_out=0.00;
        $(this).find(".income_table .amount").each(function(){
            if($(this).val()!=''){
                item_income=parseFloat(item_income+parseFloat($(this).val()));
                item_income = Math.round(item_income*100)/100;
            }
        });
        $(this).find(".out_table .amount").each(function(){
            if($(this).val()!=''){
                item_out=parseFloat(item_out+parseFloat($(this).val()));
                item_out = Math.round(item_out*100)/100;
            }
        });
        $(this).find(".item_income").html(item_income);
        $(this).find(".item_income_input").val(item_income);
        $(this).find(".item_out").html(item_out);
        $(this).find(".item_out_input").val(item_out);
        total_income = Math.round((total_income+item_income)*100)/100;
        total_out = Math.round((total_out+item_out)*100)/100;
    });
    total_left = Math.round((total_income-total_out)*100)/100;
    $("#totalsr").html(total_income);
    $("#totalkz").html(total_out);
    $("#totalyk").html(total_left);
}
// 股权发起项目是否有阶段收入
function setSR(state,obj,table_class) {
    var $textarea_obj = $(obj).parent().parent().parent().find("table");
    if(state==1) {
        $textarea_obj.show();
    } else {
	  $textarea_obj.find(".tr_income_row").remove("tr") ;
       $textarea_obj.hide();
    }
	total_price(table_class);
}

// 股权发起项目是否有阶段开支
function setKZ(state,obj,table_class) {
    var $textarea_obj = $(obj).parent().parent().parent().find("table");
    if(state==1) {
        $textarea_obj.show();
    } else {
		$textarea_obj.find(".tr_out_row").remove("tr") ;
        $textarea_obj.hide();
    }
	total_price(table_class);
}


//先使用round函数四舍五入成整数，然后再保留指定小数位  
function round2(number,fractionDigits){     
    with(Math){     
        return round(number*pow(10,fractionDigits))/pow(10,fractionDigits);     
    }     
}

$.fn.weixin_login = function(){
	$(this).live('click',function(){
		open_weixin_login=1;
		$.weeboxs.open(APP_ROOT+"/index.php?ctl=ajax&act=weixin_login", {boxid:'pop_user_login',contentType:'ajax',showButton:false, showCancel:false, showOk:false,title:'微信登录',width:270,type:'wee',onopen:function(){
			setInterval(do_weixin_login,3000);
		}});
	});
}