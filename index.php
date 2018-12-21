<?php

	function getRadarImages() {
		$files = scandir("radar_images");
		return $files;
	}

	function getDateTime($fileName) {
		$splitedFileName = explode(".", $fileName);
		if (count($splitedFileName) != 6) {
			return null;
		}

		$date = $splitedFileName[2];
		$time = $splitedFileName[3];

		if (strlen($date) == 8) {
			$year = substr($date, 0, 4);
			$month = substr(substr($date, 4, 6), 0, 2);
			$day = substr($date, 6, 8);
			$parsedDate = $year . "-" . $month . "-" . $day;
		}else {
			$parsedDate = $date;
		}

		if (strlen($time) == 4) {
			$hours = substr($time, 0, 2) + 1; //CHMI has wrong time format..
			$minutes = substr($time, 2, 4);
			$parsedTime = $hours . ":" . $minutes;
		}else {
			$parsedTime = $time;
		}

		$dateTime = $parsedDate . " " . $parsedTime;
		return $dateTime;
	}

	function getUrl() {
    	if (isset($_SERVER['HTTPS'])){
        	$protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    	}else {
        	$protocol = 'http';
    	}
    	return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	$radarImages = getRadarImages();
	$items = array();

	foreach ($radarImages as $key => $fileName) {
		$url = getUrl() . "radar_images/" . $fileName;
		$dateTime = getDateTime($fileName);
		if ($key >= 2 && $dateTime != null && $fileName != null) {
			$item = array(
			'url' => $url,
			'date' => $dateTime);
			$items[] = $item;
		}
	}

	echo json_encode($items, JSON_UNESCAPED_SLASHES);

?>
