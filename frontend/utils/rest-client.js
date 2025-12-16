let RestClient = {
  get: function (url, callback, error_callback) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: "GET",
      beforeSend: function (xhr) {
        try {
          const token = localStorage.getItem("user_token");
          if (token) {
            xhr.setRequestHeader("Authorization", "Bearer " + token);
          }
        } catch (e) {}
      },
      success: function (response) {
        if (callback) callback(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (error_callback) error_callback(jqXHR);
      },
    });
  },
  request: function (url, method, data, callback, error_callback) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: method,
      beforeSend: function (xhr) {
        try {
          const token = localStorage.getItem("user_token");
          if (token) {
            xhr.setRequestHeader("Authorization", "Bearer " + token);
          }
        } catch (e) {}
      },
      data: data,
    })
      .done(function (response, status, jqXHR) {
        if (callback) callback(response);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        if (error_callback) {
          error_callback(jqXHR);
        } else {
          try {
            const msg = jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message ? jqXHR.responseJSON.message : jqXHR.responseText || "Request failed";
            toastr.error(msg);
          } catch (e) {
            toastr.error("Request failed");
          }
        }
      });
  },
  post: function (url, data, callback, error_callback) {
    RestClient.request(url, "POST", data, callback, error_callback);
  },
  delete: function (url, data, callback, error_callback) {
    RestClient.request(url, "DELETE", data, callback, error_callback);
  },
  patch: function (url, data, callback, error_callback) {
    RestClient.request(url, "PATCH", data, callback, error_callback);
  },
  put: function (url, data, callback, error_callback) {
    RestClient.request(url, "PUT", data, callback, error_callback);
  },
};
