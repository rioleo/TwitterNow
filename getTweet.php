<?php 

$id = $_GET["id"];

$query = $_GET["query"];

include("config.php");

// Connect to the database

// Date function (this could be included in a seperate script to keep it clean)
function date_diff($d1, $d2){
	$d1 = (is_string($d1) ? strtotime($d1) : $d1);
	$d2 = (is_string($d2) ? strtotime($d2) : $d2);

	$diff_secs = abs($d1 - $d2);
	$base_year = min(date("Y", $d1), date("Y", $d2));

	$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
	$diffArray = array(
		"years" => date("Y", $diff) - $base_year,
		"months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
		"months" => date("n", $diff) - 1,
		"days_total" => floor($diff_secs / (3600 * 24)),
		"days" => date("j", $diff) - 1,
		"hours_total" => floor($diff_secs / 3600),
		"hours" => date("G", $diff),
		"minutes_total" => floor($diff_secs / 60),
		"minutes" => (int) date("i", $diff),
		"seconds_total" => $diff_secs,
		"seconds" => (int) date("s", $diff)
	);
	if($diffArray['days'] > 0){
		if($diffArray['days'] == 1){
			$days = '1 day';
		}else{
			$days = $diffArray['days'] . ' days';
		}
		return $days . ' and ' . $diffArray['hours'] . ' hours ago';
	}else if($diffArray['hours'] > 0){
		if($diffArray['hours'] == 1){
			$hours = '1 hour';
		}else{
			$hours = $diffArray['hours'] . ' hours';
		}
		return $hours . ' and ' . $diffArray['minutes'] . ' minutes ago';
	}else if($diffArray['minutes'] > 0){
		if($diffArray['minutes'] == 1){
			$minutes = '1 minute';
		}else{
			$minutes = $diffArray['minutes'] . ' minutes';
		}
		return $minutes . ' and ' . $diffArray['seconds'] . ' seconds ago';
	}else{
		return 'Less than a minute ago';
	}
}

$timestamp = time();
$date_time_array = getdate($timestamp);

$hours = $date_time_array['hours'];
$minutes = $date_time_array['minutes'];
$seconds = $date_time_array['seconds'];
$month = $date_time_array['mon'];
$day = $date_time_array['mday'];
$year = $date_time_array['year'];

// use mktime to recreate the unix timestamp
// adding 19 hours to $hours
$timestamp = mktime($hours + 0,$minutes,$seconds,$month,$day,$year);
$theDate = strftime('%Y-%m-%d %H:%M:%S',$timestamp);	

$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die ("Error connecting to database.");
$db_found = mysql_select_db("$dbDatabase", $db) or die ("Couldn't select the database.");


	$result=mysql_query("select * from ".$dbTable." where query='".$query."' LIMIT ".$id.", 1", $db);

if ($result) {

//echo "[";
$total = "";
while($row = mysql_fetch_array($result))
  {
	
	$total .= urldecode(urldecode($row['tweet']));
	
	$total .= " <br /><br /><strong>Location:</strong> ". urldecode(urldecode($row['loc']));
		$total .= " <br /><strong>User:</strong> <a href='http://www.twitter.com/". $row['name']."'>". $row['name']."</a>";
		$total .= "<br /><strong>Time:</strong> ". date_diff($theDate, strtotime($row['date']));
	//$total .= " lat ". $row['lat'];
	//$total .= " lng ". $row['lng'];
	
  }
	echo $total;
		
}
//echo "]\n";

?>