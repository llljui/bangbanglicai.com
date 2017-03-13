<?php if ($_REQUEST['is_ajax'] != 1): ?>
<?php echo $this->fetch('./inc/header.html'); ?>	
<div class="page" id='<?php echo $this->_var['data']['act']; ?>'>
<?php
	$this->_var['back_url'] = wap_url("index","init#index");
	$this->_var['back_page'] = "#init";
	$this->_var['back_epage'] = $_REQUEST['epage']=="" ? "#init" : "#".$_REQUEST['epage'];
?>
<?php echo $this->fetch('./inc/title.html'); ?>
<div class="content infinite-scroll pull-to-refresh-content" data-distance="<?php echo $this->_var['data']['rs_count']; ?>"  all_page="<?php echo $this->_var['data']['page']['page_total']; ?>" ajaxurl="<?php
echo parse_wap_url_tag("u:index|licai_deals#index|"."".""); 
?>">
<!-- 这里是页面内容区 -->
<!--理财列表-->
<ul class="recommended_nav_2 invest">
<?php endif; ?>
	<!-- 默认的下拉刷新层 -->
    <div class="pull-to-refresh-layer" all_page="<?php echo $this->_var['data']['page']['page_total']; ?>" >
        <div class="preloader"></div>
        <div class="pull-to-refresh-arrow"></div>
    </div>	 
    <!--mainborder开始-->  
    <?php $_from = $this->_var['data']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list_item');$this->_foreach['list_items'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list_items']['total'] > 0):
    foreach ($_from AS $this->_var['list_item']):
        $this->_foreach['list_items']['iteration']++;
?>  
	<li class="clearfix">
		<a href="#" onclick="RouterURL('<?php
echo parse_wap_url_tag("u:index|licai_deal|"."id=".$this->_var['list_item']['id']."".""); 
?>','#licai_deal',2);" >
		<div class="title_nav public_bor">
            
            <?php if ($this->_var['list_item']['c_status'] == 0): ?><span class="state  c666666"><?php endif; ?>
            <?php if ($this->_var['list_item']['c_status'] == 1): ?><span class="state  c56b1ea"><?php endif; ?>
            <!--<span class="state  ea544a">
            <span class="state  c66d191">-->
				<?php echo $this->_var['list_item']['status_format']; ?>
			</span>
			<span class="verticalline"></span>
            <span class="name"><?php echo $this->_var['list_item']['name']; ?></span>
        </div>
		<div class="con_height">
            <div class="content_list">
                <span class='ea544a'><i class='bold'><?php 
$k = array (
  'name' => 'number_format',
  'v' => $this->_var['list_item']['average_income_rate'],
  'f' => '2',
);
echo $k['name']($k['v'],$k['f']);
?></i><i class='unit'>%</i></span>
                <span class='unit'>历史收益率</span>
                <div class="listbr"></div>
            </div>
            <div class="content_list">
                <span class=""><?php echo $this->_var['list_item']['min_money']; ?>元</span>
                <span class='unit'>起购金额</span>
                <div class="listbr"></div>
            </div>
            <div class="content_list">
                <span><?php if ($this->_var['list_item']['time_limit']): ?><?php echo $this->_var['list_item']['time_limit']; ?>个月<?php else: ?>无期限<?php endif; ?></span>
                <span class='unit'>理财期限</span>
                <div class="listbr"></div>
            </div>
            <div class="content_list">
            	<span><em class=""><?php echo $this->_var['list_item']['subscribing_amount']; ?></em>元</span>
                <span class='unit'>成交总金额</span>
            </div>
        </div>    
        </a>
        </li>
	    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
	    <?php if ($_REQUEST['is_ajax'] != 1): ?>
<!--mainborder结束-->	
</ul>

<!-- 加载提示符 -->
<div class="infinite-scroll-preloader">
  <div class="preloader">
  </div>
</div>

<?php echo $this->fetch('./inc/footer.html'); ?>
<?php endif; ?>