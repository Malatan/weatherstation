var charts = {
}

var selected_graph = "";
var graph_printed = false;

function enlarge(idPanel) {
  $('.sensors-section').toggleClass('container-s');
  $(idPanel).toggleClass('sensor-enlarged');
  $('.sensor').toggle(0);
  $(idPanel).toggle(200);
  $(idPanel + ' .sensor-details').toggleClass('hide-details');
}

function printChart(panel) {
  //var ctx = $('#' + panel + '-canvas');
  graph_printed = true;   
  $.ajax({
    url: config.URL_SERVER + config.HISTORY_FILE,
    type: "get",
    data: {
      a : 'get' + panel.charAt(0).toUpperCase() + panel.slice(1) + 'History',
      id : id_stazione
    },
    success: function(response) {
      var ctx = $('#' + panel + '-canvas');
      var cfg = chartCfg[panel];
      cfg.data.datasets.forEach(ds => $.extend(ds.data, response));
      charts[panel] = new Chart(ctx, cfg);
      charts[panel].update();
    },
    error: function(xhr) {
      setWarningVisible(true);
    }
  });
  
}

function bindDetailsBtn() {
  $('.enlarge-btn').on('click', function() {
    var panel = $(this).parents('.sensor').attr('id');
    selected_graph = selected_graph == panel ? "" : panel;
    var panelSelector = '#' + panel;
    panel = panel.split('-')[0];
    enlarge(panelSelector);
    if ($(panelSelector + ' .sensor-details').css('display') != 'none')
      printChart(panel);
  });
}
