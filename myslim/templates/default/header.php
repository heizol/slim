<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="width=device-width,initial-scale=1" name="viewport">
	<title><?php echo $title;?></title>
	<meta content="<?php echo $description;?>" name="description">
	<meta content="<?php echo $keywords;?>" name="keywords">
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
	<header class="site-header jumbotron">
	   <div class="site-nav">
	    <a href="/">首页</a>&nbsp;&nbsp;|&nbsp;&nbsp;
	    <?php 
	       if (empty($_SESSION['user_id'])) {
	    ?>
	    <a href="/member/login">登录<font color="#FF7F00">(充值：0)</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/member/my_order">我的订单</a>
	    <?php 
	       } else {
	    ?>
	    <a href="/member/add_money">VIP用户<font color="#FF7F00">(充值：<?php echo $_SESSION['my_money'];?>)[我要充值]</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/member/my_order">我的订单</a>
	    <?php 
	       }
	    ?>
	   </div>
	   <div class="container">
	    <div class="row">
	     <div class="col-xs-12">
	      <h1>有技术的便民查询工具</h1>
	      <p><?php echo $title;?></p>
	     </div>
	    </div>
	   </div>
   </header>