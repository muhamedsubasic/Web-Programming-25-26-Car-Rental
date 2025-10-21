$(document).ready(function () {
  
  var app = $.spapp({
    defaultView: "#home",
    templateDir: "./tpl/",
    pageNotFound: "error_404",
  });

  // Page Routes
  app.route({ view: "home" });
  app.route({ view: "about" });
  app.route({ view: "service" });
  app.route({ view: "feature" });
  app.route({ view: "cars" });
  app.route({ view: "team" });
  app.route({ view: "contact" });

  // Highlight the active link in navbar
  function highlightActiveLink() {
    $(".navbar-nav .nav-link").removeClass("active");
    var currentHash = window.location.hash;
    $('.navbar-nav .nav-link[href="' + currentHash + '"]').addClass("active");
  }
  
  app.run();

  highlightActiveLink();
  $(window).on("hashchange", function () {
    highlightActiveLink();
  });

  // Login and Registration quick switch in Modal window
  $(document).ready(function () {
    $("#switchToRegister").on("click", function (e) {
      e.preventDefault();
      $("#loginForm").hide();
      $("#registerForm").show(); 
    });
  
    $("#switchToLogin").on("click", function (e) {
      e.preventDefault();
      $("#registerForm").hide();
      $("#loginForm").show();
    });
  });
});