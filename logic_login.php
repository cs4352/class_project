<?php
require('config.php');
require(dirname(__FILE__) . '/api/notebook.php');

// Start the session.
session_start();

// Unset any previous session errors.
unset($_SESSION['login_error']);

// If the user is already logged in, send them to the home page.
if (isset($_SESSION['current_user'])) {
  $host = $_SERVER['HTTP_HOST'];
  $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $extra = 'index';
  header("location: http://$host$uri/$extra");
}

// Make sure we actually were brought here from the login page, if not,
// redirect back.
if (!isset($_POST['sign_in']) && !isset($_POST['create'])) {
  $host = $_SERVER['HTTP_HOST'];
  $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $extra = 'home';
  header("location: http://$host$uri/$extra");
}

// Running error detection.
$error = '';

// Check if we are trying to sign-in.
if (isset($_POST['sign_in'])) {

  // Define $username and $password
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Get any password associated with the entered email.
  $sql = mysqli_connect($_DB_HOST_, $_DB_USER_, $_DB_PASS_) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
  mysqli_select_db($sql, $_DB_NAME_) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
  $query = 'SELECT ID, PASSWORD, FNAME, LNAME FROM ' . $_DB_USERS_TABLE_ . ' WHERE EMAIL=' . "'" . $email . "'";
  $result = mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
  $row = mysqli_fetch_row($result);
  $id = $row[0];
  $pass = $row[1];
  $fname = $row[2];
  $lname = $row[3];

  if (isset($pass) && $password == $pass) {

    // Save the user in the session.
    $_SESSION['current_user'] = $id;
    $_SESSION['fname'] = $fname;
    $_SESSION['lname'] = $lname;
  } else {

    // Save the error.
    $error = 'Looks like you entered an incorrect password.';
  }

  // Check to see if we have any errors. If we do, redirect back to the login
  // page, if not, redirect home.
  if (empty($error)) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php';
    header("location: http://$host$uri/$extra");
  } else {
    $_SESSION['login_error']['message'] = $error;
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'login.php';
    header("location: http://$host$uri/$extra");
  }
}
elseif (isset($_POST['create'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $fname = ucfirst($_POST['fname']);
  $lname = ucfirst($_POST['lname']);
  // Ensure that the email doesn't already exist.
  $sql = mysqli_connect($_DB_HOST_, $_DB_USER_, $_DB_PASS_) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
  mysqli_select_db($sql, $_DB_NAME_) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
  $query = 'SELECT * FROM ' . $_DB_USERS_TABLE_ . ' WHERE EMAIL=' . "'" . $_POST['email'] . "'";
  $result = mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
  if (!mysqli_fetch_row($result)) {
    // Save the profile info.
    $query = "INSERT INTO $_DB_USERS_TABLE_
      (EMAIL, PASSWORD, FNAME, LNAME)
      VALUES
      ('$email', '$password', '$fname', '$lname')";
    mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
    $query = "SELECT LAST_INSERT_ID()";
    $result = mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
    $row = mysqli_fetch_row($result);

    // Save the user in the session.
    $_SESSION['current_user'] = $row[0];
    $_SESSION['fname'] = $fname;
    $_SESSION['lname'] = $lname;

    // Create a default notebook for the user.
    notebookCreateNotebook("Default", $row[0]);

  } else {
    $error = 'Sorry but that email address has already been used.';
  }

  // Check to see if we have any errors. If we do, redirect back to the login
  // page, if not, redirect home.
  if (empty($error)) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php';
    header("location: http://$host$uri/$extra");
  } else {
    $_SESSION['login_error']['message'] = $error;
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'login.php';
    header("location: http://$host$uri/$extra");
  }
}
?>
