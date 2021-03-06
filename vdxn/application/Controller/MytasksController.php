<?php
namespace Mini\Controller;
session_start();
use Mini\Model\Task;

class MytasksController
{
    public function index()
    {
        $this->created();
    }
    public function created()
    {
      $Task = new Task();
      $username = $_SESSION['user']->username;
      $user_tasks = $Task->getAllUserTasks($username, 0, 10, 'title', 'ASC');
      $history_tasks = $Task->getAllHistoryUserTasks($username, 0, 10,'title', 'ASC');
      $num_user_tasks = count($Task->getAllUserTasks($username, NULL, NULL, NULL, NULL));
      $num_history_tasks = count($Task->getAllHistoryUserTasks($username, NULL, NULL, NULL, NULL));
      // load views
      require APP . 'view/_templates/header.php';
      require APP . 'view/mytasks/created.php';
      require APP . 'view/_templates/footer.php';
    }
    public function bidded()
    {
      $Task = new Task();
      $username = $_SESSION['user']->username;
      $user_tasks = $Task->getAllCurrentBiddedTasks($username, 0, 10, 'title', 'ASC');
      $history_tasks = $Task->getAllHistoryBiddedTasks($username, 0, 10, 'title', 'ASC');
      $num_user_tasks = count($Task->getAllCurrentBiddedTasks($username, NULL, NULL, NULL, NULL));
      $num_history_tasks = count($Task->getAllHistoryBiddedTasks($username, NULL, NULL, NULL, NULL));
      // load views
      require APP . 'view/_templates/header.php';
      require APP . 'view/mytasks/bidded.php';
      require APP . 'view/_templates/footer.php';
    }
}
