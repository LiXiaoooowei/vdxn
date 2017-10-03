<!-- Tasks which have been completed -->
<?php
  echo '<table border="1">';
  echo '<th>Title</th>
        <th>Description</th>
        <th>Created At</th>
        <th>Event Date</th>
        <th>Completed At</th>
        <th>Assigned To</th>
        <th>Creator Rating</th>
        <th>Assignee Rating</th></tr>';
  foreach($history_tasks as $task) {
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
?>
