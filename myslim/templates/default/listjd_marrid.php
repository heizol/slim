<?php 
    require TEMPLATE_ROOT . '/header.php';
?>   
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="page-header">
          <h5><small> Tips : 查询婚姻状态时，如果因用户输入数据错误而导致数据不准确，本工具不退查询费用。(本工具需收取：<font color="red">5 元</font>)</small></h5>
       </div>
       <div class="container">
       <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="name" placeholder="如：张大帅">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">身份证号</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="numberic" placeholder="如：320921690507060177">
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
            	<p><span>是否已婚：</span><span id="show_country"></span></p>
            	<p><span>学历：</span><span id="show_province"></span></p>
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
			name = $.trim($("#name").val());
			numberic = $.trim($("#numberic").val());
			if (name == '' || numberic == '') {
				alert("姓名和身份证号不能为空");
				return false;
			} else if (numberic.length != 18  &&  numberic.length != 15) {
				alert("身份证号长度只能是15位或者18位");
				return false;
			}else {
				$.ajax({
						url: "/list_jd/s_marrid",
						method: "post",
						data: "name=" +  name + '&numberic='+numberic,
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
								if (name != '') {
									$("#show_ip").html("[" + name + "] 查询结果如下：");
								} else if (numberic != '') {
									$("#show_ip").html("[" + numberic + "] 查询结果如下：");
								}
								info = result['msg'];
								$("#show_country").html(info['name']);
								$("#show_province").html(info['price']/100);
								$("#show_city").html(info['description']);
								$("#show_district").html(info['tag']);
								$("#show_carrier").html(info['type']);
								// 生产公司
								codes = '';
								if (info['codes']) {
									$.each(info['codes'], function(k, v) {
										codes += '条形码：' + v['code'] + '('+ v['factory'] +')<br/>';
									});
									$("#show_codes").html(codes);
								}
								
								$("#show_message").html(info['message']);
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