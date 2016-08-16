(function ($) {
  $(document).ready(function () {

    // Delete button helper function
    var deleteButton = function () {
      return '[<a class="cf1971-workout-delete" href="javascript:;">&times;</a>]';
    };

    // Make Workouts List sortable
    $('.cf1971-workouts-list').sortable();

    // Add new Workout to list
    $('.cf1971-admin-workouts').on('click', '.cf1971-workout-add', function (e) {
      e.preventDefault();

      var val = $('input[name="cf1971-workout-new"]').val();

      if (!val) {
        return;
      }

      $('.cf1971-workouts-list').append('<li>' +
          '<input type="hidden" value="' + val + '" name="workouts[]"> ' +
          '<label>' + val + '</label> ' +
          deleteButton() +
        '</li>');

      $('input[name="cf1971-workout-new"]').val('');
    });

    // Remove workout form list
    $('.cf1971-admin-workouts').on('click', '.cf1971-workout-delete', function (e) {
      var proceed = window.confirm("Are you sure you want to delete this Workout?");

      if (proceed) {
        $(this).closest('li').remove();
      }
    });

  });
})(jQuery);