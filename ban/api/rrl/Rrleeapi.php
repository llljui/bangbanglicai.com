<?php
/**
 * Created by renrenlee.com.
 * User: 人人利-小猪迷
 * Cont: 人人利API接口PHP版
 * 
 * 商务账号 zlo_txhUq
商务密码 zlopwd_YfnCerpw
商务号 9e1e96218d30471a59e31b6d1acc10
签名类型 MD5
签名KEY renrenleeandfengshoudai
 * 
 */

class Rrleeapi
{
    //Cust_id(商务编号)验证接口地址,以下为测试环境，正式需换成"http://openapi.renrenlee.com/zlo/Getp2pinfo/custid/"
    public $Cust_id_url = 'http://openapi.amoydao.com/zlo/Getp2pinfo/custid/';
    //推送url，以下为测试环境，正式需换成"http://openapi.renrenlee.com/zlo/getp2pinfo/getsubscribe/"
    public $push_url = "http://openapi.amoydao.com/zlo/getp2pinfo/getsubscribe/";
    //人人利商务账号
    public $rrl_username = "zlo_txhUq";
    //人人利商务密码
    public $rrl_password = "zlopwd_YfnCerpw";
    //人人利商务号
    public $rrl_cust_id = "9e1e96218d30471a59e31b6d1acc10";
    //人人利签名类型
    public $rrl_type = 'MD5';
    //人人利签名key
    public $rrl_key = 'renrenleeandfengshoudai';
    //各状态码对应表
    public $code_array = array(
        '886' => '商务号为空(Cust_id)',
        '887' => '商务号格式错误(Cust_id)',
        '888' => '你所在服务器的IP不允许请求,请联系人人利重新设置IP',
        '889' => '商务号不存在(Cust_id)',
    );
    //通过人人利POST传送过来的商务号(Cust_id)
    public $POST_Cust_id = '';
    //通过人人利POST传送过来的手机号
    public $POST_Phone = '';

    public function __construct()
    {
        //获取商务编号,这个是公用的判断接口
        $this->POST_Cust_id = $_POST['Cust_id'];
        $this->test_cust_id($this->POST_Cust_id); //验证商务号
    }
    //一键注册方法
    public function register()
    {
        $this->POST_Phone = $_POST['Phone'];//获取电话号
        if($this->is_empty($this->POST_Phone))
        {
            $this->json_back('102','电话号码不能为空');
        }
        //获取签名类型
        $Sign_type= $_POST['Sign_type'];
        //获取签名
        $Sign= $_POST['Sign']; //进行签名类型验证（现我们只开放md5的加密）
        if($Sign_type!=$this->rrl_type){
            $this->json_back('103','签名类型出错');
        }
        //进行签名验证
        if($Sign_type($this->rrl_cust_id.$this->POST_Phone.$this->rrl_key)!=$Sign){
            $this->json_back('104','签名出错');
        }
        //以下调用的方法为业务逻辑调用方法，也是合作方技术自己根据自己的项目框架来作相应操作
        $this->register_write(); //此方法在此类的最后面倒数第二个
    }
	
	public function checklogin(){
		$Cust_key = strim($_REQUEST['Cust_key']);
		$Access_tokens = MD5($_REQUEST['Access_tokens']);
		
		$user_info = get_user_info("*","cust_key='".$Cust_key."' AND access_tokens='".$Access_tokens."'");
		
		if($user_info){
			es_session::set("user_info",$user_info);
		}
		
		$url = url("index","index#index");
		app_redirect(str_replace("/api", "", $url));
	}
	
    //数据抓取
    public function data_capture()
    {
        //获取订单号
        $Order_no = isset($_POST['Order_no'])?$_POST['Order_no']:'';
        //获取合作方绑定的唯一KEY
        $Cust_key = isset($_POST['Cust_key'])?$_POST['Cust_key']:'';
        //获取开始时间
        $Start_date = isset($_POST['Start_date'])?$_POST['Start_date']:'';
        //获取结束时间
        $End_date = isset($_POST['End_date'])?$_POST['End_date']:'';
        //获取签名类型
        $Sign_type = $_POST['Sign_type'];
        if($Sign_type!=$this->rrl_type){
            $this->json_back('105','签名类型出错');
        }
        //获取签名
        $Sign = $_POST['Sign'];
        //进行签名验证【MD5(“商务编号+订单号+Cust_key+双方约定key值)】
        if($Sign_type($this->rrl_cust_id.$Order_no.$Cust_key.$this->rrl_key)!=$Sign){
            $this->json_back('106','签名出错');
        }
        //以下调用的方法为业务逻辑调用方法，也是合作方技术自己根据自己的项目框架来作相应操作
        $this->capture_write($Order_no,$Cust_key,$Start_date,$End_date); //此方法在此类的最后面倒数第一个
    }
    //数据推送
    public function data_push($datas = array())
    {
		
        //判断是否为二维数组
        if($this->is_empty($datas[0]['Cust_key'])===true){
           // $this->errormsg(000,'返回的数据不是二维数组');
           return false;
        }
        $data = array(
            'url' => $this->push_url,
            'params' => array(
                "Cust_id" => $this->rrl_cust_id,
                "Data" => json_encode($datas),
                "Sign_type" => "MD5",
                "Sign" => MD5($this->rrl_username.$this->rrl_password.$this->rrl_cust_id.$this->rrl_key),
            ),
        );
        $result = $this->api_curl($data);
	
        if($result['Code']==101){
            return true;
        }
        //$this->errormsg($result['Code'],$result['Tip']);
    }
    //Cust_id(商务编号)验证方法
    public function test_cust_id()
    {
        $data = array(
            'url' => $this->Cust_id_url,
            'params' => array(
                'Cust_id'   => $this->rrl_cust_id
            ),
        );
        if($this->is_empty($this->POST_Phone)===false)
        {
            $data['params']['Phone'] =$this->POST_Phone;
        }
        $curl_result = $this->api_curl($data);
        if($curl_result['Data']===false){
            //验证失败,输出错误
            $this->json_back($curl_result['Code'],$this->code_array[$curl_result['Code']],$curl_result);
        }
        //验证成功
        return true;
    }
    /**
     * user: php的curl方法
     * data: array(
     *  url=>'接口url'，
     *  methed=>'POST或GET'，
     *  params=>'参数值，必须为数组',
     *  returnFormat=>'返回值格式，这里为json',
     *  timeout=>'10', //这里默认为10秒超时
     *  ssl=>'curl ssl证书', //默认false
     * );
     * 以上data数据curl,params是必须，其它可为空，为空时默认POST请求方式,格式为json返回,默认超时为10秒,ssl证书关闭
     */
    private function api_curl($data=array())
    {
        $url = $data['url'];
        $methed = isset($data['methed'])?$data['methed']:'POST';
        $params = $data['params'];
        $returnFormat = isset($data['returnFormat'])?$data['returnFormat']:'json';
        $timeout = isset($data['timeout'])?$data['timeout']:10;
        $ssl = isset($data['ssl'])?$data['ssl']:false;
        //参数整合
        $_curlData = array();
        foreach($params as $key=>$val){
            $_curlData[] = $key.'='.urlencode($val);
        }
        $curlData = implode('&', $_curlData);
        //初始化curl
        $ch = curl_init();

        if ($methed == 'GET') {
            $c1 = explode('?', $url);
            if(!empty($params)){
                $url .= count($c1) > 1 ? '&'.$curlData : '?'.$curlData;
            }

        }else if ($methed == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlData);
        }

        if($ssl){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名
        }
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//超时时间单位：秒

        $data = curl_exec($ch);//运行curl
        $curl_errno = curl_errno($ch);
        curl_close($ch);

        if ($curl_errno > 0)
        {
            $data = array(
                'status' => '-1',
                'info' => 'Request Error :'. $curl_errno
            );
            return $data;
        }

        if($returnFormat=='json')
        {
            $result=json_decode($data,true);
            return $result;
        }
        return $data;
    }
    //输出错误,自已在开发接口时自己调用自己时使用，json_back用于人人利联测时使用,json_back合作言技术可以在止加入日志操作程序来记录每次请求的信息
    private function errormsg($Code,$Tip,$Data='')
    {
        exit(var_dump(array('Code' => $Code,'Tip' => $Tip,'Data' => $Data,)));
        //备注:这里作为测试执行出错时输出，正式上线，可以改写成你的日志方法,Tip为错误信息,Code为错误码,Data为请求返回信息显示或其它信息,将作为错误快速定位信息
    }
    private function json_back($Code,$Tip,$Data='')
    {
        exit(json_encode(array('Code' => $Code,'Tip' => $Tip,'Data' => $Data,)));
    }
    //判断是否为空的方法
    private function is_empty($str)
    {
        if(!empty($str) && isset($str) && !is_null($str))
        {
            return false;
        }
        return true;
    }
    //随机码生成
    private function random($length, $chars = '012345689abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ') {
        $hash = '';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }
    //为合作方操作的一键注册方法，此方法也可由合作方根据自身情况写在相应的地方，不做限制
    private function register_write()
    {
    	require APP_ROOT_PATH."/system/libs/user.php";
        //此方法为合作方技术开发方法，具体步骤如下：
        //第一步
        //如果你的项目是已手机号作为登入账号的话，请根据手机号链接数据，查询账号是否存在，或不是已手机作为账号可忽略此步直接进入第三步
        //第二步
        //如果此账号已存在，就说明是合作言原来的用户，那么请操作以下语句，不存在请进行第三步
        $rs = check_user("mobile",strim($_POST['Phone']));
		if($rs['status'] == 0){
        	$this->json_back(100,'此账号不可注册'); //若可注册，跳过此步
		}
        //第三步
        //新增用户信息（入数据库）
        //建议用户信息包含我们的商务号一起写入数据库中,用户要加入参数信息【必加】，其它根据合作方相关情况加入
        $data = array(
            'cust_key' => MD5($this->random(10).time()), //生成的Cust_key,此处为10个随机码加当前时间的md5值作为Cust_key，合作方可按自己规则生成
            'cust_id' => $this->rrl_cust_id, //商务号
            'mobile' => $this->POST_Phone, //手机号
            'is_effect' => 1, 
            'create_time' =>TIME_UTC,
            'register_ip' =>CLIENT_IP,
            'user_name' =>$this->POST_Phone,
        );
		
		$user_pwd = $this->random(10);
		$data['user_pwd'] = MD5($user_pwd);
		$access_tokens = $this->random(8);
		$data['access_tokens'] = MD5($access_tokens);
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"INSERT");
		
        //以上参数为小写，具体大小写，合作自行定义，这里就写个说明，但是这三个参数在新增用户时是不可少的
        //第四步【组成注册成功后返回信息】
        $DataTure = array(
            'Cust_key' => $data['cust_key'], //上面新增用户生成的Cust_key
            'Access_tokens' => $access_tokens, //用于一键登入时返回的随机码
            'Callback_url' => SITE_DOMAIN.APP_ROOT."/rrl_api.php?act=login", //返回一键登入的地址，可加相关参数,此地址为合作方的地下，这里我做个例子
        );
        //以上的$DataTure里的参数的第一个字母必须大写哦，不可忘记
        //第五步【返回以上的信息】,若注册失败，请进入第六步,过掉第五步,若成功直接第五步就返回成功信息了
        $user_id = $GLOBALS['db']->insert_id();
        if($user_id > 0){
        	if($data['mobile']){
				$msg_data['dest'] = $data['mobile'];
				$msg_data['send_type'] = 0;
				$msg_data['title'] = "人人返注册成功密码通知";
				$msg_data['content'] = "您在".app_conf("SHOP_TITLE")."的登录密码为:".$user_pwd.",您可以在登录后自行修改密码！";
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['user_id'] = $user_id;
				$msg_data['is_html'] = 0;
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data,"INSERT"); //插入
			}
			
        	$this->json_back(101,'注册成功',$DataTure);
		}
		else{
	        //第六步
	       	$this->json_back(102,'注册失败的错误信息');
		}
        //开发完后请将您的一键绑定地址发给人人利，谢谢合作
    }
    //为合作方操作的数据抓取方法，此方法也可由合作方根据自身情况写在相应的地方，不做限制
    private function capture_write($Order_no,$Cust_key,$Start_date,$End_date)
    {
        //此方法为合作方技术开发方法，具体步骤如下：
        //第一步【参数整合】
        $where = "u.cust_key<> '' AND u.cust_id='".$this->POST_Cust_id."' "; //Cust_id是必须，此条件为第一选(必须)，具体查询加不加这句，根据合作方的数据库设计而定
        //参数一，订单是否存在
        if($this->is_empty($Order_no)===false)
        {
            $where .= " and dl.id='".$Order_no."'";
        }
        //参数二，Cust_key是否存在
        if($this->is_empty($Cust_key)===false)
        {
            $where .= " and u.cust_key='".$Cust_key."'";
        }
        //参数三，抓取开始时间是否存在
        if($this->is_empty($Start_date)===false)
        {
            //此处的时间为13位时间戳，合作方请根据自己的时间来定是10位还是13位,10位的话接收的时间要转化为10位哦
			$where .= " and  dl.create_date >= '".to_date(intval($Start_date/1000),"Y-m-d")."'";
        }
        //参数四，抓取结速时间是否存在
        if($this->is_empty($End_date)===false)
        {
            //此处的时间为13位时间戳，合作方请根据自己的时间来定是10位还是13位,10位的话接收的时间要转化为10位哦
            $where .= " and  dl.create_date <= '".to_date(intval($End_date/1000),"Y-m-d")."'";
        }
		
		$sql = "SELECT dl.*,u.cust_key,d.name,d.rate,d.start_time,d.repay_start_time,d.success_time,d.last_repay_time,d.repay_time,d.repay_time_type FROM ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."user u ON u.id= dl.user_id 
				LEFT JOIN ".DB_PREFIX."deal d ON d.id = dl.deal_id WHERE ".$where;
		$list = $GLOBALS['db']->getAll($sql);
		
        //第二步,根据条件搜索数据库得到相应的数据，这里的数据是二维数组哦,格式如下
        $data =array();
		foreach($list as $k=>$v){
			$end_repay_time=0;
			if( $v['repay_start_time'] > 0){
				if($v['repay_time_type'] == 0)
					$end_repay_time =  $v['repay_start_time'] + $v['repay_time']*24*3600;
				else
					$end_repay_time =  next_replay_month($v['repay_start_time'],$v['repay_time']);
			}
	        $data[] = array(
	                "Cust_key"=>$v['cust_key'],
	                "User_name"=>$v['user_name'],
	                "Order_no"=>$v['id'],
	                "Pro_name"=>$v['name'],
	                "Pro_id"=>$v['deal_id'],
	                "Invest_money"=>$v['money'],
	                "Invest_start_date"=>$v['create_time']."000",
	                "Invest_end_date"=>$end_repay_time > 0 ? $end_repay_time."000"  : "0",
	                "Back_money"=>$GLOBALS['db']->getOne("SELECT sum(true_repay_money) FROM ".DB_PREFIX."deal_load_repay WHERE has_repay = 1 AND load_id=".$v['id']),
	                "Rate"=>$v['rate'],
	                "Back_last_date"=>$v['last_repay_time'] > 0 ? $v['last_repay_time']."000" : "0" 
	            );
		}
        //以上就是规定的数据结构返回
        //第三步【返回数据】
        $this->json_back(101,'返回成功',$data);
        //开发完成后，请将您的抓取链接发给人人利，并通知人人处进行联测，谢谢合作
    }
}