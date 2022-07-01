$(document).ready(function() {
  bindToggleMenu();

  updateClock();
  setInterval(updateClock, config.timers.clock);

  updateSunInfo();
  setInterval(updateSunInfo, config.timers.sunInfo);
});
