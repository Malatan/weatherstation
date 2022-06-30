<?php
	header('Content-Type: text/json');
	require_once("secret.php");

	$data = $_POST['data'];
	if (!isset($data)) {
		http_response_code(400);		//TODO check if correct
		return;
	}
	http_response_code(200);

	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

	$sensor_list = $data['sensor_list'];
	$timestamp = $data['timestamp'];
	$ws_id = $data['weatherstation_id'];

	$response = array();

	foreach ($sensor_list as $sensor => $sensor_val) {
		$func = "createStmt".$sensor_val['name'];
		$stmt = call_user_func($func, $ws_id, $sensor_val['value'], $timestamp);

		$r = $stmt->execute();

		$response[$sensor_val['name']] = ($r) ? '201' : '500';
	}

	return json_encode($response);


	// Functions

	function createStmtTemperature($ws_id, $val, $ts) {
		$query = 'INSERT INTO Temperature(ws_id, value, ts) VALUES (?,?,?)';
		$stmt = $mysqli->prepare($query);
	  $stmt->bind_param('idi', $ws_id, $val, $ts);
	  return $stmt;
	}

	function createStmtHumidity($ws_id, $val, $ts) {
		$query = 'INSERT INTO Humidity(ws_id, value, ts) VALUES (?,?,?)';
		$stmt = $mysqli->prepare($query);
	  $stmt->bind_param('iii', $ws_id, $val, $ts);
	  return $stmt;
	}

	function createStmtPressure($ws_id, $val, $ts) {
		$query = 'INSERT INTO Pressure(ws_id, value, ts) VALUES (?,?,?)';
		$stmt = $mysqli->prepare($query);
	  $stmt->bind_param('iii', $ws_id, $val, $ts);
	  return $stmt;
	}

	function createStmtRain($ws_id, $val, $ts) {
		$query = 'INSERT INTO Rain(ws_id, value, ts) VALUES (?,?,?)';
		$stmt = $mysqli->prepare($query);
	  $stmt->bind_param('iii', $ws_id, $val, $ts);
	  return $stmt;
	}

?>
