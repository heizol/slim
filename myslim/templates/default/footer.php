<!-- footer -->
  <footer class="footer hidden-print" id="footer">
   <div class="container">
    <div class="row">
     <div id="about" class="footer-about col-md-5 col-sm-12">
      <h4>关于 我们</h4>
      <p>我们是一家技术公司（上海聚念信息技术有限公司），以开发软件为主，我们在开发软件的过程发现有许多无法轻易查询的市场信息，或者需要花费大量精力得到的却不是想要的......</p>
      <p>所以我们推出有技术含量的便民查询工具，为同样有此困惑的客户服务。</p>
      <p>反馈或建议请发送邮件至：4165582@qq.com</p>
     </div>
     <div class="footer-links col-md-2 col-sm-12">
      <h4>友情链接</h4>
      <ul class="list-unstyled">
       <li><a target="_blank" href="javascript:;">中国数据研究中心</a></li>
       <li><a target="_blank" href="javascript:;">腾讯数据中心</a></li>
       <li><a target="_blank" href="javascript:;">百度数据中心</a></li>
      </ul>
     </div>
     <div class="footer-techs col-md-3 col-sm-12">
      <h4>我们是技术型公司</h4>
      <ul class="list-unstyled list-inline">
       <li>如果你需要开发团队(人力外包)，或者需要erp开发、电商开发、O2O平台、分销系统、快手系统、H5等开发，可以联系我们：4165582@qq.com</li>
      </ul>
     </div>
     <div class="footer-sponsors col-md-2 col-sm-12">
      <h4>赞助</h4>
      <p></p>
     </div>
    </div>
   </div>
   <div class="copy-right">
    <span>&copy; 2013-2016</span> 
    <a target="_blank" href="http://www.miibeian.gov.cn/">沪ICP备16013022号</a> 
   </div>
  </footer>
    <script src="//cdn.bootcss.com/jquery/3.0.0/jquery.min.js"></script>
	<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="//cdn.bootcss.com/geopattern/1.2.3/js/geopattern.min.js"></script>
	<script src="//cdn.bootcss.com/zeroclipboard/2.2.0/ZeroClipboard.min.js"></script>
	<script src="//cdn.bootcss.com/localforage/1.4.2/localforage.min.js"></script>
	<script src="//cdn.bootcss.com/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
	<script src="//cdn.bootcss.com/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.zh-CN.min.js"></script>
	<script src="/js/site.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#datepicker').datepicker({format:"yyyy-mm-dd", lang:"zh-CN"});
		$(".site-header").attr("style", "");
		package_num = $('a[class="package list-group-item"]').length;
		if (package_num == 0) {
			package_num = 8;
		}
		$("#get_tools_num").html(package_num);
	});
	</script>