<?php
namespace Mini\Model;

use Mini\Core\Model;

class Account extends Model
{
  public function authenticate($username, $password) {
    $userRecord = $this->getUser($username, $password);

    if(empty($userRecord)) {
      $_SESSION['user'] = null;
      return false;
    } else {
      $_SESSION['user'] = $userRecord;
      return true;
    }
  }
  function getUser($username, $password) {
    $sql = "SELECT username, password_hash, contact, email, user_type FROM User WHERE
      `username`='$username'";
    $query = $this->db->prepare($sql);
    $query->execute();
    $result = $query->fetch();

    $verify = password_verify($password, $result->{'password_hash'});
    if ($verify) {
      return $result;
    } else {
      return null;
    }
  }
  function changePassword($username, $password_old, $password_new) {

    if(!$this->getUser($username, $password_old)) return false;

    $password_new_hash = password_hash($password_new, PASSWORD_DEFAULT);

    $sql = "UPDATE User SET `password_hash`='$password_new_hash'
      WHERE `username`='$username'";
    $query = $this->db->prepare($sql);
    return $query->execute();
  }
  function create($username, $email, $firstName, $lastName, $contactNumber, $password) {
    $time = date("Y-m-d H:i:s");
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO User
    (`username`,
      `first_name`,
      `last_name`,
      `password_hash`,
      `contact`,
      `email`,
      `created_at`,
      `updated_at`,
      `deleted_at`,
      `user_type`)
      VALUES (
        '$username',
        '$firstName',
        '$lastName',
        '$passwordHash',
        '$contactNumber',
        '$email',
        '$time',
        '',
        '',
        'User'
    );";
    $query = $this->db->prepare($sql);
    return $query->execute();
  }
}
