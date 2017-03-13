<?php
class wx_do_register{
	public function index()
	{
		
		$root = array(); //用于返回的数据
		
		$mobile=strim($GLOBALS['request']['mobile']);
		$verify_coder=strim($GLOBALS['request']['code']);
		$province=strim($GLOBALS['request']['province']);
		$city=strim($GLOBALS['request']['city']);

		$user_data=array();
		$user_data['mobile'] = $mobile;
		$user_data['wx_openid']=strim($GLOBALS['request']['wx_openid']);
		$user_name=$user_data['user_name']=strim($GLOBALS['request']['user_name']);
		$user_data['sex']=strim($GLOBALS['request']['sex']);
		$user_data['referer'] = strim($GLOBALS['request']['referer']);
	
		if($mobile=="")
		{
			$root['status'] = 0;
			$root['info'] = "手机号码为空";
			output($root);
		}
	
		if($verify_coder==""){
			$root['status'] = 0;
			$root['info'] = "手机验证码为空";
			output($root);
		}
		
		
		//判断验证码是否正确=============================
		if($GLOBALS['db']->getOne("select count(*) FROM ".DB_PREFIX."mobile_verify_code where mobile=".$mobile." and verify_code='".$verify_coder."'")==0){
 			$root['status'] = 0;
			$root['info'] = "手机验证码错误";
			output($root);
		}
		
		$user_data['mobile_encrypt'] = "AES_ENCRYPT('".strim($user_data['mobile'])."','".AES_DECRYPT_KEY."')";
		$user=get_user_has('mobile_encrypt',$user_data['mobile_encrypt'],1);
		$user_data['mobilepassed'] = 1;
		if($user){
			$root['status'] = 1;
			$GLOBALS['db']->query("update ".DB_PREFIX."user set wx_openid='".$user_data['wx_openid']."' where id=".$user['id']);
 			$user_id = $user['id'];	
			$root['info']	=	"绑定成功!";
			$root['user_name'] =$user['user_name'];
			$root['user_pwd'] = $user['user_pwd'];
			
 		}else{
 			
            if(isset($user_data['referer']) && $user_data['referer']!=""){
                //$p_user_data = $GLOBALS['db']->getRow("SELECT id,user_type FROM ".DB_PREFIX."user WHERE mobile_encrypt = AES_ENCRYPT('".$user_data['referer']."','".AES_DECRYPT_KEY."') OR user_name='".$user_data['referer']."'");
                $p_user_data = get_user_info("id,user_type","mobile_encrypt =AES_ENCRYPT('".$user_data['referer']."','".AES_DECRYPT_KEY."') OR user_name='".$user_data['referer']."'");
                if($p_user_data["user_type"] == 3)
                {
                    $user_data['referer_memo'] = $p_user_data['id'];
                    $user_data['pid'] = 0;
                }
                else
                {
                    $user_data['pid'] = $p_user_data["id"];
                    if($user_data['pid'] > 0){
                        $refer_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user WHERE pid='".$user_data['pid']."' ");
                        if($refer_count == 0){
                            $user_data['referral_rate'] = (float)trim(app_conf("INVITE_REFERRALS_MIN"));
                        }
                        elseif((float)trim(app_conf("INVITE_REFERRALS_MIN")) + $refer_count*(float)trim(app_conf("INVITE_REFERRALS_RATE")) > (float)trim(app_conf("INVITE_REFERRALS_MAX"))){
                            $user_data['referral_rate'] =(float)trim(app_conf("INVITE_REFERRALS_MAX"));
                        }
                        else{
                            $user_data['referral_rate'] =(float)trim(app_conf("INVITE_REFERRALS_MIN")) + $refer_count*(float)trim(app_conf("INVITE_REFERRALS_RATE"));
                        }
                            
                        
                        if(intval(app_conf("REFERRAL_IP_LIMIT")) > 0 && $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user WHERE register_ip ='".CLIENT_IP."' AND pid='".$user_data['pid']."'") > 0){
                            $user_data['referral_rate'] = 0;
                        }
                    }
                    else{
                        $user_data['pid'] = 0;
                    }
                }
            }
			
 			if($user_data['sex']==0){
 				$user_data['sex']=-1;
 			}elseif($user_data['sex']==1){
 				$user_data['sex']=1;
 			}else{
 				$user_data['sex']=0;
 			}
 			
 		
				require_once APP_ROOT_PATH."system/libs/user.php";
				$rs = auto_create($user_data, 1);
				$user_id = intval($rs['user_data']['id']);

				if($user_id > 0)
				{
					$root['status'] = 1;
					$root['info']	=	"绑定成功!!";
					$root['data'] = $user_id;
					$root['user_name'] = $rs['user_data']['user_name'];	
					$root['user_pwd'] = $rs['user_data']['user_pwd'];					
				}else{
					$root['status'] = 0;
					$root['info'] = "绑定失败！";
					output($root);
				}
						
		
		}
			output($root);
	}
	
}
?>