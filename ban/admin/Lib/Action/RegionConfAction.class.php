<?php
class RegionConfAction extends CommonAction{
	public function index()
	{
		$this->updateRegionJS();
	}
	
	private function updateRegionJS()
	{
		$jsStr = "var regionConf = ".$this->getRegionJS();
		$path = get_real_path()."system/region.js";
		@file_put_contents($path,$jsStr);
	}
	
	private function getRegionJS($pid=-1)
	{
		$jsStr = "";
		if($pid>=0){
			$map["pid"] = $pid;
		}
		else{
			$map["region_level"] = array("in","1,2");
		}
		$childRegionList = M("RegionConf")->where($map)->order("id asc")->findAll();
		
		foreach($childRegionList as $childRegion)
		{
			if(empty($jsStr))
				$jsStr .= "{";
			else
				$jsStr .= ",";
				
			$childStr = $this->getRegionJS($childRegion['id']);
			$jsStr .= "\"r$childRegion[id]\":{\"i\":$childRegion[id],\"n\":\"$childRegion[name]\",\"c\":$childStr}";
		}
		
		if(!empty($jsStr))
			$jsStr .= "}";
		else
			$jsStr .= "\"\"";
				
		return $jsStr;
	}
}
?>