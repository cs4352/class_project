<?php

require_once(dirname(__FILE__) . '/../clipping.php');
require_once(dirname(__FILE__) . '/../user.php');
require_once(dirname(__FILE__) . '/../shared_clipping.php');

if (isset($_GET['cid']) && isset($_GET['uid'])) {
  $clipping = getClippingById($_GET['cid']);
  $newCid = saveClipping($_GET['uid'], $clipping->NOTEBOOK_ID, $clipping->ORIGFILE, $clipping->CONTENT, $clipping->NAME, $clipping->SUBTITLE, $clipping->COLOR);
  setUserNotification($_GET['uid'], 'A new clipping has been shared with you!');
  echo json_encode(shareClipping($newCid, $_GET['cid'], $_GET['uid']));
} else {
  echo json_encode(array(400 => "Invalid arguments"));
}
