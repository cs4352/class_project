<?php

require_once(dirname(__FILE__) . '/../../notebook.php');

if (isset($_GET['uid'])) {
  print(json_encode(notebookGetUserNotebooks($_GET['uid'])));
}
else {
  echo json_encode(array(400 => "Invalid arguments"));
}
