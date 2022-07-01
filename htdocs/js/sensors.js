
function bindSun() {

}

function updateSunInfo() {
  $.ajax({
  url: URL_SERVER + "query.php",
  type: "get",
  data: {
    latitude=43.7792,
    longitude=11.2462
  },
  success: function(response) {
    //TODO
  },
  error: function(xhr) {
    //TODO
  }
});
}
