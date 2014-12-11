<?php

/**
 * Creates an account for the specified user.
 *
 * @param string $email
 *  The user's email address.
 * @param string $password
 *  The user's password.
 * @param string $fname
 *  The user's first name.
 * @param string $lname
 *  The user's last name.
 */
function userCreateUser($email, $password, $fname, $lname) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();
  $query = <<<SQL
INSERT INTO USERS
(EMAIL, PASSWORD, FNAME, LNAME)
VALUES
("$email", "$password", "$fname", "$lname")
SQL;

  mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));

  // Get the newly created user's ID.
  $query = "SELECT LAST_INSERT_ID()";
  $result = mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
  $row = mysqli_fetch_row($result);
  $id = $row[0];

  // Create the user's default notebook.
  notebookCreateNotebook("Default Notebook", $row[0]);

  return $id;
}

/**
 * Fetches a user's info using their id.
 *
 * @param int $id
 *  The user's id.
 *
 * @return null|object
 *  Returns the user object or NULL if no result was found.
 */
function getUserById($id) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();
  $query = "SELECT * FROM USERS WHERE ID=$id";
  $result = mysqli_query($sql, $query);
  $obj = mysqli_fetch_object($result);
  return $obj;
}

/**
 * Fetches a user's info using their email.
 *
 * @param string $email
 *  The user's email.
 *
 * @return null|object
 *  Returns the user object or NULL if no result was found.
 */
function getUserByEmail($email) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();
  $query = "SELECT * FROM USERS WHERE EMAIL='$email'";
  $result = mysqli_query($sql, $query);
  $obj = mysqli_fetch_object($result);
  return $obj;
}

/**
 * Fetches all users.
 *
 * @return null|object
 *  Returns the user object or NULL if no result was found.
 */
function getAllUsers() {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();
  $query = "SELECT * FROM USERS";
  $result = mysqli_query($sql, $query);

  $users = array();
  while ($obj = mysqli_fetch_object($result)) {
    $users[] = $obj;
  }
  return $users;
}

function getUserNotification($uid) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();
  $query = "SELECT NOTIFICATION FROM USERS WHERE ID=$uid";
  $result = mysqli_query($sql, $query);
  return mysqli_fetch_row($result);
}

function setUserNotification($uid, $notification) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();
  $query = "UPDATE USERS
            SET NOTIFICATION='$notification'
            WHERE ID=$uid";
  mysqli_query($sql, $query);
  return TRUE;
}

function unsetUserNotification($uid) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();
  $query = "UPDATE USERS
            SET NOTIFICATION=''
            WHERE ID=$uid";
  mysqli_query($sql, $query);
  return TRUE;
}

function updateUser($user) {
  // TODO: Add update user functionality.
}
