<?php echo $this->fetch('./inc/header.html'); ?>	
<div class="page" id='<?php echo $this->_var['data']['act']; ?>'>
<?php
	$this->_var['back_url'] = wap_url("index","deals#index");
	$this->_var['back_page'] = "#deals";
	$this->_var['back_epage'] = $_REQUEST['epage']=="" ? "#init" : "#".$_REQUEST['epage'];
?>
<?php echo $this->fetch('./inc/title.html'); ?>
<div class="content">
<!-- 这里是页面内容区 -->
<!--投资借款简单详情-->
<div class="detail">
<div class="mainblok mainborder">
            <div class="detail_tit ">
                <h3><?php echo $this->_var['data']['deal']['name']; ?><?php if ($this->_var['data']['deal']['is_new']): ?><em>新</em><?php endif; ?></h3>
            </div>
            <div class="detail_rate">
            	<span><?php echo $this->_var['data']['deal']['rate_foramt']; ?><em>%</em></span>
            	<h4>年化收益率</h4>
            </div>
            <div class="detail_row">
            	<div class="it"><?php 
$k = array (
  'name' => 'loantypename',
  'v' => $this->_var['data']['deal']['loantype'],
  'type' => '1',
);
echo $k['name']($k['v'],$k['type']);
?></div>
            	<div class="it"><?php echo $this->_var['data']['deal']['min_loan_money_format']; ?>起投</div>
            	<div class="it last">期限<?php echo $this->_var['data']['deal']['repay_time']; ?><?php if ($this->_var['data']['deal']['repay_time_type'] == 0): ?>天<?php else: ?>个月<?php endif; ?></div>
            </div>
            
           	<div class="detail_process" id="detail_process" data="<?php 
$k = array (
  'name' => 'round',
  'v' => $this->_var['data']['deal']['progress_point'],
  'f' => '2',
);
echo $k['name']($k['v'],$k['f']);
?>">
           		<div class="bg">
           			<div class="tip_bar" style="padding-left:0%">
	           			<div class="tip" >
	           				<em>0%</em>
	           				<span></span>
	           			</div>
	           		</div>
           			<div class="pros" style="width:0%"></div>
           		</div>
           		
           	</div>
           	<div class="detail_hs_nd">
           		<div class="f_l tl">
           			已认购
           			<?php if ($this->_var['data']['deal']['uloadtype'] == 1): ?>
           			<em><?php echo $this->_var['data']['deal']['buy_portion']; ?>笔</em>
           			<?php else: ?>
           			<em><?php 
$k = array (
  'name' => 'format_price',
  'v' => $this->_var['data']['deal']['load_money'],
  'f' => '2',
);
echo $k['name']($k['v'],$k['f']);
?></em>
           			<?php endif; ?>
           		</div>
           		<div class="f_r tr">
           			<?php if ($this->_var['data']['deal']['uloadtype'] == 1): ?>
           			可投份数
           			<em><?php echo $this->_var['data']['deal']['need_portion']; ?></em>
           			<?php else: ?>
           			可投金额
           			<em><?php echo $this->_var['data']['deal']['need_money']; ?></em>
           			<?php endif; ?>
           		</div>
           		
           	</div>
            
        </div><!--mainblok——end--> 
		<div class="blank10"></div>
           
        <div class="mainblok mainborder">
            <div class="detail_list">
                <ul>
                    
          			<li class="bb1">
                		<label>借款编号</label>
                		<div class="list_con"><?php echo $this->_var['data']['deal']['deal_sn']; ?></div>
                	</li>
                    <li class="bb1">
                        <label>借款金额</label>
                        <div class="list_con"><?php echo $this->_var['data']['deal']['borrow_amount_format']; ?></div>
                    </li> 
					
                    <li class="bb1">
                        <label>风险等级</label>
                        <div class="list_con">
                        	<?php if ($this->_var['data']['deal']['rish_rank'] == 0): ?>低<?php elseif ($this->_var['data']['deal']['rish_rank'] == 1): ?>中<?php elseif ($this->_var['data']['deal']['rish_rank'] == 2): ?>高<?php endif; ?>
						</div>
                    </li>
                    <li >
                        <label>剩余时间</label>
                        <div class="list_con"><?php echo $this->_var['data']['deal']['remain_time_format']; ?></div>
                    </li>
                </ul>
            </div>
        </div><!--mainblok——end--> 

		<div class="blank10"></div>
       
        <div class="mainblok mainborder">
         <?php if ($this->_var['data']['deal']['deal_status'] == 1): ?>
            <div class="detail_list">
                <ul>
                    <li class="bb1">
                        <label>可用余额</label>
                        <div class="list_con">
                        	<em class="f_red"><?php 
$k = array (
  'name' => 'format_price',
  'v' => $this->_var['data']['user_money'],
  'f' => '2',
);
echo $k['name']($k['v'],$k['f']);
?></em>元
							<?php if ($this->_var['is_login'] == 0): ?>
							<a href="<?php
echo parse_wap_url_tag("u:index|login|"."".""); 
?>" class="linkbtn <?php if ($this->_var['is_weixin']): ?>external<?php endif; ?>">我要充值</a>
							<?php else: ?>
							<a href="<?php
echo parse_wap_url_tag("u:index|uc_incharge|"."".""); 
?>" class="linkbtn">我要充值</a>
							<?php endif; ?>
						</div>
                    </li>
					<?php if ($this->_var['data']['deal']['uloadtype'] == 1): ?>
					 <li class="bb1">
                        <label>投标金额</label>
                        <div class="list_con">
                        	
							 	<div class="nun_choose ">
									<div id="deal-intro" class="c_number-box" max="<?php if ($this->_var['data']['deal']['max_portion'] > 0): ?><?php echo $this->_var['data']['deal']['max_portion'] - $this->_var['data']['has_bid_portion'];  ?><?php else: ?><?php echo $this->_var['data']['deal']['need_portion']; ?><?php endif; ?>" >
										<a class="c_number Minus pull-left" rel="-">-</a>
										<input id="deal_id" type="hidden" value="<?php echo $this->_var['data']['deal']['id']; ?>"  />
										<input id="buy_number" name="buy_number" type="hidden" value="<?php echo $this->_var['data']['deal']['min_loan_money']; ?>" >
									    <input type="text" value="1" class="nuns pull-left"  autocomplete="off" name="pay_inmoney" id="pay_inmoney"/>
									    <a class="c_number Plus pull-left" rel="+">+</a>
									</div>
								</div>
                        </div>
                     </li>	
					<li class="bb1">
                     	 <label>预计收益</label>
                     	<div class="list_con "><span class="J_u_money_sy f_red">0.00</span></div>
                     </li>
					<?php else: ?>
					 <li class="bb1">
                        <label >投标金额</label>
                        <div class="list_con ">
                        	<input id="deal_id" type="hidden" value="<?php echo $this->_var['data']['deal']['id']; ?>"  />
            				<input id="pay_inmoney" name="pay_inmoney" class="ipt pull-left" placeholder="请输入投资金额" type="text" style="width:50%"/>
                        	
                        </div>
                     </li>
                     <li class="bb1">
                     	 <label>预计收益</label>
                     	<div class="list_con "><span class="J_u_money_sy f_red">0.00</span></div>
                     </li>
					<?php endif; ?>
                    <?php if ($this->_var['data']['interestrate_list']): ?>
                     <li class="bb1">
                        <label>使用加息券</label>
                        <div class="list_con">
                            <label class="ui-checkbox " rel="use_interestrate" >
                                <input type="checkbox" class='mt' value="1" name="use_interestrate" id="use_interestrate"/>
                            </label>
							<span class="f_red">加息券与红包不可同时使用</span>
                        </div>
                    </li>
                    <?php endif; ?>
					<?php if ($this->_var['data']['ecv_list']): ?>
					<li class='ecv_row bb1'>
                        <label>使用红包</label>
                        <div class="list_con" style="height:5rem">
                        	<select name="ecv_id" id="ecv_id" class="select">
                        		<option value="0">选择红包</option>
								<?php $_from = $this->_var['data']['ecv_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ecv');if (count($_from)):
    foreach ($_from AS $this->_var['ecv']):
?>
								<option value="<?php echo $this->_var['ecv']['id']; ?>"><?php echo $this->_var['ecv']['name']; ?>[抵<?php echo $this->_var['ecv']['money']; ?>元]</option>
								<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        	</select>
							<span class="f_red">超出部分不返还</span>
                        </div>
                     </li>
					<?php endif; ?>
                    
                    <?php if ($this->_var['data']['interestrate_list']): ?>
                    <li class='interestrate_row bb1' style="display:none;">
                        <label>选择加息券</label>
                        <div class="list_con">
                            <select name="interestrate_id" id='interestrate_id' class="select">
                                <option>选择加息券</option>
                                <?php $_from = $this->_var['data']['interestrate_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'interestrate');if (count($_from)):
    foreach ($_from AS $this->_var['interestrate']):
?>
                                <option value="<?php echo $this->_var['interestrate']['id']; ?>"><?php echo $this->_var['interestrate']['name']; ?>[<?php echo $this->_var['interestrate']['rate_format']; ?>]</option>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            </select>
						</div>
                    </li>
                   <?php endif; ?>
					<li >
                        <label>支付密码</label>
                        <div class="list_con">
                        <input id="pay_inmoney_password" class="ipt pull-left" type="password" placeholder="请输入您的支付密码" style="width:55%" />
                        <?php if ($this->_var['is_login'] == 0): ?>
							<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|login|"."".""); 
?>','#login'<?php if ($this->_var['is_weixin']): ?>,1<?php endif; ?>);" class="linkbtn pull-right ">设置支付密码</a>
							<?php else: ?>
							<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|reset_pay_pwd|"."".""); 
?>','#reset_pay_pwd',2)" class="linkbtn pull-right">设置支付密码</a>
							<?php endif; ?>
						</div>
					 </li>
                </ul>
            </div>
			<?php endif; ?>
        </div><!--mainblok——end--> 
   </div>
   <div class="blank0"></div>
	<div class="w_b but_box_parent padding">
		<div class="w_b_f_1 but_box">
			<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|deal_mobile|"."id=".$this->_var['data']['deal']['id']."".""); 
?>','#deal_mobile',2);"  class=" but_this" >查看详情</a>
		</div>
		<div class="w_b_f_1 but_box">
			 <?php if ($this->_var['data']['deal']['deal_status'] == 1 && $this->_var['data']['deal']['remain_time'] > 0): ?>
			    <?php if ($this->_var['is_login'] == 1): ?>
				     <div id="pay_deal" class="bg_ea544a but_this">我要投资</div>
			   	<?php else: ?>
				      <a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|login|"."".""); 
?>','#login'<?php if ($this->_var['is_weixin']): ?>,1<?php endif; ?>);" class="bg_ea544a but_this">我要投资</a>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_var['data']['deal']['deal_status'] == 1 && $this->_var['data']['deal']['remain_time'] <= 0): ?><span class="bg_dbdbdb but_this" >已过期</span><?php endif; ?>
			<?php if ($this->_var['data']['deal']['deal_status'] == 2): ?><span class="bg_dbdbdb but_this" >满标</span><?php endif; ?>
			<?php if ($this->_var['data']['deal']['deal_status'] == 3): ?><span class="bg_dbdbdb but_this" >流标</span><?php endif; ?>
			<?php if ($this->_var['data']['deal']['deal_status'] == 4): ?><span class="bg_dbdbdb but_this" >还款中</span><?php endif; ?>
			<?php if ($this->_var['data']['deal']['deal_status'] == 5): ?><span class="bg_dbdbdb but_this" >已还款</span><?php endif; ?>
			<?php if ($this->_var['data']['deal']['deal_status'] == 0): ?><span class="bg_dbdbdb but_this" >等待材料</span><?php endif; ?>
		</div>
	</div>
    


<?php echo $this->fetch('./inc/footer.html'); ?>



