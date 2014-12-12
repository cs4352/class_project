<?php

require_once(dirname(__FILE__) . '/../../notebook.php');

if (isset($_GET['id']) && isset($_GET['term'])) {
  print(json_encode(notebookDelteNotebook($_GET['id'])));
}
else {
  echo json_encode(array(400 => "Invalid arguments"));
}
