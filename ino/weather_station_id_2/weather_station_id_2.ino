#include <DHT.h>
#include <WiFi.h>
#include <ArduinoJson.h>
#include <HTTPClient.h>
#include <Adafruit_BMP085.h>
#define ID_STAZIONE 2;
#define DHTPIN 33           // sensore temperatura e umidita
#define RAIN_SENSOR_PIN 36  // sensore pioggia
#define LIGHT_SENSOR_PIN 32  // sensore luce
//i pin per il bmp180 sono fissi: 21-SDA e 22-SCL

DHT dht(DHTPIN, DHT11);
Adafruit_BMP085 bmp;
String API_KEY = "63afaad7-c66c-40fa-95dc-d45e11e6cd41";  // chiave di accesso
const char* ssid = "your-ssid";                        // nome wifi
const char* password = "your-password";                // pass wifi
String serverName = "http://weatherstationppm.atwebpages.com/iot/send.php"; // server
RTC_DATA_ATTR int first_time = 0;
#define uS_TO_S_FACTOR 1000000  /* Conversion factor for micro seconds to seconds */
#define TIME_TO_SLEEP  1800      /* 1800 secondi = 30 minuti*/  /* Time ESP32 will go to sleep (in seconds) */

void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println("===============Inizializzazione===============");
  initialize();
  Serial.println("===============Fine inizializzazione===============");

  Serial.println("=============Deep Sleep mode params=============");
  print_wakeup_reason();
  esp_sleep_enable_timer_wakeup(TIME_TO_SLEEP * uS_TO_S_FACTOR);
  Serial.println("Setup ESP32 to sleep for every " + String(TIME_TO_SLEEP) + " Seconds");
  Serial.println("==============================================");

  Serial.println("===============Operazione in corso===============");
  if (first_time == 0){
    //un ritardo che viene solo una volta all'inizio
    first_time++;
    delay(30000);
  }
  work();
  Serial.println("===============Fine operazione===============");
  Serial.println("Inizia deep sleep mode...");
  delay(1000);
  Serial.flush();
  esp_deep_sleep_start();
}

void initialize(){
  // connessione wifi
  WiFi.begin(ssid, password);
  Serial.print("Connessione al wifi in corso");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connesso al WiFi network con IP: ");
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

void work(){
  //DHT22: sensore temperatura e umidita
  Serial.println("----------DHT11 AM2302----------");
  Serial.print("Umidita' = ");
  Serial.print(dht.readHumidity());
  Serial.print("\tTemperatura = ");
  Serial.print(dht.readTemperature());
  Serial.println(" *C");

  //BMP180: sensore pressione
  Serial.println("----------BMP180----------");
  Serial.print("\tPressione = ");
  Serial.print(bmp.readPressure());
  Serial.println(" Pa");

  float temperatura = dht.readTemperature();
  float umidita = dht.readHumidity();
  int pressione = bmp.readPressure();

  //Fotoresistore: luce
  //range valore: 0 - 4095
  int valoreLuce = analogRead(LIGHT_SENSOR_PIN);
  Serial.println("----------Fotoresistore----------");
  Serial.print("Valore analogico = ");
  Serial.print(valoreLuce);

  //Sensore pioggia
  //range valore: 4095 - 0, 4095 = no pioggia.
  int valorePioggia = analogRead(RAIN_SENSOR_PIN);
  Serial.println("----------Rain Sensor----------");
  Serial.print("Valore analogico = ");
  Serial.println(valorePioggia);

  Serial.println("===============Carica i dati al db===============");
  uploadData(temperatura, umidita, pressione, valoreLuce, valorePioggia);
  Serial.println("====================Fine caricamento====================");
}

void loop() {
}

void uploadData(float t, float u, int p, int vl, int vp) {
  Serial.println("Invia i dati tramite POST al server...");
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
    Serial.println("HTTP POST fallito");
  }
  http.end();
}

void print_wakeup_reason(){
  esp_sleep_wakeup_cause_t wakeup_reason;

  wakeup_reason = esp_sleep_get_wakeup_cause();

  switch(wakeup_reason)
  {
    case ESP_SLEEP_WAKEUP_EXT0 : Serial.println("Wakeup caused by external signal using RTC_IO"); break;
    case ESP_SLEEP_WAKEUP_EXT1 : Serial.println("Wakeup caused by external signal using RTC_CNTL"); break;
    case ESP_SLEEP_WAKEUP_TIMER : Serial.println("Wakeup caused by timer"); break;
    case ESP_SLEEP_WAKEUP_TOUCHPAD : Serial.println("Wakeup caused by touchpad"); break;
    case ESP_SLEEP_WAKEUP_ULP : Serial.println("Wakeup caused by ULP program"); break;
    default : Serial.printf("Wakeup was not caused by deep sleep: %d\n",wakeup_reason); break;
  }
}
