<?php 
    require TEMPLATE_ROOT . '/header.php';
?>
<link href="//cdn.bootcss.com/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet">
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="page-header">
          <h5><small> Tips : 提供北京、天津、杭州、成都、兰州、贵阳、南昌、长春、哈尔滨、武汉、上海、深圳等城市的车辆限行时间、区域、尾号等查询。</small></h5>
       </div>
       <div class="container">
       <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">城市</label>
            <div class="col-sm-10">
              <select class="form-control" id="city_name" name="city_name">
                  <option value="beijing">北京</option>
                  <option value="shanghai">上海</option>
                  <option value="tianjin">天津</option>
                  <option value="hangzhou">杭州</option>
                  <option value="chengdu">成都</option>
                  <option value="lanzhou">兰州</option>
                  <option value="guiyang">贵阳</option>
                  <option value=nanchang>南昌</option>
                  <option value="changchun">长春</option>
                  <option value="haerbin">哈尔滨</option>
                  <option value="wuhan">武汉</option>
                  <option value="shenzhen">深圳</option>
            </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">查询日期</label>
            <div class="col-sm-10">
                <div class="input-group date">
                    <input type="text" class="form-control" name="today_time" readonly value="<?php echo date("Y-m-d");?>" id="datepicker">
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>
                </div>
              <span><small>默认查询今天的</small></span>
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
            	<p><span>城市：</span><span id="show_country"></span></p>
            	<p><span>日期：</span><span id="show_province"></span></p>
            	<p><span>星期：</span><span id="show_city"></span></p>
            	<p><span>限行时间：</span><span id="show_district"></span></p>
            	<p><span>说明：</span><span id="show_carrier"></span></p>
        	</div>
        </div>
	</div>
  </main>
  <?php 
  require TEMPLATE_ROOT . '/footer.php';
  ?>
  <script type="text/javascript">
    $(document).ready(function() {
        $("#search").click(function() {
        	city_name = $("#city_name").val();
        	today_time = $("input[name=today_time]").val();
        	csrf_name_key = $("#csrf_name").attr('name');
			csrf_value_key = $("#csrf_value").attr('name');
			csrf_name = $("#csrf_name").attr('value');
			csrf_value = $("#csrf_value").attr('value');
			if (csrf_name == '' || csrf_value == '') {
				alert('非法提交，刷新重试');
				return false;
			}
			if (today_time == '') {
				alert('请选择查询时间');
				return false;
			}
        	$.ajax({
        		url: "/list/car_number",
				method: "post",
				data: "city_name=" +  city_name + '&today_time=' + today_time,
				dataType: "json",
				headers: {
		               'X-CSRF-Token': {
		            	   csrf_name_key: csrf_name,
		            	   csrf_value_key: csrf_value,
		               }
		           },
		        success: function(msg) {
		        	if (msg['status'] != 0) {
						alert(msg['msg']);
						return false;
					} else {
						result = msg['result'];
						$(".show_result").show();
						$("#show_ip").html("[" + result['cityname'] + "] 查询结果如下：");
						$("#show_country").html(result['cityname']);
						$("#show_province").html(result['date']);
						$("#show_city").html(result['week']);
						$("#show_district").html(result['time']);
						result_html = result['area'] + "&nbsp;&nbsp;" + result['summary'] + "&nbsp;&nbsp;" + result['numberrule'] + "&nbsp;&nbsp;" + result['number'];
						$("#show_carrier").html(result_html);
					}
			    }
            });
        });
    });
  </script>
</body>
</html>