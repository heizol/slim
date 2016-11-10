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
</body>
</html>
