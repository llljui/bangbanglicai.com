<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php echo $this->fetch('./inc/header.html'); ?>	
<?php
	$this->_var['back_url'] = wap_url("index","init#index");
	$this->_var['back_page'] = "#init";
	$this->_var['back_epage'] = $_REQUEST['epage']=="" ? "#init" : "#".$_REQUEST['epage'];
?>
<div class="page" id='<?php echo $this->_var['data']['act']; ?>'>
<?php echo $this->fetch('./inc/title.html'); ?>
<div class="content register_login_content">
<?php endif; ?>	
<!-- 这里是页面内容区 -->
<div class="register_login">
	<form>
		<div class="bg_fff register_login_pd">
			<div class="bb1 bor height w_b">
				 <div class="name">账 户</div>
				 <input class="r_l_input w_b_f_1" id="email" type="text" placeholder="请输入用户名/邮箱/手机"/>
			</div>
			<div class="bb1 bor height w_b">
				 <div class="name">密 码</div>
				 <input class="r_l_input w_b_f_1" type="password" id="pwd" placeholder="请输入您的密码" />
			</div>
		</div>
		<div class="register_login_pd">
			<input class="ui-button_login r_l_but" type="button" value="登录账户"/>
			<div class="clearfix">
				<a  class="f_l    ea544a"    href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|save_reset_pwd|"."".""); 
?>','#save_reset_pwd',2)">忘记密码？</a>
				<a  class="f_r c56b1ea"    href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|register|"."".""); 
?>','#register',2)"">注册账号</a>
			</div>
		</div>	
	</form>
</div>
<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php echo $this->fetch('./inc/footer.html'); ?>
<?php endif; ?>







