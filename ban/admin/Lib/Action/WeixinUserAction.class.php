<?php
// +----------------------------------------------------------------------
// | 邦邦理财
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.bangbanglicai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------
require APP_ROOT_PATH."system/wechat/CIpLocation.php";
require APP_ROOT_PATH."system/wechat/platform_wechat.class.php";
require APP_ROOT_PATH."system/libs/words.php";

class WeixinUserAction extends WeixinAction{
  	public function __construct(){
		parent::__construct();
 		$this->assign("max_size",get_max_file_size());
 		$this->assign("max_size_byte",get_max_file_size_byte());
  	}
  	public function groups()
	{
		$groups=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_group where account_id=".$this->account_id);
 		$this->assign('groups',$groups);
 		$this->assign('box_title','分组管理');
  		$this->display();
	}
	/**
	 * 删除
	 */
	public function delgroups(){
		$ids_str = strim($_REQUEST['ids']);
		$id = intval($_REQUEST['id']);
		if($ids_str != ""){
			//批量删除
			$replys = M('WeixinGroup')->where(array('id'=>array('in',explode(',',$ids_str))))->findAll();
			foreach($replys as $reply){
				M('WeixinGroup')->where(array('id'=>$reply['id']))->delete();
 			}
			$this->success("删除成功",$this->isajax);
		}elseif($id > 0){
			//单条删除
			$reply = M('WeixinGroup')->where(array('id'=>$id))->find();
			if($reply){
				M('WeixinGroup')->where(array('id'=>$id))->delete();
 			}
			$this->success("删除成功",$this->isajax);
		}else{
			$this->error("请选择要删除的选项",$this->isajax);
		}
	}
	public function groups_add(){
		 $this->assign("box_title","添加分组");
		 $this->display();
	}
	public function groups_editor(){
		 /********基本设置**********/
        $account_id = $this->account_id;
        $id = intval($_REQUEST['id']);
        /********基本设置**********/
        
        $group =$GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_group where id=".$id." and account_id=".$account_id); 
       
        $this->assign("group",$group);
        $this->assign("box_title","修改");
		$this->display();
	}
	public function groups_save(){
        /********基本设置**********/
       $this->isajax = 1;
	   $platform = $platform= new PlatformWechat($this->option);
	   $platform_authorizer_token=$platform->check_platform_authorizer_token();
        
        if($_REQUEST['id']){
            $id = intval($_REQUEST['id']);
        }
        $name = strim($_REQUEST['name']);
        $intro = strim($_REQUEST['intro']);
        /********基本设置**********/
        if($id){//如果存在 为更新
             $local_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_group where id=".$id);
            
            //判断是否有更新分组名称
            if($local_group['name']==$name && $local_group['intro'] !=$intro ){
                 $GLOBALS['db']->autoExecute(DB_PREFIX."weixin_group",array('intro'=>$intro),'UPDATE','id='.$id);
                $this->success("更新成功",$this->isajax);
            }elseif($local_group['name']!=$name){
               $json= $platform->updateGroup($local_group['groupid'],$name);
               if($json){
               		$update_data = array();
                    $update_data['name'] = $name;
                    $update_data['intro'] = $intro;
                    $GLOBALS['db']->autoExecute(DB_PREFIX."weixin_group",$update_data,'UPDATE','id='.$id);
               		$this->success("更新成功",$this->isajax);
               }else{
               		$this->error("同步出错，错误代码".$json['errcode'].":".$json['errmsg'],$this->isajax);
               }
                
            }
            $this->success("没有修改内容",$this->isajax);
        }else{//添加新的分组
            $insert_data = array();
            $insert_data['account_id'] = $this->account_id;
            $insert_data['name'] = $name;
            $insert_data['intro'] = $intro;
            //var_dump($insert_data);
			
             //post data 
            $json = $platform->createGroup($name);
            if($json){
                $insert_data['groupid'] = $json['group']['id'];
                $GLOBALS['db']->autoExecute(DB_PREFIX."weixin_group",$insert_data);
                $this->success("新增成功",$this->isajax);
            }else{
                $this->error("新增失败",$this->isajax);
            }
        }
     }
	/**
     * 同步分组
     */
    function groups_synch(){
         /********基本设置**********/
        $this->isajax = 0;
        $platform = $platform= new PlatformWechat($this->option);
        $platform_authorizer_token=$platform->check_platform_authorizer_token();
        
        if($platform_authorizer_token){
        	$json=$platform->getGroup();
        	if($json){
        		$wechat_groups = $json['groups'];
        		if($wechat_groups){  //存在分组数据
        			$condition= ' account_id='.$this->account_id;
			        //平台端数据
			        $local_groups= $GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_group where ".$condition);
			        
 			        //格式化数据
			        
			        $update_data = array();
			        $notdel_data = array();
			        $inster_data = array();
			        
			        foreach ($wechat_groups as $wg_k=>$wg_v){
			            //判断是否存在了
			            $is_not_null = true;
			            //不能删除的数据
			            $notdel_data[] = $wg_v['id'];
			            $temp_data = array();
			            $temp_data['account_id'] = $this->account_id;
			            $temp_data['groupid'] = $wg_v['id'];
			            $temp_data['name'] = $wg_v['name'];
			            $temp_data['fanscount'] = $wg_v['count'];
			            foreach($local_groups as $lg_k=>$lg_v){
			                if($lg_v['groupid']==$wg_v['id']){
			                    //分组名称有改变 或者 粉丝数量改变的分组
			                    if($wg_v['name']!=$lg_v['name'] || $wg_v['count'] !=$lg_v['fanscount']){
			                        //更新数据
 			                        $GLOBALS['db']->autoExecute(DB_PREFIX."weixin_group",$temp_data,'UPDATE',$condition." AND groupid=".$temp_data['groupid']);
			                    }
			                    //已经存在了
			                    $is_not_null = FALSE;
			                }
			            }
			            if($is_not_null){
			                //保存不存在的数据
			                $inster_data[] = $temp_data;
			            }
			        }
			        
			        //删除微信端不存在的数据
 					$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."wechat_group WHERE ".$condition." AND groupid not in (".  implode(",", $notdel_data).") ");
			        
			        if($update_data){
			            foreach($update_data as $up_data){
			               
			            }
			        }
			        
			        //插入新的数据
			        if($inster_data){
			            foreach($inster_data as $ins_data){
 			                $GLOBALS['db']->autoExecute(DB_PREFIX."weixin_group",$ins_data);
			            }
			        }
			    }
			    $this->success("同步成功",$this->isajax);
        	}else{
        		$this->error("同步出错，错误代码".$json['errcode'].":".$json['errmsg'],$this->isajax);
        	}
        	
        }else{
        	$this->error("通讯出错，请重试",$this->isajax);
        }
       
    }
    
	public function index()
	{
    	 //分页设置
        $page_size = 20; //分页量
        $page = $this->page; //当前页码
	    $page_args = array();
        
    	$showStatistics = 1;//是否显示图表
        if (isset($_GET['p']) || isset($_POST['keyword'])) {
            $showStatistics = 0;
        }
        $this->assign('showStatistics', $showStatistics);
        
        $where = "account_id=".$this->account_id;
        if (strlen(trim($_POST['keyword']))) {
            $keyword = htmlspecialchars(trim($_POST['keyword']));
            $where  .= " and nickname like '%". $keyword."%' ";
            $list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_user where ".$where);  
        } else {
        	//print_r($this->account_id);
        	
            if (isset($_GET['groupid'])) {
                $where  .= " and groupid=".intval($_GET['groupid']);
                $page_args['groupid'] = intval($_GET['groupid']);
                $this->assign('groupid', intval($_GET['groupid']));
            }
         	$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."weixin_user where ".$where);
	        //分页
	        $pager = buildPage('WeixinUser/index',$page_args,$count,$page,$page_size);
  	        
  	        $this->assign('pager',$pager);
  	        
        	$list = $GLOBALS['db']->getAll("select wu.*,u.id as uid from ".DB_PREFIX."weixin_user wu LEFT JOIN ".DB_PREFIX."user u ON wu.openid=u.wx_openid where ".$where." order by id DESC limit ".$pager['limit']);  
        }
        
        $platform = $platform= new PlatformWechat($this->option);
	    $platform_authorizer_token=$platform->check_platform_authorizer_token();
	    if($platform_authorizer_token){
	    	$json=$platform->getGroup();
	    	 $wechat_groups = $json['groups'];
       		 $wechat_groups_ids = array();
	    	 if ($wechat_groups) {
	            foreach ($wechat_groups as $g) {
	            	$condition= ' account_id='.$this->account_id.' and groupid='.$g['id'];
	            	$thisGroupInDb = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_group where ".$condition);
 	                $arr = array('account_id' => $this->account_id, 'groupid' => $g['id'], 'name' => $g['name'], 'fanscount' => $g['count']);
	                if (!$thisGroupInDb) {
	                	$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_group",$arr);
	                    
	                } else {
 	                	$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_group",$arr,'UPDATE',"id=".$thisGroupInDb['id']);
	                }
	                array_push($wechat_groups_ids, $g['id']);
	            }
	        }
	    }else{
	    	//$this->error("通讯出错，请重试",$this->isajax);
	    }
        
        $groups=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_group where account_id=".$this->account_id." order by id ASC");
        
        $this->assign('groups', $groups);
        $groupsByWechatGroupID = array();
        if ($groups) {
            foreach ($groups as $g) {
                $groupsByWechatGroupID[$g['groupid']] = $g;
            }
        }
        if ($list) {
            $i = 0;
            foreach ($list as $item) {
                $list[$i]['smallheadimgurl'] = $item['headimgurl'];
                $list[$i]['groupName'] = $groupsByWechatGroupID[$item['groupid']]['name'];
                $list[$i]['subscribe_time'] = 	to_date($item['subscribe_time']);
                $i++;
            }
        }
        $this->assign('list', $list);
        $this->assign('box_title','会员管理');
        $this->display();
    }
	 //获取最新粉丝
    public function send()
    {
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        	
           $platform = $platform= new PlatformWechat($this->option);
	   	   $platform_authorizer_token=$platform->check_platform_authorizer_token();
            
            if (isset($_GET['next_openid'])) {
               $json_token = $platform->getUserList($_GET['next_openid']);
            }else{
            	 $json_token = $platform->getUserList();
            }
            if($json_token){
            	$arrayData = $json_token['data']['openid'];
	            $nextOpenID = $json_token['next_openid'];
	            $a = 0;
	            $b = 0;
	            foreach ($arrayData as $data) {
	            	$user = $platform->getUserInfo($data);
	            	$user['account_id'] = $this->account_id; 
	            	$check = $GLOBALS['db']->getOne("select openid from ".DB_PREFIX."weixin_user where openid = '".$data."' and account_id = ".$this->account_id);
  	                if (!$check) {
  	                	
 	                	$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_user",$user,"INSERT");
 	                    $a++;
	                } else {
	                	$user['account_id'] = $this->account_id; 
	                	$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_user",$user,"UPDATE","openid = '".$data."' and account_id = ".$this->account_id);
	                    $b++;
	                }
	            }
	            if (strlen($nextOpenID)) {
  	                $this->showFrmSuccess((('本次更新' . $a) . '条,重复') . ($b = $b == 1 ? 0 : $b . '条，正在获取下一批粉丝数据'),1,$nextOpenID);
	           		
	            } else {
	                $this->success('更新完成,现在获取粉丝详细信息',0);
	            }
            }else{
            	$this->error("获取失败",$this->isajax);
            }
            
        } else {
            $this->showFrmErr('非法操作');
        }
    }
    //刷新所有粉丝详细信息
    public function send_info()
    {
        if ($_SERVER['REQUEST_METHOD']=='GET') {
            $refreshAll = isset($_GET['all']) ? 1 : 0;
            $platform = $platform= new PlatformWechat($this->option);
	   	    $platform_authorizer_token=$platform->check_platform_authorizer_token();
            if ($refreshAll) {
                $fansCount = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."weixin_user where   account_id = ".$this->account_id);
                $i = intval($_GET['i']);
                $step = 20;//每次更新20个
                 $fans = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_user where   account_id = ".$this->account_id." order by id desc limit $i,$step");
                if ($fans) {
                    foreach ($fans as $data_all) {
                         $classData= $platform->getUserInfo($data_all['openid']);
                         if($classData){
                         	$data['subscribe'] = $classData['subscribe'];
                         	$data['openid'] = $classData['openid'];
                         	$data['nickname'] = str_replace('\'', '', $classData['nickname']);
		                    $data['sex'] = $classData['sex'];
		                    $data['city'] = $classData['city'];
		                    $data['province'] = $classData['province'];
		                    $data['country'] = $classData['country'];
		                    $data['province'] = $classData['province'];
		                    $data['language'] = $classData['language'];
		                    $data['headimgurl'] = $classData['headimgurl'];
		                    $data['subscribe_time'] = $classData['subscribe_time'];
		                    $data['unionid'] = $classData['unionid'];
		                    $data['remark'] = $classData['remark'];
		                    $data['groupid'] = $classData['groupid'];
		                    $GLOBALS['db']->autoExecute(DB_PREFIX."weixin_user",$data,'UPDATE'," openid='".$classData['openid']."'");
                         }
                          
                    }
                    $i = $step + $i;
                     $this->showFrmSuccess((('更新中请勿关闭...进度：' . $i) . '/') . $fansCount,1 ,$i);
                } else {
                	$this->success('更新完毕',1);
                    die;
                }
            }
        } else {
            $this->error('非法操作');
        }
    }
    //批量粉丝转移
    public function setgroup()
    {
        if ($_SERVER['REQUEST_METHOD']=='GET') {
        	$ids = strim($_REQUEST['ids']);
            $wechatgroupid = explode(",",$ids);
            $to_groupid = intval($_REQUEST['to_groupid']);
            $platform = $platform= new PlatformWechat($this->option);
	   	    $platform_authorizer_token=$platform->check_platform_authorizer_token();
            unset($wechatgroupid[0]);
            $openid_list = array();
            foreach($wechatgroupid as $wk => $wv){
            	$id = intval($wv);
            	$openid=$GLOBALS['db']->getOne("select openid from ".DB_PREFIX."weixin_user where id=".$id);
             	if($openid){
             		$openid_list[] = $openid ;
             	}
//             	$thisFans = $this->sdb->table("wechat_group_list")->where("id=".$id)->getRow();
//            	$url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . $access_token;
//                $post = self::system_curl($url,"POST",((('{"openid":"' . $thisFans['openid']) . '","to_groupid":') . $to_groupid) . '}');
//        		$json = json_decode($post,true);
//        		$this->sdb->table("wechat_group_list")->where("id=".$id)->update(array('g_id' => $to_groupid));
            }
           
            if($openid_list){
            	$json=$platform->batchUpdateGroupMembers($to_groupid,$openid_list);
             	if($json){
             		 
            		$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_user",array('groupid' => $to_groupid),'UPDATE',' id in ('.$ids.') ');
            	}
            }
             $this->success('设置完毕',1);
        }
    }
    /*
     * 群发列表
     */
    public function message_send(){
    	 
		$condition = " account_id=".$this->account_id." and send_type = 0";
		$page_size = 15;
		$rs_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."weixin_send where ".$condition);
		
		if($rs_count > 0){
			$pager  = buildPage("WeixinUser/message_send",$_REQUEST,$rs_count,$this->page,$page_size);
			$this->assign("pager",$pager);
			$list =  $GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_send where ".$condition."  order by id DESC limit ".$pager['limit']);
 			foreach($list as $k=>$v){
				$list[$k]['create_time_format'] = to_date($v['create_time']); 
				if($v['send_time']!=""){
					$list[$k]['send_time_format'] = to_date($v['send_time']); 
				}
			}
			$this->assign("list",$list);
			
		}
		
		$this->assign("box_title","普通群发");
		$this->display();
    }
    
    public function message_send_del(){
    	$ids_str = strim($_REQUEST['ids']);
		$id = intval($_REQUEST['id']);
		if($ids_str != ""){
			//批量删除
			$replys = M('WeixinSend')->where(array('id'=>array('in',explode(',',$ids_str)),'account_id'=>$this->account_id))->findAll();
			foreach($replys as $reply){
				M('WeixinSend')->where(array('id'=>$reply['id'],'account_id'=>$this->account_id))->delete();
 			}
			$this->success("删除成功",$this->isajax);
		}elseif($id > 0){
			//单条删除
			$reply = M('WeixinSend')->where(array('id'=>$id,'account_id'=>$this->account_id))->find();
			if($reply){
				M('WeixinSend')->where(array('id'=>$id,'account_id'=>$this->account_id))->delete();
 			}
			$this->success("删除成功",$this->isajax);
		}else{
			$this->error("请选择要删除的选项",$this->isajax);
		}
    }
    
    /**
	 * 普通推送
	 */
	public function message_send_add(){
		$msgtype = array(
			"text"=>"文本消息",
			"image"=>"图片消息",
			"voice"=>"语音消息",
			"video"=>"视频消息",
			"music"=>"音乐消息",
			"news"=>"图文消息"
		);
		$this->assign("msgtype",$msgtype); 
		
		$id = intval($_REQUEST['id']);
		
		if($id > 0){
 			$send = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_send where id=".$id." AND account_id=".$this->account_id);
 			//$send['wechat_group']= $GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_group where account_id=".$this->account_id);		
 			
 			$this->assign("box_title","编辑群发信息");
		}
		else{
			$send['send_type'] = 0;
			$this->assign("box_title","添加群发信息");
			
		}
		$time= get_gmtime();
 		$send['user_list'] = $GLOBALS['db']->getAll("SELECT wgl.* from ".DB_PREFIX."weixin_api_get_record ar LEFT JOIN ".DB_PREFIX."weixin_user wgl ON wgl.openid = ar.openid where ar.account_id=".$this->account_id ." and ar.create_time < ".$time." AND ar.create_time > ".($time-48*3600+1)."");
  		
		$this->assign("send",$send); 
		$this->assign("user_type_id",$send['user_type_id']);
		
		//输出关联的回复
		$relate_replys = $GLOBALS['db']->getAll("select relate_id from ".DB_PREFIX."weixin_send_relate where send_id=".$send['id']);  
 		foreach($relate_replys as $k=>$v){
			
			$relate_replys[$k] = M('WeixinReply') -> where(array('id'=>$v['relate_id']))->find();
		}
		$this->assign("relate_replys",$relate_replys);
 		$this->display();
	}
	/**
	 * 保存群发
	 */
	public function message_send_save(){
		
		$id = intval($_REQUEST['id']);
		$data['msgtype'] = $_POST['msgtype'];
		if(!in_array($data['msgtype'],array("text","image","voice","video","music","news"))){
			$this->showFrmErr("不支持的类型",$this->isajax,"msgtype");
		}
		$data['title'] = $_POST['title'];
		$data['user_type'] = intval($_POST['user_type']);
		$data['user_type_id'] = intval($_POST['user_type_id']);
		$data['author'] = $GLOBALS['seller_info']['public_name'];
		$data['media_file'] = ($_POST['media_file']);
		$data['content'] = ($_POST['content']);
		$data['digest'] = ($_POST['digest']);
		$data['send_type'] = intval($_POST['send_type']);
		
		
		if($data['title'] ==""){
			$this->showFrmErr("标题不能为空",$this->isajax,"title");
		}
		
		switch ($data['msgtype']){
			case "news":
				if($data['content']==""){
					$this->showFrmErr("内容不能为空",$this->isajax,"content");
				}
				break;
		}
		
		
		switch ($data['msgtype']){
			case "image":
			case "voice":
			case "video":
			case "music":
			case "news":
				if($data['media_file']==""){
					$this->showFrmErr("媒体文件不能为空",$this->isajax,"media_file");
				}
				$file_name = pathinfo($data['media_file']);
				$file_ext = strtolower($file_name['extension']);
				switch($data['msgtype']){
					case "image":
					case "news":
						if(!in_array($file_ext,array("jpg","jpeg"))){
							$this->showFrmErr("媒体文件有误,必须为:jpg",$this->isajax,"media_file");
						}
						$fsize =  filesize(APP_ROOT_PATH.$data['media_file'])/1024;
						if($fsize > 124){
							$this->showFrmErr("媒体文件太大，只允许124KB",$this->isajax,"media_file");
						}
						break;
					case "voice":
						if(!in_array($file_ext,array("amr","mp3"))){
							$this->showFrmErr("媒体文件有误,必须为:amr/mp3",$this->isajax,"media_file");
						}
						$fsize =  filesize(APP_ROOT_PATH.$data['media_file'])/1024;
						if($fsize > 256){
							$this->showFrmErr("媒体文件太大，只允许256KB",$this->isajax,"media_file");
						}
						break;
					case "music":
						if(!in_array($file_ext,array("mp3"))){
							$this->showFrmErr("媒体文件有误,必须为:mp3",$this->isajax,"media_file");
						}
						
						break;
					case "video":
						if(!in_array($file_ext,array("mp4"))){
							$this->showFrmErr("媒体文件有误,必须为:mp4",$this->isajax,"media_file");
						}
						$fsize =  filesize(APP_ROOT_PATH.$data['media_file'])/1024;
						if($fsize > 1024){
							$this->showFrmErr("媒体文件太大，只允许1M",$this->isajax,"media_file");
						}
						break;
				}
				break;
		}
		
		
		if($data['msgtype']=="news"){
			//定义链接
			$relate_type  =1; //默认为0
			$data['url'] = trim($_REQUEST['url']);
			$data['account_id'] = $this->account_id;
			if($_REQUEST['u_module']!='')
			{
				$data['url'] = '';
			}
			if($data['url']!='')
			{
				$data['u_module'] = '';
				$data['u_action'] = '';
				$data['u_id'] = '';
				$data['u_param'] = '';
			}else{
				$data['u_id'] = intval($_REQUEST['u_id']);
				$data['u_module'] = trim($_REQUEST['u_module']);
				$data['u_action'] = trim($_REQUEST['u_action']);
				$data['u_param'] = trim($_REQUEST['u_param']);
			}
		}
  		$data['account_id'] = $this->account_id;
		 
		
		if($id >0){
			//录入到数据库
 			$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send",$data,'UPDATE','id='.$id);
			$total = 0;
			//删除旧的关联 
			$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."weixin_send_relate where send_id=".$id);
 			if($_POST['relate_reply_id']){
				foreach ($_POST['relate_reply_id'] as $k=>$vv){
					if(intval($vv) > 0 && $total < 9){
						$total++;
						$link_data = array();
						$link_data['send_id'] = $id;
						$link_data['relate_id'] = $vv;
						$link_data['sort'] = $k;
						$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send_relate",$link_data);
 					}
				}
			}
		}
		else{
			$data['create_time'] = get_gmtime();
			$rs = $GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send",$data);
			if($rs>0){
				$total = 0;
				if($_POST['relate_reply_id']){
					foreach ($_POST['relate_reply_id'] as $k=>$vv){
						if(intval($vv) > 0 && $total < 9){
							$total++;
							$link_data = array();
							$link_data['send_id'] = $rs;
							$link_data['relate_id'] = $vv;
							$link_data['sort'] = $k;
							$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send_relate",$link_data);
						}
					}
				}
			}
			else{
				$this->showFrmErr("保存失败,请检查",$this->isajax);
			}
		}
		
		if($id > 0){
			$this->showFrmSuccess("保存成功",$this->isajax);
		}
		else{
			if($data['send_type'] == 1)
				$this->success("保存成功",$this->isajax);
			else
				$this->success("保存成功",$this->isajax);
		}
		
		
	}
	/**
	 * 推送消息
	 */
	public function to_send_message(){
 		$id = intval($_REQUEST['id']);
		if($id==0){
			$this->error("数据错误",$this->isajax);
		}
		
		//获取要发送的内容
		$send = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_send where id=".$id." AND account_id=".$this->account_id) ;
		if(!$send){
			$this->error("数据错误",$this->isajax);
		}
		$platform = $platform= new PlatformWechat($this->option);
	    $platform_authorizer_token=$platform->check_platform_authorizer_token();
		//判断是否是高级群发
		//var_dump($send);exit;
		if(intval($send['send_type']) == 0){
			$sendData = array();
			$sendData['msgtype'] =  $send['msgtype'];
			//普通群发
			$data['media_file'] = '@'.APP_ROOT_PATH.$send['media_file'];
			//$data['media_file'] = '@public/attachment/201507/11/10/55a079c5ce6ff.jpg';
			
 			switch($send['msgtype']){
				case "text"://文本直接提交
					$sendData['text']['content'] = $send['content'];
					break;
				case "image"://图片消息
//					$media_info = self::uploadmedia($send,$access_token);
 					$media_info=$platform->uploadMedia($data,'image');
 					$sendData['image']['media_id'] = $media_info['media_id'];
					//上传多媒体消息
					break;
				case "voice":
					$media_info=$platform->uploadMedia($data,'voice');
					$sendData['voice']['media_id'] = $media_info['media_id'];
					break;
				case "video":
//					$media_info = self::uploadmedia($send,$access_token);
					$media_info=$platform->uploadMedia($data,'video');
					$sendData['video']['media_id'] = $media_info['media_id'];
					
					$sendData['video']['title'] = $send['title'];
					$sendData['video']['description'] = $send['content'];
					break;
				case "music":
					$sendData['music']['title'] = $send['title'];
					$sendData['music']['musicurl'] = get_domain().$send['media_file'];
					$sendData['music']['hqmusicurl'] = get_domain().$send['media_file'];
					$sendData['music']['description'] = $send['content'];
 					$data['media_file'] = '@'.APP_ROOT_PATH."./public/weixin/static/images/wap/demo/box.jpg";;
  					$media_info=$platform->uploadMedia($data,'image');
  					
					$sendData['music']['thumb_media_id'] = $media_info['media_id'];
					break;
				case "news":
					$item['title']=  $send['title']."";
					$item['description']=  $send['content']."";
 					$item["picurl"] = get_domain().$send['media_file'];
 					if($send['url'] == ''){
						//由关联数据端重新获取回复的内容（reply_news_title,reply_news_description,reply_news_picurl）
						if($send['u_module']=="")$send['u_module']="index";
						if($send['u_action']=="")$send['u_action']="index";
						$route = $send['u_module'];
						if($send['u_action']!='')$route.="#".$send['u_action'];								
						$str = "u:".$route."|".$send['u_param'];					
						$send['url']  =  get_domain().parse_url_tag_coomon($str);
					}
					$item['url'] = $send['url'];
 					$sendData['news']['articles'][] = $item;
					
					//获取关联图文数据
 					$relate_data = $GLOBALS['db']->getAll("select s.* from ".DB_PREFIX."weixin_reply s LEFT JOIN ".DB_PREFIX."weixin_send_relate sr on sr.relate_id=s.id WHERE sr.send_id=".$send['id']);
					foreach($relate_data as $kk=>$vv){
						$item = array();
						$item['title'] = $vv['reply_news_title']."";
						$item['description'] = $vv['reply_news_description']."";
						$item['url'] = $vv['reply_news_url'];
						if($item['url'] == ''){
							//由关联数据端重新获取回复的内容（reply_news_title,reply_news_description,reply_news_picurl）
							if($item['u_module']=="")$item['u_module']="index";
							if($item['u_action']=="")$item['u_action']="index";
							$route = $item['u_module'];
							if($item['u_action']!='')$route.="#".$item['u_action'];								
							$str = "u:".$route."|".$item['u_param'];					
							$item['url']  =  get_domain().parse_url_tag_coomon($str);
						}
						 	
 						$item["picurl"] = get_domain().$vv['reply_news_picurl'];
						
						$sendData['news']['articles'][] = $item;
					}
					 
 					break;
				}
			 
  			//判断是否是全部发送
			if(intval($send['user_type_id'])>0){
				//推送OPENID
 				$touser_info = $GLOBALS['db']->getRow("SELECT openid,nickname from ".DB_PREFIX."weixin_user where id=".intval($send['user_type_id'])." and account_id=".$this->account_id);
				if(!$touser_info){
					$this->success("推送失败,推送的粉丝不存在,请确认是否已经同步粉丝",$this->isajax);
				}
				$sendData['touser'] = $touser_info['openid'];
				
				$res = $platform->sendCustomMessage($sendData);
				if($res){
 					$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send",array("send_time"=>TIME_UTC,"status"=>1),'UPDATE','account_id='.$this->account_id.' and id='.$send['id']);
					$this->success("推送完成",$this->isajax);
				}
				else{
					$this->error($touser_info['nickname']." 推送失败",$this->isajax);
				}
			}
			else{
 				$send_user_list = $GLOBALS['db']->getAll("SELECT wgl.openid,wgl.nickname from ".DB_PREFIX."weixin_api_get_record ar LEFT JOIN ".DB_PREFIX."weixin_user wgl ON wgl.openid = ar.openid where ar.account_id=".$this->account_id." AND ar.create_time < ".get_gmtime()." AND ar.create_time >".(get_gmtime()-48*3600+1)."");
				$err_msg = "";
				foreach($send_user_list as $k=>$v){
					if($v['openid']!=""){
						$sendData['touser'] = $v['openid'];
						$res = $platform->sendCustomMessage($sendData);
						if($res){
							 
						}
						else{
							$err_msg .=$v['nickname']." 推送失败<br>";
						}
					}
				}
				
  			    $GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send",array("send_time"=>TIME_UTC,"status"=>1),'UPDATE','account_id='.$this->account_id.' and id='.$send['id']);
				if($err_msg=="")
					$this->success("推送完成",$this->isajax);
				else
					$this->success("推送完成<span style='color:red'>".$err_msg."</span>",$this->isajax,'',u("WeixinUser/message_send"));
			}
		}
		else{//高级群发
			if($send['msgtype']!="news"){
				$this->error("数据错误",$this->isajax);
			}
			
			$json_data['articles'] = self::formatAdvSendMsg($send);
			

 			$result = $platform->uploadArticles($json_data);
			if($result){
				$rs_data['media_id'] = $result['media_id'];
 				$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send",$rs_data,'UPDATE',' account_id='.$this->account_id.' and id='.$send['id']);
				//开始推送
  				$wechat_group= $GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_group where account_id=".$this->account_id);
				//如果是群发给所有用户的话
				$send_news_data['filter']['group_id'] = $send['user_type_id']; 
				$send_news_data['mpnews']['media_id'] = $result['media_id'];
				$send_news_data['msgtype'] = "mpnews";
				
 				$sresult = $platform->sendGroupMassMessage($send_news_data);
				if($sresult){
 					$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send",array("send_time"=>TIME_UTC,"status"=>1),'UPDATE',' account_id='.$this->account_id.' and id='.$send['id']);
 					$this->success("推送完成",$this->isajax);
				}
				else{
					$this->error("推送失败",$this->isajax);
				}
 			}
			else{
				$this->error("推送失败",$this->isajax);
			}
		}
		
	}
	
	/**
	 * 格式化模板
	 */
	private function formatAdvSendMsg($send){
		$sendData = array();
		switch($send['msgtype']){
			case "news":
 				$sendData[] = self::newsAdvItem($send);
				//获取关联图文数据
				$relate_data = $GLOBALS['db']->getAll("select s.* from ".DB_PREFIX."weixin_reply s LEFT JOIN ".DB_PREFIX."weixin_send_relate sr on sr.relate_id=s.id WHERE sr.send_id=".$send['id']);
				foreach($relate_data as $kk=>$vv){
					$item = array();
					$item['title'] = $vv['reply_news_title']."";
 					$item['digest'] = $vv['reply_news_description'];
					$item['content'] = $vv['reply_news_content'];
					$item['url'] = $vv['reply_news_url'];
					if($item['url'] == ''){
						//由关联数据端重新获取回复的内容（reply_news_title,reply_news_description,reply_news_picurl）
						if($item['u_module']=="")$item['u_module']="index";
						if($item['u_action']=="")$item['u_action']="index";
						$route = $item['u_module'];
						if($item['u_action']!='')$route.="#".$item['u_action'];								
						$str = "u:".$route."|".$item['u_param'];					
						$item['url']  =  get_domain().parse_url_tag_coomon($str);
					}
					 	
					$item["media_file"] = $vv['reply_news_picurl'];
					$sendData[] = self::newsAdvItem($item);
 				}
 				break;
		}
		
		return $sendData;
	}
	
	/**
	 * 获取高级群发节点
	 */
	private function newsAdvItem($send){
		$data['media_file'] = '@'.APP_ROOT_PATH.$send['media_file'];
		$platform = $platform= new PlatformWechat($this->option);
	   	$platform_authorizer_token=$platform->check_platform_authorizer_token();
	   
	   	//var_dump($data);exit;
		$mediainfo=$platform->uploadMedia($data,'image');
 		$item['thumb_media_id'] = $mediainfo['media_id'];
		$item['author'] = $send['author'];
		$item['title'] = $send['title'];
		
		if($send['url']){
			$data['content_source_url'] = $send['url'];
		}
		else{
			//由关联数据端重新获取回复的内容（reply_news_title,reply_news_description,reply_news_picurl）
			if($send['u_module']=="")$send['u_module']="index";
			if($send['u_action']=="")$send['u_action']="index";
			$route = $send['u_module'];
			if($send['u_action']!='')$route.="#".$send['u_action'];								
			$str = "u:".$route."|".$send['u_param'];					
			$data['content_source_url']  =  get_domain().parse_url_tag_coomon($str);
			//$data['content_source_url'] =  SITE_DOMIAN."/".url("wei:".$send['relate_data'],array("id"=>$send['relate_id']));
		}
		$item['content'] = $send['content'];
		$item['digest'] = $send['digest'];
		
		return $item;
	}
	
	public function advanced(){
		
		$condition = " account_id=".$this->account_id." and send_type = 1";
 		$page_size = 15;
 		$rs_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."weixin_send where ".$condition);
		
		if($rs_count > 0){
			$pager  = buildPage("WeixinUser/advanced",$_REQUEST,$rs_count,$this->page,$page_size);
			$this->assign("pager",$pager);
			$list =  $GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_send where ".$condition."  order by id DESC limit ".$pager['limit']);
 			foreach($list as $k=>$v){
				$list[$k]['create_time_format'] = to_date($v['create_time']); 
				if($v['send_time']!=""){
					$list[$k]['send_time_format'] = to_date($v['send_time']); 
				}
			}
			$this->assign("list",$list);
			
		}
		
 		$this->assign("box_title","高级群发");
		$this->display();
	}
	
	public function advanced_add(){
		
		
		$msgtype = array("news"=>"图文消息");
		$this->assign("msgtype",$msgtype); 
		
		$id = intval($_REQUEST['id']);
		
		if($id > 0){
 			$send = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."weixin_send where id=".$id." AND account_id=".$this->account_id);
 			//$send['wechat_group']= $GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_group where account_id=".$this->account_id);		
 			
 			$this->assign("box_title","编辑群发信息");
		}
		else{
			$send['send_type'] = 1;
			$send['msgtype'] = "news";
			$this->assign("box_title","添加群发信息");
			
		}
		$time= get_gmtime();
 
   		$send['wechat_group']= $GLOBALS['db']->getAll("select * from ".DB_PREFIX."weixin_group where  account_id=".$this->account_id); 
  		
		$this->assign("send",$send); 
		$this->assign("user_type_id",$send['user_type_id']);
		
		//输出关联的回复
		$relate_replys = $GLOBALS['db']->getAll("select relate_id from ".DB_PREFIX."weixin_send_relate where send_id=".$send['id']);  
 		foreach($relate_replys as $k=>$v){
			
			$relate_replys[$k] = M('WeixinReply') -> where(array('id'=>$v['relate_id']))->find();
		}
		$this->assign("relate_replys",$relate_replys);
 		$this->display("message_send_add");
	 
	}
	
	
	/**
	 * 保存高级群发
	 */
	public function advanced_save(){
		$id = intval($_REQUEST['id']);
		
		$data['msgtype'] = $_POST['msgtype'];
		if(!in_array($data['msgtype'],array("text","image","voice","video","music","news"))){
			$this->showFrmErr("不支持的类型",$this->isajax,"msgtype");
		}
		$data['title'] = $_POST['title'];
		$data['user_type'] = intval($_POST['user_type']);
		$data['user_type_id'] = intval($_POST['user_type_id']);
		$data['author'] = $GLOBALS['seller_info']['public_name'];
		$data['media_file'] = ($_POST['media_file']);
		$data['content'] = ($_POST['content']);
		$data['digest'] = ($_POST['digest']);
		$data['send_type'] = intval($_POST['send_type']);
		
		if($data['title'] ==""){
			$this->showFrmErr("标题不能为空",$this->isajax,"title");
		}
		
		switch ($data['msgtype']){
			case "news":
				if($data['content']==""){
					$this->showFrmErr("内容不能为空",$this->isajax,"content");
				}
				break;
		}
		switch ($data['msgtype']){
			case "image":
			case "voice":
			case "video":
			case "music":
			case "news":
				if($data['media_file']==""){
					$this->showFrmErr("媒体文件不能为空",$this->isajax,"media_file");
				}
				$file_name = pathinfo($data['media_file']);
				$file_ext = strtolower($file_name['extension']);
				switch($data['msgtype']){
					case "image":
					case "news":
						if(!in_array($file_ext,array("jpg","jpeg"))){
							$this->showFrmErr("媒体文件有误,必须为:jpg",$this->isajax,"media_file");
						}
						$fsize =  filesize(APP_ROOT_PATH.$data['media_file'])/1024;
						if($fsize > 124){
							$this->showFrmErr("媒体文件太大，只允许124KB",$this->isajax,"media_file");
						}
						break;
					case "voice":
						if(!in_array($file_ext,array("amr","mp3"))){
							$this->showFrmErr("媒体文件有误,必须为:amr/mp3",$this->isajax,"media_file");
						}
						$fsize =  filesize(APP_ROOT_PATH.$data['media_file'])/1024;
						if($fsize > 256){
							$this->showFrmErr("媒体文件太大，只允许256KB",$this->isajax,"media_file");
						}
						break;
					case "music":
						if(!in_array($file_ext,array("mp3"))){
							$this->showFrmErr("媒体文件有误,必须为:mp3",$this->isajax,"media_file");
						}
						
						break;
					case "video":
						if(!in_array($file_ext,array("mp4"))){
							$this->showFrmErr("媒体文件有误,必须为:mp4",$this->isajax,"media_file");
						}
						$fsize =  filesize(APP_ROOT_PATH.$data['media_file'])/1024;
						if($fsize > 1024){
							$this->showFrmErr("媒体文件太大，只允许1M",$this->isajax,"media_file");
						}
						break;
				}
				break;
		}
		
		if($data['msgtype']=="news"){
			//定义链接
			$relate_type  =1; //默认为0
			$data['url'] = trim($_REQUEST['url']);
			$data['account_id'] = $this->account_id;
			if($_REQUEST['u_module']!='')
			{
				$data['url'] = '';
			}
			if($data['url']!='')
			{
				$data['u_module'] = '';
				$data['u_action'] = '';
				$data['u_id'] = '';
				$data['u_param'] = '';
			}else{
				$data['u_id'] = intval($_REQUEST['u_id']);
				$data['u_module'] = trim($_REQUEST['u_module']);
				$data['u_action'] = trim($_REQUEST['u_action']);
				$data['u_param'] = trim($_REQUEST['u_param']);
			}
		}
  		$data['account_id'] = $this->account_id;
		 
		
		if($id >0){
			//录入到数据库
 			$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send",$data,'UPDATE','id='.$id);
			$total = 0;
			//删除旧的关联 
			$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."weixin_send_relate where send_id=".$id);
 			if($_POST['relate_reply_id']){
				foreach ($_POST['relate_reply_id'] as $k=>$vv){
					if(intval($vv) > 0 && $total < 9){
						$total++;
						$link_data = array();
						$link_data['send_id'] = $id;
						$link_data['relate_id'] = $vv;
						$link_data['sort'] = $k;
						$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send_relate",$link_data);
 					}
				}
			}
		}
		else{
			$data['create_time'] = get_gmtime();
			$rs = $GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send",$data);
			if($rs>0){
				$total = 0;
				if($_POST['relate_reply_id']){
					foreach ($_POST['relate_reply_id'] as $k=>$vv){
						if(intval($vv) > 0 && $total < 9){
							$total++;
							$link_data = array();
							$link_data['send_id'] = $rs;
							$link_data['relate_id'] = $vv;
							$link_data['sort'] = $k;
							$GLOBALS['db']->autoExecute(DB_PREFIX."weixin_send_relate",$link_data);
						}
					}
				}
			}
			else{
				$this->showFrmErr("保存失败,请检查",$this->isajax);
			}
		}
		
		if($id > 0){
			$this->showFrmSuccess("保存成功",$this->isajax);
		}
		else{
			if($data['send_type'] == 1)
				$this->success("保存成功",$this->isajax);
			else
				$this->success("保存成功",$this->isajax);
		}
		
		
	}
	
 }
?>