<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="width=device-width,initial-scale=1" name="viewport">
	<title>有技术的便民查询工具</title>
	<meta content="便民查询工具，有技术的查询工具，查询出都数据都可以得到验证来源 --有技术的便民查询工具" name="description">
	<meta content="IP地址，尾号限行，车架号查询，药品信息查询，个人信用查询，企业融资查询，车辆故障码" name="keywords">
	<meta content="有技术的便民查询工具" name="author">
	<link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="/css/bootstrap/font-awesome.css">
	<link rel="stylesheet" href="/css/site.min.css">
	<!--[if lt IE 9]>
    <script src="/css/bootstrap/html5shiv.min.js"></script>
    <script src="/js/respond.min.js"></script>
    <![endif]-->
    <!--[if IE 9]>
    <script src="/js/base64.min.js"></script>
    <![endif]-->
    <link href="/images/apple-touch-icon-144-precomposed.png" sizes="144x144" rel="apple-touch-icon-precomposed">
    <link href="/images/favicon.ico" rel="shortcut icon">
</head>
<body class="home-template">
	<!-- banner search -->
	<header class="site-header jumbotron" style="background-image:none;">
	   <div class="site-nav">
	    <a href="/">首页</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	    <?php
	       if (empty($_SESSION['user_id'])) {
	    ?>
	    <a href="/member/login">登录<font color="#FF7F00">(余额：0)</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/member/my_order">我的订单</a>
	    <?php 
	       } else {
	    ?>
	    <a href="/member/add_money">VIP用户<font color="#FF7F00">(余额：<?php echo $_SESSION['my_money'];?>)[我要充值]</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/member/my_order">我的订单</a>
	    <?php 
	       }
	    ?>
	   </div>
	   <div class="container">
	    <div class="row">
	     <div class="col-xs-12">
	      <h1>有技术的便民查询工具</h1>
	      <p>优质、极速、稳定的便民查询工具<br /><span class="package-amount">共收录了 <strong id ="get_tools_num">8</strong> 个便民查询工具</span></p>
	      <form role="search" class="">
	       <div class="form-group">
	        <input type="text" placeholder="搜索工具，例如：车辆" class="form-control search clearable" /> 
	        <i class="fa x fa-close"></i>
	       </div>
	      </form>
	     </div>
	    </div>
	   </div>
   </header>
   
  <!-- main -->
  <main id="all-packages" class="packages-list-container">
	<div class="container">
		<div class="list-group packages">
			<!--  list -->
<!-- 			<a class="package list-group-item" target="_blank" data-library-name="ip" href="/list/ip"> -->
<!-- 			 <div class="row"> -->
<!-- 			    <div class="col-md-3"> -->
<!-- 			     <h4 class="package-name">IP地址查询</h4> -->
<!-- 			    </div> -->
<!-- 			    <div class="col-md-9 hidden-xs"> -->
<!-- 			     <p class="package-description">获取IP地址对应的省、市、区以及运营商名称，<font color="red">每天更新IP地址库</font>。</p> -->
<!-- 			    </div> -->
<!-- 			    <div class="package-extra-info col-md-9 col-md-offset-3 col-xs-12"> -->
<!-- 			     <span><i class="glyphicon glyphicon-usd"></i> 0.5 元</span> -->
<!-- 			    </div> -->
<!-- 			 </div> -->
<!-- 			 </a> -->
			 <a class="package list-group-item" target="_blank" data-library-name="车辆尾号限行查询" href="/list/car_number">
			 <div class="row">
			    <div class="col-md-3">
			     <h4 class="package-name">车辆尾号限行查询</h4>
			    </div>
			    <div class="col-md-9 hidden-xs">
			     <p class="package-description">提供<font color="red">北京、天津、杭州、成都、兰州、贵阳、南昌、长春、哈尔滨、武汉、上海、深圳</font>等城市的车辆限行时间、区域、尾号等查询。</p>
			    </div>
			    <div class="package-extra-info col-md-9 col-md-offset-3 col-xs-12">
			     <span><i class="glyphicon glyphicon-usd"></i> 0.5 元</span>
			    </div>
			 </div>
			 </a>
			 
			 <a class="package list-group-item" target="_blank" data-library-name="药品查询" href="/list/drugs">
			 <div class="row">
			    <div class="col-md-3">
			     <h4 class="package-name">药品信息查询</h4>
			    </div>
			    <div class="col-md-9 hidden-xs">
			     <p class="package-description">通过药品名字直接得到药品说明书、价格、生产厂家、国药准字，对药品具体信息一目了然。</p>
			    </div>
			    <div class="package-extra-info col-md-9 col-md-offset-3 col-xs-12">
			     <span><i class="glyphicon glyphicon-usd"></i> 0.5 元</span>
			    </div>
			 </div>
			 </a>
			 
			 <a class="package list-group-item" target="_blank" data-library-name="车架号查车辆的出厂配置信息" href="/list/car_unno">
			 <div class="row">
			    <div class="col-md-3">
			     <h4 class="package-name">车架号查车辆的出厂配置信息</h4>
			    </div>
			    <div class="col-md-9 hidden-xs">
			     <p class="package-description">通过VIN(车架号)得到车辆品牌、型号、年款、排量、变速箱类型、发动机型号、几门几座等</p>
			    </div>
			    <div class="package-extra-info col-md-9 col-md-offset-3 col-xs-12">
			     <span><i class="glyphicon glyphicon-usd"></i> 1 元</span>
			    </div>
			 </div>
			 </a>
			 
			 <a class="package list-group-item" target="_blank" data-library-name="企业投融资历史查询" href="/list/company">
			 <div class="row">
			    <div class="col-md-3">
			     <h4 class="package-name">企业投融资历史查询</h4>
			    </div>
			    <div class="col-md-9 hidden-xs">
			     <p class="package-description">输入企业名称，即可查询出该企业<font color="red">投资历史记录信息 或者 融资历史记录</font>。</p>
			    </div>
			    <div class="package-extra-info col-md-9 col-md-offset-3 col-xs-12">
			     <span><i class="glyphicon glyphicon-usd"></i> 3 元</span>
			    </div>
			 </div>
			 </a>
			 
			 <a class="package list-group-item" target="_blank" data-library-name="失信黑名单" href="/list/black_man">
			 <div class="row">
			    <div class="col-md-3">
			     <h4 class="package-name">失信黑名单</h4>
			    </div>
			    <div class="col-md-9 hidden-xs">
			     <p class="package-description">国内最大的信用黑名单数据库 <font color="red">提供企业</font>和<font color="red">个人失信、网贷逾期</font>黑名单查询(超过一千万条信贷失信记录)。</p>
			    </div>
			    <div class="package-extra-info col-md-9 col-md-offset-3 col-xs-12">
			     <span><i class="glyphicon glyphicon-usd"></i> 3 元</span>
			    </div>
			 </div>
			 </a>
			 
			 <a class="package list-group-item" target="_blank" data-library-name="简繁体火星文转换" href="/list/lang_change">
			 <div class="row">
			    <div class="col-md-3">
			     <h4 class="package-name">简繁体火星文转换</h4>
			    </div>
			    <div class="col-md-9 hidden-xs">
			     <p class="package-description">汉字的简体、繁体、火星文转换 。</p>
			    </div>
			    <div class="package-extra-info col-md-9 col-md-offset-3 col-xs-12">
			     <span><i class="glyphicon glyphicon-usd"></i> 免费</span>
			    </div>
			 </div>
			 </a>
			 
			 <a class="package list-group-item" target="_blank" data-library-name="车辆故障码DTC查询" href="/list/car_bugcode">
			 <div class="row">
			    <div class="col-md-3">
			     <h4 class="package-name">车辆故障码DTC查询</h4>
			    </div>
			    <div class="col-md-9 hidden-xs">
			     <p class="package-description">查询车辆故障码，包括<font color="red">故障位置、故障描述、造成影响、和解决建议</font>。</p>
			    </div>
			    <div class="package-extra-info col-md-9 col-md-offset-3 col-xs-12">
			     <span><i class="glyphicon glyphicon-usd"></i> 0.5 元</span>
			    </div>
			 </div>
			 </a>
			 
			 <!--  如果没有，可以留言给我们 -->
			 <a class="package list-group-item" target="_blank" data-library-name="更多" href="/more">
			 <div class="row">
			    <div class="col-md-3">
			     <h4 class="package-name">获取更多......</h4>
			    </div>
			    <div class="col-md-9 hidden-xs">
			     <p class="package-description">如果没有想要的工具，可以留言给我们。</p>
			    </div>
			    <div class="package-extra-info col-md-9 col-md-offset-3 col-xs-12">
			     <span><i class="glyphicon glyphicon-eye-open"></i> 269</span>
			    </div>
			 </div>
			 </a>
		</div>
	</div>
  </main>
  <!-- footer -->
  <?php 
  require TEMPLATE_ROOT . '/footer.php';
  ?>
</body>
</html>