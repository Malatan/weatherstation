<?php
header('Content-Type: text/json');
require_once("secret.php");
echo "Hi this is send.php \n";
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$json = file_get_contents('php://input');
	$data = json_decode($json);

	if ($data->Api_key == API_KEY){
		echo "Api key match ";
		$timestamp = date("Y-m-d H:i:s", time());
		$temperatura = $data->Temperatura;
		$umidita = $data->Umidita;
		$pressione = $data->Pressione;
		$luce = $data->Luce;
		$pioggia = $data->Pioggia;
		$id_stazione = $data->Id_stazione;
		http_response_code(200);
		/*echo "Temperatura ricevuta: " . $temperatura . "\n";
		echo "Umidita ricevuta: " . $umidita . "\n";
		echo "Pressione ricevuta: " . $pressione . "\n";
		echo "Luce ricevuta: " . $luce . "\n";
		echo "Pioggia ricevuta: " . $pioggia . "\n";
		echo "Stazione: " . $id_stazione . "\n";*/
		echo "Data: " . $timestamp . "\n";

		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		if (!$mysqli) {
			die("SQL CONNECTION FAILED: " . mysqli_connect_error());
		}
		echo "SQL CONNECTED" . "\n";
		$stmt = createStmtTemperature($mysqli, $id_stazione, $temperatura, $timestamp);
		$r = $stmt->execute();
		$stmt->close();
		echo ($r) ? 'Temperatura(' . $temperatura . ') inserito' . "\n" : 'Temperatura X' . "\n";

		$stmt = createStmtHumidity($mysqli, $id_stazione, $umidita, $timestamp);
		$r = $stmt->execute();
		$stmt->close();
		echo ($r) ? 'Umidita(' . $umidita . ') inserito' . "\n" : 'Umidita X' . "\n";

		$stmt = createStmtPressure($mysqli, $id_stazione, $pressione, $timestamp);
		$r = $stmt->execute();
		$stmt->close();
		echo ($r) ? 'Pressione(' . $pressione . ') inserito' . "\n" : 'Pressione X' . "\n";

		$stmt = createStmtLight($mysqli, $id_stazione, $luce, $timestamp);
		$r = $stmt->execute();
		$stmt->close();
		echo ($r) ? 'Luce(' . $luce . ') inserito' . "\n" : 'Luce X' . "\n";

		$stmt = createStmtRain($mysqli, $id_stazione, $pioggia, $timestamp);
		$r = $stmt->execute();
		$stmt->close();
		echo ($r) ? 'Pioggia(' . $pioggia . ') inserito' . "\n" : 'Pioggia X' . "\n";
	} else{
		http_response_code(405);
		echo "Mismatch api key";
	}
}

function createStmtTemperature($mysqli, $id_stazione, $valore, $data) {
	$query = 'INSERT INTO temperatura(id_stazione, valore, data) VALUES (?,?,?)';
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param('ids', $id_stazione, $valore, $data);
	return $stmt;
}

function createStmtHumidity($mysqli, $id_stazione, $valore, $data) {
	$query = 'INSERT INTO umidita(id_stazione, valore, data) VALUES (?,?,?)';
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param('ids', $id_stazione, $valore, $data);
	return $stmt;
}

function createStmtPressure($mysqli, $id_stazione, $valore, $data) {
	$query = 'INSERT INTO pressione(id_stazione, valore, data) VALUES (?,?,?)';
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param('iis', $id_stazione, $valore, $data);
	return $stmt;
}

function createStmtLight($mysqli, $id_stazione, $valore, $data) {
	$query = 'INSERT INTO luce(id_stazione, valore, data) VALUES (?,?,?)';
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param('iis', $id_stazione, $valore, $data);
	return $stmt;
}

function createStmtRain($mysqli, $id_stazione, $valore, $data) {
	$query = 'INSERT INTO pioggia(id_stazione, valore, data) VALUES (?,?,?)';
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param('iis', $id_stazione, $valore, $data);
	return $stmt;
}

?>
