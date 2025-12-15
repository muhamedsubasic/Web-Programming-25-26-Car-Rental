var UserService = {
  init: function () {
    var token = localStorage.getItem("user_token");
    if (token && token !== undefined) {
      try {
        UserService.generateMenuItems();
      } catch (e) {}
    }

    // Bind modal login form (if present)
    if ($("#modal-login-form").length) {
      $("#modal-login-form").validate({
        submitHandler: function (form) {
          const entity = Object.fromEntries(new FormData(form).entries());
          UserService.login(entity, { hideModal: true });
        },
      });
    }

    // Bind modal register form
    if ($("#modal-register-form").length) {
      $("#modal-register-form").validate({
        submitHandler: function (form) {
          const entity = Object.fromEntries(new FormData(form).entries());
          UserService.register(entity);
        },
      });
    }

    // Toggle between login and register inside modal
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
  },
  login: function (entity) {
    return $.ajax({
      url: Constants.PROJECT_BASE_URL + "auth/login",
      type: "POST",
      data: JSON.stringify(entity),
      contentType: "application/json",
      dataType: "json",
      success: function (result) {
        if (result && result.data && result.data.token) {
          localStorage.setItem("user_token", result.data.token);
          localStorage.setItem("user", JSON.stringify(result.data));
          toastr.success(result.message || "Login successful");
          if (arguments[1] && arguments[1].hideModal) {
            $("#getStarted").modal("hide");
          }
          try {
            UserService.generateMenuItems();
          } catch (e) {}
        } else {
          toastr.error("Unexpected login response");
        }
      },
      error: function (jqXHR) {
        let msg = "Login failed";
        try {
          if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) msg = jqXHR.responseJSON.message;
          else if (jqXHR && jqXHR.responseText) msg = jqXHR.responseText;
        } catch (e) {}
        toastr.error(msg);
      },
    });
  },

  register: function (entity) {
    return $.ajax({
      url: Constants.PROJECT_BASE_URL + "auth/register",
      type: "POST",
      data: JSON.stringify(entity),
      contentType: "application/json",
      dataType: "json",
      success: function (res) {
        toastr.success(res.message || "Registration successful. Please login.");
        // switch to login view inside modal
        $("#registerForm").hide();
        $("#loginForm").show();
      },
      error: function (jqXHR) {
        let msg = "Registration failed";
        try {
          msg = jqXHR.responseJSON?.message || jqXHR.responseText || msg;
        } catch (e) {}
        toastr.error(msg);
      },
    });
  },

  logout: function () {
    localStorage.removeItem("user_token");
    localStorage.removeItem("user");
    window.location.replace("index.html");
  },
  generateMenuItems: function () {
    const token = localStorage.getItem("user_token");
    const parsed = Utils.parseJwt(token);
    const user = parsed ? parsed.user : null;

    // Auth area (right side of navbar)
    const $auth = $("#authArea");
    if (!$auth.length) return;

    if (user && user.role) {
      const displayName = user.name || user.email || "User";
      let authHtml = "";
      if (user.role === Constants.ADMIN_ROLE) {
        authHtml += '<a class="btn btn-outline-light me-2" href="admin.html">Admin Panel</a>';
      }
      authHtml += '<span class="me-2 text-white">Hello, ' + displayName + "</span>";
      authHtml += '<button class="btn btn-primary" onclick="UserService.logout()">Logout</button>';
      $auth.html(authHtml);
    } else {
      // show Get Started button (modal trigger)
      $auth.html('<a class="btn btn-primary rounded-pill py-2 px-4" data-bs-toggle="modal" data-bs-target="#getStarted">Get Started</a>');
    }
  },
};
