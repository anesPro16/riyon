/**
 * ================================================
 * CodeIgniter 3 - Global CSRF Protection (AJAX)
 * Versi: 2.0 (auto refresh token every 5 minutes)
 * ================================================
 */

(function ($) {
  $(function () {
    let csrfName = $('meta[name="csrf-name"]').attr('content');
    let csrfHash = $('meta[name="csrf-hash"]').attr('content');

    // Global setup
    $.ajaxSetup({
      beforeSend: function (xhr, settings) {
        if (settings.type === 'POST' || settings.type === 'PUT' || settings.type === 'DELETE') {
          if (typeof settings.data === 'string') {
            settings.data += '&' + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);
          } else if (typeof settings.data === 'object') {
            settings.data = settings.data || {};
            settings.data[csrfName] = csrfHash;
          }
        }
      },
      complete: function (xhr) {
        try {
          const res = JSON.parse(xhr.responseText);
          if (res.csrfName && res.csrfHash) {
            csrfName = res.csrfName;
            csrfHash = res.csrfHash;
            $('meta[name="csrf-name"]').attr('content', csrfName);
            $('meta[name="csrf-hash"]').attr('content', csrfHash);
          }
        } catch (e) {}
      }
    });

    // Refresh token setiap 5 menit
    setInterval(function () {
      $.getJSON(base_url + 'auth/refresh_csrf', function (res) {
        if (res.csrfName && res.csrfHash) {
          csrfName = res.csrfName;
          csrfHash = res.csrfHash;
          $('meta[name="csrf-name"]').attr('content', csrfName);
          $('meta[name="csrf-hash"]').attr('content', csrfHash);
          console.log('%c[CSRF] Token diperbarui otomatis', 'color:green');
        }
      });
    }, 300000);

    console.log('%c[CSRF Enabled] Proteksi aktif + auto refresh tiap 5 menit', 'color:green');
  });
})(jQuery);
