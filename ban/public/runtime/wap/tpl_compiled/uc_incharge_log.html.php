<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php echo $this->fetch('./inc/header.html'); ?>	
<div class="page" id='<?php echo $this->_var['data']['act']; ?>'>
<?php
	$this->_var['back_url'] = wap_url("index","uc_center#index");
	$this->_var['back_page'] = "#uc_center";
	$this->_var['back_epage'] = $_REQUEST['epage']=="" ? "#uc_center" : "#".$_REQUEST['epage'];
?>
<?php echo $this->fetch('./inc/title.html'); ?>
<div class="content infinite-scroll pull-to-refresh-content"  data-distance="<?php echo $this->_var['data']['rs_count']; ?>"  all_page="<?php echo $this->_var['data']['page']['page_total']; ?>" ajaxurl="<?php
echo parse_wap_url_tag("u:index|uc_incharge_log#index|"."".""); 
?>">
<!-- 这里是页面内容区 -->
<link rel="stylesheet" type="text/css" href="<?php 
$k = array (
  'name' => 'parse_css',
  'v' => $this->_var['pagecss'],
);
echo $k['name']($k['v']);
?>" />
<!--充值日志-->		
<ul class="Money_Log invest">
	<?php endif; ?>			
 <!-- 默认的下拉刷新层 -->
<div class="pull-to-refresh-layer" all_page="<?php echo $this->_var['data']['page']['page_total']; ?>" >
        <div class="preloader"></div>
        <div class="pull-to-refresh-arrow"></div>
    </div>
<?php $_from = $this->_var['data']['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>		
		<li class="w_b">
			<div class="w_b_f_1 left_box">
				<p>
					<span class="name">借款编号</span>
					<span class="con"><?php echo $this->_var['item']['notice_sn']; ?></span>
				</p>
				<p>
					<span class="name">付款方式</span>
					<span class="con"><?php echo $this->_var['item']['name']; ?></span>
				</p>
				<p style=" margin-bottom:0;">
					<span class="name">下单时间</span>
					<span class="con"><?php echo $this->_var['item']['create_time_format']; ?></span>
				</p>
				<div class="my_bor"></div>
			</div>
			<div class="right_box ">
				<p class="name">充值金额</p>
				<p class="con"><span class="num"><?php echo $this->_var['item']['money_format']; ?></span><i></i></p>
				
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






