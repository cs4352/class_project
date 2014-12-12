<?php

require_once(dirname(__FILE__) . '/../../comment.php');

if (isset($_GET['id']) && isset($_GET['uid'])) {
  commentDeleteComment($_GET['id']);
}
else {
  echo json_encode(array(400 => "Invalid arguments"));
}
