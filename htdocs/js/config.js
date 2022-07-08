const config = {
  URL_SERVER : 'http://localhost/',
  QUERY_FILE : 'iot/query.php',
  HISTORY_FILE : 'iot/history.php',
  IMGS_PATH : 'img/',

  timers : {
    clock : 1000,
    updateTime : 60000,
    sunInfo : 3600000,
    pastInfo : 3600000,
    temperatureInfo : 60000,
    rainInfo : 60000,
    pressureInfo : 60000,
    moonInfo : 3600000
  },

  imgs : {
    nodata: 'no-data.png',
    moon : 'luna.png',
    sun : 'sole.png',
    cloudly : 'nuvoloso.png',
    partlyCloudy : 'parzialmente-nuvoloso.png',
    lightRain : 'pioggia-leggera.png',
    rain : 'pioggia.png',
    thunderstorm : 'pioggia-temporale.png',
    snow : 'neve.png',
    rainy : 'piovoso.png',
    dry : 'asciutto.png',
    wet : 'bagnato.png',
    baromether : 'barometro.png',
    thermometerUp : 'termometro-su.png',
    thermometerDown : 'termometro-giu.png'
  }
}

const chartLabel = {
  color: '#fff',
  font : {
    family: '"Georgia", "Times", "Times New Roman", serif',
    size : 14,
    weight : 100
  }
}

const chartOptions = {
  plugins : {
    legend : {
        display: true,
        labels: $.extend(true, {}, chartLabel)
    }
  },
  scales : {
    x : {
      ticks : $.extend(true, {}, chartLabel)
    },
    y : {
      ticks : $.extend(true, {}, chartLabel)
    }
  }
}

const chartCfg = {
  temperature : {
      type: 'line',
      data: {
        datasets: [{
          label: 'Temperature',
          data: [],
          borderColor: '#ff8a84',
          parsing: {
            xAxisKey: 'date',
            yAxisKey: 'temperature'
          }
        },
        {
          label: 'Humidity',
          data: [],
          borderColor: '#75a9f9',
          parsing: {
            xAxisKey: 'date',
            yAxisKey: 'humidity'
          }
        }]
      },
      options : $.extend(true, {}, chartOptions, {
        interaction: {
          intersect: false,
          mode: 'index',
        }
      })
    },

    rain : {
      type: 'bar',
      data: {
        datasets: [{
          label: 'Rainfall',
          data: [],
          backgroundColor: '#75a9f9',
          borderWidth: 1,
          parsing: {
            xAxisKey: 'date',
            yAxisKey: 'rain'
          }
        }]
      },
      options : $.extend(true, {}, chartOptions)
    },

    pressure : {
      type : 'line',
      data : {
        datasets : [{
          label : 'Pressure',
          data: [],
          borderColor: '#ff8a84',
          parsing: {
            xAxisKey: 'date',
            yAxisKey: 'pressure'
          }
        }]
      },
      options : $.extend(true, {}, chartOptions)
    }
  };
