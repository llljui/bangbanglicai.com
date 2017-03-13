</div>
</div>

<?php echo $this->fetch('./inc/left.html'); ?>
<?php if ($this->_var['signPackage']): ?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  wx.config({
      debug: false,
      appId: '<?php echo $this->_var['signPackage']['appId']; ?>',
      timestamp: <?php echo $this->_var['signPackage']['timestamp']; ?>,
      nonceStr: '<?php echo $this->_var['signPackage']['nonceStr']; ?>',
      signature: '<?php echo $this->_var['signPackage']['signature']; ?>',
      jsApiList: [
        'checkJsApi',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'translateVoice',
        'startRecord',
        'stopRecord',
        'onRecordEnd',
        'playVoice',
        'pauseVoice',
        'stopVoice',
        'uploadVoice',
        'downloadVoice',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'getNetworkType',
        'openLocation',
        'getLocation',
        'hideOptionMenu',
        'showOptionMenu',
        'closeWindow',
        'scanQRCode',
        'chooseWXPay',
        'openProductSpecificView',
        'addCard',
        'chooseCard',
        'openCard'
      ]
  });
  var shareData = {
		//title: '<?php echo $this->_var['page_title']; ?>', // 分享标题
		link: '<?php echo $this->_var['wx_url']; ?>', // 分享链接
	   	// imgUrl: '', // 分享图标
	   	
		success: function () { 
			// 用户确认分享后执行的回调函数
		},
		cancel: function () { 
			// 用户取消分享后执行的回调函数
		}
	}
   wx.ready(function () {
    // 在这里调用 API
		wx.onMenuShareTimeline(shareData);
		wx.onMenuShareAppMessage(shareData);
		wx.onMenuShareQQ({
		  title: '<?php echo $this->_var['page_title']; ?>',
		  desc: '',
		  link: '<?php echo $this->_var['wx_url']; ?>',
		  imgUrl: '',
		  trigger: function (res) {
			//$.alert('用户点击分享到QQ');
		  },
		  complete: function (res) {
			//$.alert(JSON.stringify(res));
		  },
		  success: function (res) {
			//$.alert('已分享');
		  },
		  cancel: function (res) {
			//$.alert('已取消');
		  },
		  fail: function (res) {
			//$.alert(JSON.stringify(res));
		  }
		});
		wx.onMenuShareWeibo(shareData);
  });
</script>
 <?php endif; ?>
<?php
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/suimobile/sm.min.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/suimobile/sm-extend.min.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/script.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/licai.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/deals.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/transfer.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/article.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_published_lc.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_learn.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_goods_order.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_voucher.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/integral_mall.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_address.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_carry_money_log.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_account_log.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_buyed_lc.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_redeem_lc.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_record_lc.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_expire_lc.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_borrowed.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/vip_buy.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_interestrate.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_bank.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_collect.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_transfer.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_invest.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/uc_incharge.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/public.js";
	$this->_var['common_js'][] = $this->_var['TMPL_REAL']."/js/Plugin_unit/jscharts_cr.js";
	
	
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/suimobile/sm.min.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/script.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/licai.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/deals.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/transfer.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/article.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_published_lc.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_learn.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_goods_order.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_voucher.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/integral_mall.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_address.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_carry_money_log.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_account_log.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_buyed_lc.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_redeem_lc.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_record_lc.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/licai_uc_expire_lc.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_borrowed.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/vip_buy.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_interestrate.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_bank.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_collect.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_transfer.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_invest.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/uc_incharge.js";
	$this->_var['c_common_js'][] = $this->_var['TMPL_REAL']."/js/public.js";
?>
<script type="text/javascript" src="<?php 
$k = array (
  'name' => 'parse_wap_script',
  'v' => $this->_var['common_js'],
  'c' => $this->_var['c_common_js'],
);
echo $k['name']($k['v'],$k['c']);
?>"></script>
</body>
</html>