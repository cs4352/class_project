<?php

function notebookCreateNotebook($name, $uid) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();
  $query = <<<SQL
INSERT INTO NOTEBOOKS
(NAME, UID)
VALUES
("$name", $uid)
SQL;

  mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));

  // Return the newly created notebook's ID.
  $query = "SELECT LAST_INSERT_ID()";
  $result = mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
  $row = mysqli_fetch_row($result);
  $id = $row[0];

  return $id;
}

function notebookGetUserNotebooks($uid) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();

  $query = <<<SQL
SELECT * FROM NOTEBOOKS
WHERE UID=$uid;
SQL;
  $result = mysqli_query($sql, $query);

  $notebooks = array();
  while ($obj = mysqli_fetch_object($result)) {
    $notebooks[] = $obj;
  }

  return $notebooks;
}

function notebookGetUserNotebookByName($uid, $name) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();

  $query = <<<SQL
SELECT * FROM NOTEBOOKS
WHERE UID=$uid AND NAME="$name";
SQL;
  $result = mysqli_query($sql, $query);
  $notebook = mysqli_fetch_object($result);

  return $notebook;
}
