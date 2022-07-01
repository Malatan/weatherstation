var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var days_of_week = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]

function updateClock() {
  const date = new Date();

  const hour = date.getHours();
  const min = date.getMinutes();

  const day_w = date.getDay();

  const day_m = date.getDate();
  const month = date.getMonth();
  const year = date.getFullYear();

  $('#today-hour').text(hour);
  $('#today-minute').text(min);
  $('#today-day-week').text(days_of_week[day_w]);
  $('#today-date').text(months[month] + ' ' + day_m + ' ' + year);
}
