<?php
namespace Mini\Controller;
session_start();
use Mini\Model\Task;

class MytasksController
{
    public function index()
    {
        if(!isset($_SESSION['user'])) {
            header('location: ' . URL . 'login');
        }

        $Task = new Task();
        // TO REMOVE
        $_SESSION['user_id'] = 1;
        $user_tasks = $Task->getAllUserTasks($_SESSION['user_id']);
        // load views
        require APP . 'view/_templates/header.php';
        require APP . 'view/mytasks/index.php';
        require APP . 'view/_templates/footer.php';
    }
}
