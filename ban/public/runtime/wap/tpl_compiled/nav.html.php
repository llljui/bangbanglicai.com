<ul class="w_b big_classification">
	<li class="w_b_f_1 tc">
		<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|deals|"."epage=".$this->_var['data']['act']."".""); 
?>','#deals'<?php if ($this->_var['is_weixin'] && $this->_var['is_login'] == 0): ?>,1<?php endif; ?>)">
			<p class="icon_parent"><i class="icon iconfont ffa200">&#xe63c;</i></p>
			<p class="font">我要投资</p>
		
		<div class="my_bor"></div></a>
	</li>
	<li class="w_b_f_1 tc">
		<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|transfer|"."epage=".$this->_var['data']['act']."".""); 
?>','#transfer'<?php if ($this->_var['is_weixin'] && $this->_var['is_login'] == 0): ?>,1<?php endif; ?>)">
			<p class="icon_parent"><i class="icon iconfont fe8f61">&#xe619;</i></p>
			<p class="font">债权转让</p>
		
		<div class="my_bor"></div></a>
	</li>
	<li class="w_b_f_1 tc">
		<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|article_list|"."epage=".$this->_var['data']['act']."".""); 
?>','#article_list'<?php if ($this->_var['is_weixin'] && $this->_var['is_login'] == 0): ?>,1<?php else: ?>,2<?php endif; ?>)">
            <p class="icon_parent"><i class="icon iconfont c58c881">&#xe639;</i></p>
			<p class="font">展示文章</p>
		
		<div class="my_bor"></div></a>
	</li>
	<li class="w_b_f_1 tc">
		<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|integral_mall|"."epage=".$this->_var['data']['act']."".""); 
?>','#integral_mall'<?php if ($this->_var['is_weixin'] && $this->_var['is_login'] == 0): ?>,1<?php else: ?>,2<?php endif; ?>)">
			<p class="icon_parent"><i class="icon iconfont c80bace">&#xe641;</i></p>
			<p class="font">积分商城</p>
		
		<div class="my_bor"></div></a>
	</li>
</ul>	
			
			