<?php
class ExportXlsAction extends CommonAction{
    function index(){
        $begin_time = strim($_REQUEST['begin_time'],"Y-m-d");
        if($begin_time)
            $begin_time_f = to_timespan($begin_time,"Y-m-d");
       
        $end_time = strim($_REQUEST['end_time'],"Y-m-d");
        if($end_time)
            $end_time_f = to_timespan($end_time,"Y-m-d");
        $type = intval($_REQUEST['type']);
        $list = array();
        switch($type){
           case 1 :
               $bf_loads = array();
               $bf_accounts = array();
               $bf_locks = array();
               $n_incharge = array();
               $n_repay_self = array();
               
               $n_carry = array();
               
               $n_loads = array();
               $n_locks = array();
               
               //前期余额  - 投资总额
               if($begin_time){
                   $tmploads =  $GLOBALS['db']->getAll("SELECT user_id,sum(money) as load_money from ".DB_PREFIX."deal_load WHERE create_time < ".$begin_time_f." GROUP BY user_id  ");
                   
                   foreach($tmploads as $k=>$v){
                       $bf_loads[$v['user_id']] = $v['load_money'];
                   }
                   
                   //前期余额  - 账户余额
                   $tempaccounts = $GLOBALS['db']->getAll("select * from (SELECT id,user_id,account_money from ".DB_PREFIX."user_money_log WHERE create_time < ".$begin_time_f." order by id DESC) as b group by user_id ");
                   foreach($tempaccounts as $k=>$v){
                       $bf_accounts[$v['user_id']] = $v['account_money'];
                   }
                   
                   //前期余额  - 冻结
                   $templocks = $GLOBALS['db']->getAll("select * from (SELECT id,user_id,account_lock_money from ".DB_PREFIX."user_lock_money_log WHERE type=2 and create_time < ".$begin_time_f." order by id DESC) as b group by user_id ");
                   
                   foreach($templocks as $k=>$v){
                       $bf_locks[$v['user_id']] = $v['account_lock_money'];
                   }
                  
               }
               
               $extW = "";
               if($begin_time){
                   $extW .= " AND create_time > ".$begin_time_f;
               }
               
               if($end_time){
                   $extW .= " AND create_time < ".($end_time_f + 24 *3600 - 1);
               }
               //
               
               $tmpaf_account_money = $GLOBALS['db']->getAll("select * from (SELECT id,user_id,account_money from ".DB_PREFIX."user_money_log WHERE 1=1 ".$extW." order by id DESC) as b group by user_id ");
               foreach($tmpaf_account_money as $k=>$v){
                   $af_account_money[$v['user_id']] = $v['account_money'];
               }
              
				//充值
               $tempincharge = $GLOBALS['db']->getAll("SELECT user_id,sum(money)  as incharge_money from ".DB_PREFIX."payment_notice WHERE is_paid=1 ".$extW." GROUP BY user_id  ");
               foreach($tempincharge as $k=>$v){
                   $n_incharge[$v['user_id']] = $v['incharge_money'];
               }
               //提现
               $tmpcarry = $GLOBALS['db']->getAll("SELECT user_id,sum(money)  as carry_money,sum(fee) as carry_fee from ".DB_PREFIX."user_carry WHERE status=1 ".$extW." GROUP BY user_id  ");
               foreach($tmpcarry as $k=>$v){
                 $n_carry[$v['user_id']]['carry_money'] = $v['carry_money'];
                 $n_carry[$v['user_id']]['carry_fee'] = $v['carry_fee'];
               }
               
               //投资
               $temploads = $GLOBALS['db']->getAll("SELECT dl.user_id,dl.money,e.money as ecv_money from ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."ecv e ON e.id = dl.ecv_id WHERE dl.is_repay=0 and dl.is_has_loans = 1 ".$extW."  ");
               foreach($temploads as $k=>$v){
                   if($v['money'] - $v['ecv_money'] > 0){
                       $n_loads[$v['user_id']] += $v['money'] - $v['ecv_money'];
                   }
               }
               //冻结
               $temploads = $GLOBALS['db']->getAll("SELECT dl.user_id,dl.money,e.money as ecv_money from ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."ecv e ON e.id = dl.ecv_id WHERE dl.is_repay=0 and dl.is_has_loans = 0 ".$extW." ");
               foreach($temploads as $k=>$v){
                   if($v['money'] - $v['ecv_money'] > 0){
                       $n_locks[$v['user_id']] += $v['money'] - $v['ecv_money'];
                   }
               }
               
               //本金返还  - 利息收入
               $extW = "";
               if($begin_time){
                   $extW .= " AND true_repay_time > ".$begin_time_f;
               }
                
               if($end_time){
                   $extW .= " AND true_repay_time < ".($end_time_f + 24 *3600 - 1);
               }
               $temprepay_self = $GLOBALS['db']->getAll("SELECT user_id,sum(true_self_money)  as total_self_money,sum(true_interest_money) as total_interest_money from ".DB_PREFIX."deal_load_repay WHERE 1=1 ".$extW." GROUP BY user_id  ");
               foreach($temprepay_self as $k=>$v){
                 $n_repay_self[$v['user_id']]['self_money'] = $v['total_self_money'];
                 $n_repay_self[$v['user_id']]['interest'] = $v['total_interest_money'];
               }
                   
                $sql="SELECT u.*,AES_DECRYPT(u.real_name_encrypt,'".AES_DECRYPT_KEY."') as real_name,
                    l.name as level_name,rf.user_name as ref_user_name,
                    AES_DECRYPT(rf.real_name_encrypt,'".AES_DECRYPT_KEY."') as ref_real_name
                  from ".DB_PREFIX."user u 
                  LEFT JOIN ".DB_PREFIX."user_level l ON l.id = u.level_id 
                  LEFT JOIN ".DB_PREFIX."user rf ON rf.id = u.pid ";
                
                  $list = $GLOBALS['db']->getAll($sql);
                  $rs_count = count($list);
                  foreach($list as $k=>$v){
                      
                      $list[$k]['bf_load_money'] = 0;
                      $list[$k]['bf_account_money'] = 0;
                      $list[$k]['bf_lock_money'] = 0;
                      $list[$k]['incharge'] = 0;
                      if($begin_time){
                          $list[$k]['bf_load_money'] = floatval($bf_loads[$v['id']]);
                          $list[$k]['bf_account_money'] = floatval($bf_accounts[$v['id']]);
                          $list[$k]['bf_lock_money'] = floatval($bf_locks[$v['id']]);
                      }

                      $list[$k]['bf_all_money'] = floatval($list[$k]['bf_load_money'] + $list[$k]['bf_account_money'] + $list[$k]['bf_lock_money']);
                  
                      $list[$k]['incharge'] = floatval($n_incharge[$v['id']]);
                      
                      $list[$k]['self_money'] = floatval($n_repay_self[$v['id']]['self_money']);
                      
                      $list[$k]['interest'] = floatval($n_repay_self[$v['id']]['interest']);
                      
                      $list[$k]['carry_money'] = floatval($n_carry[$v['id']]['carry_money']);
                      
                      $list[$k]['carry_fee'] = floatval($n_carry[$v['id']]['carry_fee']);
                      
                      $list[$k]['load_money'] = floatval($n_loads[$v['id']]);
                      $list[$k]['lock_money'] = floatval($n_locks[$v['id']]);
                      
                      $list[$k]['all_loads'] = $list[$k]['bf_load_money'] + $list[$k]['load_money'] ;
                      
                      
                      //$list[$k]["all_account_money"] = $list[$k]['incharge'] + $list[$k]['self_money'] + $list[$k]['interest'] - $list[$k]['carry_money'] - $list[$k]['carry_fee'] - $list[$k]['load_money'];
                      $list[$k]["all_account_money"] = floatval($af_account_money[$v['id']]);
                      
                      $list[$k]["all_lock_money"] = $list[$k]['lock_money'];
                      
                      $list[$k]["all_money"] = $list[$k]["all_lock_money"] + $list[$k]["all_account_money"] + $list[$k]['all_loads'];
                      
                      $list[$rs_count]['id'] = "";
                      $list[$rs_count]['user_name'] = "合计";
                      $list[$rs_count]['bf_load_money'] += $list[$k]['bf_load_money'];
                      $list[$rs_count]['bf_account_money'] += $list[$k]['bf_account_money'];
                      $list[$rs_count]['bf_lock_money'] += $list[$k]['bf_lock_money'];
                      $list[$rs_count]['incharge'] += $list[$k]['incharge'];
                      $list[$rs_count]['bf_all_money'] += $list[$k]['bf_all_money'];
                      $list[$rs_count]['self_money'] += $list[$k]['self_money'];
                      $list[$rs_count]['interest'] += $list[$k]['interest'];
                      $list[$rs_count]['carry_money'] += $list[$k]['carry_money'];
                      $list[$rs_count]['carry_fee'] += $list[$k]['carry_fee'];
                      $list[$rs_count]['load_money'] += $list[$k]['load_money'];
                      $list[$rs_count]['lock_money'] += $list[$k]['lock_money'];
                      $list[$rs_count]['all_loads'] += $list[$k]['all_loads'];
                      $list[$rs_count]['all_account_money'] += $list[$k]['all_account_money'];
                      $list[$rs_count]['all_lock_money'] += $list[$k]['all_lock_money'];
                      $list[$rs_count]['all_money'] += $list[$k]['all_money'];
                  }

                  
                 
               break;
           case 2 :
                $extW = "";
               if($begin_time){
                   $extW .= " AND d.start_time > ".$begin_time_f;
               }
                
               if($end_time){
                   $extW .= " AND d.start_time < ".($end_time_f + 24 *3600 - 1);
               }
               
               //获取还款列表
               $tmprepays = $GLOBALS['db']->getAll("SELECT dlr.deal_id,sum(dlr.interest_money) as yq_interest_money,sum(dlr.true_interest_money + dlr.impose_money) as sj_interest_money,sum(dlr.interest_money - dlr.true_interest_money) as left_interest_money,sum(dlr.self_money) as total_self_money , sum(dlr.true_self_money) as total_true_self_money FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dlr.deal_id WHERE d.is_effect=1 ".$extW." GROUP by dlr.deal_id");
               
               $repays = array();
               foreach($tmprepays as $k=>$v){
                   $repays[$v['deal_id']] = $v;
               }
               
               $sql = "SELECT d.*,u.user_name,AES_DECRYPT(u.real_name_encrypt,'".AES_DECRYPT_KEY."') as real_name FROM ".DB_PREFIX."deal d LEFT JOIN ".DB_PREFIX."user u ON u.id = d.user_id WHERE d.is_effect=1 AND d.deal_status in(4,5) ".$extW;
               $list = $GLOBALS['db']->getAll($sql);
               $rs_count =  count($list);
               foreach($list as $k=>$v){
                   $list[$k]['yq_interest_money'] = $repays[$v['id']]['yq_interest_money'];
                   $list[$k]['sj_interest_money'] = $repays[$v['id']]['sj_interest_money'];
                   if($v['deal_status']==4){
                        $list[$k]['left_interest_money'] = $repays[$v['id']]['left_interest_money'];
                        $list[$k]['left_self_money'] = $repays[$v['id']]['total_self_money'] - $repays[$v['id']]['total_true_self_money'];
                   }
                   
                   
                  $list[$rs_count]['id'] = "本期合计";
                  $list[$rs_count]['load_money'] +=  floatval($list[$k]['load_money']);
                  $list[$rs_count]['yq_interest_money'] +=  floatval($list[$k]['yq_interest_money']);
                  $list[$rs_count]['sj_interest_money'] +=  floatval($list[$k]['sj_interest_money']);
                  $list[$rs_count]['left_interest_money'] +=  floatval($list[$k]['left_interest_money']);
                  $list[$rs_count]['left_self_money'] +=  floatval($list[$k]['left_self_money']);
               }
              
               break;
           case 3 :
               $extW = "";
               if($begin_time){
                   $extW .= " AND create_time > ".$begin_time_f;
               }
               
               if($end_time){
                   $extW .= " AND create_time < ".($end_time_f + 24 *3600 - 1);
               }
               $site_repay = $GLOBALS['db']->getAll("SELECT sum(money) as total_money,`type` FROM ".DB_PREFIX."site_money_log WHERE 1=1 ".$extW." GROUP BY type ");
               foreach($site_repay as $k=>$v){
                   $list[$v['type']] = $v['total_money'];
               }
               //红包奖励
               $extW = "";
               if($begin_time){
                   $extW .= " AND d.create_time > ".$begin_time_f;
               }
                
               if($end_time){
                   $extW .= " AND d.create_time < ".($end_time_f + 24 *3600 - 1);
               }
               $list['red'] =  $GLOBALS['db']->getOne("select sum(IF(d.money > e.money , e.money,d.money) ) FROM ".DB_PREFIX."deal_load d LEFT JOIN ".DB_PREFIX."ecv e ON e.id = d.ecv_id  where d.is_repay = 0 and d.ecv_id > 0 ".$extW);
               $left_money = 0;
               foreach($list as $k=>$v){
                   if($k <> 26 && $k<>13)
                        $left_money += $v;
               }
               $this->assign("left_money",$left_money);
               break;
       }
       
       $this->assign("type",$type);
       $this->assign("list",$list);
       $this->display();
    }
    
    function loads(){
        $id = intval($_REQUEST['id']);
        if($id==0){
            $this->error("参数错误");
        }
        
        $deal = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal where id=".$id);
        if(!$deal){
            $this->error("标不存");
        }
        
        $repays = array();
        $temprepays = $GLOBALS['db']->getAll("SELECT user_id,sum(interest_money) as yq_interest_money,sum(true_interest_money) as sj_interest_money,sum(self_money) as total_self_money,sum(true_self_money) as total_true_self_money,CONCAT(user_id,'_',u_key) as uk FROM ".DB_PREFIX."deal_load_repay where deal_id=".$id." GROUP BY CONCAT(user_id,'_',u_key) ");
        foreach($temprepays as $k=>$v){
            $repays[$v['uk']] = $v;
        }
        
        $loans =  $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_load where deal_id=".$id." ORDER BY id ASC ");
        $rs_count = count($loans);
        foreach($loans as $k=>$v){
            $loans[$k]['yq_interest_money'] = $repays[$v['user_id']."_".$k]['yq_interest_money'];
            $loans[$k]['sj_interest_money'] = $repays[$v['user_id']."_".$k]['sj_interest_money'];
            $loans[$k]['interest_money'] = 0;
            $loans[$k]['interest_money1'] = 0;
            $loans[$k]['self_money'] = 0;
            if($deal['deal_status']==4){
                $loans[$k]['interest_money1'] = $loans[$k]['interest_money'] = $loans[$k]['yq_interest_money'] - $loans[$k]['sj_interest_money'];
                $loans[$k]['self_money'] = $loans[$k]['total_self_money'] - $loans[$k]['total_true_self_money'];
            }
            
            $loans[$rs_count]['user_id'] ="本期合计";
            $loans[$rs_count]['yq_interest_money'] +=$loans[$k]['yq_interest_money'];
            $loans[$rs_count]['sj_interest_money'] +=$loans[$k]['sj_interest_money'];
            $loans[$rs_count]['interest_money1'] +=floatval($loans[$k]['interest_money1']);
            $loans[$rs_count]['self_money'] +=floatval($loans[$k]['self_money']);
        }
        
        $this->assign("deal",$deal);
        $this->assign("list",$loans);
        
        $this->display();
        
    }
    
    function export_loads_csv(){
        $id = intval($_REQUEST['id']);
        if($id==0){
            $this->error("参数错误");
        }
        
        $deal = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."deal where id=".$id);
        if(!$deal){
            $this->error("标不存");
        }
        
        $xlsCell = array(
            array("id","编号"),
            array("deal_user_name","借款人"),
            array("deal_name","标的名称"),
            array("user_name","投资人名称"),
            array("load_money","投资金额"),
            array("yq_interest_money","预期收益"),
            array("sj_interest_money","收益金额"),
            array("interest_money","未收益金额"),
            array("repay_time","借款期限"),
            array("self_money","本金"),
            array("interest_money1","利息"),
        );
        
        $repays = array();
        $temprepays = $GLOBALS['db']->getAll("SELECT user_id,sum(interest_money) as yq_interest_money,sum(true_interest_money) as sj_interest_money,sum(self_money) as total_self_money,sum(true_self_money) as total_true_self_money,CONCAT(user_id,'_',u_key) as uk FROM ".DB_PREFIX."deal_load_repay where deal_id=".$id." GROUP BY CONCAT(user_id,'_',u_key) ");
        foreach($temprepays as $k=>$v){
            $repays[$v['uk']] = $v;
        }
        
        $loans =  $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_load where deal_id=".$id." ORDER BY id ASC ");
        $rs_count=count($loans);
        foreach($loans as $k=>$v){
            $xlsData[$k]['id'] = $deal['id'];
            $xlsData[$k]['deal_user_name'] = get_user_name_reals($deal['user_id']);
            $xlsData[$k]['deal_name'] = $deal['name'];
            $xlsData[$k]['user_name'] = get_user_name_reals($v['user_id']); 
            $xlsData[$k]['load_money'] = $v['money'];
            $xlsData[$k]['yq_interest_money'] = $repays[$v['user_id']."_".$k]['yq_interest_money'];
            $xlsData[$k]['sj_interest_money'] = $repays[$v['user_id']."_".$k]['sj_interest_money'];
            $xlsData[$k]['interest_money'] = 0;
            $xlsData[$k]['repay_time'] = $deal['repay_time']. ($deal['repay_time_type'] == 1 ? "月" : "天") ;
            $xlsData[$k]['self_money'] = 0;
            $xlsData[$k]['interest_money1'] = 0;
            if($deal['deal_status']==4){
                $xlsData[$k]['interest_money1'] = $xlsData[$k]['interest_money'] = $xlsData[$k]['yq_interest_money'] - $xlsData[$k]['sj_interest_money'];
                $xlsData[$k]['self_money'] = $xlsData[$k]['total_self_money'] - $xlsData[$k]['total_true_self_money'];
            }
            
            $xlsData[$rs_count]['id'] = $deal['id'];
            $xlsData[$rs_count]['deal_user_name'] = "";
            $xlsData[$rs_count]['user_name'] ="本期合计";
            $xlsData[$rs_count]['load_money'] += $v['money'];
            $xlsData[$rs_count]['yq_interest_money'] +=$xlsData[$k]['yq_interest_money'];
            $xlsData[$rs_count]['sj_interest_money'] +=$xlsData[$k]['sj_interest_money'];
            $xlsData[$rs_count]['interest_money'] +=floatval($xlsData[$k]['interest_money']);
            $xlsData[$rs_count]['interest_money1'] +=floatval($xlsData[$k]['interest_money1']);
            $xlsData[$rs_count]['self_money'] +=floatval($xlsData[$k]['self_money']);
        }
        
        $xlsName  = $deal['name']."的投资人明细表";
        $this->exportExcel($xlsName,$xlsCell,$xlsData,4);
    }
    
    function export_csv(){
        $type = intval($_REQUEST['type']);
        
        $actionname =  "export_csv_".$type;
        if($type > 0){
            $this->$actionname();
        }
    }
    
    function export_csv_1($page=1){
        $begin_time = strim($_REQUEST['begin_time'],"Y-m-d");
        if($begin_time)
            $begin_time_f = to_timespan($begin_time,"Y-m-d");
        
        $end_time = strim($_REQUEST['end_time'],"Y-m-d");
        if($end_time)
            $end_time_f = to_timespan($end_time,"Y-m-d");
        
        set_time_limit(0);
        $limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
        
        $xlsName  = "会员收支统计表";
        
         $xlsCell  = array(
            array('user_id','编号'),
            array('user_name','会员'),
            array('bf_load_money','投资总额'),
			array('bf_account_money','账户余额'),
			array('bf_lock_money','投资冻结'),
			array('bf_all_money','资金总额'),
			array('incharge','充值'),
			array('self_money','本金返还'),
			array('interest','利息收入'),
			array('carry_money','提现'),
			array('load_money','当期投资'),
			array('lock_money','投资冻结'),
			array('carry_fee','手续费'),
			array('all_loads','投资总额'),
			array('all_account_money','账户余额'),
			array('all_lock_money','投资冻结'),
			array('all_money','资金总额'),
			array('point','信用'),
			array('quota','额度'),
			array('level_name','等级'),
			array('ref_user_name','邀请人'),
			array('status','状态'),
			array('is_black','黑名单'),
			array('is_three','第三方'),
			
        );
       
       $bf_loads = array();
       $bf_accounts = array();
       $bf_locks = array();
       $n_incharge = array();
       $n_repay_self = array();
       
       $n_carry = array();
       
       $n_loads = array();
       $n_locks = array();
       
       //前期余额  - 投资总额
       if($begin_time){
           $tmploads =  $GLOBALS['db']->getAll("SELECT user_id,sum(money) as load_money from ".DB_PREFIX."deal_load WHERE create_time < ".$begin_time_f." GROUP BY user_id  ");
           
           foreach($tmploads as $k=>$v){
               $bf_loads[$v['user_id']] = $v['load_money'];
           }
           
           //前期余额  - 账户余额
           $tempaccounts = $GLOBALS['db']->getAll("select * from (SELECT id,user_id,account_money from ".DB_PREFIX."user_money_log WHERE create_time < ".$begin_time_f." order by id DESC) as b group by user_id ");
           foreach($tempaccounts as $k=>$v){
               $bf_accounts[$v['user_id']] = $v['account_money'];
           }
           
           //前期余额  - 冻结
           $templocks = $GLOBALS['db']->getAll("select * from (SELECT id,user_id,account_lock_money from ".DB_PREFIX."user_lock_money_log WHERE type=2 and create_time < ".$begin_time_f." order by id DESC) as b group by user_id ");
           
           foreach($templocks as $k=>$v){
               $bf_locks[$v['user_id']] = $v['account_lock_money'];
           }
          
       }
       
       $extW = "";
       if($begin_time){
           $extW .= " AND create_time > ".$begin_time_f;
       }
       
       if($end_time){
           $extW .= " AND create_time < ".($end_time_f + 24 *3600 - 1);
       }
       
       $tmpaf_account_money = $GLOBALS['db']->getAll("select * from (SELECT id,user_id,account_money from ".DB_PREFIX."user_money_log WHERE 1=1 ".$extW." order by id DESC) as b group by user_id ");
       foreach($tmpaf_account_money as $k=>$v){
           $af_account_money[$v['user_id']] = $v['account_money'];
       }
       
       //充值
       $tempincharge = $GLOBALS['db']->getAll("SELECT user_id,sum(money)  as incharge_money from ".DB_PREFIX."payment_notice WHERE is_paid=1 ".$extW." GROUP BY user_id  ");
       foreach($tempincharge as $k=>$v){
           $n_incharge[$v['user_id']] = $v['incharge_money'];
       }
       //提现
       $tmpcarry = $GLOBALS['db']->getAll("SELECT user_id,sum(money)  as carry_money,sum(fee) as carry_fee from ".DB_PREFIX."user_carry WHERE status=1 ".$extW." GROUP BY user_id  ");
       foreach($tmpcarry as $k=>$v){
         $n_carry[$v['user_id']]['carry_money'] = $v['carry_money'];
         $n_carry[$v['user_id']]['carry_fee'] = $v['carry_fee'];
       }
       
       //投资
       $temploads = $GLOBALS['db']->getAll("SELECT dl.user_id,dl.money,e.money as ecv_money from ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."ecv e ON e.id = dl.ecv_id WHERE dl.is_repay=0 and dl.is_has_loans = 1 ".$extW."  ");
       foreach($temploads as $k=>$v){
           if($v['money'] - $v['ecv_money'] > 0){
               $n_loads[$v['user_id']] += $v['money'] - $v['ecv_money'];
           }
       }
       //冻结
       $temploads = $GLOBALS['db']->getAll("SELECT dl.user_id,dl.money,e.money as ecv_money from ".DB_PREFIX."deal_load dl LEFT JOIN ".DB_PREFIX."ecv e ON e.id = dl.ecv_id WHERE dl.is_repay=0 and dl.is_has_loans = 0 ".$extW." ");
       foreach($temploads as $k=>$v){
           if($v['money'] - $v['ecv_money'] > 0){
               $n_locks[$v['user_id']] += $v['money'] - $v['ecv_money'];
           }
       }
       
       //本金返还  - 利息收入
       $extW = "";
       if($begin_time){
           $extW .= " AND true_repay_time > ".$begin_time_f;
       }
        
       if($end_time){
           $extW .= " AND true_repay_time < ".($end_time_f + 24 *3600 - 1);
       }
       $temprepay_self = $GLOBALS['db']->getAll("SELECT user_id,sum(true_self_money)  as total_self_money,sum(true_interest_money) as total_interest_money from ".DB_PREFIX."deal_load_repay WHERE 1=1 ".$extW." GROUP BY user_id  ");
       foreach($temprepay_self as $k=>$v){
         $n_repay_self[$v['user_id']]['self_money'] = $v['total_self_money'];
         $n_repay_self[$v['user_id']]['interest'] = $v['total_interest_money'];
       }
           
    $sql="SELECT u.*,AES_DECRYPT(u.real_name_encrypt,'".AES_DECRYPT_KEY."') as real_name,
        l.name as level_name,rf.user_name as ref_user_name,
        AES_DECRYPT(rf.real_name_encrypt,'".AES_DECRYPT_KEY."') as ref_real_name
      from ".DB_PREFIX."user u 
      LEFT JOIN ".DB_PREFIX."user_level l ON l.id = u.level_id 
      LEFT JOIN ".DB_PREFIX."user rf ON rf.id = u.pid ";
    
      $list = $GLOBALS['db']->getAll($sql);
     $rs_count= count($list);
      foreach($list as $k=>$v){
          $xlsData[$k]['user_id'] = $v['id'];
          $xlsData[$k]['user_name'] = $v['user_name'] .($v['real_name']=="" ? "" : "[".$v['real_name']."]");
          $xlsData[$k]['bf_load_money'] = 0;
          $xlsData[$k]['bf_account_money'] = 0;
          $xlsData[$k]['bf_lock_money'] = 0;
          $xlsData[$k]['incharge'] = 0;
          if($begin_time){
              $xlsData[$k]['bf_load_money'] = floatval($bf_loads[$v['id']]);
              $xlsData[$k]['bf_account_money'] = floatval($bf_accounts[$v['id']]);
              $xlsData[$k]['bf_lock_money'] = floatval($bf_locks[$v['id']]);
          }

          $xlsData[$k]['bf_all_money'] = floatval($xlsData[$k]['bf_load_money'] + $xlsData[$k]['bf_account_money'] + $xlsData[$k]['bf_lock_money']);
      
          $xlsData[$k]['incharge'] = floatval($n_incharge[$v['id']]);
          
          $xlsData[$k]['self_money'] = floatval($n_repay_self[$v['id']]['self_money']);
          
          $xlsData[$k]['interest'] = floatval($n_repay_self[$v['id']]['interest']);
          
          $xlsData[$k]['carry_money'] = floatval($n_carry[$v['id']]['carry_money']);
          
          $xlsData[$k]['carry_fee'] = floatval($n_carry[$v['id']]['carry_fee']);
          
          $xlsData[$k]['load_money'] = floatval($n_loads[$v['id']]);
          $xlsData[$k]['lock_money'] = floatval($n_locks[$v['id']]);
          
          $xlsData[$k]['all_loads'] = $xlsData[$k]['bf_load_money'] + $xlsData[$k]['load_money'] ;
          
          
          //$xlsData[$k]["all_account_money"] = $xlsData[$k]['incharge'] + $xlsData[$k]['self_money'] + $xlsData[$k]['interest'] - $xlsData[$k]['carry_money'] - $xlsData[$k]['carry_fee'] - $xlsData[$k]['load_money'];

          $xlsData[$k]["all_account_money"] = floatval($af_account_money[$v['id']]);
          
          $xlsData[$k]["all_lock_money"] = $xlsData[$k]['lock_money'];
          
          $xlsData[$k]["all_money"] = $xlsData[$k]["all_lock_money"] + $xlsData[$k]["all_account_money"] + $xlsData[$k]['all_loads'];
          
          $xlsData[$k]["point"] = $v["point"] ;
          
          $xlsData[$k]["quota"] = $v["quota"] ;
          
          $xlsData[$k]["level_name"] = $v["level_name"] ;
          
          $xlsData[$k]["ref_user_name"] = $v["ref_user_name"] .($v['ref_real_name']=="" ? "" : "[".$v['ref_real_name']."]");
          
          $xlsData[$k]["status"] = $v['is_effect']== 1? "有效" : "无效";
          
          $xlsData[$k]["is_black"] = $v['is_black']== 1? "是" : "否";
          
          $xlsData[$k]["is_three"] = $v['ips_acct_no']!= ""? "是" : "否";
          
          
          $xlsData[$rs_count]['id'] = "";
          $xlsData[$rs_count]['user_name'] = "合计";
          $xlsData[$rs_count]['bf_load_money'] += $xlsData[$k]['bf_load_money'];
          $xlsData[$rs_count]['bf_account_money'] += $xlsData[$k]['bf_account_money'];
          $xlsData[$rs_count]['bf_lock_money'] += $xlsData[$k]['bf_lock_money'];
          $xlsData[$rs_count]['incharge'] += $xlsData[$k]['incharge'];
          $xlsData[$rs_count]['bf_all_money'] += $xlsData[$k]['bf_all_money'];
          $xlsData[$rs_count]['self_money'] += $xlsData[$k]['self_money'];
          $xlsData[$rs_count]['interest'] += $xlsData[$k]['interest'];
          $xlsData[$rs_count]['carry_money'] += $xlsData[$k]['carry_money'];
          $xlsData[$rs_count]['carry_fee'] += $xlsData[$k]['carry_fee'];
          $xlsData[$rs_count]['load_money'] += $xlsData[$k]['load_money'];
          $xlsData[$rs_count]['lock_money'] += $xlsData[$k]['lock_money'];
          $xlsData[$rs_count]['all_loads'] += $xlsData[$k]['all_loads'];
          $xlsData[$rs_count]['all_account_money'] += $xlsData[$k]['all_account_money'];
          $xlsData[$rs_count]['all_lock_money'] += $xlsData[$k]['all_lock_money'];
          $xlsData[$rs_count]['all_money'] += $xlsData[$k]['all_money'];
                 
        }
         
        

        $this->exportExcel($xlsName,$xlsCell,$xlsData,1);
        
        
    }
    
    function export_csv_2($page=1){
        $begin_time = strim($_REQUEST['begin_time'],"Y-m-d");
        if($begin_time)
            $begin_time_f = to_timespan($begin_time,"Y-m-d");
        
        $end_time = strim($_REQUEST['end_time'],"Y-m-d");
        if($end_time)
            $end_time_f = to_timespan($end_time,"Y-m-d");
        set_time_limit(0);
        $xlsName  = "标的借款明细表";
        
        $extW = "";
       if($begin_time){
           $extW .= " AND d.start_time > ".$begin_time_f;
       }
        
       if($end_time){
           $extW .= " AND d.start_time < ".($end_time_f + 24 *3600 - 1);
       }
       
       //获取还款列表
       $tmprepays = $GLOBALS['db']->getAll("SELECT dlr.deal_id,sum(dlr.interest_money) as yq_interest_money,sum(dlr.true_interest_money + dlr.impose_money) as sj_interest_money,sum(dlr.interest_money - dlr.true_interest_money) as left_interest_money,sum(dlr.self_money) as total_self_money , sum(dlr.true_self_money) as total_true_self_money FROM ".DB_PREFIX."deal_load_repay dlr LEFT JOIN ".DB_PREFIX."deal d ON d.id=dlr.deal_id WHERE d.is_effect=1 ".$extW." GROUP by dlr.deal_id");
       
       $repays = array();
       foreach($tmprepays as $k=>$v){
           $repays[$v['deal_id']] = $v;
       }
       
       $xlsCell  = array(
            array('id','编号'),
            array('user_name','借款人'),
            array('name','标的名称'),
            array('load_money','投资金额'),
            array('yq_interest_money','预期收益'),
            array('sj_interest_money','收益金额'),
            array('left_interest_money','未收益金额'),
            array('repay_time','借款期限'),
            array('left_self_money','本金'),
            array('left_interest_money1','利息'),
        );
       
       $sql = "SELECT d.*,u.user_name,AES_DECRYPT(u.real_name_encrypt,'".AES_DECRYPT_KEY."') as real_name FROM ".DB_PREFIX."deal d LEFT JOIN ".DB_PREFIX."user u ON u.id = d.user_id WHERE d.is_effect=1 AND d.deal_status in(4,5) ".$extW;
       $list = $GLOBALS['db']->getAll($sql);
       $rs_count= count($list);
       foreach($list as $k=>$v){
            $xlsData[$k]['id'] = $v['id'];
            $xlsData[$k]['user_name'] = $v['user_name'] .($v['real_name']=="" ? "" : "[".$v['real_name']."]");
            $xlsData[$k]['name'] = $v['name'];
            $xlsData[$k]['load_money'] = $v['load_money'];
            $xlsData[$k]['yq_interest_money'] = $repays[$v['id']]['yq_interest_money'];
            $xlsData[$k]['sj_interest_money'] = $repays[$v['id']]['sj_interest_money'];
            $xlsData[$k]['left_interest_money'] = 0;
            $xlsData[$k]['repay_time'] = $v['repay_time']. ($v['repay_time_type'] == 1 ? "月" : "天") ;
            $xlsData[$k]['left_self_money'] = 0;
            $xlsData[$k]['left_interest_money1'] = 0;
           if($v['deal_status']==4){
               
                $xlsData[$k]['left_interest_money1'] = $xlsData[$k]['left_interest_money'] = $repays[$v['id']]['left_interest_money'];
                $xlsData[$k]['left_self_money'] = $repays[$v['id']]['total_self_money'] - $repays[$v['id']]['total_true_self_money'];
                
           }
           
          $xlsData[$rs_count]['id'] = "本期合计";
          $xlsData[$rs_count]['load_money'] +=  $xlsData[$k]['load_money'];
          $xlsData[$rs_count]['yq_interest_money'] +=  $xlsData[$k]['yq_interest_money'];
          $xlsData[$rs_count]['sj_interest_money'] +=  $xlsData[$k]['sj_interest_money'];
          $xlsData[$rs_count]['left_interest_money'] +=  $xlsData[$k]['left_interest_money'];
          $xlsData[$rs_count]['left_self_money'] +=  $xlsData[$k]['left_self_money'];
          $xlsData[$rs_count]['left_interest_money1'] +=  $xlsData[$k]['left_interest_money1'];
       }
      
        
        $this->exportExcel($xlsName,$xlsCell,$xlsData,2);
        
    }
    
    function export_csv_3($page=1){
        $begin_time = strim($_REQUEST['begin_time'],"Y-m-d");
        if($begin_time)
            $begin_time_f = to_timespan($begin_time,"Y-m-d");
        
        $end_time = strim($_REQUEST['end_time'],"Y-m-d");
        if($end_time)
            $end_time_f = to_timespan($end_time,"Y-m-d");
        
        set_time_limit(0);
        
        $xlsName = "平台收支表";
        $xlsCell = array(
            array("type_1","充值手续费"),
            array("type_9","提现手续费"),
            array("type_10","借款管理费"),
            array("type_12","逾期管理费"),
            array("type_14","借款服务费"),
            array("type_17","债权转让管理费"),
            array("type_20","投标管理费"),
            array("type_26","逾期罚金（垫付后）"),
            array("type_13","人工操作"),
            array("type_18","开户奖励"),
            array("type_22","兑换"),
            array("type_23","邀请返利"),
            array("type_24","投标返利"),
            array("type_25","签到成功"),
            array("type_28","投资奖励"),
            array("type_29","非体现金"),
            array("type_red","红包奖励"),
            array("type_27","其他费用"),
            array("left_money","结余"),
        );
        
        $extW = "";
        if($begin_time){
            $extW .= " AND create_time > ".$begin_time_f;
        }
         
        if($end_time){
            $extW .= " AND create_time < ".($end_time_f + 24 *3600 - 1);
        }
        $list = $GLOBALS['db']->getAll("SELECT sum(money) as total_money,`type` FROM ".DB_PREFIX."site_money_log WHERE 1=1 ".$extW." GROUP BY type ");
        foreach($list as $k=>$v){
            $xlsData['type_'.$v['type']] = floatval($v['total_money']);
        }
        //红包奖励
        $extW = "";
        if($begin_time){
            $extW .= " AND d.create_time > ".$begin_time_f;
        }
        
        if($end_time){
            $extW .= " AND d.create_time < ".($end_time_f + 24 *3600 - 1);
        }
        $xlsData['type_red'] =  (float)$GLOBALS['db']->getOne("select sum(IF(d.money > e.money , e.money,d.money) ) FROM ".DB_PREFIX."deal_load d LEFT JOIN ".DB_PREFIX."ecv e ON e.id = d.ecv_id  where d.is_repay = 0 and d.ecv_id > 0 ".$extW);
        $left_money = 0;
        foreach($list as $k=>$v){
            if($v['type'] <> 26 && $v['type']<>13)
                $xlsData['left_money'] += floatval($v['total_money']);
        }
        
        
        $this->exportExcel($xlsName,$xlsCell,$xlsData,3);
    }
    
    /**
     +----------------------------------------------------------
     * Export Excel | 2013.08.23
     * Author:HongPing <hongping626@qq.com>
     +----------------------------------------------------------
     * @param $expTitle     string File name
     +----------------------------------------------------------
     * @param $expCellName  array  Column name
     +----------------------------------------------------------
     * @param $expTableData array  Table data
     +----------------------------------------------------------
      $xlsName  = "User";
        $xlsCell  = array(
            array('id','账号序列'),
            array('account','登录账户'),
            array('nickname','账户昵称')
        );
        $xlsModel = M('Post');
        $xlsData  = $xlsModel->Field('id,account,nickname')->select();
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
     */
    public function exportExcel($expTitle,$expCellName,$expTableData,$type){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel");
        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        if($type==1){
            $objPHPExcel->getActiveSheet(0)->mergeCells('A1:X1');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "会员收支统计表");
            $objPHPExcel->getActiveSheet(0)->getStyle('A1')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
             );
            
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('A2:X2');//合并单元格
            $begin_time = strim($_REQUEST['begin_time']);
            $end_time = strim($_REQUEST['end_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "日期：".$begin_time."至".$end_time);  
            
            $objPHPExcel->getActiveSheet(0)->getStyle('A2')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
             );
            
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('A3:A4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', "编号");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('B3:B4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', "会员");
            
            //============================
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('C3:F3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', "前期余额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('C4:C4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C4', "投资总额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('D4:D4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D4', "账户余额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('E4:E4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E4', "投资冻结");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('F4:F4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F4', "资金总额");
            
            //============================
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('G3:I3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G3', "当期收入");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('G4:G4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G4', "充值");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('H4:H4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H4', "本金返还");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('I4:I4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I4', "利息收入");
            
            //============================
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('J3:M3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J3', "当期支出");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('J4:J4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J4', "提现");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('K4:K4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K4', "当期投资");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('L4:L4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L4', "投资冻结");
        
            $objPHPExcel->getActiveSheet(0)->mergeCells('M4:M4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M4', "手续费");
            
            
            //============================
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('N3:Q3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N3', "期末余额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('N4:N4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N4', "投资总额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('O4:O4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O4', "账户余额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('P4:P4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P4', "投资冻结");
        
            $objPHPExcel->getActiveSheet(0)->mergeCells('Q4:Q4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q4', "资金总额");
            
            
            //============================
            
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('R4:R4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R4', "信用");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('S4:S4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S4', "额度");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('T4:T4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T4', "等级");
        
            $objPHPExcel->getActiveSheet(0)->mergeCells('U4:U4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('U4', "推荐人");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('V4:V4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('V4', "状态");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('W4:W4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('W4', "黑名单");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('X4:X4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('X4', "第三方");
            
            
            for($i=0;$i<$dataNum;$i++){
              for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+5), " ".$expTableData[$i][$expCellName[$j][0]]);
              }             
            }  
                
        }
        elseif($type==2){
            $objPHPExcel->getActiveSheet(0)->mergeCells('A1:K1');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "标的投资明细表");
            $objPHPExcel->getActiveSheet(0)->getStyle('A1')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
             );
            
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('A2:K2');//合并单元格
            $begin_time = strim($_REQUEST['begin_time']);
            $end_time = strim($_REQUEST['end_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "日期：".$begin_time."至".$end_time);  
            
            $objPHPExcel->getActiveSheet(0)->getStyle('A2')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
             );
             
            $objPHPExcel->getActiveSheet(0)->mergeCells('A3:A4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', "编号");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('B3:B4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', "借款人");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('C3:C4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', "标的名称");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('D3:D4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', "投资金额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('E3:E4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E3', "预期收益");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('F3:F4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F3', "收益金额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('G3:G4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G3', "未收益金额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('H3:H4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', "借款期限");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('I3:J3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I3', "未还款金额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('I4:I4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I4', "本金");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('J4:J4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J4', "利息");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('K3:K4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K3', "备注");
            
            for($i=0;$i<$dataNum;$i++){
              for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+5), " ".$expTableData[$i][$expCellName[$j][0]]);
              }             
            } 
           
        }
        elseif($type==3){
            $objPHPExcel->getActiveSheet(0)->mergeCells('A1:X1');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "平台收支表");
            $objPHPExcel->getActiveSheet(0)->getStyle('A1')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );
            
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('A2:X2');//合并单元格
            $begin_time = strim($_REQUEST['begin_time']);
            $end_time = strim($_REQUEST['end_time']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "日期：".$begin_time."至".$end_time);
            $objPHPExcel->getActiveSheet(0)->getStyle('A2')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('A3:F3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', "收入");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', "充值手续费");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B4', "提现手续费");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C4', "借款管理费");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D4', "逾期管理费");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E4', "借款服务费");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F4', "债权转让管理费");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G4', "投标管理费");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G4', "逾期罚金（垫付后）");

            $objPHPExcel->getActiveSheet(0)->mergeCells('I3:R3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I3', "支出");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I4', "人工操作");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J4', "开户奖励");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K4', "兑换");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L4', "邀请返利");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M4', "投标返利");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N4', "签到成功");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O4', "投资奖励");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P4', "非提现金奖励");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q4', "红包奖励");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R4', "其他费用");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('S3:S4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S3', "结余");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('T3:T4');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T3', "备注");
            
            $j=0;
            foreach($expCellName as $k=>$v){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j]."5", " ".(float)$expTableData[$v[0]]);
                $j++;
            }
        }
        elseif($type==4){
            $objPHPExcel->getActiveSheet(0)->mergeCells('A1:L1');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "标的投资人明细表");
            $objPHPExcel->getActiveSheet(0)->getStyle('A1')->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
             );
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('A2:A3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "编号");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('B2:B3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', "借款人");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('C2:C3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', "标的名称");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('D2:D3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', "投资人名称");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('E2:E3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', "投资金额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('F2:F3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', "预期收益");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('G2:G3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', "收益金额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('H2:H3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', "未收益金额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('I2:I3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', "借款期限");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('J2:K2');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J2', "未还款金额");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('J3:J3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J3', "本金");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('K3:K3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K3', "利息");
            
            $objPHPExcel->getActiveSheet(0)->mergeCells('L2:L3');//合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L2', "备注");
            
            
            for($i=0;$i<$dataNum;$i++){
                if($i>0)
                {
                    for($j=3;$j<$cellNum;$j++){
                        $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+4), " ".$expTableData[$i][$expCellName[$j][0]]);
                    }
                }
                else{
                    $objPHPExcel->getActiveSheet(0)->mergeCells('A4:A'.($dataNum + 3));//合并单元格
                    $objPHPExcel->getActiveSheet(0)->getStyle('A4')->applyFromArray(
                        array(
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            )
                        )
                    );
                    
                    $objPHPExcel->getActiveSheet(0)->setCellValue("A4", $expTableData[$i][$expCellName[0][0]]);
                    
                    
                    $objPHPExcel->getActiveSheet(0)->mergeCells('B4:B'.($dataNum + 3));//合并单元格
                    $objPHPExcel->getActiveSheet(0)->getStyle('B4')->applyFromArray(
                        array(
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            )
                        )
                    );
                    
                    $objPHPExcel->getActiveSheet(0)->setCellValue("B4", $expTableData[$i][$expCellName[1][0]]);
                    
                   
                    
                    $objPHPExcel->getActiveSheet(0)->mergeCells('C4:C'.($dataNum + 3));//合并单元格
                    $objPHPExcel->getActiveSheet(0)->getStyle('C4')->applyFromArray(
                        array(
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            )
                        )
                    );
                    
                    
                    $objPHPExcel->getActiveSheet(0)->setCellValue("C4", $expTableData[$i][$expCellName[2][0]]);
                    
                    for($j=3;$j<$cellNum;$j++){
                        $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j]."4", " ".$expTableData[$i][$expCellName[$j][0]]);
                    }
                    
                }
            }
        }
        
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
    }
     
    /**
     +----------------------------------------------------------
     * Import Excel | 2013.08.23
     * Author:HongPing <hongping626@qq.com>
     +----------------------------------------------------------
     * @param  $file   upload file $_FILES
     +----------------------------------------------------------
     * @return array   array("error","message")
     +----------------------------------------------------------     
     */   
    public function importExecl($file){ 
        if(!file_exists($file)){ 
            return array("error"=>0,'message'=>'file not found!');
        } 
        Vendor("PHPExcel.IOFactory"); 
        $objReader = PHPExcel_IOFactory::createReader('Excel5'); 
        try{
            $PHPReader = $objReader->load($file);
        }catch(Exception $e){}
        if(!isset($PHPReader)) return array("error"=>0,'message'=>'read error!');
        $allWorksheets = $PHPReader->getAllSheets();
        $i = 0;
        foreach($allWorksheets as $objWorksheet){
            $sheetname=$objWorksheet->getTitle();
            $allRow = $objWorksheet->getHighestRow();//how many rows
            $highestColumn = $objWorksheet->getHighestColumn();//how many columns
            $allColumn = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $array[$i]["Title"] = $sheetname; 
            $array[$i]["Cols"] = $allColumn; 
            $array[$i]["Rows"] = $allRow; 
            $arr = array();
            $isMergeCell = array();
            foreach ($objWorksheet->getMergeCells() as $cells) {//merge cells
                foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
                    $isMergeCell[$cellReference] = true;
                }
            }
            for($currentRow = 1 ;$currentRow<=$allRow;$currentRow++){ 
                $row = array(); 
                for($currentColumn=0;$currentColumn<$allColumn;$currentColumn++){;                
                    $cell =$objWorksheet->getCellByColumnAndRow($currentColumn, $currentRow);
                    $afCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn+1);
                    $bfCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn-1);
                    $col = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
                    $address = $col.$currentRow;
                    $value = $objWorksheet->getCell($address)->getValue();
                    if(substr($value,0,1)=='='){
                        return array("error"=>0,'message'=>'can not use the formula!');
                        exit;
                    }
                    if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
                        $cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();
                        $formatcode=$cellstyleformat->getFormatCode();
                        if (preg_match('/^([$[A-Z]*-[0-9A-F]*])*[hmsdy]/i', $formatcode)) {
                            $value=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));
                        }else{
                            $value=PHPExcel_Style_NumberFormat::toFormattedString($value,$formatcode);
                        }                
                    }
                    if($isMergeCell[$col.$currentRow]&&$isMergeCell[$afCol.$currentRow]&&!empty($value)){
                        $temp = $value;
                    }elseif($isMergeCell[$col.$currentRow]&&$isMergeCell[$col.($currentRow-1)]&&empty($value)){
                        $value=$arr[$currentRow-1][$currentColumn];
                    }elseif($isMergeCell[$col.$currentRow]&&$isMergeCell[$bfCol.$currentRow]&&empty($value)){
                        $value=$temp;
                    }
                    $row[$currentColumn] = $value; 
                } 
                $arr[$currentRow] = $row; 
            } 
            $array[$i]["Content"] = $arr; 
            $i++;
        } 
        spl_autoload_register(array('Think','autoload'));//must, resolve ThinkPHP and PHPExcel conflicts
        unset($objWorksheet); 
        unset($PHPReader); 
        unset($PHPExcel); 
        unlink($file); 
        return array("error"=>1,"data"=>$array); 
    }
}
?>