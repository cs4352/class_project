<?php

require_once(dirname(__FILE__) . '/../../notebook.php');

if (isset($_GET['name']) && isset($_GET['uid'])) {
  print(json_encode(notebookCreateNotebook($_GET['name'], $_GET['uid'])));
}
else {
  echo json_encode(array(400 => "Invalid arguments"));
}
