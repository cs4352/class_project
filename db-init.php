<?php
require 'config.php';

// Connect to the MYSQL server.
$sql = mysqli_connect($_DB_HOST_, $_DB_USER_, $_DB_PASS_) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));

// Drop the database.
if ($_GET['drop'] == 'true') {
  $query = 'DROP DATABASE ' . $_DB_NAME_;
  mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
  echo 'DB dropped<br />';
}

// Create the database and select it.
$query = 'CREATE DATABASE IF NOT EXISTS ' . $_DB_NAME_;
mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
mysqli_select_db($sql, $_DB_NAME_);

// Create the users table.
$query = 'CREATE TABLE IF NOT EXISTS ' . $_DB_USERS_TABLE_ . '(
  ID int(11) AUTO_INCREMENT,
  EMAIL varchar(255) UNIQUE NOT NULL,
  PASSWORD varchar(255) NOT NULL,
  FNAME varchar(255) NOT NULL,
  LNAME varchar(255) NOT NULL,
  NOTIFICATION varchar(255),
  PRIMARY KEY (ID)
  )';
mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));

// Create the files table.
$query = 'CREATE TABLE IF NOT EXISTS ' . $_DB_FILES_TABLE_ . '(
  ID int(11) AUTO_INCREMENT,
  UPLOADED int(11) NOT NULL,
  ACCESSED int(11) NOT NULL,
  UID int(11) NOT NULL,
  NAME varchar(255) UNIQUE NOT NULL,
  FILETYPE varchar(255) NOT NULL,
  PRIMARY KEY (ID)
  )';
mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));

// Create the clippings table.
$query = 'CREATE TABLE IF NOT EXISTS ' . $_DB_CLIPPINGS_TABLE_ . '(
  ID int(11) AUTO_INCREMENT,
  CREATED int(11) NOT NULL,
  ACCESSED int(11) NOT NULL,
  UID int(11) NOT NULL,
  NOTEBOOK_ID int(11) NOT NULL,
  ORIGFILE int(11) NOT NULL,
  CONTENT longtext NOT NULL,
  NAME varchar(255),
  SUBTITLE varchar(255),
  COLOR varchar(255),
  PRIMARY KEY (ID)
  )';
mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));

// Create the shared clippings table.
$query = 'CREATE TABLE IF NOT EXISTS ' . $_DB_SHARED_CLIPPINGS_TABLE_ . '(
  ID int(11) AUTO_INCREMENT,
  CID int(11) NOT NULL,
  ORIGCID int(11) NOT NULL,
  UID int(11) NOT NULL,
  PRIMARY KEY (ID)
  )';
mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));

// Create the comments table.
$query = "CREATE TABLE IF NOT EXISTS COMMENTS (
  ID int(11) AUTO_INCREMENT,
  CID int(11) NOT NULL,
  UID int(11) NOT NULL,
  CONTENT longtext,
  PRIMARY KEY (ID)
)";
mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));

// Create the notebooks table.
$query = <<<SQL
CREATE TABLE IF NOT EXISTS NOTEBOOKS (
  ID int(11) AUTO_INCREMENT,
  NAME varchar(255) NOT NULL,
  UID int(11) NOT NULL,
  PRIMARY KEY (ID),
  UNIQUE KEY (NAME, UID)
)
SQL;
mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));

echo 'DB initialized<br />';
$img = 'http://' . $_SERVER['HTTP_HOST'] . $_IWP_DIR_ . '/assets/images/party_hard.gif';
echo '<img src="' . $img . '" />';
