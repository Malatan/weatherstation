const config = {
  URL_SERVER : 'http://localhost/',
  QUERY_FILE : 'iot/query.php',
  HISTORY_FILE : 'iot/history.php',
  IMGS_PATH : 'img/',

  timers : {
    clock : 1000,
    sunInfo : 3600000,
    pastInfo : 3600000,
    temperatureInfo : 60000,
    rainInfo : 60000,
    pressureInfo : 60000,
    moonInfo : 3600000
  },

  imgs : {
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

//{d:'2016-12-25', t:20, h:54}, {d:'2016-12-26', t:10, h:48}, {d:'2016-12-27', t:20, h:50}, {d:'2016-12-28', t:10, h:49}
//{d:'2016-12-25', r:20}, {d:'2016-12-25', r:20}, {d:'2016-12-25', r:20}

const chartLegendOptions = {
    display: true,
    labels: {
        color: '#fff',
        font : {
          family: "'Helvetica Neue', sans-serif",
          size : 14,
          weight : 100
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
      options: {
        interaction: {
          intersect: false,
          mode: 'index',
        },
        plugins: {
            legend: chartLegendOptions
        }
      }
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
      options : {
        plugins: {
            legend: chartLegendOptions
        }
      }
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
      options : {
        plugins : {
          legend : chartLegendOptions
        }
      }
    }
  };
