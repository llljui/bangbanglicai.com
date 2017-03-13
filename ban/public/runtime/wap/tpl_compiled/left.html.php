<?php if ($_REQUEST['hasleftpanel'] != 1): ?>
<div class="panel-overlay"></div>
<!-- Left Panel with Reveal effect -->
<div class="panel panel-left panel-cover theme-fw " id='panel-left-box'>
  <div class="content-block">
  	<img 	src="<?php echo $this->_var['TMPL']; ?>/images/left_bg.png" class="bg_img"/>
	<?php if ($this->_var['is_login'] == 1): ?>
	<div class="rela_user">
		<div class="user_pic">
			<img src="<?php 
$k = array (
  'name' => 'wap_user_avatar',
  'uid' => $this->_var['data']['user_id'],
);
echo $k['name']($k['uid']);
?>" style="width:2.4rem;height:2.4rem;"/>
		</div>
		<div class="use_name tc">您好，<?php echo $this->_var['data']['user_name']; ?></div>
		<div class="w_b user_but">
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_center|"."epage=".$this->_var['data']['act']."".""); 
?>','#uc_center');" class="w_b_f_1 tc"><i class="icon iconfont">&#xe60b;</i>&nbsp;&nbsp;&nbsp;账户</a>
			<a href="#" data-url="<?php
echo parse_wap_url_tag("u:index|login_out|"."epage=".$this->_var['data']['act']."".""); 
?>" onclick="loginout();" id="login-out-btn"  class="w_b_f_1 tc"><i class="icon iconfont">&#xe60d;</i>&nbsp;&nbsp;&nbsp;退出</a>
		 </div>
	</div>
	<?php else: ?>
	<div class="rela_user">
		<div class="user_pic">
			<img src="<?php echo $this->_var['TMPL']; ?>/images/login_out.png" width="100%"/>
		</div>
		<div class="use_name tc">您好，您还未登录</div>
		<div class="w_b user_but">
			<a  class="w_b_f_1  tc"  href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|login|"."epage=".$this->_var['data']['act']."".""); 
?>','#login'<?php if ($this->_var['is_weixin']): ?>,1<?php else: ?>,2<?php endif; ?>);" ><i class="icon iconfont">&#xe60b;</i>&nbsp;&nbsp;&nbsp;登录</a>
			<a  class="w_b_f_1  tc"  href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|register|"."epage=".$this->_var['data']['act']."".""); 
?>','#register'<?php if ($this->_var['is_weixin']): ?>,1<?php else: ?>,2<?php endif; ?>);" ><i class="icon iconfont">&#xe60f;</i>&nbsp;&nbsp;&nbsp;注册</a>
		</div>
	</div>
	<?php endif; ?>
  </div>
<div class="new_list_block">
	<dl>
		<dd >
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|init|"."epage=".$this->_var['data']['act']."".""); 
?>','#init');">
				<div class="i_father"><i class="icon iconfont">&#xe608;</i></div>
				<span>首页</span>
			</a>
		</dd>
		<dd>
			    <?php if ($this->_var['is_login'] == 1): ?>
                <a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_invest|"."epage=".$this->_var['data']['act']."".""); 
?>','#uc_invest',2);">
                <?php else: ?>
                <a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|login|"."epage=".$this->_var['data']['act']."".""); 
?>','#login'<?php if ($this->_var['is_weixin']): ?>,1<?php endif; ?>);">
                <?php endif; ?>   
				    <div class="i_father"><i class="icon iconfont">&#xe61f;</i></div>
				    <span>我的投资</span>
			    </a>
		</dd>
<!--		<dd>
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|ewswin|"."epage=".$this->_var['data']['act']."".""); 
?>','#ewswin',2);">
				<div class="i_father"><i class="icon iconfont">&#xe607;</i></div>
				<span>股票配资</span>
			</a>
		</dd>-->
		<dd>
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|licai_deals|"."epage=".$this->_var['data']['act']."".""); 
?>','#licai_deals',2);">
				<div class="i_father"><i class="icon iconfont">&#xe60c;</i></div>
				<span>理财列表</span>
			</a>
		</dd>
		<dd>
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|transfer|"."epage=".$this->_var['data']['act']."".""); 
?>','#transfer',2);">
				<div class="i_father"><i class="icon iconfont">&#xe61e;</i></div>
				<span>债权转让</span>
			</a>
		</dd>
		<dd>
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|learn_activity|"."epage=".$this->_var['data']['act']."".""); 
?>','#learn_activity',2);">
				<div class="i_father"><i class="icon iconfont">&#xe621;</i></div>
				<span>体验区</span>
			</a>
		</dd>
		<dd>
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|integral_mall|"."epage=".$this->_var['data']['act']."".""); 
?>','#integral_mall',2);">
				<div class="i_father"><i class="icon iconfont">&#xe603;</i></div>
				<span>积分商城</span>
			</a>
		</dd>
		<dd>
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|article_list|"."epage=".$this->_var['data']['act']."".""); 
?>','#article_list',2);">
				<div class="i_father"><i class="icon iconfont">&#xe602;</i></div>
				<span>信息披露</span>
			</a>
		</dd>
		<dd>
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|article_list|"."epage=".$this->_var['data']['act']."".""); 
?>','#article_list',2);">
				<div class="i_father"><i class="icon iconfont">&#xe60e;</i></div>
				<span>帮助中心</span>
			</a>
		</dd>
	</dl>
</div> 
<?php endif; ?>
