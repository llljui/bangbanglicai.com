/*验证*/
$.minLength = function(value, length , isByte) {
	var strLength = $.trim(value).length;
	if(isByte)
		strLength = $.getStringLength(value);
		
	return strLength >= length;
};

$.maxLength = function(value, length , isByte) {
	var strLength = $.trim(value).length;
	if(isByte)
		strLength = $.getStringLength(value);
		
	return strLength <= length;
};
$.getStringLength=function(str)
{
	str = $.trim(str);
	
	if(str=="")
		return 0; 
		
	var length=0; 
	for(var i=0;i <str.length;i++) 
	{ 
		if(str.charCodeAt(i)>255)
			length+=2; 
		else
			length++; 
	}
	
	return length;
};

$.checkMobilePhone = function(value){
	if($.trim(value)!='')
		return /^\d{6,}$/i.test($.trim(value));
	else
		return true;
};
$.checkEmail = function(val){
	var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/; 
	return reg.test(val);
};

function loginout(){
	var ajaxurl = $("#login-out-btn").attr("data-url"); 
	$.confirm("确认退出吗?", function(){
		$.showIndicator();
		$.ajax({
			url:ajaxurl,
			type:"POST",
			dataType:"json",
			success:function(result){
				$.hideIndicator();
				if(result.status==1){
					$.toast(result.info);
					var integrate = $("<span id='integrate'>"+result.data+"</span>");
					$("body").append(integrate);				
					$("#integrate").remove();
					try{
						App.logout();
					}
					catch(e){
					}
					window.location.href=WAP_PATH+"/index.php?ctl=init";
				}
				else{
					$.alert(result.info);
				}
			}
			,error:function(){
				$.toast("通信失败");
				$.hideIndicator();
			}
		});
	}, function(){
		return false;
	});
		
}

function bind_user_login()
{
	$("#user_login_form").find("input[name='submit_form']").bind("click",function(){

		do_login_user();
	});
	$("#user_login_form").find("input[name='user_pwd']").bind("keydown",function(e){
		if(e.keyCode==13)
		{
			do_login_user();
		}
	});
	$("#user_login_form").find("input[name='email']").bind("keydown",function(e){
		if(e.keyCode==9||e.keyCode==13)
		{
			$("#user_login_form").find("input[name='user_pwd']").val("");
			$("#user_login_form").find("input[name='user_pwd']").focus();
			return false;
		}
	});

	$("#user_login_form").bind("submit",function(){
		return false;
	});
}

function newObject(){
	var query = new Object();
	query.fhash=__HASH_KEY__;
	return query;
}


function round2(number,fractionDigits){     
    with(Math){     
        return round(number*pow(10,fractionDigits))/pow(10,fractionDigits);     
    }     
}  

// 限制只能输入金额
function amount(th){
    var regStrs = [
        ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
        ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
        ['\\.(\\d?)\\.+', '.$1'], //禁止录入两个以上的点
        ['^(\\d+\\.\\d{2}).+', '$1'] //禁止录入小数点后两位以上
    ];
    for(i=0; i<regStrs.length; i++){
        var reg = new RegExp(regStrs[i][0]);
        th.value = th.value.replace(reg, regStrs[i][1]);
    }
}

function fav_result(o)
{
	$(o).parent().html("已关注");
}

function loadDealSy(){
	var ajaxurl = WAP_PATH+'/index.php?ctl=calc_bid';
	
	var query = newObject();
	query.id =  $.trim($("#deal #deal_id").val());
	query.money = $.trim($("#deal #pay_inmoney").val());
	query.number = $.trim($("#deal #buy_number").val());
	
	if(bid_calculate) bid_calculate.abort(); /*终止之前所有的未结束的ajax请求，然后重新开始新的请求  */
	query.post_type = "json";
	$.ajax({
		url:ajaxurl,
		data:query,
		type:"post",
		dataType:"json",
		success:function(result){
			$("#deal .J_u_money_sy").html(result.profit);
			
		}
	});
}

var regsiter_vy_time = null;  	//定义时间
var is_lock_send_vy = false;	//解除锁定
var left_rg_time = 0;			//开始时间
function left_time_to_send_regvy(obj){
	clearTimeout(regsiter_vy_time);
	if(left_rg_time > 0){
		regsiter_vy_time = setTimeout(left_time_to_send_regvy,1000);
		obj.val(left_rg_time+"秒后重获验证码");
		obj.addClass("btn_disable");
		left_rg_time -- ;
	}
	else{
		is_lock_send_vy = false;
		obj.removeClass("btn_disable");
		obj.val("重新获取验证码");
		
		left_rg_time = 0;
	}
}


function viewintegralNav(iIndex)
{ 
    var Object_iIndex='.Object_'+iIndex;
    $(Object_iIndex).show();
}


function reloadpage(url,page,cls,func){
	$.showIndicator();
	$.ajax({
		url:url,
		type:"post",
		dataType:"html",
		success:function(result){
			$("body").append('<div id="tmpHTML">'+result+'</div>');
			var html = $("#tmpHTML").find(page).find(cls).html();
			$("#tmpHTML").remove();
			$(page).find(cls).html(html);
			$(page).find(".content").attr("now_page",1);
			$.hideIndicator();
			$.refreshScroller(page);
			if(func!=null){
				func.call(this);
			}
		}
	});
}

/** 
 * @param {Object} url  请求URL
 * @param {Object} 页面ID
 * @param {Object} w  0 正常LOAD  1打开新页面LOAD   2重载
 */
function RouterURL(url,page,w){
	if(isapp=="1" && url.indexOf("app")==-1){
		if(url.indexOf("?")==-1){
			url +="?app=1";
		}
		else{
			url +="&app=1";
		}
	}
	$.closePanel();
	if($("#panel-left-box").length > 0 && w!=1){
		if(url.indexOf("?")==-1){
			url +="?hasleftpanel=1";
		}
		else{
			url +="&hasleftpanel=1";
		}
	}
	if($(page).length > 0&&w!=1){
		if(w==2){
			if(!$(page).hasClass("page-current")){
				$(page).remove();
				loadUrl(url);
			}
		}
		else{
			if(!$(page).hasClass("page-current"))
				$.router.loadPage(page);
		}
	}
	else{
		loadUrl(url,page,w);
	}
}

function loadUrl(url,page,w){
	if (w == 1) {
		if(url.indexOf(SITE_DOMAIN+"/wap")===-1){
			try{
				var open_url_type = 0;
				if(page=="#adv_1"){
					open_url_type = 1;
				}
				var sjson = '{"url":"'+url+'","open_url_type":'+open_url_type+'}';
				App.open_type(sjson);
			}
			catch(e){
				if(page=="#adv_1"){
					window.open(url);
				}
				else{
					window.location.href = url;
				}
			}
		}
		else{
			if(page=="#adv_1"){
				window.open(url);
			}
			else{
				window.location.href = url;
			}
		}
	}
	else
		$.router.loadPage(url);
}

function RouterBack(url,page,epage){
	if($(page).length > 0 || (epage!=null && $(epage).length >0)){
		$.router.back(url);
	}
	else{
		$.router.loadPage(url);
	}
}

function project_cart(id){
	if(id > 0){
		var query = newObject();
		query.id = id;
		query.post_type = 'json';
		$.ajax({
			url:WAP_PATH + "/index.php?ctl=check_project_cart&id="+id,
			data:query,
			type:"post",
			dataType:"json",
			success:function(result){
				if(result.user_login_status == 0){
					RouterURL(WAP_PATH + "/index.php?ctl=login",'#login');
					return false;
				}
				else if(result.response_code == 0){
					$.alert(result.show_err);
					return false;
				}
				else{
					RouterURL(WAP_PATH + "/index.php?ctl=project_cart&id="+id,'#project_cart',2);
				}
			}
		});
	}
}


function jsonToString (obj){
    var THIS = this;      
    switch(typeof(obj)){     
        case 'string':     
            return '"' + obj.replace(/(["\\])/g, '\\$1') + '"';     
        case 'array':     
            return '[' + obj.map(THIS.jsonToString).join(',') + ']';     
        case 'object':     
             if(obj instanceof Array){
                var strArr = [];     
                var len = obj.length;     
                for(var i=0; i<len; i++){     
                    strArr.push(THIS.jsonToString(obj[i]));     
                }     
                return '[' + strArr.join(',') + ']';     
            }else if(obj==null){     
                return 'null';     

            }else{     
                var string = [];     
                for (var property in obj) string.push(THIS.jsonToString(property) + ':' + THIS.jsonToString(obj[property]));     
                return '{' + string.join(',') + '}';     
            }     
        case 'number':     
            return obj;     
        case false:     
            return obj;     
    }     
}  


function user_sign(){
	var ajaxurl = WAP_PATH +'/index.php?ctl=signin';
	var query = newObject();
	$.showIndicator();
	query.post_type = "json";
		$.ajax({
			url:ajaxurl,
			data:query,
			type:"Post",
			dataType:"json",
			success:function(data){
				$.hideIndicator();
				if (data.result.status == 1) {
					$.alert(data.result.info,function(){
						reloadpage(WAP_PATH +'/member.php?ctl=uc_center','#uc_center',".content");
					});
				}
				else{
					$.alert(data.result.info);
				}
			},error:function(){
				$.toast("通信失败");
				$.hideIndicator();
			}			
		});	
}