<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $this->_var['data']['program_title']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=0,minimum-scale=0.5">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <?php
   	 	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/js/suimobile/sm.min.css";
   	 	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/js/suimobile/sm-extend.min.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/public.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/init.css";
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/deals.css";   
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/deal.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/css3-3d-circle-progress.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/datepicker.css";
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/lynn.css";    
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/doexchange.css";	
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/integral_mall.css";
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/goods_exchange.css";
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/goods_information.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_learn.css";
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/licai_deal_detail.css";
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/datepicker.css";
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/lynn.css";
    	$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/Log_recharge.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/deal_mobile.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/Repayment.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/Statistics.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/cart_index.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/youhui_comment_list.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/user_addr_list.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/pay_order_index.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/search.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_add_bank.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_bank.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_to_transfer.css	";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_carry_money_log.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_save_carry.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_center.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_collect.css	";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_incharge.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_voucher.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_money_log.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_quick_refund.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_account_log.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/login.css";	
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_collect.css";
		$this->_var['parent_pagecss'][] = $this->_var['TMPL_REAL']."/css/uc_transfer.css";
	?>
	<link rel="stylesheet" type="text/css" href="<?php 
$k = array (
  'name' => 'parse_css',
  'v' => $this->_var['parent_pagecss'],
);
echo $k['name']($k['v']);
?>" />
    <script type='text/javascript' src='./js/suimobile/zepto.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='./js/fastclick.js' charset='utf-8'></script>

	<link rel="stylesheet" type="text/css" href="./css/font-awesome-4.2.0/css/font-awesome.min.css" />
			 
    <script type="text/javascript">
		var APP_ROOT = '<?php echo $this->_var['APP_ROOT']; ?>';
		var WAP_PATH = '<?php echo $this->_var['WAP_ROOT']; ?>';
		var APP_ROOT_ORA = '<?php echo $this->_var['PC_URL']; ?>';
		var SITE_DOMAIN = "<?php echo $this->_var['data']['site_domain']; ?>";
		var isapp = "<?php 
$k = array (
  'name' => 'intval',
  'f' => $_REQUEST['app'],
);
echo $k['name']($k['f']);
?>"
		var __HASH_KEY__ = "<?php 
$k = array (
  'name' => 'get_hash_key',
);
echo $this->_hash . $k['name'] . '|' . base64_encode(serialize($k)) . $this->_hash;
?>";
		 $.config = {
		    swipePanelOnlyClose: true
		  }
		  
		  if ('addEventListener' in document) {
			    document.addEventListener('DOMContentLoaded', function() {
			        FastClick.attach(document.body);
			    }, false);
			}
	</script> 	
	</head>
	
<body>
