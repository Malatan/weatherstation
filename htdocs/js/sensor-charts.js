var charts = {
}

function enlarge(idPanel) {
  $('.sensors-section').toggleClass('container-s');
  $(idPanel).toggleClass('sensor-enlarged');
  $('.sensor').toggle(0);
  $(idPanel).toggle(200);
  $(idPanel + ' .sensor-details').toggleClass('hide-details');
}

function printChart(panel) {
  var ctx = $('#' + panel + '-canvas');
  if (charts[panel])
    charts[panel].destroy();

  $.ajax({
    url: config.URL_SERVER + config.HISTORY_FILE,
    type: "get",
    data: {
      a : 'get' + panel.charAt(0).toUpperCase() + panel.slice(1) + 'History'
    },
    success: function(response) {
      var cfg = chartCfg[panel];
      cfg.data.datasets.forEach(ds => $.extend(ds.data, response));

      const c = new Chart(ctx, cfg);
      charts[panel] = c;
    },
    error: function(xhr) {
      setWarningVisible(true);
    }
  });
}

function bindDetailsBtn() {
  $('.enlarge-btn').on('click', function() {
    var panel = $(this).parents('.sensor').attr('id');
    var panelSelector = '#' + panel;
    panel = panel.split('-')[0];

    enlarge(panelSelector);

    if ($(panelSelector + ' .sensor-details').css('display') != 'none')
      printChart(panel);
  });
}
