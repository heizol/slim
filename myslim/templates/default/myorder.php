<?php 
    require TEMPLATE_ROOT . '/header.php';
?>   
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="page-header">
          <h5><small> Tips : 用户充值余消费记录。</small></h5>
       </div>
       <div class="container">
       <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>订单ID</th>
              <th>产品名称</th>
              <th>Flag</th>
              <th>金额</th>
              <th>时间</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if (!empty($count)) {
                foreach ($list as $info) {
            ?>
            <tr>
              <td><?php echo $info['id'];?></td>
              <td><?php echo $info['order_id'];?></td>
              <td><?php echo $info['product_name'];?></td>
              <td><?php echo $info['is_flag'] == 1 ? '充值' : '消费';?></td>
              <td><?php echo $info['sales'];?></td>
              <td><?php echo date("Y-m-d H:i:s", $info['add_time']);?></td>
            </tr>
            <?php 
                }
            } else {
                echo '<tr style="text-align:center;"><td colspan="6">暂无消费／充值记录</td></tr>';
            }
            ?>
        </table>
       </div>
	</div>
  </main>
  <?php 
  require TEMPLATE_ROOT . '/footer.php';
  ?>
</body>
</html>