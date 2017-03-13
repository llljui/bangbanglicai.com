<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php echo $this->fetch('./inc/header.html'); ?>	
<div class="page" id='<?php echo $this->_var['data']['act']; ?>'>	
<?php
	$this->_var['back_url'] = wap_url("index","uc_center#index");
	$this->_var['back_page'] = "#uc_center";
	$this->_var['back_epage'] = $_REQUEST['epage']=="" ? "#uc_center" : "#".$_REQUEST['epage'];
?>
<?php echo $this->fetch('./inc/title.html'); ?>
<div class="content infinite-scroll "  data-distance="<?php echo $this->_var['data']['rs_count']; ?>" now_page="1">
	<!-- 这里是页面内容区 -->
<!--我的投资-->
<div class="<?php echo $this->_var['data']['act']; ?>-box"  all_page="<?php echo $this->_var['data']['page']['page_total']; ?>" ajaxurl="<?php
echo parse_wap_url_tag("u:index|uc_invest#index|"."status=".$this->_var['data']['status']."".""); 
?>">
	<div class="blank15"></div>
	<div class="choose">
		<table>
			<tr>
				<th <?php if ($this->_var['data']['status'] == 0): ?>class="y"<?php endif; ?>><a href="#" onclick="reloadpage('<?php
echo parse_wap_url_tag("u:index|uc_invest|"."status=0".""); 
?>','#uc_invest','.<?php echo $this->_var['data']['act']; ?>-box')">全部</a></th>
				<th <?php if ($this->_var['data']['status'] == 1): ?>class="y"<?php endif; ?>><a href="#" onclick="reloadpage('<?php
echo parse_wap_url_tag("u:index|uc_invest|"."status=1".""); 
?>','#uc_invest','.<?php echo $this->_var['data']['act']; ?>-box')">进行中</a></th>
				<th <?php if ($this->_var['data']['status'] == 2): ?>class="y"<?php endif; ?>><a href="#" onclick="reloadpage('<?php
echo parse_wap_url_tag("u:index|uc_invest|"."status=2".""); 
?>','#uc_invest','.<?php echo $this->_var['data']['act']; ?>-box')">还款中</a></th>
				<th <?php if ($this->_var['data']['status'] == 3): ?>class="y"<?php endif; ?>><a href="#" onclick="reloadpage('<?php
echo parse_wap_url_tag("u:index|uc_invest|"."status=3".""); 
?>','#uc_invest','.<?php echo $this->_var['data']['act']; ?>-box')">已还清</a></th>
				<th <?php if ($this->_var['data']['status'] == 4): ?>class="y"<?php endif; ?>><a href="#" onclick="reloadpage('<?php
echo parse_wap_url_tag("u:index|uc_invest|"."status=4".""); 
?>','#uc_invest','.<?php echo $this->_var['data']['act']; ?>-box')">满标</a></th>
				<th <?php if ($this->_var['data']['status'] == 5): ?>class="y"<?php endif; ?>><a href="#" onclick="reloadpage('<?php
echo parse_wap_url_tag("u:index|uc_invest|"."status=5".""); 
?>','#uc_invest','.<?php echo $this->_var['data']['act']; ?>-box')">流标</a></th>
			</tr>
		</table>
	</div>
	<div class="blank15"></div>
	<ul class="detail_list">
		<?php endif; ?>
		<?php $_from = $this->_var['data']['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'deal');if (count($_from)):
    foreach ($_from AS $this->_var['deal']):
?>
		<li>
			
			<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] > 0): ?>
			<div class="h clearfix b_3b95d3">
				<span class="Status">进行中</span>
				<div class="bor clearfix">
					<span class="name f_l"><?php echo $this->_var['deal']['name']; ?></span>
				</div>
			</div>
			<?php endif; ?>
			<?php if ($this->_var['deal']['deal_status'] == 1 && $this->_var['deal']['remain_time'] <= 0): ?>
			<div class="h clearfix b_b0b0b0">
				<span class="Status">已过期</span>
				<div class="bor clearfix">
					<span class="name f_l"><?php echo $this->_var['deal']['name']; ?></span>
				</div>
			</div>
			<?php endif; ?>
			<?php if ($this->_var['deal']['deal_status'] == 2): ?>
			<div class="h clearfix b_ff6f6f">
				<span class="Status">满标</span>
				<div class="bor clearfix">
					<span class="name f_l"><?php echo $this->_var['deal']['name']; ?></span>
				</div>
			</div>
			<?php endif; ?>	
			<?php if ($this->_var['deal']['deal_status'] == 3): ?>
			<div class="h clearfix b_b0b0b0">
				<span class="Status">流标</span>
				<div class="bor clearfix">
					<span class="name f_l"><?php echo $this->_var['deal']['name']; ?></span>
				</div>
			</div>
			<?php endif; ?>
			<?php if ($this->_var['deal']['deal_status'] == 5): ?>
			<div class="h clearfix b_a4ce1c">
				<span class="Status">已还清</span>
				<div class="bor clearfix">
					<span class="name f_l"><?php echo $this->_var['deal']['name']; ?></span>
				</div>
			</div>
			<?php endif; ?>
			<?php if ($this->_var['deal']['deal_status'] == 4): ?>
			<div class="h clearfix b_ff8800">
				<span class="Status">还款中</span>
				<div class="bor clearfix">
					<span class="name f_l"><?php echo $this->_var['deal']['name']; ?></span>
				</div>
			</div>
			<?php endif; ?>
			
			<div class="bor_1">
				<div class="middle clearfix">
					<table>
						<tr>
							<td>
								<span class="name">金额</span>
								<span><?php 
$k = array (
  'name' => 'format_price',
  'v' => $this->_var['deal']['u_load_money'],
);
echo $k['name']($k['v']);
?></span>
							</td>
							<td>
								<span class="name">年利率</span>
								<span class="c_ff8800"><?php 
$k = array (
  'name' => 'number_format',
  'v' => $this->_var['deal']['rate'],
  'f' => '2',
);
echo $k['name']($k['v'],$k['f']);
?>%</span>
							</td>
							<td>
								<span class="name" >期限</span>
								<span><?php echo $this->_var['deal']['repay_time']; ?><?php if ($this->_var['deal']['repay_time_type'] == 0): ?>天<?php else: ?>个月<?php endif; ?></span>
							</td>
							<td>
<!------------------------------------------------------------------------------------------------------->					            
								
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
<!------------------------------------------------------------------------------------------------------->
								</td>
						</tr>
					</table>
				</div>
				<div class="bottom clearfix">
					<span class="time">发布日期<?php 
$k = array (
  'name' => 'to_date',
  'v' => $this->_var['deal']['start_time'],
  'f' => 'Y-m-d',
);
echo $k['name']($k['v'],$k['f']);
?></span>
					<?php if ($this->_var['deal']['has_transfer'] > 0 && $this->_var['deal']['t_user_id'] <> $this->_var['data']['user_id']): ?>
					<div class="f_r">
						<a href="#" class="but_b bor_b0b0b0 b_b0b0b0">已转让</a>
					</div>
					<?php else: ?>
					<div class="f_r">
						<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|deal_mobile|"."id=".$this->_var['deal']['id']."&epage=".$this->_var['data']['act']."".""); 
?>','#deal_mobile',2);" class="but_c c_aad421 bor_aad421">标的详情</a>
					<?php if ($this->_var['deal']['deal_status'] >= 4): ?>
						<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_invest_refdetail|"."id=".$this->_var['deal']['id']."&load_id=".$this->_var['deal']['load_id']."".""); 
?>','#uc_invest_refdetail',2);" class="but_c bor_3b95d3 c_3b95d3">回款详情</a>
					<?php endif; ?>
					</div>
					<?php endif; ?>
					
					
			   </div>
			</div>
		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php if ($_REQUEST['is_ajax'] != 1): ?>		
	</ul>
	<div class="blank15"></div>
</div>
<!-- 加载提示符 -->
<div class="infinite-scroll-preloader">
  <div class="preloader">
  </div>
</div>
<?php echo $this->fetch('./inc/footer.html'); ?>
<?php endif; ?>






