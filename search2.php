<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Twitter Search and Map</title>


</head>

<body>



<div style="display:none;" id="search">
<form action="" method="get">
  <label>
  Search twitter 
  <input type="text" name="q" id="searchbox" />
  <input type="submit" name="submit" id="submit" value="Search" />
  </label>
</form>
</div>

<?php

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





// Work out the Date plus 8 hours
// get the current timestamp into an array
$timestamp = time();
$date_time_array = getdate($timestamp);
$ip = $_SERVER['REMOTE_ADDR'];
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

//$q="#egypt";
$q="#petpeeves"; 

$radius=$_POST["radius"];
$max=10;
$lat=$_POST['lat'];
$ip=$_POST['ip'];
$lng=$_POST['lng'];
$geocode="off";
include("config.php");
	$dbTable = "codes";
			$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die ("Error connecting to database.");
			$db_found = mysql_select_db("$dbDatabase", $db) or die ("Couldn't select the database.");
			
$sql = "INSERT INTO recentweets (query) values ('".$q."');";
	//echo $sql;
	$result = mysql_query($sql);
	
// END DATE FUNCTION


function run($pagenum) {
	global $q, $radius, $max, $lat, $lng, $ip, $geocode, $counter2;

	if ($geocode == "on") {
		$search = "http://search.twitter.com/search.atom?page=".$pagenum."&rpp=".$max."&geocode=".$lat.",".$lng.",".$radius."km&q=".urlencode($q);
	} else {
		$search = "http://search.twitter.com/search.atom?page=".$pagenum."&rpp=".$max."&q=".urlencode($q);
		
	}
	//echo $search;
	$tw = curl_init();
	
	curl_setopt($tw, CURLOPT_URL, $search);
	curl_setopt($tw, CURLOPT_RETURNTRANSFER, TRUE);
	$twi = curl_exec($tw);
	$search_res = new SimpleXMLElement($twi);
	
		
	## Echo the Search Data
	
	foreach ($search_res->entry as $twit1) {
	
	$description = $twit1->content;
	$counter2++;
	$description = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\" >@\\2</a>'", $description);  
	$description = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\" >\\2</a>'", $description);
	$description = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://\\2\" >\\2</a>'", $description);
	
	$retweet = strip_tags($description);
	
	$date =  strtotime($twit1->updated);
	$dayMonth = date('Y-m-d H:i:s', $date);
	//$year = date('y', $date);
	//echo $dayMonth;
	//$datediff = date_diff($theDate, $date);
	
	
	$link = $twit1->author->uri;
	$homepage = file_get_contents($link);	
	$dom = new DOMDocument();
	@$dom->loadHTML($homepage);
	$xpath = new DOMXPath($dom);
	
	$hrefs = $xpath->evaluate("//span[@class='adr']");
	foreach ($hrefs as $e) {
	$children = $e->childNodes;
	    foreach ($children as $child) {
	        $tmp_doc = new DOMDocument();
	        $tmp_doc->appendChild($tmp_doc->importNode($child,true));       
	        $innerHTML = $tmp_doc->saveHTML();
	    }
	}
	echo $innerHTML;
	//echo $innerHTML == "";
	$auth = str_replace("http://twitter.com/", "", $twit1->author->uri);
	$pos = strpos($innerHTML,"&Uuml;T");
	if ($pos === false) {
		//$two = "http://local.yahooapis.com/MapsService/V1/geocode?appid=YD-9G7bey8_JXxQP6rxl.fBFGgCdNjoDMACQA--&street=".urlencode(trim($innerHTML));
		//
		$two = "http://tinygeocoder.com/create-api.php?q=".urlencode(trim($innerHTML));
	//echo $two;
	
	$homepage2 = file_get_contents($two);
	$latlng = explode(",", $homepage2);
	$lat = $latlng[0];
	$lng = $latlng[1];
	//$tw3 = curl_init();
	//curl_setopt($tw3, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"));
	//curl_setopt($tw3, CURLOPT_URL, $two);
	//curl_setopt($tw3, CURLOPT_RETURNTRANSFER, TRUE);
	//$twi = curl_exec($tw3);
	//$search_res3 = new SimpleXMLElement($twi);
	//print_r($search_res3);
	//$counter = 0;
	//foreach ($search_res3->Result as $twit2) {
		//echo "here";
		//if ($counter == 0) {
			//$lat = $twit2->Latitude;
			//$lng = $twit2->Longitude;
			//$counter++;
		//} else {
		//}
	//}
	//curl_close($tw3);
	}	else{
	
	//echo "FOUND".$innerHTML;
	$pieces = explode(" ", $innerHTML);
	$pieces2 = explode(",", $pieces[1]);
	$lat = trim($pieces2[0]);
	$lng = trim($pieces2[1]);
	//echo $lat;
	//echo $lng;
	
		}
	
	echo $lat;
	echo $lng;
//$q = "France";
	$sql = "INSERT INTO tweets (date,lat,lng,loc,tweet,name,ip,query) values ('".$dayMonth."',  '".$lat."',  '".$lng."',  '".urlencode($innerHTML)."',  '".urlencode($description)."',  '".$auth."', '".$ip."', '".$q."');";
	//echo $sql;
	$result = mysql_query($sql);
	
	echo $counter2."<br /><br />";
	echo "<div class='text'>".$description."<div class='description'>From: ", $twit1->author->name,"</div>";
	
	
		if ($counter2 >= $max) {
			return $counter2;
		}
	}
	curl_close($tw);
	return $counter2;
	}

//Search API Script




//echo "<h3>Twitter search results for '".$q."'(<a href='map.php' target='_blank'>launch map in separate window</a>)</h3>";
$overall++;
run("1");

if ($max > 100 && $max >= 200) {
	run("2");
	if ($max > 100 && $max >= 300) {
		run("3");
		if ($max > 100 && $max >= 400) {
			run("4");
			if ($max > 100 && $max >= 500) {
				run("5");
				if ($max > 100 && $max >= 600) {
					run("6");
						if ($max > 100 && $max >= 700) {
							run("7");
			if ($max > 100 && $max >= 800) {
				run("8");
				if ($max > 100 && $max >= 900) {
					run("9");
			if ($max > 100 && $max >= 1000) {
				run("10");
				if ($max > 100 && $max >= 1100) {
					run("11");
					if ($max > 100 && $max >= 1200) {
						run("12");
						if ($max > 100 && $max >= 1300) {
							run("13");
			if ($max > 100 && $max >= 1400) {
				run("14");
				if ($max > 100 && $max >= 1500) {
					run("15");
				}
			}
						}
					}
				}
			}
			
				}
			}
						}
				}
			}
		}

	}
}

if ($counter2 >= $max) {
	
echo "<a href='http://www.rioleo.org/twitternow/map.php'>Go to map!</a>";	
} 

mysql_close($db);



?>
</div>
<div class="lat"></div>

<div class="lng"></div>
<script src="http://code.jquery.com/jquery-1.4.3.min.js"></script>


</body>
</html>