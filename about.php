
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Twitter Search and Map</title>
<link rel="image_src" href="http://www.rioleo.org/twitternow/small.png" />

<style>



/*     Creating a form without table    */
/*     Author= "AJAY TALWAR"            */
/*     Email- ajayslide183@gmail.com    */


*{ margin:0; padding:0;}
body{ font:100% normal Arial, Helvetica, sans-serif; background:#033d51 url(background.png) no-repeat center;}
form,input,select,textarea{margin:0; padding:0;}
div.wrapper {
	width:650px;
	margin:0 auto;
}
div.box2{
margin:0 auto;
width:100px;
float:left;
margin-right:10px;
background:#0d709e;
position:relative;
top:30px;
-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; 
padding:10px;
}
.box2 ul {
	color:#eee;
margin-top:10px;

}
.box2 ul li {
	list-style-type:none;
}
.box2 a {
	color:#eee;
}
div.box{
margin:0 auto;
width:500px;
float:left;
background:#0d709e;
position:relative;
top:30px;
-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; 
padding:10px;
}
div.box2 h1{ 
color:#FFF;
font-size:12px;
text-transform:uppercase;
padding:5px 0 5px 5px;
border-bottom:1px solid #161712;
border-top:1px solid #161712; 
}


div.box h1{ 
color:#FFF5CC;
font-size:18px;
text-transform:uppercase;
padding:5px 0 5px 5px;
border-bottom:1px solid #161712;
border-top:1px solid #161712; 
}

div.box label{
width:100%;
display: block;

padding:10px 0 10px 0;
}
div.box label span{
display: block;
color:#fff;
font-size:16px;
float:left;
width:100px;
text-align:right;
padding:5px 20px 0 0;
}

div.box input{
padding:10px 10px;
width:200px;
-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; 
color:#333;
border:1px solid #aaa;
font-size:20px;
background:#e7f5fc;

}

div.box .message{
padding:7px 7px;
width:350px;
background:#262626;
border-bottom: 1px double #171717;
border-top: 1px double #171717;
border-left:1px double #333;
border-right:1px double #333;
overflow:hidden;
height:150px;
}

div.box .button
{
margin:0 0 10px 0;
padding:4px 7px;
background:#CC0000;
border:0px;
position: relative;
top:10px;
left:382px;
width:100px;
border-bottom: 1px double #660000;
border-top: 1px double #660000;
border-left:1px double #FF0033;
border-right:1px double #FF0033;
}
#pb1 {
	display:none;
}
.one {
	color:#fff;
	background: #033d51;
	display:block;
	padding:10px;
	font-size:20px;
	width:200px;
	
	text-decoration:none;
}
.box p {
	color:#fff;
}
.box a {
	color:#eee;
}
</style>

</head>

<body>

<?php 
$ip=$_SERVER['REMOTE_ADDR']; 

$purge = $_GET["purge"];
include("config.php");
$dbTable = "codes";
		$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die ("Error connecting to database.");
		$db_found = mysql_select_db("$dbDatabase", $db) or die ("Couldn't select the database.");
if (isset($purge) && $purge == "true") {


//$sql = "delete from tweets where ip = '".$ip."'";
//$result = mysql_query($sql);

}
?>
<script src="http://code.jquery.com/jquery-1.4.3.min.js"></script>
<script type="text/javascript" src="http://justnear.me/jquery.qtip-1.0.0-rc3.min.js"></script>

		<script type="text/javascript" src="js/jquery.progressbar.min.js"></script>
<br />

<p style="display:none"><img src="http://www.rioleo.org/twitternow/small.png" /></p>
<div class="wrapper">
 <div class="box2">
 <h1>Recent queries</h1>
 <ul>
 <?php
 $sql = "select distinct query from recentweets";
$result = mysql_query($sql);
 while($row = mysql_fetch_array($result)) {
 	echo "<li><a href='map.php?query=".urlencode($row['query'])."'>".urldecode($row['query'])."</a></li>";
 }
?>
 
 </ul><br />
 <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.rioleo.org%2Ftwitternow&amp;layout=box_count&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:65px;" allowTransparency="true"></iframe><br /><br />
 <a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
 <br /><p><a href="about.php">about</a></p>
 </div>
 <div class="box">
<p>TwitterNow is a quick and easy way to find out who is tweeting what, and where. TwitterNow is unique in that it uses a completely server-side solution (no SSH!), combining the Twitter Search API with some clever location retrieval and some final geocoding provided by the wonderful folks at <a href="http://tinygeocoder.com">tinygeocoder</a></p><br />
<p>If there are particular features you'd like to have implemented, please let me know via email at: rio[at]rioleo[.]org.</p>
<br /><p>You can also <a href="http://www.twitter.com/rioakasaka">follow me</a> on Twitter.</p>
</div>
<div style="clear:both;"></div>
</div>
<script type="text/javascript">
$("#geocode").click(function() {
	$("#hide").toggle();
});
$(".submit").click(function() {
	$.post("search.php", { geocode: $("#geocode").val(), radius: $(".radius").val(), lat: $(".lat").val(), lng : $(".lng").val(), ip : $("#ip").val(), max: $(".maxx").val(), query: escape($("#query").val()) });
begin($(".maxx").val());		
	return false;
});
$(document).ready(function() {
				$("#pb1").progressBar();
					$('.info-setup').qtip({
	   content: 'Retrieve tweets within a certain geographic radius. Doing so will return more accurate, but limited, results.',
	   show: 'mouseover',
	   hide: 'mouseout',
	   style: { 
	   	tip: true,
	      name: 'cream' // Inherit from preset style
	   },
	  position: {
	      corner: {
	         target: 'rightMiddle',
	         tooltip: 'leftMiddle'
	      }
	   }
	});	

		});
	function begin(value) {
		var counter = 0;
		$("#pb1").fadeIn();
			var i = setInterval(function() { 
				counter = counter + (100/value);
				//$(".statusPB").html("<a class='one' target='_blank' href='map.php?query="+escape($("#query").val())+"'>See map while its loading (new window) &raquo;</a>");

				//alert(counter);
				//alert(counter);
				if (counter == 100) {
					clearInterval(i);
					
					$(".statusPB").html("<a class='one' href='map.php?query="+escape($("#query").val())+"'>All done! go to map &raquo;</a>");
				}
			$("#pb1").progressBar(counter);
			//});
		}, 1500);	
	}

navigator.geolocation.getCurrentPosition(foundLocation, noLocation);

 function foundLocation(position)
 {
   var lat = position.coords.latitude;
   var lng = position.coords.longitude;
   $(".lat").val(lat);
   $(".lng").val(lng);
 
 }
 function noLocation()
 {
   alert('Could not find location');
 }
 </script>
<?php
mysql_close($db);
?>
</body>
</html>