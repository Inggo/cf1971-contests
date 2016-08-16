(function ($) {
  $(document).ready(function () {

    // Delete button helper function
    var deleteButton = function () {
      return '<span style="float: right;">[<a class="cf1971-delete" href="javascript:;">&times;</a>]</span>';
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
    $('.cf1971-admin-workouts').on('click', '.cf1971-delete', function (e) {
      var proceed = window.confirm("Are you sure you want to delete this Workout?");

      if (proceed) {
        $(this).closest('li').remove();
      }
    });

    // Make Leaderboards body sortable
    $('.cf1971-leaderboards-body').sortable();

    // Add new Team to Leaderboards
    $('.cf1971-admin-leaderboards').on('click', '.cf1971-team-add', function (e) {
      e.preventDefault();

      var val = $('input[name="cf1971-team-new"]').val();

      if (!val) {
        return;
      }

      var $tr = $('<tr><td>' +
          '<input type="hidden" value="' + val + '" name="teams[]"> ' +
          '<label>' + val + '</label> ' +
        '</td></tr>');

      for (var i = 0; i < cf1971_admin.workouts.length; i++) {
        $tr.append('<td>' +
            '<input class="cf1971-team-score" placeholder="Enter Score" type="text" name="team_scores[' + i + '][]">' +
          '</td>');
      }

      $tr.append('<td>' + deleteButton() + '</td>');

      $('.cf1971-leaderboards-body').append($tr);

      $('input[name="cf1971-team-new"]').val('');
    });

    // Remove Team form Leaderboard
    $('.cf1971-admin-leaderboards').on('click', '.cf1971-delete', function (e) {
      var proceed = window.confirm("Are you sure you want to delete this Team?");

      if (proceed) {
        $(this).closest('tr').remove();
      }
    });
  });
})(jQuery);