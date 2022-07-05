function setWarningVisible(visible) {
  if(visible)
    $('#warning-top').show();
  else
    $('#warning-top').hide();
}

function updateSunInfo() {
  $.ajax({
    url: config.URL_SERVER + config.QUERY_FILE,
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

function updateMoonInfo() {
  $.ajax({
    url: config.URL_SERVER + config.QUERY_FILE,
    type: "get",
    data: {
      a : 'getMoonInfo'
    },
    success: function(response) {
      $('#moon-phase').text(response.phase);
      $('#moon-phase-name').text(response.name);
      $('#moon-full').text(response.nextFull);
      $('#moon-new').text(response.nextNew);
      $('#moon-age').text(response.age);
      $('#moon-illumination').text(response.illumination);

      $('.moon-shadow').css('left', (function(phase) {
        // return left position, function of phase
        if (phase <= 0 || phase >= 100)
          return '-0.5rem';

        var y = (phase > 50) ? (phase - 50) : phase;
        var x = (-0.21 * y) - 0.5;
        x = (phase > 50) ? (10+x) : x;

        return x + 'rem';
      })(response.phase));
    },
    error: function(xhr) {
      setWarningVisible(true);
    }
  });
}

function updatePastInfo() {
  const MAX_DAYS = 3;
  $.ajax({
    url: config.URL_SERVER + config.QUERY_FILE,
    type: "get",
    data: {
      a : 'getPastInfo'
    },
    success: function(response) {
      for (var i = 1; i <= MAX_DAYS; i++) {
        var j = i - 1;
        var img = config.URL_SERVER + config.IMGS_PATH + config.imgs[response[j].weather];
        $('#past-day-' + i + '-date').text(response[j].day);
        $('#past-day-' + i + '-img').attr('src', img);
        $('#past-day-' + i + '-max').text(response[j].t_max);
        $('#past-day-' + i + '-min').text(response[j].t_min);
        $('#past-day-' + i + '-rainfall').text(response[j].rainfall);
      }
    },
    error: function(xhr) {
      setWarningVisible(true);
    }
  });
}

function updateTemperatureInfo() {
  $.ajax({
    url: config.URL_SERVER + config.QUERY_FILE,
    type: "get",
    data: {
      a : 'getTemperatureInfo'
    },
    success: function(response) {
      $('#temperature-temp').text(response.temperature);
      $('#temperature-perceived').text(response.perceived);
      $('#temperature-humidity').text(response.humidity);
      $('#temperature-trend').text(response.trend);
      if (response.trend <= 0)
        $('#temperature-trend').parent().addClass('blue');
      else
        $('#temperature-trend').parent().removeClass('blue');
    },
    error: function(xhr) {
      setWarningVisible(true);
    }
  });
}

function updateRainInfo() {
  $.ajax({
    url: config.URL_SERVER + config.QUERY_FILE,
    type: "get",
    data: {
      a : 'getRainInfo'
    },
    success: function(response) {
      $('#rain-val').text(response.rain);

      const date = new Date();

      $('#rain-month').text(months[date.getMonth()]);
      $('#rain-month-val').text(response.month);

      $('#rain-year').text(date.getFullYear());
      $('#rain-year-val').text(response.year);
      rain = response.rain / 2;
      $('#rain-beker').css('background-size', '100% ' + rain + '%');
    },
    error: function(xhr) {
      setWarningVisible(true);
    }
  });
}

function updatePressureInfo() {
  $.ajax({
    url: config.URL_SERVER + config.QUERY_FILE,
    type: "get",
    data: {
      a : 'getPressureInfo'
    },
    success: function(response) {
      $('#pressure-val').text(response.pressure);
      $('#pressure-bar').css('background', function(perc) {
        var deg = perc * 360 / 100;
        var delta = 180 - (deg / 2);
        return 'conic-gradient(from ' + delta + 'deg, #ff8a84 ' + deg + 'deg, #606060 0deg)';
      }(response.percentage))
    },
    error: function(xhr) {
      setWarningVisible(true);
    }
  });
}
