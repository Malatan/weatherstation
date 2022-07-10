$(document).ready(function() {
        
  updateTimeLabel();
  setInterval(updateTimeLabel, config.timers.updateTime);
  
  updateLightLevel();
  setInterval(updateLightLevel, config.timers.lightLevel);
  
  // menu.js
  initStazione();
  bindToggleMenu();

  // clock.js
  updateClock();
  setInterval(updateClock, config.timers.clock);

  // sensors.js
  updateSunInfo();
  setInterval(updateSunInfo, config.timers.sunInfo);

  updatePastInfo();
  setInterval(updatePastInfo, config.timers.pastInfo);

  updateTemperatureInfo();
  setInterval(updateTemperatureInfo, config.timers.temperatureInfo);

  updateRainInfo();
  setInterval(updateRainInfo, config.timers.rainInfo);

  updatePressureInfo();
  setInterval(updatePressureInfo, config.timers.pressureInfo);

  updateMoonInfo();
  setInterval(updateMoonInfo, config.timers.moonInfo);

  bindDetailsBtn();
});
