<?php 

$date = $_GET["date"];
$query = $_GET["query"];
$hour = -1;
$date = "2010-01-13";
$hour = "3";
//if (isset($_GET["hour"])) {
	//$hour = $_GET["hour"];
	if ($hour < 10) {
		$hour = "0".$hour;
	}
	if (($hour + 1) <10) {
		$hourplus = "0".($hour + 1);
	}
	else {
		$hourplus = ($hour + 1);
	}
//}



include("config.php");
$ip=$_SERVER['REMOTE_ADDR']; 
// Connect to the database

$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die ("Error connecting to database.");
$db_found = mysql_select_db("$dbDatabase", $db) or die ("Couldn't select the database.");


if (!isset($query)) {
	
$result = mysql_query("select * from ".$dbTable);

	$row =  mysql_fetch_array(mysql_query("SELECT date FROM tweets order by date asc limit 0,1"));
} else {
		$result = mysql_query("select * from ".$dbTable." where query='".$query."'");
		
//echo "select * from ".$dbTable." where query='".$query."' and ip = '".$ip."'";
		$row =  mysql_fetch_array(mysql_query("SELECT date FROM tweets where query='".$query."' order by date asc limit 0,1"));

}
//echo $query;
	$firstdate = $row["date"];
//$result=mysql_query("select * from ".$dbTable." where date between '".$date." ".$hour.":00:00' and '".$date." ".$hourplus.":10:00'", $db);
//$firstdate = "2010-01-13 03:21:27";
if ($result) {

echo "[";
$total = "";
while($row = mysql_fetch_array($result))
  {
	$date = $row['date'];
	$lat = $row['lat'];
	$lng = $row['lng'];
	$diff = abs(strtotime($date) - strtotime($firstdate)); 
	$tweet = $row['tweet'];
	//if ($diff < 100) {
		$total .= "{'t':'".$lat."',";
		$total .= "'g':'".$lng."',";
		$total .= "'d':'".$diff."'},";
	//}
  }
	$total = rtrim($total, ',');
	echo $total;	
}
echo "]\n";

?>