<?php
header('Content-Type: text/json');
require_once("secret.php");
include('include/MoonPhase.php');
date_default_timezone_set('Europe/Rome');
define('DEF_PIOGGIA',4095);
use Solaris\MoonPhase;
$id_stazione = 1;

if (isset($_GET['a'])) {
    $action = $_GET['a'];
    $id_stazione = $_GET['id'];
    http_response_code(200);
    call_user_func($action);
} else
    http_response_code(400);

// FUNCTIONS
// not-sensor info

function getSunInfo(){
    if (isset($_GET['latitude']) && isset($_GET['longitude'])) {
        $lat  = $_GET['latitude'];
        $long = $_GET['longitude'];
        
        $sun_info       = date_sun_info(time(), $lat, $long);
        $res            = array();
        $res['sunrise'] = date('H:i', $sun_info['sunrise']);
        $res['sunset']  = date('H:i', $sun_info['sunset']);
        echo json_encode($res);
    } else {
        http_response_code(400);
    }
}

function getMoonInfo(){
    if (isset($_GET['datetime'])) {
        $dt   = $_GET['datetime'];
        $dt   = new DateTime($dt);
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
        'phase' => (int) ($moon->getPhase() * 100),
        'name' => ($moon->getPhaseName()),
        'nextFull' => date('d-m', $fullMoon),
        'nextNew' => date('d-m', $newMoon),
        'age' => (int) ($moon->getAge()),
        'illumination' => (int) ($moon->getIllumination() * 100)
    );
    
    echo json_encode($res);
}

//TODO past panel
// sensor info

function getPastInfo() {
        global $id_stazione;
        //genera le date di 3 giorni passati
        $date = date('Y-m-d');
        $days = [date('Y-m-d', strtotime($date . " -1 day")),
                 date('Y-m-d', strtotime($date . " -2 day")),
                 date('Y-m-d', strtotime($date . " -3 day"))];
	$r = array(
		array(
                        'day' => $days[0],
			'weather' => 'nodata',
			't_max' => 0,
			't_min' => 0,
			'rainfall' => 999
		),
		array(
			'day' => $days[1],
			'weather' => 'nodata',
			't_max' => 0,
			't_min' => 0,
			'rainfall' => 999
		),
		array(
			'day' => $days[2],
			'weather' => 'nodata',
			't_max' => 0,
			't_min' => 0,
			'rainfall' => 999
		)
	);

        
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if (!$mysqli) {
            die("SQL CONNECTION FAILED: " . mysqli_connect_error());
        }
        
        for ($i = 0; $i < 3; $i++) {
                //fetch max tempeatura
                $query = "SELECT * FROM temperatura WHERE DATE(data) = '" . $days[$i] . "' 
                        AND id_stazione = " . $id_stazione . " ORDER BY valore DESC LIMIT 1";                 
                if ($result = $mysqli->query($query)) {
                    if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                            if ($row['valore'] != 0){
                                    $r[$i]['t_max'] = $row['valore'];
                            }
                    } 
                    $result->free_result();
                }
                
                //fetch min tempeatura
                $query = "SELECT * FROM temperatura WHERE DATE(data) = '" . $days[$i] . "' 
                        AND id_stazione = " . $id_stazione . " ORDER BY valore ASC LIMIT 1";                 
                if ($result = $mysqli->query($query)) {
                    if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                            if ($row['valore'] != 0){
                                    $r[$i]['t_min'] = $row['valore'];
                            }
                    } 
                    $result->free_result();
                }
                
                //fetch pioggia
                $query = "SELECT id_stazione, AVG(valore) as media FROM pioggia 
                        WHERE DATE(data) = '" . $days[$i] . "' AND id_stazione = " . $id_stazione;                 
                if ($result = $mysqli->query($query)) {
                    if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                            if ($row['media'] != 0) {
                                //rain% = ((default - value) / default) * 100
                                $rain_percentage = ((DEF_PIOGGIA - round($row['media'])) / DEF_PIOGGIA) * 100;
                                $r[$i]['rainfall']     = round($rain_percentage);
                            }
                    } 
                    $result->free_result();
                }
                
                //weather
                //fetch light
                $light = 4095;
                $query = "SELECT id_stazione, AVG(valore) as media FROM luce WHERE DATE(data) = '" . $days[$i] . "' 
                AND TIME(data) >= '09:00:00' AND TIME(data) <= '16:00:00' AND id_stazione = " . $id_stazione;                 
                if ($result = $mysqli->query($query)) {
                    if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                            if ($row['media'] != 0) {
                                $light     = round($row['media']);
                            }
                    } 
                    $result->free_result();
                }
                if ($light <= 800){
                        $r[$i]['weather'] = 'cloudly';
                } else if($light <= 1200){
                        $r[$i]['weather'] = 'partlyCloudy';
                } else {
                        $r[$i]['weather'] = 'sun';
                }
                
                if ( $r[$i]['rainfall'] >= 60){
                        $r[$i]['weather'] = 'thunderstorm';
                } else if( $r[$i]['rainfall'] >= 30){
                        $r[$i]['weather'] = 'rain';
                } else if( $r[$i]['rainfall'] >= 15){
                        $r[$i]['weather'] = 'lightRain';
                }
                if ( $r[$i]['rainfall'] == 999){
                        $r[$i]['weather'] = 'nodata';
                }
        }
        
	echo json_encode($r);
}

function getTemperatureInfo(){
    global $id_stazione;
    
    $res = array(
        'temperature' => 0.0,
        'trend' => 0.0,
        'perceived' => 0.0,
        'humidity' => 0.0
    );
    
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    if (!$mysqli) {
        die("SQL CONNECTION FAILED: " . mysqli_connect_error());
    }
    
    $calcolo_perceived = true;
    //get temperatura
    $query = "SELECT * FROM temperatura 
                    WHERE DATE(DATE_ADD(NOW(), INTERVAL 2 HOUR)) = DATE(data) 
                    AND id_stazione = " . $id_stazione . " ORDER BY data DESC LIMIT 1";
    if ($result = $mysqli->query($query)) {
            if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $res['temperature'] = $row['valore'];
            } else {
                    $calcolo_perceived = false;
            }
            $result->free_result();
    }
    
    //get umidita
    $query = "SELECT * FROM umidita 
                    WHERE DATE(DATE_ADD(NOW(), INTERVAL 2 HOUR)) = DATE(data) 
                    AND id_stazione = " . $id_stazione . " ORDER BY data DESC LIMIT 1";
    if ($result = $mysqli->query($query)) {
        if ($row = $result->fetch_array(MYSQLI_ASSOC)){
            $res['humidity'] = $row['valore'];
        } else {
            $calcolo_perceived = false;
        }
            $result->free_result();
    }
    
    //calcolo perceived
    //HUMIDEX : H = T + 5/9 * (e-10), e = 6.112 * 10^(7.5*T/(237.7+T))*U/100
    if ($calcolo_perceived == true){
        $pva              = 6.112 * pow(10, ((7.5 * $res['temperature']) / (237.7 + $res['temperature']))) * ($res['humidity'] / 100);
        $res['perceived'] = round($res['temperature'] + 5 / 9 * ($pva - 10), 1);
    }
    
    
    //get temperatura media ieri
    $query = "SELECT id_stazione, AVG(valore) as media FROM temperatura 
	WHERE DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) = DATE(data) AND id_stazione = " . $id_stazione;
    if ($result = $mysqli->query($query)) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if ($row['media'] != 0) {
            $temperatura_ieri = $row['media'];
            $res['trend']     = round($res['temperature'] - $row['media'], 1);
        }
        $result->free_result();
    }
    
    echo json_encode($res);
}

function getRainInfo(){  
    global $id_stazione;
    $res = array(
        'rain' => 0.0,
        'month' => 0.0,
        'year' => 0.0
    );
	
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    if (!$mysqli) {
        die("SQL CONNECTION FAILED: " . mysqli_connect_error());
    }
	//get pioggia giornaliera
    $query = "SELECT id_stazione, AVG(valore) as media FROM pioggia WHERE DATE(DATE_ADD(NOW(), INTERVAL 2 HOUR)) = DATE(data)
                AND id_stazione = " . $id_stazione;
    if ($result = $mysqli->query($query)) {
        if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                if ($row['media'] != 0) {
                        //rain% = ((default - value) / default) * 100
                        $rain_percentage = ((DEF_PIOGGIA - round($row['media'])) / DEF_PIOGGIA) * 100;
                        $res['rain']     = round($rain_percentage);
                }
        }
        $result->free_result();
    }
    $date = date('Y-m');
    //get pioggia mensile
    $query = "SELECT * FROM pioggia_mensile_" . $id_stazione . " WHERE mese = '" . $date . "'";
    if ($result = $mysqli->query($query)) {
        if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                $rain_percentage = ((DEF_PIOGGIA - round($row['media'])) / DEF_PIOGGIA) * 100;
                $res['month']     = round($rain_percentage);
        }
        $result->free_result();
    }
    $date = date('Y');
    //get pioggia annuale
    $query = "SELECT * FROM pioggia_annuale_" . $id_stazione . " WHERE anno = '" . $date . "'";
    if ($result = $mysqli->query($query)) {
        if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                $rain_percentage = ((DEF_PIOGGIA - round($row['media'])) / DEF_PIOGGIA) * 100;
                $res['year']     = round($rain_percentage);
        }
        $result->free_result();
    }
    
    echo json_encode($res);
}

function getPressureInfo(){
    global $id_stazione;
    // percentage = (pressione - 800) * 1 / 7
    $res = array(
        'pressure' => 0,
        'percentage' => 0.0
    );
	
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    if (!$mysqli) {
        die("SQL CONNECTION FAILED: " . mysqli_connect_error());
    }
        //get pressione
    $query = "SELECT * FROM pressione WHERE DATE(DATE_ADD(NOW(), INTERVAL 2 HOUR)) = DATE(data) 
                    AND id_stazione = " . $id_stazione . " ORDER BY data DESC LIMIT 1";
    if ($result = $mysqli->query($query)) {
        if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                $res['pressure'] = $row['valore']/100; //Pa --> hPa
                $res['percentage'] = ($res['pressure'] - 800) * 1/7;
        }
        $result->free_result();
    }
	
    echo json_encode($res);
}

function getLightInfo(){
    global $id_stazione;
    $res = -1;
    
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    if (!$mysqli) {
        die("SQL CONNECTION FAILED: " . mysqli_connect_error());
    }
    //get luce
    $query = "SELECT * FROM luce WHERE DATE(DATE_ADD(NOW(), INTERVAL 2 HOUR)) = DATE(data) 
                    AND id_stazione = " . $id_stazione . " ORDER BY data DESC LIMIT 1";
    if ($result = $mysqli->query($query)) {
        if ($row = $result->fetch_array(MYSQLI_ASSOC)){
                $res = $row['valore'];
        }
        $result->free_result();
    }
    
    echo json_encode($res);
}
?>