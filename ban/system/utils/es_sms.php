<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
// todo 现只用chuanglan接口

class sms_sender
{
	var $sms;
	
	public function __construct()
    { 	
		//$sms_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms where is_effect = 1");
		//if($sms_info)
		//{
		//	$sms_info['config'] = unserialize($sms_info['config']);
		//	
		//	require_once APP_ROOT_PATH."system/sms/".$sms_info['class_name']."_sms.php";
		//	
		//	$sms_class = $sms_info['class_name']."_sms";
		//	$this->sms = new $sms_class($sms_info);
		//}

        require_once APP_ROOT_PATH."system/sms/ChuanglanSms.php";
        $this->sms = new ChuanglanSms();
    }
    
	
	public function sendSms($mobiles,$content,$is_code=0)
	{
		if(!is_array($mobiles))
			$mobiles = preg_split("/[ ,]/i",$mobiles);
		
		if(count($mobiles) > 0 )
		{
			if(!$this->sms)
			{
				$result['status'] = 0;
			}
            else
            {
                $count = 0;
                foreach ($mobiles AS $k=>$v) {
                    $result = $this->sms->sendSms($v,$content);
                    $result && ++$count;
                } 
                if ($count > 0) {
                    $result = true;
                } else {
                    $result = false;
                }
            }
        }
		else
		{
			$result['status'] = 0;
			$result['msg'] = "没有发送的手机号";
		}
		
        logger::write('sms:'.json_encode($result));
		return $result;
	}
}
?>
