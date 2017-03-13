/************zhaoyu 20150304 公共变量***************/
//域名
var hostName="www.9259.com";

/*
 * 正则验证
 */
var regLoginName=/^\w{4,20}$/;//登录用户名
var regPassword=/^[0-9a-zA-Z`~\!@#$%\^&*\(\)-_+={}|\[\];':\",\.\\\/\?\<\>]{6,16}$/;//登录密码
var regregisterName=/^[a-zA-z]+\w*$|^1[0-9]{10}$/;//注册用户名
//var regregisterName=[\u4E00-\u9FA5]{2,5}(?:·[\u4E00-\u9FA5]{2,5})*;//注册用户名
var regemail=/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;//邮箱
var regcredit=/^(([0-9]|([1-9][0-9]{0,9}))((\.[0-9]{1,2})?))$/;//充值金额
var regrphone=/^1[0-9]{10}$/;//验证手机号码
var regNum=/^\d+$/;//数字
var regCard = /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/;//身份证号码
var regdate=/^(19|20)\d\d(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])$/;//出生日期
var regCirelName = /^[\u4E00-\u9FA5]{2,5}(?:·[\u4E00-\u9FA5]{2,5})*$/;//真实姓名
//var regcirelname = /^[\u4E00-\u9FA5]{2,5}$/;//真实姓名
var regcirelname = /^[\u4E00-\u9FA5]{2,5}(?:·[\u4E00-\u9FA5]{2,5})*$/;//真实姓名
var checkC = /^[\u4E00-\u9FA5]+$/;//真实姓名
/*
 * 登录注册判断使用
 */
var loginName=0;
var registerName=0;
var loginPwd=0;
var codeid=0;
var registerPwd1=0;
var registerEmail=0;
var registerRphone=0;
/*
 * 忘记密码使用
 */
var findpwdName=0;
/*
 * 充值提现使用
 */
var creditMsg=0;
var txmoneymsg=0;
//添加银行卡使用
var regbankid=0;
var regbankagin=0;
/*
*安全中心使用
*/
var regphone=0;
var regcard=0;
var regrename=0


/*
*帮助中心使用
*/
var helpNum=7;//左边菜单子项的个数
var urlName499=['list_499','detail_507','detail_508'];//了解就爱金融
var urlName502=['list_502','list_551'];//新手必读
var urlName503=['list_503'];//收费问题
var urlName504=['list_504','list_552'];//投资问题
var urlName505=['list_505'];//借款问题
var urlName506=['list_506'];//名词解释
var urlName581=['list_581'];//债权转让解释

/*
*关于我们使用
*/
var aboutNum=5;//左边菜单子项的个数
var urlName509=['aboutUS'];//公司介绍
var urlName510=['safe'];//安全保障
var urlName519=['newslist'];//站内公告
var urlName529=['newsdetail_529'];//站内公告

var aboutMaxLine=10;//公告列表显示条数
/*
 * 判断是否为登录注册充值页面
 */
var checkHrefReg="/MemberCenter/Register.html";
var checkHrefLogin="/MemberCenter/login.html";
var checkHrefFind="/MemberCenter/find_0.html";
var checkHrefFinds=/^\/[M][e][m][b][e][r][C][e][n][t][e][r]\/[f][i][n][d][_]\d[.][h][t][m][l]$/;
var checkHrefIam33="/iam33.html";
/*
 * 投资列表
 */
var investlen=0;
var creidtlen=0;
/*
 * 投资详情
 */
var SHORTEST="即若借款人在放款日后的最短收息期内（含最短收息期 ）提前还款的，则借款人需向投资人支付最短收息期的利息；超过最短收息期后提前还款则按实际占用天数计算应收利息。";
/*
 * 借款标状态
 * 02竟标中
 * 03满标
 * 04还款中
 * 05还款完成
 */ 
var STATUS_01="01";
var STATUS_02="02";
var STATUS_03="03";
var STATUS_04="04";
var STATUS_05="05";
var STATUS_06="06";
var STATUS_07="07"; 
var STATUS_99="99";
/*
 * 期限单位
 * unit 
 * 0月 1季 2天 4周   3未知
 */
var UNIT01=0;
var UNIT02=1;
var UNIT03=2;
var UNIT04=4;
var UNIT05=3

var D='D';
var DUNIT='天';
var M='M';
var MUNIT='个月';

/*
 * 还款方式
 * 01按期等额本息还款;
 * 03分期付息到期还本;
 * 40自定义还款;
 * 41一次性
 */
var RPAYMENTWAY01='01';
var RPAYMENTWAY03='03';
var RPAYMENTWAY40='40';
var RPAYMENTWAY41='41';

var RPAYWAY01='按期等额本息还款';
var RPAYWAY03='分期付息到期还本';
var RPAYWAY40='自定义还款';
var RPAYWAY41='一次性还款';
//按日等额本息
var RPAYWAY201="在还款期内，借款人每日偿还同等数额的借款(包括本金和利息)。每日还款额中的本金比重逐日递增、利息比重逐日递减。";
//按周等额本息
var RPAYWAY401="在还款期内，借款人每周偿还同等数额的借款(包括本金和利息)。每周还款额中的本金比重逐周递增、利息比重逐周递减。";
//按月等额本息
var RPAYWAY001="在还款期内，借款人每月偿还同等数额的借款(包括本金和利息)。每月还款额中的本金比重逐月递增、利息比重逐月递减。";
//按季等额本息
var RPAYWAY101="在还款期内，借款人每3个月偿还同等数额的借款(包括本金和利息)。每3个月还款额中的本金比重每次递增、利息比重每次递减。";
//按日付息到期还本
var RPAYWAY203="在还款期内，借款人每日偿还借款的利息，到期后归还本金及未结清利息的还款方式。";
//按周付息到期还本
var RPAYWAY403="在还款期内，借款人每周偿还借款的利息，到期后归还本金及未结清利息的还款方式。";
//按月付息到期还本
var RPAYWAY003="在还款期内，借款人每月偿还借款的利息，到期后归还本金及未结清利息的还款方式。";
//按季付息到期还本
var RPAYWAY103="在还款期内，借款人每3个月偿还1次借款利息，到期后归还本金及未结清利息的还款方式。";
//一次性还款
var RPAYWAY041="在还款到期后，借款人一次性偿还借款的本金和利息的还款方式。";
//自定义还款
var RPAYWAY040="在还款期内，借款人按原先约定的期限和每期偿还的金额还款的还款方式。";


/*
 * mobile
 */

var MOBILEPHONE=0;


var publicdebtday="1";
var publicdebtdays="1";
var publicdebtper="0.5";
//正式
var appid9259="wx7523d1b0832b439f";
























