<?php
//配资参数
class peizi_conf_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$peizi_conf = $GLOBALS['cache']->get($key);
		if($peizi_conf === false||true)
		{
			$id = intval($param['id']);
			
			$peizi_conf = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."peizi_conf where id = ".$id." limit 1");
			
			//0:天天;1:周;2:月
			if ($peizi_conf['type'] == 2){
				
				$levers = explode(',',$peizi_conf['money_list']);
				$month_list = explode(',',$peizi_conf['day_month_list']);
				
				$peizi_conf['list_lever'] = $levers;
				$peizi_conf['min_lever'] = $levers[0];
				$peizi_conf['max_lever'] = $levers[count($levers) - 1];
				
				$peizi_conf['month_list'] = $month_list;
				$peizi_conf['min_month'] = $month_list[0];
				$peizi_conf['max_month'] = $month_list[count($month_list) - 1];
				
			}else{
				$money_array = array();
				$money_list = explode(',',$peizi_conf['money_list']);
					
				for($index=0;$index<count($money_list);$index++)
				{
				$money = $money_list[$index];
				$money_array[] = array('id'=>$index,'money'=>$money_list[$index],'money_format'=>$this->format_money2($money));
				}
				$peizi_conf['money_array'] = $money_array;
				
				
				$day_list = explode(',',$peizi_conf['day_month_list']);
				$peizi_conf['day_list'] = $day_list;
				$peizi_conf['min_day'] = $day_list[0];
				$peizi_conf['max_day'] = $day_list[count($day_list) - 1];
			}

			
			

			$peizi_conf['min_money_format'] = $this->format_money($peizi_conf['min_money']);
			$peizi_conf['max_money_format'] = $this->format_money($peizi_conf['max_money']);
			
			
			$peizi_conf['money_limit_info'] = '最少'.$peizi_conf['min_money_format'].',最多'.$peizi_conf['max_money_format'];
			
			if ($peizi_conf['type'] == 2){
				$peizi_conf['money_limit_info'] = '请输入投资本金,'.$peizi_conf['money_limit_info'];
			}
			
			$sql = "select title from ".DB_PREFIX."contract where id = ".intval($peizi_conf['contract_id']);
			$peizi_conf['contract_title'] = $GLOBALS['db']->getOne($sql);
			
			
			
			
			$lever_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."peizi_conf_lever_list where pid = ".intval($peizi_conf['id'])." order by min_money asc");	
			foreach($lever_list as $k=>$v)
			{
				$lever_array = array();
				$list = explode(',',$v['lever_list']);
				for($index=0; $index < count($list); $index++)
				{
					$lever_array[] = array('id'=>$index,'lever'=>$list[$index]);
				}
				
				$lever_list[$k]['lever_array'] = $lever_array;
			}
					
			
			$peizi_conf['lever_list'] = $lever_list;
			
			$lever_coefficient_list = $GLOBALS['db']->getAll("select lm.*,(select type from ".DB_PREFIX."peizi_conf pc where pc.id = lm.pid) as type  from ".DB_PREFIX."peizi_conf_lever_coefficient_list lm where lm.pid = ".intval($peizi_conf['id'])." order by lm.lever asc");
			
			$peizi_conf['lever_coefficient_list'] = $lever_coefficient_list;
			
			
			$rate_list = $GLOBALS['db']->getAll("select lm.*,(select type from ".DB_PREFIX."peizi_conf pc where pc.id = lm.pid) as type  from ".DB_PREFIX."peizi_conf_rate_list lm where lm.pid = ".intval($peizi_conf['id'])." order by lm.min_month, lm.min_money asc");
				
			$peizi_conf['rate_list'] = $rate_list;			
			
			
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$peizi_conf,500);
		}
		return $peizi_conf;
	}
	
	function  format_money($money){
		
		if ($money >= 10000){
			$money = ($money / 10000).'万';
		}else if ($money >= 1000){
			$money = ($money / 1000).'千';
		}else{
			$money = $money.'元';
		} 
		
		return $money;
	}
	
	function  format_money2($money){
	
		if ($money >= 10000){
			$money = ($money / 10000).'万';
		}
	
		return $money;
	}
		
	public function rm($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$GLOBALS['cache']->rm($key);
	}
	
	public function clear_all()
	{
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$GLOBALS['cache']->clear();
	}
	
}
?>