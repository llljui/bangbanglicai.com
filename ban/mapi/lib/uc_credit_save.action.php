<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------

class uc_credit_save
{
	public function index(){
		
		$root = get_baseroot();
		
		$user =  $GLOBALS['user_info'];
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		if ($user_id >0){
			$root['user_login_status'] = 1;
			
			$type = "credit_identificationscanning";
	    	$credit_type= load_auto_cache("credit_type");
	    	
	    	$field_array = array(
				"credit_identificationscanning"=>"idcardpassed",
			);
			$u_c_data[$field_array[$type]] = 0;
	    	//身份认证
	    	if($type == "credit_identificationscanning"){
	    		$u_c_data['real_name_encrypt'] = "AES_ENCRYPT('".strim($GLOBALS['request']['real_name'])."','".AES_DECRYPT_KEY."')";
	    		$u_c_data['idno_encrypt'] = "AES_ENCRYPT('".strim($GLOBALS['request']['idno'])."','".AES_DECRYPT_KEY."')";
                $u_c_data['byear'] = substr($GLOBALS['request']['idno'],6,4);
                $u_c_data['bmonth'] = substr($GLOBALS['request']['idno'],10,2);
                $u_c_data['bday'] = substr($GLOBALS['request']['idno'],12,2);
	    		if(getIDCardInfo(strim($GLOBALS['request']['idno']))==0){
	    			$root['show_err'] = "提交失败,身份证号码错误！"; 
	    			$root['status'] = 0; 
	    			output($root);
	    		}
	    		if(get_user_info("count(*)","idno_encrypt = ".$u_c_data['idno_encrypt']." and id <> ".intval($user_id),"ONE")>0){
	    			$root['show_err'] = "提交失败,身份证号码已使用！"; 
	    			$root['status'] = 0; 
	    			output($root);
	    		}
	    		
	    	}
	    	
	    	
	    	$GLOBALS['db']->autoExecute(DB_PREFIX."user",$u_c_data,"UPDATE","id=".$user_id);
	    	
	    	$file=array();
	    	if($credit_type['list'][$type]['file_count'] > 0){
		    	for($i=0;$i<=$credit_type['list'][$type]['file_count']-1;$i++){
		    		if($GLOBALS['request']['is_wap']==1){
		    			if(trim($GLOBALS['request']['file'][$i])!=""){
		    				$file[] = $GLOBALS['request']['file'][$i];				
		    			}
	    			}
	    			else{
						$tmpfile = pathinfo($_FILES['file']['name'][$i]);
						if($tmpfile['error'] == 0){
						
							$time = to_date(TIME_UTC,"Ym");
							if(!file_exists(APP_ROOT_PATH."/public/attachment/".$time))
								@mkdir(APP_ROOT_PATH."/public/attachment/".$time,0777);
							
							$d = to_date(TIME_UTC,"d");
							if(!file_exists(APP_ROOT_PATH."/public/attachment/".$time."/".$d))
								@mkdir(APP_ROOT_PATH."/public/attachment/".$time."/".$d,0777);
								
							$h = to_date(TIME_UTC,"h");
							if(!file_exists(APP_ROOT_PATH."/public/attachment/".$time."/".$d."/".$h))
								@mkdir(APP_ROOT_PATH."/public/attachment/".$time."/".$d."/".$h,0777);
						
							$file_name = md5(TIME_UTC.$_FILES['file']['name'][$i]).".".$tmpfile['extension'];
							
							move_uploaded_file($_FILES['file']['tmp_name'][$i],APP_ROOT_PATH."/public/attachment/".$time."/".$d."/".$h."/".$file_name);
							
							if(file_exists(APP_ROOT_PATH."/public/attachment/".$time."/".$d."/".$h."/".$file_name)){
								$file[] = "./public/attachment/".$time."/".$d."/".$h."/".$file_name;
							}
						
						}
	    			}
		    	}
		    	if(count($file)==0){
	    			$root['show_err'] = "提交失败,请选择图片！"; 
	    			$root['status'] = 0; 
	    			output($root);
		    	}
	    	}
	    	$root['ffile'] = $file;
	    	//file_put_contents("test.txt", print_r($file,1));
	    	
	    	$mode = "INSERT";
	    	$condition = "";
	    	
	    	$temp_info = $GLOBALS['db']->getRow("SELECT user_id,`type`,`file` FROM ".DB_PREFIX."user_credit_file WHERE user_id=".$user_id." AND type='".$type."'");
	    	if($temp_info){
	    		$file_list = unserialize($temp_info['file']);
	    		//认证是否过期
				$time = TIME_UTC;
				$expire_time = $credit_type['list'][$type]['expire']*30*24*3600;
	    		
	    		$mode = "UPDATE";
	    		$condition = "user_id=".$user_id." AND type='".$type."'";
	    	}
	    	$root['temp_info'] = $temp_info;
			if($file){
				foreach($file as $v){
					$file_list[] = $v;
				}
			}
	
	    	$data['user_id'] = $user_id;
	    	$data['type'] = $type;
	    	$data['status'] = 0;
	    	$data['file'] = serialize($file);
	    	$data['create_time'] = TIME_UTC;
	    	$data['passed'] = 0;
	    	
	    	$GLOBALS['db']->autoExecute(DB_PREFIX."user_credit_file",$data,$mode,$condition);
	    	$root['status'] = 1;
	    	$root['show_err'] ="身份验证已提交，待审核";
			
		}else{
			$root['response_code'] = 0;
			$root['show_err'] ="未登录";
			$root['user_login_status'] = 0;
		}
		$root['program_title'] = "身份认证保存";
		output($root);		
	}
}
?>
