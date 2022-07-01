function setWarningVisible(visible) {
  if(visible)
    $('#warning-top').show();
  else
    $('#warning-top').hide();
}

function updateSunInfo() {
  $.ajax({
    url: config.URL_SERVER + "query.php",
    type: "get",
    data: {
      a : 'getSunInfo',
      latitude : 43.7792,
      longitude : 11.2462
    },
    success: function(response) {
      $('#today-sunrise').text(response.sunrise);
      $('#today-sunset').text(response.sunset);
    },
    error: function(xhr) {
      setWarningVisible(true);
    }
  });
}

function updateTemperatureInfo() {
//// TODO: 
}
