#include <DHT.h>
#include <WiFi.h>
#include <ArduinoJson.h>
#include <HTTPClient.h>
#include <Adafruit_BMP085.h>
#define ID_STAZIONE 1;
#define DHTPIN 33           // sensore temperatura e umidita
#define RAIN_SENSOR_PIN 36  // sensore pioggia
#define LIGHT_SENSOR_PIN 32  // sensore luce
//i pin per il bmp180 sono fissi: 21-SDA e 22-SCL

DHT dht(DHTPIN, DHT22);
Adafruit_BMP085 bmp;
String API_KEY = "63afaad7-c66c-40fa-95dc-d45e11e6cd41";  // chiave di accesso
const char* ssid = "TIM-09669504";                        // nome wifi
const char* password = "3207290065Matteo";                // pass wifi
String serverName = "http://192.168.1.4:80/iot/send.php"; // server
unsigned long period = 15000;                             // frequenza

void setup() {
  Serial.begin(115200);
  delay(1000);
  // connessione wifi
  WiFi.begin(ssid, password);
  Serial.print("Connessione al wifi in corso");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());

  // init dht22
  Serial.println("Inizializzazione DHT22");
  dht.begin();
  Serial.println("DHT22 inzializzato");

  // init bmp180
  Serial.println("Inizializzazione BMP180");
  while (!bmp.begin()) {
    Serial.println("No bmp180 rilevato. Riprova in 2 secondi");
    delay(2000);
  }
  Serial.println("BMP180 inzializzato");

}

void loop() {
  //DHT22: sensore temperatura e umidita
  Serial.println("----------DHT22 AM2302----------");
  Serial.print("Umidita' = ");
  Serial.print(dht.readHumidity());
  Serial.print("\tTemperatura = ");
  Serial.print(dht.readTemperature());
  Serial.println(" *C");

  //BMP180: sensore pressione
  Serial.println("----------BMP180----------");
  Serial.print("Temperatura = ");
  Serial.print(bmp.readTemperature());
  Serial.println(" *C");
  Serial.print("Pressione = ");
  Serial.print(bmp.readPressure());
  Serial.println(" Pa");
  Serial.print("Altitudine = ");
  Serial.print(bmp.readAltitude());
  Serial.println(" metri");
  /*Serial.print("Pressure at sealevel (calculated) = ");
    Serial.print(bmp.readSealevelPressure());
    Serial.println(" Pa");
    Serial.print("Real altitude = ");
    Serial.print(bmp.readAltitude(102000));
    Serial.println(" meters");*/

  float temperatura = dht.readTemperature();
  float umidita = dht.readHumidity();
  int pressione = bmp.readPressure();
  
  //Fotoresistore: luce
  //range valore: 0 - 4095
  int valoreLuce = analogRead(LIGHT_SENSOR_PIN);
  Serial.println("----------Fotoresistore----------");
  Serial.print("Valore analogico = ");
  Serial.print(valoreLuce);
  if (valoreLuce < 40) {
    Serial.println(" => Dark");
  } else if (valoreLuce < 800) {
    Serial.println(" => Dim");
  } else if (valoreLuce < 2000) {
    Serial.println(" => Light");
  } else if (valoreLuce < 3200) {
    Serial.println(" => Bright");
  } else {
    Serial.println(" => Very bright");
  }

  //Sensore pioggia
  //range valore: 4095 - 0, 4095 = no pioggia.
  int valorePioggia = analogRead(RAIN_SENSOR_PIN);
  Serial.println("----------Rain Sensor----------");
  Serial.print("Valore analogico = ");
  Serial.println(valorePioggia);

  Serial.println("---------------------------");
  if (WiFi.status() == WL_CONNECTED) {
    uploadData(temperatura, umidita, pressione, valoreLuce, valorePioggia);
  } else {
    Serial.println("WiFi Disconnected. Attempting to reconnect");
    Serial.print("Reconnecting to WiFi");
    WiFi.disconnect();
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
      delay(500);
      Serial.print(".");
    }
    Serial.println("");
    Serial.print("Connected to WiFi network with IP Address: ");
    Serial.println(WiFi.localIP());
  }
  delay(period);
}

void uploadData(float t, float u, int p, int vl, int vp) {
  Serial.println("Posting JSON data to server...");
  HTTPClient http;
  http.begin(serverName);
  http.addHeader("Content-Type", "application/json");

  StaticJsonDocument<200> doc;
  doc["Api_key"] = API_KEY;
  doc["Id_stazione"] = ID_STAZIONE;
  doc["Temperatura"] = t;
  doc["Umidita"] = u;
  doc["Pressione"] = p;
  doc["Luce"] = vl;
  doc["Pioggia"] = vp;

  String requestBody;
  serializeJson(doc, requestBody);
  int httpResponseCode = http.POST(requestBody);
  Serial.println(httpResponseCode);
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println(response);
  }else {
    Serial.println("HTTP POST failed");  
  }
  http.end();
}
