
function bindToggleMenu(){
  $(".toggle-menu").on("click", function() {

    if ($('main').css('display') == 'none') {
      $("main").fadeIn(300);
      $(".settings").hide();
    } else {
      $(".settings").fadeIn(300);
      $("main").hide();
    }
    return false;

  });
}
