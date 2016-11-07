<?php 
    require TEMPLATE_ROOT . '/header.php';
?>   
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="page-header">
          <h5><small> Tips : 查询车辆故障码，包括故障位置、故障描述、造成影响、和解决建议。(本工具需收取：<font color="red">0.5 元</font>)</small></h5>
       </div>
       <div class="container">
       <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Code码</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="code" placeholder="如：P0108">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input type="hidden" id="csrf_name" name="<?=$csrf_name_key?>" value="<?=$csrf_name?>"/>
              <input type="hidden" id="csrf_value" name="<?=$csrf_value_key?>" value="<?=$csrf_value?>"/>
              <button type="button" id ="search" class="btn btn-default">查询</button>
            </div>
          </div>
        </form>
        </div>
        <div class="container show_result" style="display:none;color:red;font-size:12px">
        	<div class="jumbotron">
            	<h4 id="show_ip"></h4>
            	<p><span>故障类型:</span><span id="show_1"></span></p>
            	<p><span>故障描述:</span><span id="show_2"></span></p>
            	<p><span>出现结果:</span><span id="show_3"></span></p>
            	<p><span>解决建议:</span><span id="show_4"></span></p>
        	</div>
        </div>
	</div>
  </main>
  <?php 
  require TEMPLATE_ROOT . '/footer.php';
  ?>
  <script type="text/javascript">
	$(document).ready(function(){
		$("#search").click(function() {
			$("#ip").attr("readonly", true);
			csrf_name_key = $("#csrf_name").attr('name');
			csrf_value_key = $("#csrf_value").attr('name');
			csrf_name = $("#csrf_name").attr('value');
			csrf_value = $("#csrf_value").attr('value');
			if (csrf_name == '' || csrf_value == '') {
				alert('非法提交，刷新重试');
				return false;
			}
			code = $.trim($("#code").val());
			if (content == '') {
				alert("请输入故障Code码");
				return false;
			} else {
				$.ajax({
						url: "/list/car_bugcode",
						method: "post",
						data: "code=" + code,
						dataType: "json",
						headers: {
				               'X-CSRF-Token': {
				            	   csrf_name_key: csrf_name,
				            	   csrf_value_key: csrf_value,
				               }
				           },
						success: function(msg) {
							result = msg;//eval("(" + msg + ")");
							console.log(result);
							if (result['result'] == -1) {
								alert(result['msg']);
								return false;
							} else {
								$(".show_result").show();
								code = $("#code").val();
								$("#show_ip").html("[" + code + "] 查询结果如下：");
								if (result['msg']['type']) {
									$("#show_1").html(result['msg']['type']);
								}
								if (result['msg']['description']) {
									$("#show_2").html(result['msg']['description']);
								}
								if (result['msg']['aftermath']) {
									$("#show_3").html(result['msg']['aftermath']);
								}
								if (result['msg']['remind']) {
									$("#show_4").html(result['msg']['remind']);
								}
							}
						},
					    error: function(e)  {
						    if (e.status == 400) {
								alert(e.responseText);
								window.location.reload();
								return false;
							}
						}
					});
			}
		});
	});
	</script>
</body>
</html>