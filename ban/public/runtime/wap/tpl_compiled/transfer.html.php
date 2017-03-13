<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php
$this->_var['hide_back'] = 1;
?>
<?php echo $this->fetch('./inc/header.html'); ?>	
<div class="page" id='<?php echo $this->_var['data']['act']; ?>'>
<?php echo $this->fetch('./inc/title.html'); ?>
<div class="content infinite-scroll pull-to-refresh-content" data-distance="<?php echo $this->_var['data']['rs_count']; ?>"  all_page="<?php echo $this->_var['data']['page']['page_total']; ?>" ajaxurl="<?php
echo parse_wap_url_tag("u:index|transfer#index|"."".""); 
?>">

<!--债权转让列表-->
<ul class=" transfer recommended_nav">
	<?php endif; ?>
	<!-- 默认的下拉刷新层 -->
    <div class="pull-to-refresh-layer" all_page="<?php echo $this->_var['data']['page']['page_total']; ?>" >
        <div class="preloader"></div>
        <div class="pull-to-refresh-arrow"></div>
    </div>	
 <?php $_from = $this->_var['data']['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
		<li onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|transfer_show|"."id=".$this->_var['item']['id']."&deal_id=".$this->_var['item']['deal_id']."".""); 
?>','#transfer_show',2);">
				
					<div class="title_set bb1">
						<span class="state c666666"><?php echo $this->_var['item']['user']['user_name']; ?></span>
						<span class="name"><?php echo $this->_var['item']['name']; ?></span>
					</div>
					<dl class="w_b detail">
						<dd class="w_b_f_1 tc">
							<p class="con ea544a"><?php echo $this->_var['item']['left_benjin_format']; ?></p>
							<p class="name">剩余本金</p>
						</dd>
						<dd class="w_b_f_1 tc">
							<p class="con c666666"><?php echo $this->_var['item']['left_lixi_format']; ?></p>
							<p class="name">剩余利息</p>
						</dd>
						<dd class="w_b_f_1 tc">
							<p class="con c666666"><?php echo $this->_var['item']['transfer_amount_format']; ?></p>
							<p class="name">转让价</p>
						</dd>
						<dd class="w_b_f_1 tc">
							<p class="con c666666"><?php echo $this->_var['item']['rate']; ?>%</p>
							<p class="name">利率</p>
						</dd>
					</dl>
					<div class="title_set top_bor w_b ">
						<span class="c666666 tf w_b_f_1">
							<?php if ($this->_var['item']['t_user_id'] > 0): ?>
								已转让
							<?php else: ?>
								<?php if ($this->_var['item']['status'] == 0): ?>
									已撤销
								<?php else: ?>
								<?php echo $this->_var['item']['remain_time_format']; ?>
								<?php if ($this->_var['item']['remain_time'] < 0): ?>
								  	逾期还款
								<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
							</span>
							<span class="c666666 tr w_b_f_1">承接人：
							<?php if ($this->_var['item']['t_user_id'] > 0): ?><?php echo $this->_var['item']['tuser']['user_name']; ?><?php else: ?>无<?php endif; ?></span>
							
					</div>
				
			</li>
	 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
	 
	 <?php if ($_REQUEST['is_ajax'] != 1): ?>  
	</ul>
<!-- 加载提示符 -->
<div class="infinite-scroll-preloader">
  <div class="preloader">
  </div>
</div>
<?php echo $this->fetch('./inc/footer.html'); ?>
<?php endif; ?>








