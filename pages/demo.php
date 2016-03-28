<?php 
/*
	template name: 网站演示
*/
$pid = $_GET['pid'];
$values = get_post_custom_values('demo',$pid);
if(empty($values)){
	Header('Location:/');
}else{
	foreach($values as $value){
		$theCode = $value;
	}
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8" />
<title>网站演示：<?php echo get_the_title($pid); ?> - 君子不器</title>
<style type="text/css">
	html body{color:#eee;font-family:Verdana,Helvetica,Arial,sans-serif;font-size:10px;height:100%;margin:0;overflow:hidden;padding:0;width:100%;}
	#header-bar{background-color: #000;) repeat-x;font-size:10px;height:30px;line-height:30px;z-index:100;margin:0;padding:0;}
	#header-bar a.site-loopback{background-position:left top;background-repeat:no-repeat;display:block;float:right;margin-left:-10px;text-indent:-9999px;}
	#header-bar .preview-logo{height:30px;width:118px;margin-right:30px;}
	#header-bar p.meta-data{float:left;margin:0;padding:0;}
	#header-bar p.meta-data p{display:inline;margin:0;}
	#header-bar p.meta-data a{color:#e6f6f6;text-decoration:none;}
	#header-bar p.meta-data a.back{border-left:1px solid #4575d4;margin-left:10px;padding-left:15px;}
	#header-bar p.meta-data a:hover,#header-bar p.meta-data a.activated{color:#FFFFFF;}
	#header-bar div.close-header{float:left;height:29px;margin-left:15px;width:30px;}
	#header-bar div.close-header a#close-button{background-color: #000;background-repeat:no-repeat;border:1px solid #4575d4;display:block;height:12px;margin:9px auto 0;text-indent:-9999px;width:12px;overflow:hidden;}
	#header-bar div.close-header a#close-button:hover,#header-bar div.close-header a#close-button.activated{background-position:0 -12px;}
	#header-bar span.preview{color:#D2D1D0;display:none;font-family:MgOpen Modata,Tahoma,Geneva;font-size:13px;letter-spacing:1px;margin-left:10px;padding-left:20px;text-decoration:none;}
	#preview-frame{background-color:#FFFFFF;width:100%;}
	
</style>

<script src="http://apps.bdimg.com/libs/jquery/1.7.2/jquery.min.js"></script> 

<script type="text/javascript">
      var calcHeight = function() {
        var headerDimensions = $('#header-bar').height();
        $('#preview-frame').height($(window).height() - headerDimensions);
      }
      
      $(document).ready(function() {
        calcHeight();
        $('#header-bar a.close').mouseover(function() {
          $('#header-bar a.close').addClass('activated');
        }).mouseout(function() {
          $('#header-bar a.close').removeClass('activated');
        });
      });
      
      $(window).resize(function() {
        calcHeight();
      }).load(function() {
        calcHeight();
      });
</script>
</head>
<body>
   <div id="header-bar">
     <div class="close-header">
       <script type="text/javascript">document.write("<a id=\"close-button\" title=\"关闭工具条\" class=\"close\" href=\"<?php echo $theCode; ?>/\">X</a>");</script>
     </div>
     <p class="meta-data">
       <script type="text/javascript">document.write("<a target=\"_blank\" class=\"close\" href=\"<?php echo $theCode; ?>\">移除顶部</a>");</script> <a class="back" href="<?php echo get_permalink($pid); ?>">&laquo; 返回原文：<?php echo get_the_title($pid); ?></a>&laquo; <a class="back" href="http://junzibuqi.com/">返回君子不器（Junzibuqi.Com）首页</a>
     
</p>

   
   </div>

<script type="text/javascript">
document.write("<iframe id=\"preview-frame\" src=\"<?php echo $theCode; ?>\" name=\"preview-frame\" frameborder=\"0\" noresize=\"noresize\" security=\"restricted\" sandbox=\"\"></iframe>");
</script>



</body>
</html>
