<?php 
header("Content-type: text/xml"); 

$date = $_GET["date"];
$hour = -1;
if (isset($_GET["hour"])) {
	$hour = $_GET["hour"];
	if ($hour < 10) {
		$hour = "0".$hour;
	}
	if (($hour + 1) <10) {
		$hourplus = "0".($hour + 1);
	}
	else {
		$hourplus = ($hour + 1);
	}
}



include("config.php");

// Connect to the database

$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die ("Error connecting to database.");
$db_found = mysql_select_db("$dbDatabase", $db) or die ("Couldn't select the database.");

if ($hour != -1) {
$result = mysql_query("select * from ".$dbTable);
	//echo "select * from ".$dbTable." where date between '".$date." ".$hour.":00:00' and between '".$date." ".$hourplus.":00:00'";
	//$result=mysql_query("select * from ".$dbTable." where date between '".$date." ".$hour.":00:00' and '".$date." ".$hourplus.":00:00'", $db);
} else {

$result = mysql_query("select * from ".$dbTable);
	//$result=mysql_query("select * from ".$dbTable." where date > '".$date."' and date < date_add('".$date."', interval 24 hour)", $db);
}
if ($result) {
 $_xml ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
 $_xml .="<xml>\r\n";
 $_xml .="<markers>\r\n";

 while ($row = mysql_fetch_array($result)) {

 //if ($row["pageTitle"]) {

 //$_xml .="\t<page title=\"" . $row["lat"] . "\">\r\n";

 $_xml .="\t\t<marker lat=\"" . $row["lat"] . "\" lng=\"" . $row["lng"] . "\" />\r\n";
//$_xml .="\t</page>\r\n";

 }
 
 
 $_xml .="</markers>";
  $_xml .="</xml>\r\n";

 
 }
echo $_xml;
//$total = "";
//while($row = mysql_fetch_array($result))
//  {
//	$date = $row['date'];
//	$lat = $row['lat'];
//	$lng = $row['lng'];
//	$tweet = $row['tweet'];
//	$total .= "{'lat':".$lat.",";
//	$total .= "'lng':".$lng."},";
  //}
	//$total = rtrim($total, ',');
	//echo $total;	
//}
//echo "]\n";
//echo "here";
?>