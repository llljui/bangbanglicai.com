<?php
//配资推广参数
class peizi_invite_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$peizi_invite = $GLOBALS['cache']->get($key);
		if($peizi_invite === false||true)
		{
			$user_id = intval($param['user_id']);//用户ID
			$type = intval($param['type']);//推荐人类型; 0:投资人;1:借款人
			
			$user = $GLOBALS['db']->getRow("select total_invite_borrow_money,total_invite_invest_money from ".DB_PREFIX."user where id = ".$user_id." limit 1");
			if ($type == 0)
				$money = floatval($user['total_invite_invest_money']);
			else
				$money = floatval($user['total_invite_borrow_money']);
			
			$sql = "select * from ".DB_PREFIX."peizi_invite where type = ".$type." and min_money <= ".$money. " and max_money >= ".$money;
			
			$peizi_invite = $GLOBALS['db']->getRow($sql);
			
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$peizi_invite,500);
		}
		return $peizi_invite;
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