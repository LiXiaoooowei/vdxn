<?php
  echo '<div class="table-wrapper"'>;
  include ('dropdown.php');
  echo '<table border="1" class="sortable paginate dropdown">';
  echo '<th data-col="title">Title</th>
        <th data-col="description">Description</th>
        <th data-col="start_at">Start At</th>
        <th data-col="curr_min_bid">Current Min bid</th>
        <th data-col="curr_max_bid">Current Max bid</th>
        <th data-col="myBid">My Bid</th>
        <th data-col="created_at">Created by</th>
        <th data-col="creator_rating">Creator Rating</th>
        <th data-col="assignee_rating">Assignee Rating</th></tr>';
  foreach($user_tasks as $task) {
    echo '<tr class="data-row" data-attr="edit link delete">';
    foreach($task as $item) {
      echo "<td>$item</td>";
    }
    echo "<td><a href='/tasks/task?title=" .
          $task->title . "&creator_username=" .
          $task->creator_username .
          "'>Link</a></td>";
          echo "<td><a href='/tasks/edittask?title=" .
                $task->title . "&creator_username=" .
                $task->creator_username .
                "'>Edit</a></td>";
          echo "<td><a href='/tasks/deletetask?title=" .
                $task->title . "&creator_username=" .
                $task->creator_username .
                "'>Delete</a></td>";
    echo '</tr>';
  }
  echo '</table>';
  echo '</div>';
?>
