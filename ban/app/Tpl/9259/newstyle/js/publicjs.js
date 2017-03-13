/************zhaoyu 20150303 公共js文件***************/
/*$(document).ready(function(){
	//显示 or 隐藏   “温馨提示：每日23:00至次日01:00为系统结算期，在此期间系统不可做资金操作，如投资、充值、提现等。给您带来不便敬请谅解。”
	var urls = window.location.href;
	if(urls.split("login").length>1||urls.split("register").length>1||urls.split("find_0").length>1||urls.split("find_2").length>1||urls.split("Register").length>1){
		return;
	}
	var url = getHpoleFrontURL() + "SY810S";
	$.ajax({
		type : "post",
		dataType : "json",
		url : url,
		data : {
			'temp' : 6
		},
		success : function(datas) {
			var h = datas.time;
			if (h == 23 || h == 0) {
				$("#spanTe").css({
					display : 'block'
				});
			} else {
				$("#spanTe").css({
					display : 'none'
				});
			}
		}
	});

//	if (!$.support.leadingWhitespace) {
//		location.href="/error/IEbearError.html";
//    }


});*/

/*
 * formatMoney(s,type) type 的值为0 没有小数点的默认不带小数点如10000==10，000
 * 如果带小数点但不为00如10000.12==10,000.12 功能：金额按千位逗号分割 参数：s，需要格式化的金额数值.
 * 参数：type,判断格式化后的金额是否需要小数位. 返回：返回格式化后的数值字符串.
 */
function formatMoney(s, type) {
	var b="";
	if(s==undefined||s==null||s==""){
		return "0.00";
	}
	if(s.toString().indexOf("-") != -1){
		b="-";
		s=s.toString().substring(1);
	}
	if (/[^0-9\.]/.test(s))
		return "0.00";
	if (s == null || s == "")
		return "0.00";
	s = s.toString().replace(/^(\d*)$/, "$1.");
	s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
	s = s.replace(".", ",");
	var re = /(\d)(\d{3},)/;
	while (re.test(s))
		s = s.replace(re, "$1,$2");
	s = s.replace(/,(\d\d)$/, ".$1");
	if (type == 0) {// 不带小数位(默认是有小数位)
		var a = s.split(".");
		if (a[1] == "00") {
			s = a[0];
		}
	}
	return b+s;
}
/*
 * 去除千位符
 */
function formatMoneyDel(s) {
	var a = s.split(',');
	var b = "";
	for (var i = 0; i < a.length; i++) {
		b += a[i];
	}
	return b;
}
/*
 * JS处理加减乘除误差较大  开始
 */
//减法
function sub(arg1, arg2) {
	var r1, r2, m, n;
	try {
		r1 = arg1.toString().split('.')[1].length
	} catch (e) {
		r1 = 0
	}
	try {
		r2 = arg2.toString().split('.')[1].length
	} catch (e) {
		r2 = 0
	}
	m = Math.pow(10, Math.max(r1, r2));
	//动态控制精度长度
	n = (r1 >= r2) ? r1 : r2;
	return parseFloat(((arg1 * m - arg2 * m) / m).toFixed(n));
}

function add(arg1, arg2) {
	var r1, r2, m;
	try {
		r1 = arg1.toString().split('.')[1].length
	} catch (e) {
		r1 = 0
	}
	try {
		r2 = arg2.toString().split('.')[1].length
	} catch (e) {
		r2 = 0
	}
	m = Math.pow(10, Math.max(r1, r2))
	return (arg1 * m + arg2 * m) / m
}
//乘法
function mul(arg1, arg2) {
	var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
	try {
		m += s1.split('.')[1].length
	} catch (e) {
	}
	try {
		m += s2.split('.')[1].length
	} catch (e) {
	}
	return Number(s1.replace('.', '')) * Number(s2.replace('.', ''))
			/ Math.pow(10, m)
}
//除法
function div(arg1, arg2) {
	var t1 = 0, t2 = 0, r1, r2;
	try {
		t1 = arg1.toString().split('.')[1].length
	} catch (e) {
	}
	try {
		t2 = arg2.toString().split('.')[1].length
	} catch (e) {
	}
	with (Math) {
		r1 = Number(arg1.toString().replace('.', ''))
		r2 = Number(arg2.toString().replace('.', ''))
		return (r1 / r2) * pow(10, t2 - t1);
	}
}
/*
 * JS处理加减乘除误差较大 结束
 */
/**
 * 验证18位数身份证号码中的生日是否是有效生日
 * @param idCard18 18位书身份证字符串
 * @return
 */

function isValidityBrithBy18IdCard(idCard18) {
	var year = idCard18.substring(6, 10);
	var month = idCard18.substring(10, 12);
	var day = idCard18.substring(12, 14);
	var temp_date = new Date();
	// 这里用getFullYear()获取年份，避免千年虫问题
	if (temp_date.getFullYear() - 18 < parseFloat(year)) {
		return false;
	} else if (temp_date.getFullYear() - 18 == parseFloat(year)) {
		if (temp_date.getMonth() < parseFloat(month) - 1) {
			return true;
		} else if (temp_date.getMonth() == parseFloat(month) - 1) {
			if (temp_date.getDate() >= parseFloat(day)) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	} else {
		return true;
	}
}

/**
 * 验证15位数身份证号码中的生日是否是有效生日
 * @param idCard15 15位书身份证字符串
 * @return
 */
function isValidityBrithBy15IdCard(idCard15) {
	var year = idCard15.substring(6, 8);
	var month = idCard15.substring(8, 10);
	var day = idCard15.substring(10, 12);
	var temp_date = new Date(year, parseFloat(month) - 1, parseFloat(day));
	// 对于老身份证中的你年龄则不需考虑千年虫问题而使用getYear()方法
	if (temp_date.getFullYear() - 18 < parseFloat(year)) {
		return false;
	} else if (temp_date.getFullYear() - 18 == parseFloat(year)) {
		if (temp_date.getMonth() < parseFloat(month) - 1) {
			return true;
		} else if (temp_date.getMonth() == parseFloat(month) - 1) {
			if (temp_date.getDate() < parseFloat(day)) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	} else {
		return true;
	}
}

//显示样式
function checkP_block(pid) {
	$("#" + pid).css({
		display : 'block'
	});
}
//隐藏样式
function checkP_none(pid) {
	$("#" + pid).css({
		display : 'none'
	});
}

//获取33Lend工程的 url地址	
function getHpoleURL() {
	var localhostPath = getLocalhostPath();
	var rootPath = localhostPath // + projectName;  以后可以修改
	return rootPath;
}

//获取33Lend工程的 url地址	
function getHpoleURLhs() {
	var localhostPath = getLocalhostPathhs();
	var rootPath = localhostPath // + projectName;  以后可以修改
	return rootPath;
}

//获取33Lend工程的 url地址	----前置接口公用路劲
function getHpoleFrontURL() {
	var localhostPath = getLocalhostPath();
	var rootPath = localhostPath + "/payfor/sylend?meta.service=" // + projectName;

	return rootPath;
}

//获取33Lend工程的 url地址	----前置接口公用路劲
function getHpoleFrontURLhs() {
	var localhostPath = getLocalhostPathhs();
	var rootPath = localhostPath + "/payfor/sylend?meta.service=" // + projectName;

	return rootPath;
}
//获取33Lend工程的 url地址	----前置接口分页公用路劲

//第一次调用的时候total和pages可以不传或者""但不能传0，page第一次传1
function getHpoleFrontPageURL(total, page, pages) {
	var localhostPath = getLocalhostPath();
	var rootPath = localhostPath + "/payfor/sylend?meta.total=" + total
			+ "&meta.page=" + page + "&meta.pages=" + pages
			+ "&meta.orderby=NULL:bankid,NULL:userid,NULL:bankid&meta.service=" // + projectName; hponline以后可以修改

	return rootPath;
}
//取本地路径
function getLocalhostPath() {
	var localhostPath ="http://" + window.location.host;
	return localhostPath;
}

//取https
//取本地路径
function getLocalhostPathhs() {
	var localhostPath ="https://" + window.location.host;
	//var localhostPath ="http://" + window.location.host;
	return localhostPath;
}

//登录注册充值页面的头部底部
function tophref(){
	/*
	 * top start
	 */
	var htmls="";
	htmls+="<li><a class=\"m\" href=\""+getLocalhostPath()+"/\">首页</a></li>";
	htmls+=" <li><a class=\"m\" href=\""+getLocalhostPath()+"/investment/investlist_0.html\">我要投资</a></li>";
	htmls+="<li><a class=\"m\" href=\""+getLocalhostPath()+"/about/safe.html\">安全保障</a></li>";
	htmls+="<li><a class=\"m\" href=\""+getLocalhostPath()+"/PracticalData.html\">实时数据</a></li>";
	htmls+="<li><a class=\"m\" href=\""+getLocalhostPath()+"/about/aboutUS.html\">关于我们</a></li>";
	htmls+="<li class=\"subli\"><a class=\"n\" href=\"javascript:;\" onclick=\"openCenter(1)\">我的账户</a><span class=\"sdown\"></span>";
	htmls+="<div class=\"subnav\">";
	htmls+="<div class=\"sn_top\"></div>";
	htmls+="<div class=\"subnav_li\">";
	htmls+="<div><a href=\"/MemberCenter/find_0.html\">充值</a></div>";
	htmls+="<div><a href=\"/MemberCenter/find_2.html\">提现</a></div>";
	htmls+="<div><a href=\"/MemberCenter/invest_list.html\">我的投资</a></div>";
	htmls+="<div><a href=\"/MemberCenter/cPlan.html\">收款计划</a></div>";
	htmls+="</div>";
	htmls+="<div class=\"sn_bottom\"></div>";
	htmls+="</div></li>";
	$("#nav-list").html(htmls);
	$(".subli").hover(function(){
		$(".subli span").removeClass().addClass("sup");
		$(".subnav").slideDown(300);
	},function(){
		$(".subli span").removeClass().addClass("sdown");
		$(".subnav").hide();
	});
	var erweiStyle="";
	erweiStyle+="<div class=\"dropdown-menu top-erwei-style\">"
	erweiStyle+="<ul class=\"header-top-erwei clear\">"
	erweiStyle+="<li class=\"pull-left\"><img src=\"/${skin}/img/erwei-ding.jpg\" alt=\"订阅号\" /><p>订阅号</p></li>"
	erweiStyle+="<li class=\"pull-right\"><img src=\"/${skin}/img/erwei-fuwu.jpg\" alt=\"服务号\"/><p>服务号</p></li>"
	erweiStyle+="</ul>"
	erweiStyle+="</div>"
	$(".d-r-erwei").html(erweiStyle);
	var urlsrc=getLocalhostPath()+"/borrow/borrow.html"
	$(".pull-right a:eq(3)").attr({'src':urlsrc});
	var wrap="";
	wrap+="<a href=\""+getLocalhostPath()+"/\"></a>";
	wrap+="<i class=\"light\"></i>";
	$(".logoimg-wrap").html(wrap);
	/*
	 * top end
	 */

	/*
	 * foot start
	 */
	var footl="";


		footl+="<ul>";
		footl+="<li class=\"ulh usIco\">关于我们</li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/about/aboutUs.html\">企业简介</a></li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/about/mebia.html\">媒体报道</a></li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/about/myPartner.html\">合作伙伴</a></li>";
		footl+="<li class=\"ulist\"><a href=\"#\">专家顾问</a></li>";
		footl+="</ul>";
		footl+="<ul>";
		footl+="<li class=\"ulh anIco\">安全保障</li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/about/safe.html\">投资安全</a></li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/about/safe.html\">资金安全</a></li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/about/safe.html\">政策法规</a></li>";
		footl+="</ul>";
		footl+="<ul>";
		footl+="<li class=\"ulh helpIco\">帮助中心</li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/userguide/userguide.html\">新手指引</a></li>";
//		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/help/help.html\">收费标准</a></li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/help/help.html\">常见问题</a></li>";
		footl+="</ul>";
		footl+="<ul>";
		footl+="<li class=\"ulh touIco\">投资指南</li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"/help/list_505.html\">自动投标</a></li>";
		footl+="<li class=\"ulist\"><a href=\""+getLocalhostPath()+"s/help/list_581.html\">债权转让</a></li>";
		footl+="</ul>";
//		footl+="<ul>";
//		footl+="<li class=\"ulh jiaoIco\">交流社区</li>";
//		footl+="<li class=\"ulist\"><a href=\"#\">模块1</a></li>";
//		footl+="<li class=\"ulist\"><a href=\"#\">模块2</a></li>";
//		footl+="</ul>";

	$(".footer-conL").html(footl);
	/*
	 * foot start
	 */
}

//充值页面的左边菜单
function lefthref(){
	$(".vipNav-header").html("<a href=\""+getLocalhostPath()+"/MemberCenter/member.html\">账户总览</a>");
	var leftl="";
		leftl+="<li class=\"vipNav-grade1\">";
		leftl+="<h5 class=\"hIco1\">借款管理</h5>";
		leftl+="<ul>";
		leftl+="<li><a href=\""+getLocalhostPath()+"/MemberCenter/borrow.html\" ><span class=\"navSline\"></span>我的借款</a></li>";
		leftl+=" </ul>";
		leftl+="<div class=\"vip-navLev leveline\"></div>";
		leftl+="</li>";
		leftl+=" <li class=\"vipNav-grade1\">";
		leftl+="<h5 class=\"hIco2\">投资管理</h5>";
		leftl+=" <ul>";
		leftl+="<li><a href=\""+getLocalhostPath()+"/MemberCenter/invest_list.html\"><span class=\"navSline\"></span>我的投资</a></li>";
		leftl+=" <li>";
		leftl+="	<a href=\""+getLocalhostPath()+"/MemberCenter/cPlan.html\"><span class=\"navSline\"></span>收款计划</a>";
		leftl+=" </li>";
		leftl+="<li>";
		leftl+="<a href=\""+getLocalhostPath()+"/MemberCenter/credit_list.html\"><span class=\"navSline\"></span>债权转让</a>";
		leftl+="</li>";
		leftl+="</ul>";
		leftl+="<div class=\"vip-navLev leveline\"></div>";
		leftl+=" </li>";
		leftl+=" <li class=\"vipNav-grade1\">";
		leftl+="	<h5 class=\"hIco5\">账户管理</h5>";
		leftl+="<ul>";
		leftl+="<li><a href=\""+getLocalhostPath()+"/MemberCenter/find_0.html\"><span class=\"navSline\"></span>账户充值</a></li>";
		leftl+=" <li><a href=\""+getLocalhostPath()+"/MemberCenter/find_2.html\"><span class=\"navSline\"></span>账户提现</a></li>";
		leftl+=" <li><a href=\""+getLocalhostPath()+"/MemberCenter/trade_0.html\"><span class=\"navSline\"></span>账户流水</a></li>";
		leftl+=" <li><a href=\""+getLocalhostPath()+"/MemberCenter/SecurityCenter.html\"><span class=\"navSline\"></span>账户安全</a></li>";
		leftl+=" <li><a href=\""+getLocalhostPath()+"/MemberCenter/CardVerification.html\"><span class=\"navSline\"></span>银行卡管理</a></li>";
		leftl+="<li><a href=\""+getLocalhostPath()+"/MemberCenter/news.html\"><span class=\"navSline\"></span>账户消息</a></li>";
		leftl+=" </ul>";
		leftl+=" <div class=\"vip-navLev leveline\"></div>";
		leftl+="</li>";
		leftl+="<li class=\"vipNav-grade1\">";
		leftl+="<h5 class=\"hIco4\">互动管理</h5>";
		leftl+=" <ul>";
		leftl+="  <li><a href=\""+getLocalhostPath()+"/MemberCenter/share.html\"><span class=\"navSline\"></span>我的推荐</a></li>";
		leftl+=" <li><a href=\""+getLocalhostPath()+"/MemberCenter/Bonus.html\"><span class=\"navSline\"></span>我的奖金</a></li>";
		leftl+="<li><a href=\""+getLocalhostPath()+"/MemberCenter/myaward.html\"><span class=\"navSline\"></span>我的优惠劵</a></li>";
		leftl+=" </ul>";
		leftl+=" </li>";
		$(".nav-stacked").html(leftl);
}

gethttps();
function gethttps() {
	//	protocol: 协议 "http:"
	//	hostname: 服务器的名字 "b.a.com"
	//	port: 端口 "88"
	//	pathname: URL中主机名后的部分 "/index.php"
	//	search: "?"后的部分，又称为查询字符串 "?name=kang&when=2011"
	//	hash: 返回"#"之后的内容 "#first"
	//	host: 等于hostname + port "b.a.com:88"
	//	href: 当前页面的完整URL "http://www.a.com:88/index.php?name=kang&when=2011#first"

	var hrefName = window.location.host;//等于hostname + port
	var hrefProtocol = window.location.protocol;//协议
	var hrefHostname = window.location.hostname;
	var search = window.location.search;
	var aherf=window.location.href;
	//if (hrefHostname == hostName&&hrefProtocol=="http:")
	if (hrefProtocol=="http:") {
		if (window.location.pathname == checkHrefReg
				|| window.location.pathname == checkHrefLogin
				||  checkHrefFinds.test(window.location.pathname)||window.location.pathname==checkHrefIam33) {
			hrefProtocol = "https" + window.location.href.substring(4);
			window.location.href = hrefProtocol;
		}

	}else if(hrefProtocol=="https"){
		if (window.location.pathname == checkHrefReg
				|| window.location.pathname == checkHrefLogin
				|| checkHrefFinds.test(window.location.pathname)||window.location.pathname==checkHrefIam33) {
			window.location.href =aherf;
		}else{
			window.location.href ="http" + window.location.href.substring(4);
		}

	}
}
//退出登录
function onlogin(falg) {
	var url = "";
	if(falg==1){
		url = getHpoleFrontURLhs() + "SY810S";
	}else{
		url = getHpoleFrontURL() + "SY810S";
	}
	$.ajax({
		type : "post",
		dataType : "json",
		url : url,
		data : {
			'temp' : '5'
		},
		success : function(datas) {
			location.href = getLocalhostPath() + "/MemberCenter/login.html";
		}

	});
}

//验证登录注册密码
function checkEnrolpassword(password, errorpassword) {
	var password = $("#" + password).val();
	if ("" == password) {
		$("#" + errorpassword).text("密码不能为空");
		return;
	} else if (password.length < 6 || password.length > 16){
		$("#" + errorpassword).text("请输入6-16位字符，支持数字，字母和符号");
		loginPwd = 1;
		return;
	} else {
		$("#" + errorpassword).text("");
		loginPwd = 0;
	}

}
//验证手机号码
function checkphone(phone, errorphone) {
	if ($("#" + phone).val() == "") {
		regphone = 1;
		$("#" + errorphone).text("请输入11位数字。");
		return;
	} else if (isNaN($("#" + phone).val())) {
		regphone = 1;
		$("#" + errorphone).text("请输入11位数字。");
		return;
	} else if (regrphone.test($("#" + phone).val()) == false) {
		regphone = 1;
		$("#" + errorphone).text("请输入正确的手机号码格式。");
		return;
	} else if ($("#" + phone).val().substring(0, 1) != 1) {
		regphone = 1;
		$("#" + errorphone).text("请输入正确的手机号码格式。");
		return;
	} else {
		regphone = 0;
		$("#" + errorphone).val("");
	}
}
//判断手机是否存在
function rphone(phone, errorphone) {
	var url = getHpoleFrontURL() + "SY808S";
	$.ajax({
		type : "post",
		dataType : "json",
		url : url,
		async:false,
		data : {
			'phone' : $("#" + phone).val()
		},
		success : function(datas) {
			if (datas.code == "Succeeded") {
				//手机号存在
				regphone = 1;
			} else {
				//手机号不存在
				regphone = 0;
			}
		}

	});
}

//验证图形验证码
function checkCode(imgCode, errorimgcode) {
	if ("" == imgCode) {
		//验证码不能为空
		$("#" + errorimgcode).text("验证码不能为空");
		codeid = 1;
		return;
	} else if ($.trim(imgCode).length != 4) {
		//图形验证码的长度
		$("#" + errorimgcode).text("图形验证码的长度为4");
		codeid = 1;
		return;
	} else {
		$("#" + errorimgcode).text("");
		codeid = 0;
	}
}

//获取url中的参数
function getvParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg); //匹配目标参数

	if (r != null) {
		return decodeURI(r[2]);
	} else {
		return null; //返回参数值
	}
}

//获取带参值
function getUrlParam() {
	var hrefName = window.location.href;
	var lindex0 = hrefName.lastIndexOf('_');
	var lindex1 = hrefName.lastIndexOf('.');
	var strName = hrefName.substring(lindex0 + 1, lindex1);
	return strName;
}

/*
 * 分页控件加载样式
 * 调用参考myinvest.js
 * 分页点击事件参考 myinvest.js中的pageclick(id)方法
 * 20150312
 */

function pagination(id) {
	var pages = $("#pages").val();
	var page = $("#page").val();
	var total = $("#total").val();
	var pages1 = $("#pages1").val();
	var page1 = $("#page1").val();
	var total1 = $("#total1").val();
	var varhtml = "";
	if (id == 1) {
		if (page1 == "") {
			page1 = 1;
		}
		if (pages1 > 0) {
			varhtml += "<li>";
			varhtml += "<a href=javascript:void(0) onclick=perclick() aria-label=Previous>";
			varhtml += " <span aria-hidden=\"true\">&laquo;</span>";
			varhtml += "</a>";
			varhtml += "</li>";
			for (var i = 0; i < pages1; i++) {
				if ((i + 1) == page1) {//默认选中当前页
					varhtml += "<li class=active><a href=javascript:void(0) onclick=pageclick("
							+ (i + 1) + ")>" + (i + 1) + "</a></li>";
				} else if (i > 3 && pages1 - i > 1 && pages1 > 5) {
					if (varhtml.split("<li><a disable>.&nbsp;.&nbsp;.</a></li>").length == 1) {
						varhtml += "<li><a disable>.&nbsp;.&nbsp;.</a></li>";
					}
				} else {
					varhtml += "<li><a href=javascript:void(0) onclick=pageclick("
							+ (i + 1) + ")>" + (i + 1) + "</a></li>";
				}
			}

		varhtml += "<li>";
		varhtml += " <a href=javascript:void(0) onclick=nextclick() aria-label=Next>";
		varhtml += "<span aria-hidden=\"true\">&raquo;</span>";
		varhtml += "</a>";
		varhtml += "</li>";
		}else{
//			varhtml+="<span style=font-size:14px;>&nbsp;&nbsp;暂&nbsp;&nbsp;无&nbsp;&nbsp;数&nbsp;&nbsp;据&nbsp;&nbsp;</span>";
		}
		$(".pagination1").html(varhtml);
	} else {
		if (page == "") {
			page = 1;
		}
		if (pages > 0) {
			varhtml += "<li>";
			varhtml += "<a href=javascript:void(0) onclick=perclick() aria-label=Previous>";
			varhtml += " <span aria-hidden=\"true\">&laquo;</span>";
			varhtml += "</a>";
			varhtml += "</li>";
			for (var i = 0; i < pages; i++) {
				if ((i + 1) == page) {//默认选中当前页
					varhtml += "<li class=active><a href=javascript:void(0) onclick=pageclick("
							+ (i + 1) + ")>" + (i + 1) + "</a></li>";
				} else if (i > 3 && pages - i > 1 && pages > 5) {
					if (varhtml.split("<li><a disable>.&nbsp;.&nbsp;.</a></li>").length == 1) {
						varhtml += "<li><a disable>.&nbsp;.&nbsp;.</a></li>";
					}
				} else {
					varhtml += "<li><a href=javascript:void(0) onclick=pageclick("
							+ (i + 1) + ")>" + (i + 1) + "</a></li>";
				}
			}

		varhtml += "<li>";
		varhtml += " <a href=javascript:void(0) onclick=nextclick() aria-label=Next>";
		varhtml += "<span aria-hidden=\"true\">&raquo;</span>";
		varhtml += "</a>";
		varhtml += "</li>";
		}else{
//			varhtml+="<span style=font-size:14px;>&nbsp;&nbsp;暂&nbsp;&nbsp;无&nbsp;&nbsp;数&nbsp;&nbsp;据&nbsp;&nbsp;</span>";
		}
		$(".pagination").html(varhtml);
	}

}

var ck = 0;
var cname = "";
/*
 * 点击发送短信验证码
 * code 交易码
 * phone 手机号码
 * str 按钮id
 */
function sendMsgauthenode(code, phone, str, temp, memid) {
	var url = getHpoleFrontURL() + "SY809U";
	if ("" == memid) {

	} else {
		url += "&memid=" + memid;
	}
	cname = str;
	$.ajax({
		type : "post",
		dataType : "json",
		url : url,
		data : {
			'phone' : $("#" + phone).val(),
			'trancode' : code,
			'temp' : temp
		},
		success : function(datas) {
			if (datas.code == "Succeeded") {
				show_time(120);//成功将按钮修改
				alert("验证码为"+datas.data.mcode);
				MOBILEPHONE=0;
			} else {
				MOBILEPHONE=1;
				ck = 0;
				sameModal("发送失败.");
			}
		}

	});
}

var ck1 = 0;

function sendMsgauthenode1(code, phone, str, temp, memid) {
	var url = "";
	if(code=="SY801M"){
		url =getHpoleFrontURLhs() + "SY809U";
	}else{
		url =getHpoleFrontURL() + "SY809U";
	}
	if ("" == memid) {

	} else {
		url += "&memid=" + memid;
	}
	cname = str;
	$.ajax({
		type : "post",
		dataType : "json",
		url : url,
		data : {
			'phone' : $("#" + phone).val(),
			'trancode' : code,
			'temp' : temp
		},
		success : function(datas) {
			if (datas.code == "Succeeded") {
			       //	alert("验证码为："+datas.data.mcode);
				show_time(120);//成功将按钮修改
			} else {
				alert("发送失败")
				ck1 = 0;
			}
		}

	});
}

//用于手机端
function sendMsgauthenode2(code, phone, str, temp, memid) {
	var url = "";
	if(code=="SY801M"){
		url =getHpoleFrontURL() + "SY809U";
	}else{
		url =getHpoleFrontURL() + "SY809U";
	}
	if ("" == memid) {

	} else {
		url += "&memid=" + memid;
	}
	cname = str;
	$.ajax({
		type : "post",
		dataType : "json",
		url : url,
		data : {
			'phone' : $("#" + phone).val(),
			'trancode' : code,
			'temp' : temp
		},
		success : function(datas) {
			if (datas.code == "Succeeded") {
			       //	alert("验证码为："+datas.data.mcode);
				show_time(120);//成功将按钮修改
			} else {
				alert("发送失败")
				ck1 = 0;
			}
		}

	});
}
//发送倒计时
function show_time(n) {
	n = n - 1;
	$("#" + cname).attr({
		disabled : 'disabled'
	});
	var timer = document.getElementById(cname);
	var str_time = n + "秒后重发";
	timer.innerHTML = str_time;
	if (n == 0) {
		ck = 0;
		ck1 = 0;
		timer.innerHTML = "发送验证码";
		$("#" + cname).removeAttr('disabled');
		return;
	}
	setTimeout("show_time(" + n + ")", 1000);
}
/**
 * js判断是否为空
 * @param v
 * @returns {Boolean}
 */
function isEmpty(v) {
	switch (typeof v) {
	case 'undefined':
		return true;
	case 'string':
		if (v.replace(/(^[ \t\n\r]*)|([ \t\n\r]*$)/g, '').length == 0)
			return true;
		break;
	case 'boolean':
		if (!v)
			return true;
		break;
	case 'number':
		if (0 === v || isNaN(v))
			return true;
		break;
	case 'object':
		if (null === v || v.length === 0)
			return true;
		for ( var i in v) {
			return false;
		}
		return true;
	}
	return false;
}

/**
 * 验证验证码
 * @param a
 * @param b
 */
function valCode(veryCode, errorverycode,isflag) {
	var code = $("#" + veryCode).val()+"999";
	code = "c=" + code;
	var flag = false;
	var url="";
	if(isflag!=1){
		url= getHpoleURL() + "/payfor/sylend?meta.service=resultVerify";
	}else{
		url= getHpoleURLhs() + "/payfor/sylend?meta.service=resultVerify";
	}

	$.ajax({
		type : "POST",
		url :url,
		async : false,
		data : code/*{'c':code}*/,
		success : function(postData) {
			jsonData = eval("(" + postData + ")");
			if (jsonData.result) {
				$("#" + errorverycode).text("");
				flag = true;
			} else {
				changeImg(100, 50);
				$("#" + errorverycode).text('验证码错误!');
				flag = false;
			}

		}
	});
	return flag;
}

/**
 * 获取验证码
 * @param a
 * @param b
 */
function changeImg(a, b) {
	var imgSrc = $("#imgObj");
	var src = imgSrc.attr("src");
	var urls = chgUrl(src);
	imgSrc.attr("src", urls);
	// 时间戳
	// 为了使每次生成图片不一致，即不让浏览器读缓存，所以需要加上时间戳
	function chgUrl(url) {
		var timestamp = (new Date()).valueOf();
		urlurl = url.substring(0, 33);
		if ((url.indexOf("?") >= 0)) {
			urlurl = url + "&t=" + timestamp;
		} else {
			urlurl = url + "?t=" + timestamp + "&ImageWidth=" + a
					+ "&ImageWidth=" + b;
		}

		return urlurl;
	}
}

//循环判断是否存在
function in_array(stringToSearch, arrayToSearch) {
	for (s = 0; s < arrayToSearch.length; s++) {
		thisEntry = arrayToSearch[s].toString();
		if (thisEntry == stringToSearch) {
			return true;
		}
	}
	return false;
}
//打开借款项目页面
function openUrl(ln_no, typ) {
	if (typ == 0) {
		window.location.href = "/investment/detail_" + ln_no + ".html";
	} else if (typ == 1) {
		window.location.href = "/creditassign/detail_" + ln_no + ".html";
	}else if(typ == 2){
		location.href = "/mobile/detail_" + ln_no + ".html";
	}
        else if(typ==3){
		location.href = "/mobile/detailz_" + ln_no + ".html";
	}

}

//验证 输入项是否为  正数、负数、小数点
function checknumformt(val) {
	var value = /^\-?[0-9\,]*\.?\d*$/;
	if (value.test(val)) {
		return true;
	} else {
		return false;
	}
}
//弹出框
function sameModal(context,local){
	$("#modalbody").html(context);
	if(local==undefined){

	}else if("self"==local){
		$("#closeup").bind("click",function(){
			location.reload();
        });
		$("#closeupd").bind("click",function(){
			location.reload();
        });

	} else{
		$("#closeup").bind("click",function(){
			window.location.href = local;
        });
		$("#closeupd").bind("click",function(){
			window.location.href = local;
        });
	}
//	if(local!=""||local!=undefined||local!="undefined"||local!=null){
//		$("#closeup").bind("click",function(){
//			window.location.href = local;
//        });
//	}
	$("#sameModal").modal('show');
}

function repam(){
	$('#repam').popover('show');
}
//payway 还款方式说明
function repexplain(explain,unit){
	var repam='';
	if(unit==UNIT01){
		if(explain==RPAYMENTWAY01){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY001+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY03){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY003+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY40){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY040+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY41){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY041+'</p>" ></span>';
		}
	}else if(unit==UNIT02){
		if(explain==RPAYMENTWAY01){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY101+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY03){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY103+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY40){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY040+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY41){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY041+'</p>" ></span>';
		}
	}else if(unit==UNIT03){
		if(explain==RPAYMENTWAY01){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY201+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY03){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY203+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY40){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY040+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY41){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY041+'</p>" ></span>';
		}
	}else if(unit==UNIT05){
		repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY040+'</p>" ></span>';
	}else if(unit==UNIT04){
		if(explain==RPAYMENTWAY01){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY401+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY03){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY403+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY40){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY040+'</p>" ></span>';
		}else if(explain==RPAYMENTWAY41){
			repam+='<span onmouseover="repam()" id="repam" class="ico-wenhao ico-pos-rel "  data-toggle="popover" data-placement="right"  data-trigger="hover"  data-html="ture" data-content="<p class=popfontsz>'+RPAYWAY041+'</p>" ></span>';
		}
	}
	return repam;
}


/*截取字段*/
function cutstr(str,len)
{
   var str_length = 0;
   var str_len = 0;
      str_cut = new String();
      str_len = str.length;
      for(var i = 0;i<str_len;i++)
     {
        a = str.charAt(i);
        str_length++;
        if(escape(a).length > 4)
        {
         //中文字符的长度经编码之后大于4
         str_length++;
         }
         str_cut = str_cut.concat(a);
         if(str_length>=len)
         {
         str_cut = str_cut.concat("...");
         return str_cut;
         }
    }
    //如果给定字符串小于指定长度，则返回源字符串；  
    if(str_length<len){
     return  str;
    }
}

function ggchuli(){
	var titleid = $("#gonggaoid").val();
	if(!isEmpty(titleid)){
		$("#ggtime").text(titleid.split("#")[1]);
	}

	var len=$(".i-gonggao-con ul").length;
	$("#607").css({display:'none'});
//	$(".i-gonggao-con ul li:eq("+(len-1)+")").css({display:'none'});
}
function meitichuli(){
	$("#598").css({display:'none'});
}
//两时间差
function  DateDiff(sDate1,  sDate2){    //sDate1和sDate2是2006-12-18格式
    var  aDate,  oDate1,  oDate2,  iDays
    var endtime=Date.parse(sDate2.replace(/-/g,   "/"));
	var startime=Date.parse(sDate1.replace(/-/g,   "/"));

    oDate1  =  new  Date(endtime).getTime() ;   //转换为12-18-2006格式
    oDate2  =  new  Date(startime).getTime();
    iDays  =  parseInt(Math.abs(oDate1  -  oDate2)  /  1000  /  60  /  60  /24)    //把相差的毫秒数转换为天数
    return  formatMoney(iDays ,0);
}


//最热认购
function hotDown(id,time){
    var liHeight=$("#"+id+" ul li").height();
    var time=time||2500;
    setInterval(function(){
    $("#"+id+" ul").prepend($("#"+id+" ul li:last").css("height","0px").animate({
    	height:liHeight+"px"
    },"slow"));
    },time);
}

function  hottest(){
    $(".i-hot-list").slide({
        mainCell: ".hotlist",
        effect: "topLoop",
        vis: 4,
        opp: true,
        autoPlay: true,
        delayTime: 800
    });
}

/*顶部导航区域悬浮弹出下拉菜单*/
$('.header-top .dropdown').hover(function() {
  $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
}, function() {
  $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
});
/*下拉菜单end*/

/*项目对应图标*/
function changeitemimg(val){
			 if(val=="农批贷"){
				 return "<img class=\"nong\" src=\"/skin/pms/default/img/ico/nong.png\" alt=\"农批贷\"/>";
			 }else if(val=="花卉贷"){
				 return "<img class=\"hua\" src=\"/skin/pms/default/img/ico/hua.png\" alt=\"花卉贷\"/>";
			 }else if(val=="养殖贷"){
				 return "<img class=\"yang\" src=\"/skin/pms/default/img/ico/yang.png\" alt=\"养殖贷\"/>";
			 }else if(val=="速贷通"){
				 return "<img class=\"su\" src=\"/skin/pms/default/img/ico/su.png\" alt=\"速贷通\"/>";
			 }else if(val=="农批转"){
				 return "<img class=\"nong\" src=\"/skin/pms/default/img/ico/nong.png\" alt=\"农批转\"/>";
			 }else if(val=="花卉转"){
				 return "<img class=\"hua\" src=\"/skin/pms/default/img/ico/hua.png\" alt=\"花卉转\"/>";
			 }else if(val=="养殖转"){
				 return "<img class=\"yang\" src=\"/skin/pms/default/img/ico/yang.png\" alt=\"养殖转\"/>";
			 }else if(val=="速贷转"){
				 return "<img class=\"su\" src=\"/skin/pms/default/img/ico/su.png\" alt=\"速贷转\"/>";
			 }else {
				 return "<img class=\"yang\" src=\"/skin/pms/default/img/ico/yang.png\" alt=\"养殖贷\"/>";
			 }

}
