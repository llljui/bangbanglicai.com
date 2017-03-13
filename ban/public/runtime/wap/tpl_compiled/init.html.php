<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php echo $this->fetch('./inc/header.html'); ?>	
<?php
$this->_var['hide_back'] = 1;
?>
<div class="page" id='<?php echo $this->_var['data']['act']; ?>'>
<?php echo $this->fetch('./inc/title.html'); ?>
<div class="content pull-to-refresh-content">
<?php endif; ?>
<!-- 这里是页面内容区 -->

<div class="pull-to-refresh-layer">
    <div class="preloader"></div>
    <div class="pull-to-refresh-arrow"></div>
</div>
<!--网站主页-->
    <div class="swiper-container" data-space-between='10'>
	    <div class="swiper-wrapper">
	    	<?php $_from = $this->_var['data']['index_list']['adv_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'adv');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['adv']):
?>
	    	<div class="swiper-slide"><a href="#" onclick="RouterURL('<?php echo $this->_var['adv']['data']; ?>','#adv_<?php echo $this->_var['adv']['open_url_type']; ?>',1);" style="background-image:url('<?php echo $this->_var['adv']['img']; ?>')"></a></div>
	    	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	    </div>
	    <?php if (count ( $this->_var['data']['index_list']['adv_list'] ) > 1): ?>
	    <div class="swiper-pagination"></div>
	    <?php endif; ?>
	 </div>	
		<?php echo $this->fetch('./inc/nav.html'); ?>
		
		<div class="blank055"></div>
		<?php if ($this->_var['data']['index_list']['rec_deal_list']): ?>		
		<ul class="recommended_nav_2">
				<?php $_from = $this->_var['data']['index_list']['rec_deal_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'deal');if (count($_from)):
    foreach ($_from AS $this->_var['deal']):
?> 
			<li class="clearfix">
				<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|deal|"."id=".$this->_var['deal']['id']."".""); 
?>','#deal',2);">
					<div class="novice f_l">
						<?php if ($this->_var['deal']['is_advance'] == 1 && $this->_var['deal']['start_time'] > TIME_UTC): ?>
						<img src="<?php echo $this->_var['TMPL']; ?>/images/novice_0.png" width="100%" height="100%"/>
						<?php elseif ($this->_var['deal']['is_new'] == 1): ?>
						<img src="<?php echo $this->_var['TMPL']; ?>/images/novice.png" width="100%" height="100%"/>
						<?php endif; ?>
					</div>
					<div class="title_nav bb1">
						<?php if ($this->_var['deal']['is_wait'] == 1): ?><span class="state  c666666">
			            	<?php else: ?>
							<?php if ($this->_var['deal']['deal_status'] == 0): ?><span class="state  c666666"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] > 0): ?><span class="state  c56b1ea"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] <= 0): ?><span class="state  c666666"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 2): ?><span class="state  ea544a"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 3): ?><span class="state  c666666"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 4): ?><span class="state  c66d191"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 5): ?><span class="state  c666666"><?php endif; ?>
							<?php endif; ?>
								
							<?php if ($this->_var['deal']['is_wait'] == 1): ?>未开始
			            	<?php else: ?>
							<?php if ($this->_var['deal']['deal_status'] == 0): ?>等待材料<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] > 0): ?>进行中<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] <= 0): ?>已过期<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 2): ?>已满标<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 3): ?>已流标<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 4): ?>还款中<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 5): ?>已还清<?php endif; ?>
							<?php endif; ?>
							</span>
						<span class="name"><?php echo $this->_var['deal']['name']; ?></span>
						<span class="r_state"><?php 
$k = array (
  'name' => 'loantypename',
  'v' => $this->_var['deal']['loantype'],
  'type' => '1',
);
echo $k['name']($k['v'],$k['type']);
?></span>
					</div>
					<div class=" con_height">
						<div class="  w_b  pt tc">
							<div class=" tl lv"><i class="big"><?php 
$k = array (
  'name' => 'round',
  'v' => $this->_var['deal']['rate'],
  'f' => '0',
);
echo $k['name']($k['v'],$k['f']);
?></i><i class="unit">%</i></div>
							<div class=" tl je"><i class="small"><?php 
$k = array (
  'name' => 'replacemoney',
  'v' => $this->_var['deal']['borrow_amount'],
);
echo $k['name']($k['v']);
?></i><i class="unit">万</i></div>
							<div class=" tl sj"><i class="small"><?php echo $this->_var['deal']['repay_time']; ?></i><i class="unit"><?php if ($this->_var['deal']['repay_time_type'] == 1): ?>月<?php else: ?>天<?php endif; ?></i></div>
						<div class="w_b_f_1"></div>
						<div  class="progress-radial_parent ">
							<div class="progress-radial  progress-<?php 
$k = array (
  'name' => 'round',
  'v' => $this->_var['deal']['progress_point'],
  'f' => '0',
);
echo $k['name']($k['v'],$k['f']);
?>
							<?php if ($this->_var['deal']['is_wait'] == 1): ?> c999999
			            	<?php else: ?>
							<?php if ($this->_var['deal']['deal_status'] == 0): ?>  c999999 <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] > 0): ?>  c56b1ea <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] <= 0): ?>  c999999 <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 2): ?>  ea544a <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 3): ?>  c999999  <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 4): ?>  c66d191  <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 5): ?>  c999999  <?php endif; ?>
							<?php endif; ?>
							"><b></b></div>
						</div>
					</div>
						</div>
						
				</a>
			</li>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>		
		</ul>
		<?php endif; ?>
		<div class="clear_both init-bar-statics" >
				
				   <div class="posbar">
				   		<div class="col col1" nowpos="0" pos="<?php echo $this->_var['data']['virtual_money_1_pos']; ?>">
				   			<div class="point"><em></em></div>
				   		</div>
				   		<div class="col col2" nowpos="0" pos="<?php echo $this->_var['data']['virtual_money_2_pos']; ?>">
				   			<div class="point"><em></em></div>
				   		</div>
				   		<div class="col col3" nowpos="0" pos="<?php echo $this->_var['data']['virtual_money_3_pos']; ?>">
				   			<div class="point"><em></em></div>
				   		</div>
				   </div>
				   <div class="tit">
				   		<div class="col"><span class="dot dot1 pull-left"></span><span class="pull-left">总计成交额：</span><span class="pull-right"><em><?php echo $this->_var['data']['virtual_money_1']; ?></em>(万元)</span></div>
				   		<div class="col"><span class="dot dot2 pull-left"></span><span class="pull-left">总创造收益：</span><span class="pull-right"><em><?php echo $this->_var['data']['virtual_money_2']; ?></em>(万元)</span></div>
				   		<div class="col"><span class="dot dot3 pull-left"></span><span class="pull-left">本息保证金：</span><span class="pull-right"><em><?php echo $this->_var['data']['virtual_money_3']; ?></em>(万元)</span></div>
				   </div>
		</div>
	     <div class="blank055"></div>		
		<div class="init_title"><div class="hr bb1"></div><p>理财推荐</p></div>
		<div class="blank055"></div>		
		<ul class="recommended_nav_2">
			<?php $_from = $this->_var['data']['index_list']['deal_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'deal');if (count($_from)):
    foreach ($_from AS $this->_var['deal']):
?> 
			<li class="clearfix" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|deal|"."id=".$this->_var['deal']['id']."".""); 
?>','#deal',2);">
				
					<div class="title_nav bb1">
						<?php if ($this->_var['deal']['is_wait'] == 1): ?><span class="state  c666666">
			            	<?php else: ?>
							<?php if ($this->_var['deal']['deal_status'] == 0): ?><span class="state  c666666"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] > 0): ?><span class="state  c56b1ea"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] <= 0): ?><span class="state  c666666"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 2): ?><span class="state  ea544a"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 3): ?><span class="state  c666666"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 4): ?><span class="state  c66d191"><?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 5): ?><span class="state  c666666"><?php endif; ?>
							<?php endif; ?>
								
							<?php if ($this->_var['deal']['is_wait'] == 1): ?>未开始
			            	<?php else: ?>
							<?php if ($this->_var['deal']['deal_status'] == 0): ?>等待材料<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] > 0): ?>进行中<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] <= 0): ?>已过期<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 2): ?>已满标<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 3): ?>已流标<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 4): ?>还款中<?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 5): ?>已还清<?php endif; ?>
							<?php endif; ?>
							</span>
						<span class="name"><?php echo $this->_var['deal']['name']; ?></span>
						<span class="r_state"><?php 
$k = array (
  'name' => 'loantypename',
  'v' => $this->_var['deal']['loantype'],
  'type' => '1',
);
echo $k['name']($k['v'],$k['type']);
?></span>
					</div>
					<div class=" con_height">
						<div class="  w_b  pt tc">
							<div class=" tl lv"><i class="big"><?php 
$k = array (
  'name' => 'round',
  'v' => $this->_var['deal']['rate'],
  'f' => '0',
);
echo $k['name']($k['v'],$k['f']);
?></i><i class="unit">%</i></div>
							<div class=" tl je"><i class="small"><?php 
$k = array (
  'name' => 'replacemoney',
  'v' => $this->_var['deal']['borrow_amount'],
);
echo $k['name']($k['v']);
?></i><i class="unit">万</i></div>
							<div class=" tl sj"><i class="small"><?php echo $this->_var['deal']['repay_time']; ?></i><i class="unit"><?php if ($this->_var['deal']['repay_time_type'] == 1): ?>月<?php else: ?>天<?php endif; ?></i></div>
						<div class="w_b_f_1"></div>
						<div  class="progress-radial_parent ">
							<div class="progress-radial  progress-<?php 
$k = array (
  'name' => 'round',
  'v' => $this->_var['deal']['progress_point'],
  'f' => '0',
);
echo $k['name']($k['v'],$k['f']);
?>
							<?php if ($this->_var['deal']['is_wait'] == 1): ?> c999999
			            	<?php else: ?>
							<?php if ($this->_var['deal']['deal_status'] == 0): ?>  c999999 <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] > 0): ?>  c56b1ea <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] <= 0): ?>  c999999 <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 2): ?>  ea544a <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 3): ?>  c999999  <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 4): ?>  c66d191  <?php endif; ?>
							<?php if ($this->_var['deal']['deal_status'] == 5): ?>  c999999  <?php endif; ?>
							<?php endif; ?>
							"><b></b></div>
						</div>
					</div>
				
			</li>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>		
		</ul>
<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php echo $this->fetch('./inc/footer.html'); ?>
<?php endif; ?>





