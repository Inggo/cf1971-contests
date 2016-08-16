(function ($) {
  $(document).ready(function () {
    $('#cf1971-contest-registration-form').on('submit', function (e) {
      e.preventDefault();

      var $form = $(this);
      var $btn = $form.find('button[type="submit"]');

      $btn.prop('disabled', true);

      $.ajax({
        url: $form.attr('action'),
        method: $form.attr('method'),
        data: $form.serialize(),
        dataType: 'json',
        success: function (d) {
          alert(d.success);
          if (d.redirect) {
            $('#cf1971-payment').submit();
          } else {
            $btn.prop('disabled', false);
          }
        },
        error: function (x, t, e) {
          $btn.prop('disabled', false);
          r = JSON.parse(x.responseText);
          alert(r.error);
        }
      });
    });
  });
})(jQuery);