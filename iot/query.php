<?php
	header('Content-Type: text/json');
	require_once("secret.php");

  if (isset($_GET['a'])) {
		$action = $_GET['a'];
    http_response_code(200);
		call_user_func($action);
  }
  http_response_code(400);

	// Functions

  function getSunInfo() {
		if (isset($_GET['latitude']) && isset($_GET['longitude'])) {
			$lat = $_GET['latitude'];
			$long = $_GET['longitude'];

	    $sun_info = date_sun_info(time(), $lat, $long);
			$res = array();
			$res['sunrise'] = date('H:i', $sun_info['sunrise']);
			$res['sunset'] = date('H:i', $sun_info['sunset']);
			echo json_encode($res);
		} else {
			http_response_code(400);
		}
  }

?>
