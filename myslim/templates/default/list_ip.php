<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="width=device-width,initial-scale=1" name="viewport">
	<title>有技术的便民查询工具</title>
	<meta content="便民查询工具，有技术的查询...." name="description">
	<meta content="" name="keywords">
	<meta content="Bootstrap中文网" name="author">
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="http://cdn.bootcss.com/font-awesome/4.3.0/css/font-awesome.css">
	<link rel="stylesheet" href="http://www.bootcdn.cn/assets/css/site.min.css">
	<!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!--[if IE 9]>
    <script src="http://cdn.bootcss.com/geopattern/1.2.3/js/base64.min.js"></script>
    <script src="http://cdn.bootcss.com/geopattern/1.2.3/js/typedarray.min.js"></script>
    <![endif]-->
    <link href="http://www.bootcdn.cn/assets/ico/apple-touch-icon-144-precomposed.png" sizes="144x144" rel="apple-touch-icon-precomposed">
    <link href="http://www.bootcdn.cn/assets/ico/favicon.ico" rel="shortcut icon">
</head>
<body class="home-template">
	<!-- banner search -->
	<header class="site-header jumbotron" style="background-image:none;">
	   <div class="site-nav">
	    <a href="#about">登录</a> | <a href="#about">我的订单</a>
	   </div>
	   <div class="container">
	    <div class="row">
	     <div class="col-xs-12">
	      <h1>有技术的便民查询工具</h1>
	      <p>IP地址查询</p>
	     </div>
	    </div>
	   </div>
   </header>
   
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="page-header">
          <h5><small> Tips : 获取IP地址对应的省、市、区以及运营商名称，每天更新IP地址库。</small></h5>
       </div>
       <div class="container">
       <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">IP地址</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="ip" placeholder="如：127.0.0.1" value="127.0.0.1">
              <span><small>目前支持ipv4,如果需要ipv6，请联系我们...</small></span>
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
            	<p><span>国家：</span><span id="show_country"></span></p>
            	<p><span>省份：</span><span id="show_province"></span></p>
            	<p><span>城市：</span><span id="show_city"></span></p>
            	<p><span>坐标：</span><span id="show_district"></span></p>
            	<p><span>服务商／动态IP：</span><span id="show_carrier"></span></p>
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