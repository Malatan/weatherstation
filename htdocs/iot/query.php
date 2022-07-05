<?php
	header('Content-Type: text/json');

	require_once("secret.php");

	include ('include/MoonPhase.php');
	use Solaris\MoonPhase;

  if (isset($_GET['a'])) {
		$action = $_GET['a'];
    http_response_code(200);
		call_user_func($action);
  } else
  	http_response_code(400);

	// FUNCTIONS

	// not-sensor info

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

	function getMoonInfo() {
		if (isset($_GET['datetime'])) {
			$dt = $_GET['datetime'];
			$dt = new DateTime($dt);
			$moon = new MoonPhase($dt);
		} else {
			$moon = new MoonPhase();
		}

		$fullMoon = $moon->getPhaseFullMoon();
		if (time() > $fullMoon)
			$fullMoon = $moon->getPhaseNextFullMoon();

		$newMoon = $moon->getPhaseNewMoon();
		if (time() > $newMoon)
			$newMoon = $moon->getPhaseNextNewMoon();

		$res = array(
			'phase' => (int)($moon->getPhase() * 100),
			'name' => ($moon->getPhaseName()),
			'nextFull' => date('d-m', $fullMoon),
			'nextNew' => date('d-m', $newMoon),
			'age' => (int)($moon->getAge()),
			'illumination' => (int)($moon->getIllumination() * 100)
		);

		echo json_encode($res);
	}

	//TODO past panel

	// sensor info

	function getTemperatureInfo() {
		if (isset($_GET['day'])) {
			$day = $_GET['day'];
		} else {
			$day = date("Y-m-d");
		}

		//TODO query

		$res = array(
			'temperature' => 25.6,
			'trend' => -1,
			'perceived' => 26.0,
			'humidity' => 54
		);

		echo json_encode($res);
	}

	function getRainInfo() {
		if (isset($_GET['day'])) {
			$day = $_GET['day'];
		} else {
			$day = date("Y-m-d");
		}

		//TODO query

		$res = array(
			'rain' => 5,
			'month' => 16,
			'year' => 600
		);

		echo json_encode($res);
	}

	function getPressureInfo() {
		if (isset($_GET['day'])) {
			$day = $_GET['day'];
		} else {
			$day = date("Y-m-d");
		}

		//TODO query

		$res = array(
			'pressure' => 99,
			'percentage' => (99 - 80) * 10/7
		);

		echo json_encode($res);
	}

?>
