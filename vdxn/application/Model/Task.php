<?php

namespace Mini\Model;

use Mini\Core\Model;

class Task extends Model
{
    private $DEFAULT_FROM_DATE = '0000-00-00 00:00:00:000';
    private $DEFAULT_TO_DATE = '2999-00-00 00:00:00:000';

    /**
     * Get all tasks from database
     */
     public function getAllTasks()
     {
         $sql = 'SELECT * FROM Task';
         $query = $this->db->prepare($sql);
         $query->execute();
         return $query->fetchAll();
     }

    public function getTask($title, $creator_username)
    {
      $sql = "SELECT * FROM Task WHERE title='$title' AND creator_username='$creator_username'";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    public function getAllUserTasks($tkername, $offset = NULL, $limit = NULL, $order_by = NULL, $dir = 'ASC')
    {
      $sql = $this->getAllUserTasksQuery($tkername, $offset, $limit, $order_by, $dir);
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetchAll();
    }

    public function getAllUserTasksQuery($tkername, $offset = NULL, $limit = NULL, $order_by = NULL, $dir = 'ASC')
    {
      $sql = "SELECT title, description, created_at,
      start_at, updated_at, min_bid, max_bid, assignee_username, creator_rating,
      assignee_rating
      FROM Task WHERE creator_username='$tkername' AND completed_at IS NULL";
      if(isset($order_by)) {
        if(!isset($dir)) {
          $dir = 'ASC';
        }
        $sql .= " ORDER BY $order_by $dir";
      }
      if(isset($limit)) {
        $sql .= " LIMIT $limit";
      }
      if(isset($offset)) {
        $sql .= " OFFSET $offset";
      }

      return $sql;
    }

    public function getAllHistoryUserTasks($tkername, $offset = NULL, $limit = NULL, $order_by = NULL, $dir = 'ASC')
    {
      // tasks created by this user and has been completed
      $sql = $this->getAllHistoryUserTasksQuery($tkername, $offset, $limit, $order_by, $dir);
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetchAll();
    }

    public function getAllHistoryUserTasksQuery($tkername, $offset = NULL, $limit = NULL, $order_by = NULL, $dir = 'ASC')
    {
      $sql = "SELECT title, description, created_at,
      start_at, completed_at, assignee_username, creator_rating,
      assignee_rating
      FROM Task WHERE creator_username='$tkername' AND completed_at IS NOT NULL";
      if(isset($order_by)) {
        if(!isset($dir)) {
          $dir = 'ASC';
        }
        $sql .= " ORDER BY $order_by $dir";
      }
      if(isset($limit)) {
        $sql .= " LIMIT $limit";
      }
      if(isset($offset)) {
        $sql .= " OFFSET $offset";
      }
      return $sql;
    }

    public function getAllCurrentBiddedTasks($bdername,$offset = NULL, $limit = NULL, $order_by = NULL, $dir = 'ASC')
    {
      // tasks this user has bidded for
      $sql = $this->getAllCurrentBiddedTasksQuery($bdername, $offset, $limit, $order_by, $dir);
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetchAll();
    }

    public function getAllCurrentBiddedTasksQuery($bdername, $offset = NULL, $limit = NULL, $order_by = NULL, $dir = 'ASC')
    {
      $sql = "SELECT title, description,
      start_at, s.curr_min_bid as minB,s.curr_max_bid as maxB, myBid,
      creator_username, creator_rating,
      assignee_rating FROM
      (SELECT title, description, start_at, b1.amount AS myBid, creator_username, creator_rating, assignee_rating
      FROM Task
      INNER JOIN Bid b1 ON Task.creator_username = b1.task_creator_username
      AND Task.title = b1.task_title
      WHERE b1.bidder_username =  '$bdername'
      AND Task.assignee_username IS NULL)t
      INNER JOIN
      (SELECT b2.task_title as b2_title, b2.task_creator_username as b2_creator, MAX(b2.amount) as curr_max_bid, MIN(b2.amount) as curr_min_bid
      FROM Bid b2 GROUP BY b2.task_title)s
      ON s.b2_title = t.title AND s.b2_creator = t.creator_username";
      if(isset($order_by)) {
        if(!isset($dir)) {
          $dir = 'ASC';
        }
        $sql .= " ORDER BY $order_by $dir";
      }
      if(isset($limit)) {
        $sql .= " LIMIT $limit";
      }
      if(isset($offset)) {
        $sql .= " OFFSET $offset";
      }
      return $sql;
    }

    public function getAllHistoryBiddedTasks($bdername, $offset = NULL, $pagesize = NULL, $order_by = NULL, $dir = 'ASC')
    {
      // tasks this user has bidded for and has had an assignee chosen
      $sql = $this->getAllHistoryBiddedTasksQuery($bdername, $offset, $pagesize, $order_by, $dir);
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetchAll();
    }

    public function getAllHistoryBiddedTasksQuery($bdername, $offset = NULL, $limit = NULL, $order_by = NULL, $dir = 'ASC') {
      $sql = "SELECT title, description,
      start_at, myBid, winningBid.amount as winning_bid, creator_username,
      assignee_username
      FROM
      ((SELECT title, description,
      start_at, myBid.amount as myBid, creator_username,
      assignee_username FROM Task
      INNER JOIN Bid myBid ON Task.creator_username=task_creator_username AND Task.title = myBid.task_title
      INNER JOIN User ON username=bidder_username
      WHERE bidder_username='$bdername' AND assignee_username IS NOT NULL) t
      INNER JOIN Bid winningBid ON winningBid.bidder_username=t.assignee_username AND winningBid.task_creator_username = t.creator_username AND t.title = winningBid.task_title
      INNER JOIN User winner ON winner.username = t.assignee_username)";
      if(isset($order_by)) {
        if(!isset($dir)) {
          $dir = 'ASC';
        }
        $sql .= " ORDER BY $order_by $dir";
      }
      if(isset($limit)) {
        $sql .= " LIMIT $limit";
        if(isset($offset)) {
          $sql .= " OFFSET $offset";
        }
      }

      return $sql;
    }

    /**
     * Find all tasks whose title matches the string pattern.
     *
     * @param $search_string   String pattern to be matched
     * @return mixed
     */

    public function findAllTasksContaining($search_string)
    {
      $sql = 'SELECT * FROM (Task t LEFT JOIN Tag_task g ON t.title = g.task_title AND t.creator_username = g.task_creator_username)
              LEFT JOIN Category_task c ON t.title = c.task_title AND t.creator_username = c.task_creator_username
              WHERE t.title LIKE "%' . $search_string . '%"  OR g.tag_name LIKE "%' . $search_string . '%" OR c.category_name LIKE "%' . $search_string . '%"';
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetchAll();
    }

    public function findAllTagsContaining($search_string) {
        $sql = 'SELECT * FROM Tag t WHERE t.name LIKE "%' . $search_string . '%"';
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Sort all tasks by given attributes.
     *
     * @param $attribute_str
     * @return mixed
     */

    public function sortAllTasks($attribute_str) {
        $sql = "SELECT Task.title, Task.description, Task.created_at, Task.updated_at, Task.start_at, Task.end_at,
        Task.min_bid, Task.max_bid, Task.creator_username, Task.assignee_username, Task.completed_at,
        Task.remarks, TIMESTAMPDIFF(SECOND, Task.end_at, Task.start_at) AS duration FROM Task ORDER BY $attribute_str";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        return $query -> fetchAll();
    }

    public function filterAllTasks($str) {
        $sql = "SELECT t.title, t.description, t.created_at, t.updated_at, t.start_at, t.end_at,
        t.min_bid, t.max_bid, t.creator_username, t.assignee_username, t.completed_at,
        t.remarks, TIMESTAMPDIFF(SECOND, t.end_at, t.start_at) AS duration FROM (Task t LEFT JOIN Tag_task g ON t.title = g.task_title AND t.creator_username = g.task_creator_username)
              LEFT JOIN Category_task c ON t.title = c.task_title AND t.creator_username = c.task_creator_username
              WHERE $str";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        return $query -> fetchAll();
    }

    public function createTask($task_params)
    {
      $time = date("Y-m-d H:i:s");

      // If no proper start_at_date is given, force a NULL value to force the
      // integrity constraints to come into effect
      $start_at_date = strlen($task_params['taskdate']) == 0 ? 'NULL' : $task_params['taskdate'];
      $end_at_date = strlen($task_params['enddate']) == 0 ? 'NULL' : $task_params['enddate'];

      // Since bids are not required, and have a default value, set them here
      $min_bid = strlen($task_params['min_bid']) == 0 ? 1 : $task_params['min_bid'];
      $max_bid = strlen($task_params['max_bid']) == 0 ? 100 : $task_params['max_bid'];

      $sql = "INSERT INTO `mini`.`Task`
      (
      `title`,
      `description`,
      `created_at`,
      `updated_at`,
      `start_at`,
      `end_at`,
      `min_bid`,
      `max_bid`,
      `creator_username`,
      `assignee_username`,
      `completed_at`,
      `creator_rating`,
      `assignee_rating`)
      VALUES (
      '".$task_params['title']."',
      '".$task_params['description']."',
      '$time',
      NULL,
      '$start_at_date',
      '$end_at_date',
      '$min_bid',
      '$max_bid',
      '".$_SESSION['user']->username."',
      NULL,
      NULL,
      NULL,
      NULL)";
      $tags_string = $task_params['tagsinput'];
      $tags_arr = explode(",", $tags_string);
      for($i = 0; $i < sizeof($tags_arr); $i++) {
          if($this -> existsTag($tags_arr[$i])) {
              $this->createTagTask($tags_arr[$i], $_SESSION['user']->username, $task_params['title'], $time);
          } else {
              $this -> createTag($tags_arr[$i], $time);
              $this->createTagTask($tags_arr[$i], $_SESSION['user']->username, $task_params['title'], $time);
          }
      }
      $this -> createCategoryTask($_SESSION['user']->username,$task_params['title'], $task_params['category'],$time);
      $query = $this->db->prepare($sql);
      return $query->execute();
    }

    public function createCategoryTask($creator_name, $task_title, $task_category,$created_at) {
         $sql = "INSERT INTO `mini`.`Category_task`
         (`category_name`,
         `task_title`,
         `task_creator_username`,
         `created_at`,
         `updated_at`)
         VALUES (
         '".$task_category."',
         '".$task_title."',
         '".$creator_name."',
         '".$created_at."',
         NULL
         )";
         $query = $this -> db -> prepare($sql);
         return $query -> execute();
    }

    /**
     * check if a tag exists in the database, and return a boolean value of whether the tag exists.
     *
     * @param $tag_name Name of the tag
     * @return bool     Boolean value indicating whether tag exists in Tag table.
     */
    public function existsTag($tag_name) {
         $sql = "SELECT COUNT(*) AS count FROM Tag WHERE Tag.name = '$tag_name'";
         $query = $this -> db -> prepare($sql);
         $query -> execute();
         $result = $query -> fetch();
         return $result -> count != 0;
    }

    /**
     * Create a tag instance in Tag table
     *
     * @param $tag_name     Name of tag
     * @param $created_at   Time of tag creation
     * @return mixed
     */

    public function createTag($tag_name, $created_at) {
        $sql = "INSERT INTO `mini`.`Tag`
         (`name`,
         `created_at`,
         `updated_at`)
         VALUES (
         '".$tag_name."',
         '".$created_at."',
         NULL
         )";
        $query = $this -> db -> prepare($sql);
        return $query -> execute();
    }

    /**
     * Create an instance of tag-task relationship in Tag_task table.
     *
     * @param $tag_name          Name of tag
     * @param $creator_name      Username of task creator
     * @param $task_title        Title of task
     * @param $created_at        Time of task creation
     * @return mixed
     */
    public function createTagTask($tag_name, $creator_name, $task_title, $created_at){
        $sql = "INSERT INTO `mini`.`Tag_task`
         (`tag_name`,
         `task_creator_username`,
         `task_title`,
         `created_at`,
         `updated_at`)
         VALUES (
         '".$tag_name."',
         '".$creator_name."',
         '".$task_title."',
         '".$created_at."',
         NULL
         )";
        $query = $this -> db -> prepare($sql);
        return $query -> execute();
    }

    public function deleteTask($title, $creator_username)
    {
      $sql = "DELETE FROM Task WHERE Task.title='$title' AND Task.creator_username='$creator_username';";
      $query = $this->db->prepare($sql);
      return $query->execute();
    }

    public function editTask($task_title, $task_creator_username, $params)
    {
      $time = date("Y-m-d H:i:s");

      $sql = "UPDATE Task SET ".
        "title='".$params['title'].
        "', description='".$params['description'].
        "', updated_at='".$time.
        "', start_at='".$params['taskdate'].
        "', end_at='".$params['enddate'].
        "', min_bid='".$params['min_bid'].
        "', max_bid='".$params['max_bid'].
        "' WHERE title='".$task_title."' AND creator_username='".$task_creator_username."';";

      $query = $this->db->prepare($sql);
      return $query->execute();
    }

    //==========================================
    // TASK CREATOR ASSIGNING DOER FUNCTIONS
    //==========================================
    /**
     * Gets the specified user attributes of a task's assignee / doer.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @return Object    User profile of the assignee/doer for this task
     */
    public function getTaskAssigneeUserProfile($task_title, $task_creator_username)
    {
      $sql = "SELECT username, first_name, last_name, contact, email, assignee_rating FROM Task t, User u ".
        "WHERE t.title = '".$task_title.
        "' AND t.creator_username = '".$task_creator_username.
        "' AND t.assignee_username = u.username";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    /**
     * Gets the date when this task was marked as completed by the task creator.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @return String    Date of completion of this task
     */
    public function getTaskCompletedDate($task_title, $task_creator_username)
    {
      $sql = "SELECT completed_at FROM Task t ".
        "WHERE t.title = '".$task_title.
        "' AND t.creator_username = '".$task_creator_username."'";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    /**
     * Gets this task's creator rating.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @return Float     Task creator's rating
     */
    public function getTaskCreatorRating($task_title, $task_creator_username)
    {
      $sql = "SELECT creator_rating FROM Task t ".
        "WHERE t.title = '".$task_title.
        "' AND t.creator_username = '".$task_creator_username."'";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    /**
     * Gets this task's doer's rating.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @return Float     Task doer's rating
     */
    public function getTaskDoerRating($task_title, $task_creator_username)
    {
      $sql = "SELECT assignee_rating FROM Task t ".
        "WHERE t.title = '".$task_title.
        "' AND t.creator_username = '".$task_creator_username."'";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    /**
     * Marks a Task as completed. This is done by the Task creator.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     */
    public function markTaskAsComplete($task_title, $task_creator_username)
    {
      $time = date("Y-m-d H:i:s");
      $sql = "UPDATE Task ".
        "SET completed_at='".$time."'".
        " WHERE title='".$task_title."' AND creator_username='".$task_creator_username."';";
      $query = $this->db->prepare($sql);
      return $query->execute();
    }

    /**
     * Assigns a bidder to the Task. This is done by the Task creator.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @param  String $bidder_username          Username of the bidder
     */
    public function assignBidderToTask($task_title, $task_creator_username, $bidder_username)
    {
      $sql = "UPDATE Task SET assignee_username='".$bidder_username.
        "' WHERE title='".$task_title."' AND creator_username='".$task_creator_username."';";
      $query = $this->db->prepare($sql);
      return $query->execute();
    }

    /**
     * Sets the assignee rating for a given task. This is done by the Task creator.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @param  Float  $assignee_rating          Rating of the doer, given by
     *                                          the task creator
     */
    public function rateTaskDoer($task_title, $task_creator_username, $assignee_rating)
    {
      $sql = "UPDATE Task SET assignee_rating='".$assignee_rating.
        "' WHERE title='".$task_title."' AND creator_username='".$task_creator_username."';";
      $query = $this->db->prepare($sql);
      return $query->execute();
    }

    /**
     * Sets the assignee rating for a given task. This is done by the Task doer.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @param  Float  $creator_rating           Rating of the creator, given by
     *                                          the task doer
     */
    public function rateTaskCreator($task_title, $task_creator_username, $creator_rating)
    {
      $sql = "UPDATE Task SET creator_rating='".$creator_rating.
        "' WHERE title='".$task_title."' AND creator_username='".$task_creator_username."';";
      $query = $this->db->prepare($sql);
      return $query->execute();
    }




    //==========================================
    // BIDDING RELATED FUNCTIONS
    //==========================================

    /**
     * Gets all the bids for this specified task.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @return Array    Array of bids for this specified task
     */
    public function getBids($task_title, $task_creator_username)
    {
      $sql = "SELECT
      `task_title`,
      `task_creator_username`,
      `bidder_username`,
      `amount`,
      `details`,
      `created_at`,
      `updated_at`
      FROM Bid
      WHERE task_title='$task_title'
      AND task_creator_username='$task_creator_username'";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetchAll();
    }

    /**
     * Gets top N bids for this specified task.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @param  Int    $topN                     The top N bids ordered either in
     *                                          ascending or descending order.
     * @param  String $orderByAscOrDesc         'ASC' to order in ascending order,
     *                                          'DESC' to order in descending order.
     * @return Array    Array of top N bids for this specified task
     */
    public function getTopNBids($task_title, $task_creator_username, $topN, $orderByAscOrDesc)
    {
      $sql = "SELECT
      `task_title`,
      `task_creator_username`,
      `bidder_username`,
      `amount`,
      `details`,
      `created_at`,
      `updated_at`
      FROM Bid
      WHERE task_title='$task_title'
      AND task_creator_username='$task_creator_username'
      ORDER BY Bid.amount ".$orderByAscOrDesc." LIMIT ".$topN;
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetchAll();
    }

    /**
     * Retrieves the bidder's bid for this task. Returns an empty array if
     * this bidder does not have any bids for this task.
     *
     * @param  String $task_title         Title of the task
     * @param  String $bidder_username    Username of the bidder
     * @return Object    Bid object representing the bid made for this task by
     *                   the specified bidder.
     */
    public function getUserBidForTask($task_title, $bidder_username)
    {
      $sql = "SELECT
      `task_title`,
      `task_creator_username`,
      `bidder_username`,
      `amount`,
      `details`,
      `created_at`,
      `updated_at`
      FROM Bid
      WHERE task_title='$task_title'
      AND bidder_username='$bidder_username'";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    /**
     * Creates a new bid for this task, associated with the current logged in
     * user.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @param  Int    $amount                   Amount of bid points placed for this task
     * @param  String $details                  Additional comments made for this bid
     */
    public function createTaskBid($task_title, $task_creator_username, $amount, $details)
    {
      $time = date("Y-m-d H:i:s");
      $sql = "INSERT INTO Bid (
        `task_title`,
        `task_creator_username`,
        `bidder_username`,
        `details`,
        `amount`,
        `created_at`,
        `updated_at`)
      VALUES (
        '".$task_title."',
        '".$task_creator_username."',
        '".$_SESSION['user']->username."',
        '".$details."',
        $amount,
        '$time',
        NULL)";
      $query = $this->db->prepare($sql);
      return $query->execute();
    }

    /**
     * Edits the amount and details of a bid.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @param  String $bidder_username          Username of the bidder
     * @param  Int    $amount                   Amount of bid points placed for this task
     * @param  String $details                  Additional comments made for this bid
     */
    public function editTaskBid($task_title, $task_creator_username, $bidder_username, $amount, $details)
    {
      $time = date("Y-m-d H:i:s");
      $sql = "UPDATE Bid SET amount=".$amount.", details='".$details."', updated_at='".$time."'".
        " WHERE task_title='".$task_title."' AND task_creator_username='".
        $task_creator_username."' AND bidder_username='".$bidder_username."';";
      $query = $this->db->prepare($sql);
      return $query->execute();
    }

    /**
     * Deletes a bid from the Bid table.
     *
     * @param  String $task_title               Title of the task
     * @param  String $task_creator_username    Username of the task creator
     * @param  String $bidder_username          Username of the bidder
     */
    public function deleteTaskBid($task_title, $task_creator_username, $bidder_username)
    {
      $sql = "DELETE FROM Bid".
        " WHERE task_title='".$task_title."'".
        " AND task_creator_username='".$task_creator_username."'".
        " AND bidder_username='".$bidder_username."';";
      $query = $this->db->prepare($sql);
      return $query->execute();
    }

    //===========================
    //TASK CATEGORY AND TAG RELATED FUNCTIONS
    //===========================

    /**
     * Get the Tags associated with a task from Tag_task table.
     *
     * @param String $task_title    Title of the task
     * @param String $creator_name  Username of task creator
     */
    public function getTagsOfTask($task_title, $creator_name) {
        $sql = "SELECT t.tag_name AS tags FROM Tag_task t WHERE t.task_title = '$task_title' AND t.task_creator_username = '$creator_name'";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        $results = $query -> fetchAll();
        $tags = "";
        if (sizeof($results)==0) {
            return '';
        } else {
            foreach ($results as $result) {
                $tag = $result -> tags;
                $tags .= "#".$tag." ";
            }
            return $tags;
        }
    }

    public function getTagsArrayOfTask($task_title, $creator_name) {
        $sql = "SELECT t.tag_name FROM Tag_task t WHERE t.task_title = '$task_title' AND t.task_creator_username = '$creator_name'";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        return $query -> fetchAll();
    }

    /**
     * Get the Category label of a task from Category_task table.
     *
     * @param String $task_title      Title of the task
     * @param String $creator_name    Username of task creator
     */
    public function getCategoryOfTask($task_title, $creator_name) {
        $sql = "SELECT g.category_name AS category FROM Category_task g WHERE g.task_title = '$task_title' AND g.task_creator_username = '$creator_name'";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        $result = $query -> fetch();
        if ($result == '') {
            return '';
        } else {
            return $result->category;
        }
    }

    public function getTasksWithSameCreator($task_name, $creator_name) {
        $sql = "SELECT * FROM Task t WHERE t.title <> '$task_name' AND t.creator_username = '$creator_name'";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        return $query -> fetchAll();
    }


    //==========================================
    // ADMIN SYSTEM STATS FUNCTIONS
    //==========================================
    /**
     * Gets the no. of completed & uncompleted tasks
     *
     * @return Object    Number of completed tasks as one value & number of
     *                   uncompleted tasks as another value.
     */
    public function getNumCompletedUncompletedTasks($from_date = NULL, $to_date = NULL) {
      $from_date = $from_date ? $from_date : $this->DEFAULT_FROM_DATE;
      $to_date = $to_date ? $to_date : $this->DEFAULT_TO_DATE;
      $sql = "SELECT COUNT(completed_at) AS num_tasks_completed,
        (COUNT(*) - COUNT(completed_at)) AS num_tasks_uncompleted
        FROM Task
        WHERE created_at BETWEEN '".$from_date."' AND '".$to_date."'";

      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    /**
     * Gets the no. of completed tasks between a specified datetime range.
     *
     * @param  String $from_date   Start Date in the format of YYYY-MM-DD hh:mm:ss:000
     * @param  String $to_date     End Date in the format of YYYY-MM-DD hh:mm:ss:000
     * @return Object    Number of completed tasks
     */
    public function getNumCompletedTasksBetween($from_date = NULL, $to_date = NULL) {
      $from_date = $from_date ? $from_date : $this->DEFAULT_FROM_DATE;
      $to_date = $to_date ? $to_date : $this->DEFAULT_TO_DATE;

      $sql = "SELECT COUNT(completed_at) AS num_tasks_completed
        FROM Task
        WHERE completed_at BETWEEN '".$from_date."' AND '".$to_date."'";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    /**
     * Gets the no. of bids created between a specified datetime range.
     *
     * @param  String $from_date   Start Date in the format of YYYY-MM-DD hh:mm:ss:000
     * @param  String $to_date     End Date in the format of YYYY-MM-DD hh:mm:ss:000
     * @return Object    Number of bids created
     */
    public function getNumBidsBetween($from_date = NULL, $to_date = NULL) {
      $from_date = $from_date ? $from_date : $this->DEFAULT_FROM_DATE;
      $to_date = $to_date ? $to_date : $this->DEFAULT_TO_DATE;

      $sql = "SELECT COUNT(*) AS num_bids
        FROM Bid
        WHERE created_at BETWEEN '".$from_date."' AND '".$to_date."'";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    /**
     * Gets the task(s) with the largest number of bids (most popular tasks)
     * Note: Can have more than 1 task that's "most popular"
     *
     * @return Array    Array of tasks with values title, description &
     *                  creator_username
     */
    public function getMostPopularTasks() {
      $sql = "SELECT t.title AS title, t.description AS description,
        t.creator_username AS username, COUNT(*) AS num_bids
        FROM Task t, Bid b
        WHERE t.title = b.task_title AND t.creator_username = b.task_creator_username
        GROUP BY t.title, t.creator_username
        HAVING COUNT(*) >= ALL (
            SELECT COUNT(*)
            FROM Task t1, Bid b1
            WHERE t1.title = b1.task_title AND t1.creator_username = b1.task_creator_username
            GROUP BY t1.title, t1.creator_username
        )";

      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetchAll();
    }

    /**
     * Get the number of users who bidded for at least 1 task
     *
     * @return Object    Number of users who did so
     */
    public function getNumWhoBiddedAtLeastOnce() {
      $sql = "SELECT COUNT(*) AS num_users FROM (
        SELECT b.bidder_username, COUNT(*) AS num_bids
        FROM Bid b
        GROUP BY b.bidder_username
        HAVING COUNT(*) >= 1
        ORDER BY COUNT(*) DESC
      ) AS NumBidsPerUserTable;";

      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
    }

    public function getCountOfCreatedTasksByMonth() {
        $sql = "DROP VIEW IF EXISTS count_tasks_created_by_month;";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        $sql = "CREATE VIEW count_tasks_created_by_month AS
                SELECT MONTH(t.created_at) AS month, COUNT(*) AS num_tasks_created
                FROM Task t
                GROUP BY MONTH(t.created_at)
                ORDER BY MONTH(t.created_at) ASC;";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        $sql = "SELECT m.value, c.num_tasks_created
                FROM Months m LEFT JOIN count_tasks_created_by_month c
                ON m.value = c.month
                GROUP BY m.value
                ORDER BY CAST(m.value AS UNSIGNED) ASC;";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        return $query -> fetchAll();
    }
    public function getCountOfBiddedTasksByMonth() {
        $sql = "DROP VIEW IF EXISTS count_bids_created_by_month;
                CREATE VIEW count_bids_created_by_month AS
                SELECT MONTH(b.created_at) AS month, COUNT(*) AS num_bids_created
                FROM Bid b
                GROUP BY MONTH(b.created_at)
                ORDER BY MONTH(b.created_at) ASC; ";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        $sql =  "SELECT m.value, b.num_bids_created
                FROM Months m LEFT JOIN count_bids_created_by_month b
                ON m.value = b.month
                GROUP BY m.value
                ORDER BY CAST(m.value AS UNSIGNED) ASC;";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        return $query -> fetchAll();
    }
    public function getCountOfCompletedTasksByMonth() {
        $sql = "DROP VIEW IF EXISTS count_tasks_completed_by_month;
                CREATE VIEW count_tasks_completed_by_month AS
                SELECT MONTH(t.completed_at) AS month, COUNT(*) AS num_tasks_completed
                FROM Task t
                GROUP BY MONTH(t.completed_at)
                ORDER BY MONTH(t.completed_at) ASC;";
           $query = $this -> db -> prepare($sql);
        $query -> execute();
          $sql = "  SELECT m.value, t.num_tasks_completed
                FROM Months m LEFT JOIN count_tasks_completed_by_month t
                ON m.value = t.month
                GROUP BY m.value
                ORDER BY CAST(m.value AS UNSIGNED) ASC;";
        $query = $this -> db -> prepare($sql);
        $query -> execute();
        return $query -> fetchAll();
    }

}
