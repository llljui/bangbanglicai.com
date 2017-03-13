<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php echo $this->fetch('./inc/header.html'); ?>	
<?php
$this->_var['hide_back'] = 1;
?>
<div class="page" id='<?php echo $this->_var['data']['act']; ?>'>
<?php echo $this->fetch('./inc/title.html'); ?>
<div class="content">
	<?php endif; ?>
<!-- 这里是页面内容区 -->

<div class="uc_center">
	<div class="uc_c_top">
		<div class="uc_c_top_conten">
			<div class="table_block">
				<a class="table_cell ">
					<div class="f_r show">
						<p class="icon_block"><i class="icon iconfont <?php if ($this->_var['data']['vip_id'] > 0): ?>ff9785<?php endif; ?>">&#xe616;</i></p>
						<p class="text <?php if ($this->_var['data']['vip_id'] > 0): ?>ff9785<?php endif; ?>"><?php echo $this->_var['data']['vip_grade']; ?></p>
					</div>
				</a>
				<div class="table_cell">
					<div class="uc_img_bor_big">
					<div class="uc_img_bor_small">
						<div class="uc_img"><img src="<?php 
$k = array (
  'name' => 'wap_user_avatar',
  'uid' => $this->_var['data']['user_id'],
);
echo $k['name']($k['uid']);
?>" style="width:100%;height:100%;"/></div>
					</div>
				    </div>
				</div>
				<?php if ($this->_var['data']['t_sign_data']): ?>
				<a class="table_cell ">
					<div class="f_l show">
						<p class="icon_block "><i class="icon iconfont ff9785">&#xe615;</i></p>
						<p class="text ff9785">已经签到过了</p>
					</div>
				</a>
				<?php else: ?>
				<a class="table_cell" onclick="user_sign();">
					<div class="f_l show">
						<p class="icon_block "><i class="icon iconfont">&#xe615;<t class="nosignicon"></t></i></p>
						<p class="text">点击进行签到</p>
					</div>
				</a>
				<?php endif; ?>			
			</div>	
			<div class="tc user_name"><?php echo $this->_var['data']['user_name']; ?></div>
			<div class="table_block money_situation">
				<div class="table_cell tc child">
					<p class="num"><?php 
$k = array (
  'name' => 'number_format',
  'v' => $this->_var['data']['total_money'],
  'f' => '2',
);
echo $k['name']($k['v'],$k['f']);
?></p>
					<p class="name">资金余额</p>
				</div>
				<div class="table_cell tc child">
					<p class="num"><?php 
$k = array (
  'name' => 'number_format',
  'v' => $this->_var['data']['money'],
  'f' => '2',
);
echo $k['name']($k['v'],$k['f']);
?></p>
					<p class="name">可用资金</p>
				</div>
				<div class="table_cell tc child">
					<p class="num"><?php 
$k = array (
  'name' => 'number_format',
  'v' => $this->_var['data']['lock_money'],
  'f' => '2',
);
echo $k['name']($k['v'],$k['f']);
?></p>
					<p class="name">冻结资金</p>
				</div>
			</div>			
		</div>
	</div>
	<div class="uc_c_middle">
		<dl>
			<dd>
				<div class="top_f bb1">
					<span class="f_l name">我的资产</span>
					<a href="#" class="f_r con" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_learn_deal_invest|"."".""); 
?>','#uc_learn_deal_invest',2);">体验金投资<i class="icon iconfont icon_rigth">&#xe61a;</i></a>
				</div>
				<div class="blank0"></div>
				<div class="middle_con flex">
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_voucher|"."".""); 
?>','#uc_voucher',2);">
						<i class="icon iconfont icon_left">&#xe64d;</i>
						<p>我的红包</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_interestrate|"."".""); 
?>','#uc_interestrate',2);">
						<i class="icon iconfont icon_left">&#xe647;</i>
						<p>加息券</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_account_log|"."".""); 
?>','#uc_account_log',2);">
						<i class="icon iconfont icon_left">&#xe65b;</i>
						<p>账户日志</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_learn|"."".""); 
?>','#uc_learn',2);">
						<i class="icon iconfont icon_left">&#xe649;</i>
						<p>理财体验金</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_qrcode|"."".""); 
?>','#uc_qrcode',2);">
						<i class="icon iconfont icon_left">&#xe653;</i>
						<p>我的邀请</p>
					</a>
				</div>
			</dd>
			<dd>
				<div class="top_f bb1">
					<span class="f_l name">我的投资</span>
					<a class="f_r con"><!--自动投标--><i class="icon iconfont icon_rigth">&#xe61a;</i></a>
				</div>
				<div class="blank0"></div>
				<div class="middle_con flex">
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:inde|uc_invest|"."status=2".""); 
?>','#uc_invest',2);">
						<i class="icon iconfont icon_left">&#xe65a;</i>
						<p>待收款</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_transfer|"."".""); 
?>','#uc_transfer',2);">
						<i class="icon iconfont icon_left">&#xe658;</i>
						<p>债权转让</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_financial_statistics|"."".""); 
?>','#uc_financial_statistics',2);">
						<i class="icon iconfont icon_left">&#xe64b;</i>
						<p>投资统计</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_collect|"."".""); 
?>','#uc_collect',2);">
						<i class="icon iconfont icon_left">&#xe64c;</i>
						<p>我的关注</p>
					</a>
					<a  class="flex-1 con tc"></a>
				</div>
			</dd>
			<dd>
				<div class="top_f bb1">
					<span class="f_l name">我的借款</span>
					<a class="f_r con"><!--发标贷款--><i class="icon iconfont icon_rigth">&#xe61a;</i></a>
				</div>
				<div class="blank0"></div>
				<div class="middle_con flex">
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:inde|uc_borrowed|"."status=2".""); 
?>','#uc_borrowed',2);">
						<i class="icon iconfont icon_left">&#xe646;</i>
						<p>还款中</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:inde|uc_borrowed|"."status=3".""); 
?>','#uc_borrowed',2);">
						<i class="icon iconfont icon_left">&#xe657;</i>
						<p>已还清</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_financial_statistics|"."".""); 
?>','#uc_financial_statistics',2);">
						<i class="icon iconfont icon_left">&#xe659;</i>
						<p>贷款统计</p>
					</a>
					<a  class="flex-1 con tc"></a>
					<a  class="flex-1 con tc"></a>
				</div>
			</dd>

			<?php if ($this->_var['data']['licai_open'] == 1): ?>
			<dd>
				<div class="top_f bb1">
					<span class="f_l name">我的理财</span>
					<a class="f_r con"><!--发起理财--><i class="icon iconfont icon_rigth">&#xe61a;</i></a>
				</div>
				<div class="blank0"></div>
				<div class="middle_con flex">
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|licai_uc_published_lc|"."".""); 
?>','#licai_uc_published_lc',2);">
						<i class="icon iconfont icon_left">&#xe64f;</i>
						<p>我的理财</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|licai_uc_expire_lc|"."".""); 
?>','#licai_uc_expire_lc',2);">
						<i class="icon iconfont icon_left">&#xe648;</i>
						<p>理财发放</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|licai_uc_redeem_lc|"."".""); 
?>','#licai_uc_redeem_lc',2);">
						<i class="icon iconfont icon_left">&#xe64a;</i>
						<p>赎回管理</p>
					</a>
					<a href="#" class="flex-1 con tc" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|licai_uc_buyed_lc|"."".""); 
?>','#licai_uc_buyed_lc',2);">
						<i class="icon iconfont icon_left">&#xe655;</i>
						<p>我购买的</p>
					</a>
					<a class="flex-1 con tc"></a>
				</div>
			</dd>
			<?php endif; ?>
		</dl>
		
		<ul class="uc_c_middle_content_list">
			<li>
				<a href="#" class="href_first w_b" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_incharge_log|"."".""); 
?>','#uc_incharge_log',2);">
					
					<p class="w_b_f_1">充值日志</p>
					<i class="icon iconfont icon_rigth">&#xe61a;</i>
				</a>
				<a href="#" class="href_second" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_incharge|"."".""); 
?>','#uc_incharge',2);">我要充值</a>	
			</li>
			<li>
				<a href="#" class="href_first w_b" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_carry_money_log|"."".""); 
?>','#uc_carry_money_log',2);">
					
					<p class="w_b_f_1">提现日志</p>
					<i class="icon iconfont icon_rigth">&#xe61a;</i>
				</a>
				<a href="#" class="href_second c56b1ea" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_bank|"."".""); 
?>','#uc_bank',2);">我要提现</a>
			</li>
			<li>
				<a href="#" class="href_first w_b" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_account_info|"."".""); 
?>','#uc_account_info',2);">
					
					<p class="w_b_f_1">账户明细</p>
					<i class="icon iconfont icon_rigth">&#xe61a;</i>
				</a>
			</li>
			
			<li>
				<a href="#" class="href_first w_b" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_goods_order|"."".""); 
?>','#uc_goods_order',2);">
					
					<p class="w_b_f_1">我的兑换</p>
					<i class="icon iconfont icon_rigth">&#xe61a;</i>
				</a>
			</li>
			<li>
				<a href="#" class="href_first w_b" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_vip_buy_log|"."".""); 
?>','#uc_vip_buy_log',2);">
					
					<p class="w_b_f_1">VIP购买日志</p>
					<i class="icon iconfont icon_rigth">&#xe61a;</i>
				</a>
				<a href="#" class="href_second" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_vip_buy|"."".""); 
?>','#uc_vip_buy',2);">我要购买</a>
			</li>
			<li>
				<a href="#" class="href_first w_b" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|uc_setting|"."".""); 
?>','#uc_setting',2);">
					
					<p class="w_b_f_1">设置</p>
					<i class="icon iconfont icon_rigth">&#xe61a;</i>
				</a>	
			</li>
		</ul>


		
		
		
	</div>
	
</div>
<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php echo $this->fetch('./inc/footer.html'); ?>
<?php endif; ?>



