<?php 
    require TEMPLATE_ROOT . '/header.php';
?>   
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="page-header">
          <h5><small> Tips : 国内最大的信用黑名单数据库提供企业和个人失信、网贷逾期黑名单查询(超过一千万条信贷失信记录)。(本工具需收取：<font color="red">3 元</font>)</small></h5>
       </div>
       <div class="container">
       <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">类型</label>
            <div class="col-sm-10">
              <select class="form-control" id="s_type" name="s_type">
              	<option value="person">个人</option>
              	<option value="company">企业</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label id="inputEmail3" class="col-sm-2 control-label">名称</label>
            <div class="col-sm-10">
            <input type="text" class="form-control" id="name" placeholder="如：聚念" value="">
            <span><small>请写全名称</small></span>
            </div>
          </div>
          <div class="form-group">
            <label id="inputEmail4" class="col-sm-2 control-label">身份证/营业执照编号</label>
            <div class="col-sm-10">
            <input type="text" class="form-control" id="name" placeholder="如：聚念" value="">
            <span><small>请写全代码</small></span>
            </div>
          </div>
        </form>
        </div>
        <div class="container show_result" style="display:none;color:red;font-size:12px">
        	<div class="jumbotron">
            	<h4 id="show_ip"></h4>
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
			ip = $.trim($("#ip").val());
			reg_ip = /^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/;
			flag_ip = ip.match(reg_ip);
			if (flag_ip != undefined && flag_ip != '') {
				$.ajax({
						url: "/list/ip",
						method: "post",
						data: "ip_val=" +  ip,
						dataType: "json",
						headers: {
				               'X-CSRF-Token': {
				            	   csrf_name_key: csrf_name,
				            	   csrf_value_key: csrf_value,
				               }
				           },
						success: function(msg) {
							$("#ip").attr("readonly", false);
							result = msg;//eval("(" + msg + ")");
							console.log(result);
							if (result['result'] == -1) {
								alert(result['msg']);
								return false;
							} else {
								$(".show_result").show();
								$("#show_ip").html("[" + $("#ip").val() + "] 查询结果如下：");
								$("#show_country").html(result['country']);
								$("#show_province").html(result['province']);
								$("#show_city").html(result['city']);
								$("#show_district").html(result['district']);
								$("#show_carrier").html(result['carrier']);
							}
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
				$("#ip").attr("readonly", false);
				alert("请输入正确的IP");
				return false;
			}
		});
	});
	</script>
</body>
</html>