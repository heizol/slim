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
            <label id="inputEmail3" class="col-sm-2 control-label">个人姓名</label>
            <div class="col-sm-10">
            <input type="text" class="form-control" id="name" placeholder="" value="">
            <span><small>请写全</small></span>
            </div>
          </div>
          <div class="form-group">
            <label id="inputEmail4" class="col-sm-2 control-label">身份证</label>
            <div class="col-sm-10">
            <input type="text" class="form-control" id="number" placeholder="" value="">
            <span><small>请写全</small></span>
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
            	<div id="show_content">
            		
            	</div>
        	</div>
        </div>
	</div>
  </main>
  <?php 
  require TEMPLATE_ROOT . '/footer.php';
  ?>
  <script type="text/javascript">
	$(document).ready(function(){
		$("#s_type").change(function(){
			_val = $(this).val();
			if (_val == 'person') {
				$("#inputEmail3").html("个人姓名");
				$("#inputEmail4").html("身份证");
			} else if (_val == 'company') {
				$("#inputEmail3").html("企业名称");
				$("#inputEmail4").html("营业执照编码");
			} else {
				$("#inputEmail3").html("个人姓名");
				$("#inputEmail4").html("身份证");
			}
		});
		$("#search").click(function() {
			csrf_name_key = $("#csrf_name").attr('name');
			csrf_value_key = $("#csrf_value").attr('name');
			csrf_name = $("#csrf_name").attr('value');
			csrf_value = $("#csrf_value").attr('value');
			if (csrf_name == '' || csrf_value == '') {
				alert('非法提交，刷新重试');
				return false;
			}
			s_type = $("#s_type").val();
			name = $.trim($("#name").val());
			number = $.trim($("#number").val());
			if (name == "" || number == "") {
				input_3 = $("#inputEmail3").html();
				input_4 = $("#inputEmail4").html();
				alert( input_3 + "或者" ＋ input_4 + "不能为空");
				return false;
			} else { 
				$.ajax({
						url: "/list/black_man",
						method: "post",
						data: "s_type=" +  s_type + '&name=' + name + '&number= ' + number,
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
								$("#show_ip").html("[" + $("#name").val() + "] 查询结果如下：");
								
								$.each(result['data'], function(k, v){
										_html = "";
										if (v['duty']) {
											_html += "<p>法律文书: "+ v['duty'] +"</p>";
										}
										if (v['disrupt_type']) {
											_html += "<p>被执行人行为: "+ v['disrupt_type'] +"</p>";
										}
										if (v['code']) {
											_html += "<p>案号: "+ v['code'] +"</p>";
										}
										if (v['sex']) {
											_html += "<p>性别: "+ v['sex'] +"</p>";
										}
										if (v['pub_time']) {
											_html += "<p>发布时间: "+ v['pub_time'] +"</p>";
										}
										if (v['court']) {
											_html += "<p>执行法院: "+ v['court'] +"</p>";
										}
										if (v['name']) {
											_html += "<p>姓名: "+ v['name'] +"</p>";
										}
										if (v['area']) {
											_html += "<p>省份: "+ v['area'] +"</p>";
										}
										if (v['age']) {
											_html += "<p>年龄: "+ v['age'] +"</p>";
										}
										if (v['performance']) {
											_html += "<p>履行情况: "+ v['performance'] +"</p>";
										}
										_html += "<p></p>";
										$("#show_content").html(_html);
									});
								
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