<?php
	header('Content-Type: text/json');

	require_once("secret.php");

	$ws_id = NULL;
  if (isset($_GET['a']) && isset($_GET['id'])) {
		$action = $_GET['a'];
    http_response_code(200);

		if (is_callable($action))
			call_user_func($action);
		else
			http_response_code(400);

  } else {
  	http_response_code(400);
		exit();
	}
// FUNCTIONS

function getTemperatureHistory() {
	$ws_id = $_GET['id'];
  $query = "SELECT DATE(t.data) AS 'date', ROUND(AVG(t.valore),1) AS 'temperature', ROUND(AVG(u.valore),1) AS 'humidity'
		FROM temperatura t, umidita u
		WHERE t.data = u.data AND t.id_stazione = {$ws_id} AND u.id_stazione = {$ws_id}
		GROUP BY DATE(t.data), DATE(u.data)
		LIMIT 10";

	$r = execQuery($query);
	echo json_encode($r);
}

function getRainHistory() {
	$ws_id = $_GET['id'];
  $query = "SELECT DATE(data) AS 'date', ROUND((((4095 - AVG(valore)) / 4095) * 100),0) AS 'rain'
			FROM pioggia
			WHERE id_stazione = {$ws_id}
			GROUP BY DATE(data)
			LIMIT 10";

	$r = execQuery($query);
	echo json_encode($r);
}

function getPressureHistory() {
	$ws_id = $_GET['id'];
  $query = "SELECT DATE(data) AS 'date', ROUND((AVG(valore)/100),0) AS 'pressure'
			FROM pressione
			WHERE id_stazione = {$ws_id}
			GROUP BY DATE(data)
			LIMIT 10";

	$r = execQuery($query);
	echo json_encode($r);
}

function execQuery($query) {
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	if (!$mysqli)
			exit("SQL CONNECTION FAILED: " . mysqli_connect_error());

	$r = array();
	if ($result = $mysqli->query($query)) {
		while ($row = $result->fetch_array(MYSQLI_ASSOC)){
			array_push($r, $row);
		}
		$result->free_result();
	}

	return $r;
}
