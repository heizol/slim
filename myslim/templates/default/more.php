<?php 
    require TEMPLATE_ROOT . '/header.php';
?>   
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="page-header">
          <h5><small> Tips : 在留言的详情里，请写明工具的作用，工具如何使用，工具使用的人群即可，并留下您的联系方式。</small></h5>
       </div>
       <div class="container">
       <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">请留言：</label>
            <div class="col-sm-10">
            	<textarea class="form-control" rows="10" name="message" id="message" placeholder="请留言....."></textarea>
            	<span><small><font color="red">不要忘记在输入框内留下您的联系方式</font></small></span>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input type="hidden" id="csrf_name" name="<?=$csrf_name_key?>" value="<?=$csrf_name?>"/>
              <input type="hidden" id="csrf_value" name="<?=$csrf_value_key?>" value="<?=$csrf_value?>"/>
              <button type="button" id ="search" class="btn btn-default">提交</button>
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
		$("#search").click(function() {
			csrf_name_key = $("#csrf_name").attr('name');
			csrf_value_key = $("#csrf_value").attr('name');
			csrf_name = $("#csrf_name").attr('value');
			csrf_value = $("#csrf_value").attr('value');
			if (csrf_name == '' || csrf_value == '') {
				alert('非法提交，刷新重试');
				return false;
			}
			message = $.trim($("#message").val();
			if (message != '') {
				$.ajax({
						url: "/more",
						method: "post",
						data: "message=" +  message,
						dataType: "json",
						headers: {
				               'X-CSRF-Token': {
				            	   csrf_name_key: csrf_name,
				            	   csrf_value_key: csrf_value,
				               }
				           },
						success: function(msg) {
							result = msg; //eval("(" + msg + ")");
							alert(result['msg']);
							return false;
						},
					    error: function(e)  {
					    	$("#ip").attr("readonly", false);
						    if (e.status == 400) {
								alert(e.responseText);
								window.location.reload();
								return false;
							}
						}
					});
			} else {
				alert("请输入留言内容");
				return false;
			}
		});
	});
	</script>
</body>
</html>