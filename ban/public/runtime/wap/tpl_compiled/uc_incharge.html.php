<?php echo $this->fetch('./inc/header.html'); ?>	
<div class="page" id='<?php echo $this->_var['data']['act']; ?>'>
<?php
	$this->_var['back_url'] = wap_url("index","uc_center#index");
	$this->_var['back_page'] = "#uc_center";
	$this->_var['back_epage'] = $_REQUEST['epage']=="" ? "#deal" : "#".$_REQUEST['epage'];
?>
<?php echo $this->fetch('./inc/title.html'); ?>

<div class="content">
	
<!-- 这里是页面内容区 -->

<!--账户充值-->
<style>
	.pay_detail{min-height:65px;}
</style>
<?php if ($this->_var['data']['c_number'] == 3): ?>
<style>
	.Menubox ul li {width: 33.333333333333333%;}
	.Menubox ul li.hover {width: 33.33333333333333%;}	
	.Menubox ul li:nth-child(3){border-right:0px;}
</style>
<?php endif; ?>
<?php if ($this->_var['data']['c_number'] == 2): ?>
<style>
	.Menubox ul li {width: 50%;}
	.Menubox ul li.hover {width: 50%;}	
	.Menubox ul li:nth-child(2){border-right:0px;}
</style>
<?php endif; ?>
<?php if ($this->_var['data']['c_number'] == 1): ?>
<style>
	.Menubox ul li {width: 100%;}
	.Menubox ul li.hover {width: 100%;}	
	.Menubox ul li:nth-child(1){border-right:0px;}
</style>
<?php endif; ?>

<div class="Menubox">
        <ul>
        	<?php if ($this->_var['data']['payment_list']): ?>
            <li id="one1" onclick="setInChrgTab('one',1,<?php echo $this->_var['data']['c_number']; ?>)" class="hover">线上支付</li>
			<?php endif; ?>
			<?php if ($this->_var['data']['below_payment']): ?>
				<?php if ($this->_var['data']['c_one'] == 0): ?>
					<li id="one1" onclick="setInChrgTab('one',1,<?php echo $this->_var['data']['c_number']; ?>)" class="hover">线下支付</li>
				<?php else: ?>
					<li id="one2" onclick="setInChrgTab('one',2,<?php echo $this->_var['data']['c_number']; ?>)">线下支付</li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_var['data']['ips_bank_list']): ?>
				<?php if ($this->_var['data']['c_one'] == 0 && $this->_var['data']['c_two'] == 0): ?>
					<li id="one1" onclick="setInChrgTab('one',1,<?php echo $this->_var['data']['c_number']; ?>)" class="hover">第三方托管</li>
				<?php else: ?>
					<?php if ($this->_var['data']['c_one'] == 1 && $this->_var['data']['c_two'] == 1): ?>
						<li id="one3" onclick="setInChrgTab('one',3,<?php echo $this->_var['data']['c_number']; ?>)">第三方托管</li>
					<?php else: ?>
						<li id="one2" onclick="setInChrgTab('one',2,<?php echo $this->_var['data']['c_number']; ?>)">第三方托管</li>
					<?php endif; ?>
				<?php endif; ?>
            		
            <?php endif; ?>
        </ul>
</div>
<?php if ($this->_var['data']['payment_list']): ?>
<div id="con_one_1" class="uc_incharge"><!--线上支付-->
		<div class="figure">
			<input id="money1" class="" type="text"  style="height:39px;" placeholder="请输入金额">
		</div>
		<div class="figure">
			<div style="padding-top:10px;" id="Fee_t">充值费用 &nbsp; 0.00</div>
		</div>
		<?php if ($this->_var['data']['payment_list']): ?>
		<div class="bank_list in_line">
			<ul>
				<?php $_from = $this->_var['data']['payment_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
				<li>
					<div class="pay_detail clearfix">
						<div class="inline_pay_img_show">
							<?php if ($this->_var['item']['img']): ?>
						      <img src="<?php echo $this->_var['item']['img']; ?>"  height="35px">
						     <?php endif; ?>
						</div>
						<div class="inline_pay_name">
							<?php echo $this->_var['item']['class_name']; ?>
						</div>
					</div>
					<input class="mt  dw" onclick="InchargeRes(<?php echo $this->_var['item']['id']; ?>)" type="radio" iclass="<?php echo $this->_var['item']['iclass_name']; ?>" value="<?php echo $this->_var['item']['id']; ?>" id="ufee" name="paypath1">
				</li>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			</ul>
		</div>
		<?php else: ?>
		<div class="no_data_block">
    	暂无数据
       </div>
		<?php endif; ?>
		
		<div class="w_b but_box_parent padding">
					<div class="w_b_f_1 but_box">
						<button id="on_incharge_done"  class="but_this">确认</button>
					</div>
				</div>
</div>
<?php endif; ?>
<?php if ($this->_var['data']['below_payment']): ?>
<?php if ($this->_var['data']['c_one'] == 0): ?>
<div id="con_one_1" class="uc_incharge"  ><!--线下支付-->
<?php else: ?>
<div id="con_one_2" class="uc_incharge"  style=" display:none;" ><!--线下支付-->
<?php endif; ?>
	<div class="figure">
		<input id="money2" class="" style="height:39px;" type="text" placeholder="请输入金额">
	</div>
	<div class="figure">
		<input id="memo" class="" style="height:39px;" type="text" placeholder="请输入银行流水号">
	</div>
	<div class="figure">
		<div style="padding-top:10px;" id="Fee_two">充值费用 &nbsp; 0.00</div>
	</div>
		
	<?php if ($this->_var['data']['below_payment']): ?>
	<div class="bank_list out_line">
		<ul>
		<?php $_from = $this->_var['data']['below_payment']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
			
			<li>
				<div class="pay_detail">
					<h5><?php echo $this->_var['item']['pay_name']; ?></h5>
					<div class="info">
						<span class="peoplo">收款人:<?php echo $this->_var['item']['pay_account_name']; ?></span>
						<span>开户行:<?php echo $this->_var['item']['pay_bank']; ?></span>
					</div>
					<p>账户：<?php echo $this->_var['item']['pay_account']; ?></p>
				</div>
				<input class="mt dw" type="radio" value="<?php echo $this->_var['item']['bank_id']; ?>" name="paypath" onclick="InchargeRestwo(<?php echo $this->_var['item']['pay_id']; ?>)" >
				<?php if ($this->_var['key'] == 0): ?>
				<input id="payment_id" type="hidden" value="<?php echo $this->_var['item']['pay_id']; ?>"  />
				<?php endif; ?>
			</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
	</div>
	<?php else: ?>
		<div class="no_data_block">
    	暂无数据
       </div>
		<?php endif; ?>

	 <div class="w_b but_box_parent padding">
					<div class="w_b_f_1 but_box">
						<button id="incharge_done"  class="but_this">确认</button>
					</div>
				</div>
</div>
<?php endif; ?>
<?php if ($this->_var['data']['ips_bank_list']): ?>
<!-- <form action="<?php
echo parse_wap_url_tag("u:index|collocation|"."DoDpTrade".""); 
?>" method="get" id="search_form" >  -->
<input type="hidden" value="collocation" name="ctl">
<input type="hidden" value="DoDpTrade" name="act">
<input type="hidden" value="0" name="user_type">
<input type="hidden" value="wap" name="from">
<input type="hidden" value="<?php echo $this->_var['data']['user_id']; ?>" id="user_id" name="user_id">
<?php if ($this->_var['data']['c_one'] == 0 && $this->_var['data']['c_two'] == 0): ?>
		<div id="con_one_1" class="uc_incharge"><!--第三方支付-->
<?php else: ?>
  <?php if ($this->_var['data']['c_one'] == 1 && $this->_var['data']['c_two'] == 1): ?>
		<div id="con_one_3" class="uc_incharge" style=" display:none;"><!--第三方支付-->
  <?php else: ?>
		<div id="con_one_2" class="uc_incharge" style=" display:none;"><!--第三方支付-->
  <?php endif; ?>
<?php endif; ?>
		<div class="figure">
			<input id="money3" name="pTrdAmt" class="" style="height:39px;" type="text" placeholder="请输入金额">
		</div>
		<?php if ($this->_var['data']['ips_bank_list']): ?>
		<div class="bank_list in_line">
			<ul>
				<?php $_from = $this->_var['data']['ips_bank_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
				<li>
					<div class="pay_detail clearfix">
						<div class="inline_pay_img_show">
						      <img src="<?php echo $this->_var['item']['img']; ?>"  height="35px">
						</div>
						<div class="inline_pay_name">
							<?php echo $this->_var['item']['name']; ?>(<?php echo $this->_var['item']['sub_name']; ?>)
						</div>
					</div>
					<input class="mt  dw"  type="radio" value="<?php echo $this->_var['item']['id']; ?>" name="pTrdBnkCode">
				</li>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			</ul>
		</div>
		
		<?php else: ?>
		<div class="no_data_block">
    	暂无数据
       </div>
		<?php endif; ?>

		<div class="w_b but_box_parent padding">
					<div class="w_b_f_1 but_box">
						<button id="other_incharge_done"  class="but_this">确认</button>
					</div>
				</div>
</div>

<!--</form> -->
<?php endif; ?>

<?php echo $this->fetch('./inc/footer.html'); ?>





