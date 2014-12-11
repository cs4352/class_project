<?php

require_once(dirname(__FILE__) . '/../../file.php');

if (isset($_GET['fid'])) {
  print(json_encode(getFileById($_GET['fid'])));
}
else {
  echo json_encode(array(400 => "Invalid arguments"));
}