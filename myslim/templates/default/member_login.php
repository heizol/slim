<?php 
    require TEMPLATE_ROOT . '/header.php';
?>   
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="page-header">
          <h5><small> Tips : 只有登录的用户才能查询免费的数据；只有登录的用户且账户余额不等余0的用户才能查询付费的服务。</small></h5>
       </div>
       <div class="container">
       <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="mobile" placeholder="请输入手机号" value="">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">验证码</label>
            <div class="col-sm-4">
              <input type="text" class="form-control" id="mobile_code" placeholder="验证码" value="">
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn btn-default" id="get_code">点击获取</button>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input type="hidden" id="csrf_name" name="<?=$csrf_name_key?>" value="<?=$csrf_name?>"/>
              <input type="hidden" id="csrf_value" name="<?=$csrf_value_key?>" value="<?=$csrf_value?>"/>
              <button type="button" id ="search" class="btn btn-default">登录</button>
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
		// 验证码
		$("#get_code").click(function() {
			csrf_name_key = $("#csrf_name").attr('name');
			csrf_value_key = $("#csrf_value").attr('name');
			csrf_name = $("#csrf_name").attr('value');
			csrf_value = $("#csrf_value").attr('value');
			if (csrf_name == '' || csrf_value == '') {
				alert('非法提交，刷新重试');
				return false;
			}
			mobile = $.trim($("#mobile").val());
			reg_mobile = /^1(3|5|7|8|4){1}[0-9]{9}$/;
			flag_mobile = mobile.match(reg_mobile);
			if (flag_mobile != undefined && flag_mobile != '') {
				$.ajax({
					url: "/member/set_code",
					method: "post",
					data: "mobile=" +  mobile,
					dataType: "json",
					headers: {
			               'X-CSRF-Token': {
			            	   csrf_name_key: csrf_name,
			            	   csrf_value_key: csrf_value,
			               }
			           },
					success: function(msg) {
						if (msg['status'] == -1) {
							alert(msg['msg']);
							return false;
							} else {
								$("#get_code").html('已发送[5分钟内有效]');
								$("#get_code").attr("disabled", true);
								}
						},
					});
			} else {
				alert("请输入正确的手机号");
				return false;
			}
		});
		// 登录
		$("#search").click(function() {
			csrf_name_key = $("#csrf_name").attr('name');
			csrf_value_key = $("#csrf_value").attr('name');
			csrf_name = $("#csrf_name").attr('value');
			csrf_value = $("#csrf_value").attr('value');
			if (csrf_name == '' || csrf_value == '') {
				alert('非法提交，刷新重试');
				return false;
			}
			mobile = $.trim($("#mobile").val());
			mobile_code = $.trim($("#mobile_code").val());
			reg_mobile = /^1(3|5|7|8|4){1}[0-9]{9}$/;
			flag_mobile = mobile.match(reg_mobile);
			if (flag_mobile != undefined && flag_mobile != '' && mobile_code != '') {
				$.ajax({
						url: "/member/login",
						method: "post",
						data: "mobile=" +  mobile + '&mobile_code=' + mobile_code,
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
							if (result['status'] == -1) {
								$("#get_code").html('点击获取');
								$("#get_code").attr("disabled", false);
								alert(result['msg']);
							} else {
								alert(result['msg']);
								window.location.href = '/';
							}
							return false;
						},
					    error: function(e)  {
						    if (e.status == 400) {
								alert(e.responseText);
								window.location.reload();
								return false;
							}
						}
					});
			} else {
				alert("请输入正确的电话号码或者验证码");
				return false;
			}
		});
	});
	</script>
</body>
</html>