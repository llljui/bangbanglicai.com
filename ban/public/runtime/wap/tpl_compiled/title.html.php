<?php if ($_REQUEST['app'] != '1'): ?>
<header class="bar bar-nav">
	<?php if ($this->_var['hide_back'] != 1): ?>
	<a class="button button-link button-nav pull-left" onclick="RouterBack('<?php echo $this->_var['back_url']; ?>','<?php echo $this->_var['back_page']; ?>','<?php echo $this->_var['back_epage']; ?>');" data-transition='slide-out'>
	  <span class="icon icon-left"></span>
	</a>
	<?php endif; ?>
	
	<a class="button  button-link button-nav  pull-right open-panel  nobar" data-panel='#panel-left-box'>
		 <i class="icon iconfont">&#xe650;</i>
	</a>
	<?php if ($this->_var['view_search'] == 1): ?>
	<a class="button  button-link pull-right search_icon_size" href="#" onclick="RouterURL('<?php echo $this->_var['search_url']; ?>','#<?php echo $this->_var['search_act']; ?>');">
		<i class="icon iconfont">&#xe614;</i>
	</a>
	<?php endif; ?>
	<h1 class="title pull-left"><?php echo $this->_var['data']['program_title']; ?></h1>
</header>
<?php if ($this->_var['hide_footer'] != 1): ?>
<nav class="bar bar-tab">
	<a class="tab-item <?php if ($this->_var['data']['act'] == 'init'): ?>active<?php endif; ?>" href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|init|"."".""); 
?>','#init');">
	  <i class="icon iconfont">&#xe63f;</i>
	  <span class="tab-label">首页</span>
	</a>
	<a class="tab-item <?php if ($this->_var['data']['act'] == 'deals'): ?>active<?php endif; ?>" href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|deals|"."".""); 
?>','#deals')">
	  <i class="icon iconfont">&#xe63b;</i>
	  <span class="tab-label">投资</span>
	</a>
	<a class="tab-item <?php if ($this->_var['data']['act'] == 'transfer'): ?>active<?php endif; ?>" href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|transfer|"."".""); 
?>','#transfer')">
	  <i class="icon iconfont">&#xe642;</i>
	  <span class="tab-label">债权</span>
	</a>
	<?php if ($this->_var['is_login'] == 0): ?>
		<a class="tab-item <?php if ($this->_var['data']['act'] == 'login'): ?>active<?php endif; ?>" href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|login|"."".""); 
?>','#login'<?php if ($this->_var['is_weixin']): ?>,1<?php endif; ?>);">
			<i class="icon iconfont">&#xe640;</i>
			<span class="tab-label">登录</span>
		</a>
	<?php else: ?>
	<a class="tab-item <?php if ($this->_var['data']['act'] == 'uc_center'): ?>active<?php endif; ?>" href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_center|"."".""); 
?>','#uc_center');">
		<i class="icon iconfont">&#xe640;</i>
		<span class="tab-label">我的账户</span>
	</a>
   <?php endif; ?>
</nav>
<?php endif; ?>
<?php endif; ?>
