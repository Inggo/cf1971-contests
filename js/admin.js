(function ($) {
  $(document).ready(function () {
    $('.cf1971-workouts-list').sortable();
    $('.cf1971-workout-add').on('click', function () {
      // Create new elements
      var $input = $('input').attr('type', 'hidden').val($('input[name="cf1971-workout-new"]').val());
      var $li = $('li').html('<label>' + $input.val() + '</label>');
      $li.append($input);

      // Append to workout list
      $('.cf1971-workouts-list').append($li);
      
      // Clear the input value
      $('input[name="cf1971-workout-new"]').val('');
    });
  });
})(jQuery);