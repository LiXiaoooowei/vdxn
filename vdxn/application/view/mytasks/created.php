<div class="container">
    <h1>My Tasks</h1>
    <a href="/tasks/newtask">New</a>
    <div class="tasks__tabs-container">
      <span class="tasks__tab tasks__tab--selected">Created by Me
      </span>
      <span class="tasks__tab"><a href="/mytasks/getBiddedTasks">Bidded</a></span>
    </div>
    <div class="tasks__tab-results">
      <div class="tasks__tab">Ongoing</div>
      <?php include ("created_tasks.php") ?>
        <div class="tasks__tab">History</div>
      <?php include ("created_tasks_history.php") ?>
    </div>
</div>