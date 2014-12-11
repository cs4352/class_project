<?php

require_once(dirname(__FILE__) . '/../../clipping.php');

if (isset($_GET['uid']) && isset($_GET['term'])) {
  print(json_encode(clippingGetClippingSearch($_GET['uid'], $_GET['term'])));
}
else {
  echo json_encode(array(400 => "Invalid arguments"));
}
