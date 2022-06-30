<?php
	header('Content-Type: text/json');
	require_once("secret.php");

	$data = $_POST['data'];
	if (!isset($data))
		return json_encode(array('result' => '400'));

	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$mysqli->begin_transaction();

	$sensor_list = $data['sensor_list'];
	$timestamp = $data['timestamp'];
	$ws_id = $data['weatherstation_id'];

	foreach ($sensor_list as $sensor => $sensor_val) {		//TODO
		$query = "INSERT INTO Temperature VALUES (?,?,?)";

		try {
			$stmt = $mysqli->prepare($query);
	    $stmt->bind_param('idi', $ws_id, $sensor_val['value'], $timestamp);
	    $stmt->execute();
		} catch (mysqli_sql_exception $exception) {
		    $mysqli->rollback();
				return json_encode(array('result' => '500'));
		}
	}
	$mysqli->commit();

	return json_encode(array('result' => '201'));

?>
