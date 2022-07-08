
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
                        // Do something with your data
                        let myArray = data.split("\n");
                        let db_1 = myArray[0].split("=");
                        let db_2 = myArray[1].split("=");
                        let display = "DB last update on " + db_1[1];
                        if (db_1[1] === ''){
                                 display = "DB last update on 0000-00-00 00:00:00";
                        }
                        document.getElementById("updatetime-label").textContent = display;
                });
}