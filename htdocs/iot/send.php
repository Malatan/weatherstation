<?php 
header('Content-Type: text/json');
date_default_timezone_set('Europe/Rome');
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
		
                $update_properties = false;
                //parametri per il controllo dei valori
                $temperatura_min = 16;
                $temperatura_max = 45;
                $umidita_min = 10;
                $umidita_max = 100;
                $pressione_min = 90000;
                $pressione_max = 120000;
                
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		if (!$mysqli) {
			die("SQL CONNECTION FAILED: " . mysqli_connect_error());
		}
		echo "SQL CONNECTED" . "\n";
                
                if($temperatura >= $temperatura_min && $temperatura <= $temperatura_max){
                        $stmt = createStmtTemperature($mysqli, $id_stazione, $temperatura, $timestamp);
                        $r = $stmt->execute();
                        $stmt->close();
                        echo ($r) ? 'Temperatura(' . $temperatura . ') inserito' . "\n" : 'Temperatura X' . "\n";
                        $update_properties = true;
                } else{
                        writeLog('bad_values.txt', $id_stazione . " - [" . $timestamp . "] Temperatura = " . $temperatura);
                        echo "Temperatura non valida\n";
                }
		
                if($umidita >= $umidita_min && $umidita <= $umidita_max){
                        $stmt = createStmtHumidity($mysqli, $id_stazione, $umidita, $timestamp);
                        $r = $stmt->execute();
                        $stmt->close();
                        echo ($r) ? 'Umidita(' . $umidita . ') inserito' . "\n" : 'Umidita X' . "\n";
                        $update_properties = true;
                } else{
                        writeLog('bad_values.txt', $id_stazione . " - [" . $timestamp . "] Umidita = " . $umidita);
                        echo "Umidita non valida\n";
                }
                
		if($pressione >= $pressione_min && $pressione <= $pressione_max){
                        $stmt = createStmtPressure($mysqli, $id_stazione, $pressione, $timestamp);
                        $r = $stmt->execute();
                        $stmt->close();
                        echo ($r) ? 'Pressione(' . $pressione . ') inserito' . "\n" : 'Pressione X' . "\n";
                        $update_properties = true;
                } else{
                        writeLog('bad_values.txt', $id_stazione . " - [" . $timestamp . "] Pressione = " . $pressione);
                        echo "Pressione non valida\n";
                }
		
                if($luce <= 4095){
                        $stmt = createStmtLight($mysqli, $id_stazione, $luce, $timestamp);
                        $r = $stmt->execute();
                        $stmt->close();
                        echo ($r) ? 'Luce(' . $luce . ') inserito' . "\n" : 'Luce X' . "\n";
                        $update_properties = true;
                } else{
                        writeLog('bad_values.txt', $id_stazione . " - [" . $timestamp . "] Luce = " . $luce);
                        echo "Luce non valida\n";
                }
                
		if($pioggia <= 4095){
                        $stmt = createStmtRain($mysqli, $id_stazione, $pioggia, $timestamp);
                        $r = $stmt->execute();
                        $stmt->close();
                        echo ($r) ? 'Pioggia(' . $pioggia . ') inserito' . "\n" : 'Pioggia X' . "\n";
                        $update_properties = true;
                } else{
                        writeLog('bad_values.txt', $id_stazione . " - [" . $timestamp . "] Pioggia = " . $pioggia);
                        echo "Pioggia non valida\n";
                }
                
                //last db update time
                if ($update_properties == true){
                        $lines = file('properties.txt', FILE_IGNORE_NEW_LINES);
                        foreach ($lines as $line){
                                $lineA = explode("=", $line);
                                $file_data[$lineA[0]]=$lineA[1];
                        }
                        $file_data[$id_stazione . "_db_last_update"] = $timestamp;
        
                        $myfile = fopen("properties.txt", "w") or die("Unable to open file!");
                        foreach ($file_data as $key=>$string) {
                                fwrite($myfile, $key ."=".$string);
                                fwrite($myfile, "\n");
                        }
                        fclose($myfile);
                } 
                
	} else{
		http_response_code(405);
		echo "Mismatch api key";
	}
}

function writeLog($log_file, $log){
        $fp = fopen($log_file, 'a');  
        fwrite($fp, $log . "\n"); 
        fclose($fp);  
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