<?php
//指定父分类下子分类树状格式化后的结果
class cache_money_type_auto_cache extends auto_cache{
	public function load($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$type_list = $GLOBALS['cache']->get($key);
		if($type_list===false)
		{
			if(isset($param['class'])){
				$extW = " AND `class` = '".strim($param['class'])."' ";
			}
			$temp_type_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."money_type WHERE is_effect=1 $extW ORDER BY `sort` ASC");
			$type_list = array("100" => "全部");
			foreach($temp_type_list as $k=>$v){
				$type_list[$v['type']] = $v['name'];
			}
			
			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$type_list);
		}
		return $type_list;
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