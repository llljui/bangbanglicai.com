<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 华少（273017814@qq.com）
// +----------------------------------------------------------------------
//require APP_ROOT_PATH.'app/Lib/uc.php';
class uc_trader_add_op
{
	public function index(){
		
		$root = get_baseroot();
		$user =  $GLOBALS['user_info']; 
		$root['session_id'] = es_session::id();
		$user_id  = intval($user['id']);
		
		if ($user_id >0){
			require_once APP_ROOT_PATH.'system/libs/peizi.php';
			$id = intval($GLOBALS['request']['id']);
			$type = intval($GLOBALS['request']['type']);
			$root['type'] = $type;
			$root['id'] = $id;
			$info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order where id = ".$id." and user_id = ".$user_id);
			if(!$info)
			{
				$root["status"] = 0;
				$root["msg"] = "操作失败请重试";
				$root['info'] = $info;
				output($root);	
			}
			
			
			$op_info = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$id." and user_id = ".$user_id." and op_status in (0,1) ");
			if($op_info)
			{
				$root["status"] = 0;
				$root["msg"] = "您还有申请未审核通过，请等待申请通过后操作";
				output($root);	
			}
			
			//有成功追加：保证金 外,不让：增资，减资 了
			$op_type_1 = $GLOBALS["db"] -> getRow("select * from ".DB_PREFIX."peizi_order_op where peizi_order_id = ".$id." and op_type = 0 and user_id = ".$user_id." and op_status = 3 ");
			if($op_type_1 && ($type==2||$type==3))
			{
				$root["status"] = 0;
				$root["msg"] = "提交错误，请刷新重试";
				output($root);	
			}
			switch($type)
			{
				case 0:
				case 4:
				case 5:
					$root["status"] = 1;
					$root["title"] = "金额";
					if($type == 5)
					{
						$root["title"] = "股票账户余额";
					}
					$root["title_val"] = "<input name='op_val' id='op_val' class='f-input' value='1'/>";
					break;
				case 1:
					if($info["type"]==1)
					{
						$root["status"] = 0;
						$root["msg"] = "操作失败请重试";
						
						break;
					}
					$root["status"] = 1;
					$root["title"] = "时间";
					if($info["type"] == 0)
					{
						$root["title_val"] = "<input name='op_val' id='op_val' class='f-input' value='1'/>天";
						$root["title_val_unit"] = "天";
					}
					elseif($info["type"] == 2)
					{
						$root["title_val"] = "<input name='op_val' id='op_val' class='f-input' value='1'/>月";
						$root["title_val_unit"] = "月";
					}
					;
					break;
				case 2:
				case 3:
					if($info["type"]==1)
					{
						$root["status"] = 0;
						$root["msg"] = "操作失败请重试";
						
						break;
					}
					$parma = get_peizi_conf(1,$info["borrow_money"],$info["lever"],0,1);
					$lever_list = $parma["peizi_conf"]["lever_list"][0]["lever_array"];
					$max = -1;
					$min = -1;
					foreach($lever_list as $k=>$v)
					{
						if($max == -1 && $min ==-1)
						{
							$max = $min = $v["lever"];
						}
						if($v["lever"]>$max)
						{
							$max = $v["lever"];
						}
						if($v["lever"]<$min)
						{
							$min = $v["lever"];
						}
					};
					
					$root["status"] = 1;
					$root["title"] = "倍率";
					$root["title_val"] = "<select name='op_val' id='op_val' class='ui-select w120 select-w120 m10' value='0'>";
					if($type == 2)
					{
						$i = $info["lever"]+1;
						if($min == $max || $info["lever"] >= $max)
						{
							$root["status"] = 0;
							$root["msg"] = "当前值已经不能调整";
							
							break;
							//$return["title_val"] .= "<option value='".$info["lever"]."'>".$info["lever"]."</option>";
						}
						else
						{
							while($i <= $max)
							{
								$root["title_val"] .= "<option value='".$i."'>".$i."</option>";
								$i ++ ;
							}
						};
					}
					else
					{
						$i = $info["lever"]-1;
						if($min == $max || $info["lever"] <= $min)
						{
							$root["title_val"] .= "<option value='".$info["lever"]."'>".$info["lever"]."</option>";
						}
						else
						{
							while($i >= $min)
							{
								$root["title_val"] .= "<option value='".$i."'>".$i."</option>";
								$i -- ;
							}
						};
					};
					$root["title_val"] .= "</select>";
					//$root["title_val_fam"] = array();
					if($type == 2)
					{
						$i = $info["lever"]+1;
						if($min == $max || $info["lever"] >= $max)
						{
							$root["status"] = 0;
							$root["msg"] = "当前值已经不能调整";
							
							break;
							//$return["title_val"] .= "<option value='".$info["lever"]."'>".$info["lever"]."</option>";
						}
						else
						{
							while($i <= $max)
							{
								$return[] = $i;
								$i ++ ;
							}
						};
					}
					else
					{
						$i = $info["lever"]-1;
						if($min == $max || $info["lever"] <= $min)
						{
							$return[]= $info["lever"];
						}
						else
						{
							while($i >= $min)
							{
								$return[]= $i;
								$i -- ;
							}
						};
					};
					$root['title_val_fam'] = $return;
					break;
			}
			
			
			$root['user_login_status'] = 1;
			$root['response_code'] = 1;
			
		}else{
			$root['response_code'] = 0;
			$root['msg'] ="未登录";
			$root['user_login_status'] = 0;
		}
		
		if($type == 0){
			$program_title = "追加保证金";
		}elseif($type == 1){
			$program_title = "配资延期";
		}elseif($type == 2){
			$program_title = "申请增资";
		}elseif($type == 3){
			$program_title = "申请减资";
		}elseif($type == 4){
			$program_title = "提前盈余";
		}elseif($type == 5){
			$program_title = "结束配资";
		}
		
		$root['program_title'] = $program_title;
		output($root);		
		
	}
}
?>
