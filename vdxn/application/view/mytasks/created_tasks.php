<?php
echo '<div class="table-wrapper">';
include ('created_tasks_dropdown.php');
  echo '<table id="created-tasks-table" border="1" class="sortable paginate dropdown" data-pagesize="10" data-offset="0">';
  echo '<thead><tr><th data-col="title">Title</th>
        <th data-col="description">Description</th>
        <th data-col="created_at">Created At</th>
        <th data-col="start_at">Start Date</th>
        <th data-col="updated_at">Last Updated At</th>
        <th data-col="min_bid">Min Bid</th>
        <th data-col="max_bid">Max Bid</th>
        <th data-col="assignee_username">Assigned To</th>
        <th data-col="creator_rating">Creator Rating</th>
        <th data-col="assignee_rating">Assignee Rating</th></tr></thead>';
  foreach($user_tasks as $task) {
    echo '<tr>';
    foreach($task as $item) {
      echo "<td>$item</td>";
    }
    echo "<td><a href='/tasks/task?title=" .
          $task->title . "&creator_username=" .
          $_SESSION['user']->username .
          "'>Link</a></td>";
          echo "<td><a href='/tasks/edittask?title=" .
                $task->title . "&creator_username=" .
                $_SESSION['user']->username .
                "'>Edit</a></td>";
          echo "<td><a href='/tasks/deletetask?title=" .
                $task->title . "&creator_username=" .
                $_SESSION['user']->username .
                "'>Delete</a></td>";
    echo '</tr>';
  }
  echo '</table>';
  echo '</div>';
?>
