<?php
/* *
 * 功能：创蓝发送信息
 * @author wuzeguo
 */
class ChuanglanSms 
{
    public $api_obj;

    public function __construct()
    { 	    	
        require_once APP_ROOT_PATH."system/libs/ChuanglanSmsApi.php";  //引入接口
        $this->api_obj = new ChuanglanSmsApi();
    }

    public function getSMSType()
    {
        //创蓝接口参数
        return array(
            '0' =>'发送成功',
            '101'=>'无此用户',
            '102'=>'密码错',
            '103'=>'提交过快',
            '104'=>'系统忙',
            '105'=>'敏感短信',
            '106'=>'消息长度错',
            '107'=>'错误的手机号码',
            '108'=>'手机号码个数错',
            '109'=>'无发送额度',
            '110'=>'不在发送时间内',
            '111'=>'超出该账户当月发送额度限制',
            '112'=>'无此产品',
            '113'=>'extno格式错',
            '115'=>'自动审核驳回',
            '116'=>'签名不合法，未带签名',
            '117'=>'IP地址认证错',
            '118'=>'用户没有相应的发送权限',
            '119'=>'用户已过期',
            '120'=>'内容不是白名单',
            '121'=>'必填参数。是否需要状态报告，取值true或false',
            '122'=>'5分钟内相同账号提交相同消息内容过多',
            '123'=>'发送类型错误(账号发送短信接口权限)',
            '124'=>'白模板匹配错误',
            '125'=>'驳回模板匹配错误'
        );
    }

    //发送接口
    public function sendSms($mobile, $content)
    {
        $result = $this->api_obj->sendSMS($mobile, strip_tags($content));
        $result = $this->api_obj->execResult($result);
        if(isset($result[1]) && $result[1] == 0){
            return true;
            //$type_list = self::getSMSType();
            //echo $type_list[$result[1]];
        }else{
            return false;
            //echo "未连接上服务器";
        }
    }

    //发送验证码
    public function sendVerify($mobile, $verify)
    {
        $this->sendSMS($mobile, '【邦邦理财】您好，您的验证码是'.$verify);
    }
}
