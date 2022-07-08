<?php
	header('Content-Type: text/json');

	require_once("secret.php");

  if (isset($_GET['a'])) {
		$action = $_GET['a'];
    http_response_code(200);

		if (is_callable($action))
			call_user_func($action);
		else
			http_response_code(400);

  } else
  	http_response_code(400);

// FUNCTIONS

function getTemperatureHistory() {
  $r = array(
    array(
      'date' => '2016-12-25',
      'temperature' => 20,
      'humidity' => 54
    ),
    array(
      'date' => '2016-12-26',
      'temperature' => 10,
      'humidity' => 57
    ),
    array(
      'date' => '2016-12-27',
      'temperature' => 20,
      'humidity' => 59
    ),
    array(
      'date' => '2016-12-28',
      'temperature' => 10,
      'humidity' => 44
    )
  );

  echo json_encode($r);
}

function getRainHistory() {
  $r = array(
    array(
      'date' => '2016-12-28',
      'rain' => 10
    ),
    array(
      'date' => '2016-12-29',
      'rain' => 20
    ),
    array(
      'date' => '2016-12-30',
      'rain' => 15
    ),
    array(
      'date' => '2016-12-31',
      'rain' => 11
    )
  );

  echo json_encode($r);
}

function getPressureHistory() {
  $r = array(
    array(
      'date' => '2016-12-28',
      'pressure' => 999
    ),
    array(
      'date' => '2016-12-29',
      'pressure' => 1002
    ),
    array(
      'date' => '2016-12-30',
      'pressure' => 1000
    ),
    array(
      'date' => '2016-12-31',
      'pressure' => 1010
    )
  );

  echo json_encode($r);
}
