<?php
  echo '<div class="table-wrapper">';
  include ('bidded_history_dropdown.php');
  echo '<table id="bidded-history-table" border="1" class="sortable paginate dropdown" data-order_by="title"  data-dir="ASC" data-pagesize="10" data-offset="0">';
  echo '<thead><tr><th data-col="title">Title</th>
        <th data-col="description">Description</th>
        <th data-col="start_at">Start At</th>
        <th data-col="myBid">My Bid</th>
        <th data-col="winning_bid">Winning Bid</th>
        <th data-col="creator_username">Created by</th>
        <th data-col="assignee_username">Assigned To</th></tr></thead>';
  echo '<tbody>';
  foreach($history_tasks as $task) {
    echo '<tr>';
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
  echo '</tbody>';
  echo '</table>';
  echo '</div>';
?>
