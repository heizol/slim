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
              <button type="button" class="btn btn-default btn-lg" onclick="javascript:window.location.href='/member/add_money_pay?money=50'">0.5元</button>
              &nbsp;&nbsp;
              <button type="button" class="btn btn-default btn-lg" onclick="javascript:window.location.href='/member/add_money_pay?money=100'">1元</button>
              &nbsp;&nbsp;
              <button type="button" class="btn btn-default btn-lg" onclick="javascript:window.location.href='/member/add_money_pay?money=200'">2元</button>
              &nbsp;&nbsp;
              <button type="button" class="btn btn-default btn-lg" onclick="javascript:window.location.href='/member/add_money_pay?money=300'">3元</button>
              &nbsp;&nbsp;
              <button type="button" class="btn btn-default btn-lg" onclick="javascript:window.location.href='/member/add_money_pay?money=500'">5元</button>
              &nbsp;&nbsp;
              <button type="button" class="btn btn-default btn-lg" onclick="javascript:window.location.href='/member/add_money_pay?money=1000'">10元</button>
            </div>
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
					// msg = eval("(" + msg +")");
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
