var id_stazione = 1;

function bindToggleMenu(){
  $(".toggle-menu").on("click", function() {

    if ($('main').css('display') == 'none') {
      $("main").fadeIn(300);
      $(".settings").hide();
    } else {
      $(".settings").fadeIn(300);
      $("main").hide();
    }
    return false;

  });
}

function updateTimeLabel(){
        fetch('iot/properties.txt')
                .then(response => response.text())
                .then(data => {
                        var db_times = [];
                        var myArray = data.split("\n");
                        db_times.push(myArray[0].split("="));
                        db_times.push(myArray[1].split("="));
                        var display = "DB last update on ";
                        if (id_stazione == 1){
                                display += db_times[0][1];
                                if (db_times[0][1] === ''){
                                        display = "DB last update on 0000-00-00 00:00:00";
                                }
                        } else if (id_stazione == 2){
                                display += db_times[1][1];
                                if (db_times[1][1] === ''){
                                        display = "DB last update on 0000-00-00 00:00:00";
                                }
                        }
                        document.getElementById("updatetime-label").textContent = display;
                });
}

function initStazione(){
        getCookieStation();
        var stazione = id_stazione == 2 ? 'Calenzano' : 'Prato';
        document.getElementById("ws-name").textContent = "Weather Station of " + stazione;
}

function selezionaStazione(val){
        var id_selected = val == 'Calenzano' ? 2 : 1;
        var same_station = id_selected == id_stazione ? true : false;
        id_stazione  = id_selected;
        $("main").fadeIn(300);
        $(".settings").hide();
        if (same_station != true){
                document.getElementById("ws-name").textContent = "Weather Station of " + val;
                setCookieStation();
                if (graph_printed){
                        console.log('refresh');
                        location.reload();
                } else{
                        /*if (selected_graph != ""){
                                var panel = selected_graph;
                                var panelSelector = '#' + panel;
                                panel = panel.split('-')[0];
                                enlarge(panelSelector);
                                selected_graph = "";
                        }*/
                        //ricarica i dati relativi alla stazione scelta
                        updateTimeLabel();
						updateLightLevel();
                        updatePastInfo();
                        updateTemperatureInfo();
                        updateRainInfo();
                        updatePressureInfo();
                        /*updateMoonInfo();
                        updateClock();
                        updateSunInfo();*/
                }
        }
        
        
        
        
        
        
        
        
        
        
}

function setCookieStation() {
    document.cookie = "id_stazione=" + id_stazione +";";
}

function getCookieStation() {
    var id_cookie = document.cookie.split('=');
    var id = parseInt(id_cookie[1]);
    if (id != 1 && id !=2)
            id = 1;
    id_stazione = id;
}
