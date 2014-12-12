<?php

require_once(dirname(__FILE__) . '/../../clipping.php');
require_once(dirname(__FILE__) . '/../../shared_clipping.php');


if (isset($_GET['id']) && isset($_GET['uid'])) {
  clippingDeleteClipping($_GET['id']);
  deleteSharedClipping($_GET['id'], $_GET['uid']);
}
else {
  echo json_encode(array(400 => "Invalid arguments"));
}
