<?php 
    require TEMPLATE_ROOT . '/header.php';
?>   
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="page-header">
          <h5><small> Tips : 用户充值中心，充值后可查询付费的工具，我们秉承搜完即走的理念，每次最多充值2元。</small></h5>
       </div>
       <div class="container">
       <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">充值金额</label>
            <div class="col-sm-10">
              <font color="red"><strong>2&nbsp;&nbsp;RMB</strong></font>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">微信二维码充值</label>
            <div class="col-sm-10">
              <?php 
              require_once ROOT_PATH . "lib/wxpay/lib/WxPay.Api.php";
              require_once ROOT_PATH . "lib/wxpay/example/WxPay.NativePay.php";
              $notify = new NativePay();
              // s means search
              $product_id = date("mdHis");
              $order_num = 100 . rand(100, 999) . 'S' . $product_id . $_SESSION['user_id'];
              $input = new WxPayUnifiedOrder();
              $input->SetBody("有技术的便民查询工具");
              $input->SetAttach("信息来源可以考证");
              $input->SetOut_trade_no($order_num);
              $input->SetTotal_fee("20");
              $input->SetTime_start(date("YmdHis"));
              $input->SetTime_expire(date("YmdHis", time() + 600));
//               $input->SetGoods_tag("test");
              $input->SetNotify_url("http://www.joinear.com/call_money_back");
              $input->SetTrade_type("NATIVE");
              $input->SetProduct_id($product_id);
              $result = $notify->GetPayUrl($input);
              $code_url = $result["code_url"];
              ?>
              <img alt="微信支付2元" src="/qrcode?data=<?php echo urlencode($code_url);?>" style="width:300px;height:300px;"/>
            </div>
            <div>支付成功后，页面不刷新时，请手动刷新，看账号变化</div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input type="hidden" id="csrf_name" name="<?=$csrf_name_key?>" value="<?=$csrf_name?>"/>
              <input type="hidden" id="csrf_value" name="<?=$csrf_value_key?>" value="<?=$csrf_value?>"/>
            </div>
          </div>
        </form>
        </div>
	</div>
  </main>
  <?php 
  require TEMPLATE_ROOT . '/footer.php';
  ?>
  <script type="text/javascript">
  $(document).ready(function(){
	  var i = 0;
	  setInterval(function(){
		// 每隔5秒执行一次
		if (i == 60) {
			window.location.reload();
		}
		csrf_name_key = $("#csrf_name").attr('name');
		csrf_value_key = $("#csrf_value").attr('name');
		csrf_name = $("#csrf_name").attr('value');
		csrf_value = $("#csrf_value").attr('value');
		$.ajax({
			url : '/get_wxpay?order_num=<?php echo $order_num;?>',
			type:'get',
			dataType: "json",
			headers: {
	               'X-CSRF-Token': {
	            	   csrf_name_key: csrf_name,
	            	   csrf_value_key: csrf_value,
	               }
	           },
	         success: function(msg) {
					msg = eval("(" + msg +")");
					if (msg['status'] == 1) {
						window.location.href='/pay_success';
					}
		         }
			});
		i ++;
		
	  }, 5000);
  });
  </script>
</body>
</html>