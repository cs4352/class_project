<?php

require_once(dirname(__FILE__) . '/../clipping.php');
require_once(dirname(__FILE__) . '/../user.php');
require_once(dirname(__FILE__) . '/../shared_clipping.php');

if (isset($_GET['cid']) && isset($_GET['uids']) && isset($_GET['current_uid'])) {

  // Get all of the UIDS.
  $uids = stripslashes($_GET['uids']);
  $uids = json_decode($uids);

  // Get the clipping.
  $clipping = getClippingById($_GET['cid']);

  // Create a message for the current user.
  setuserNotification($_GET['current_uid'], 'Sharing settings saved!');

  // Share the clipping.
  foreach ($uids as $uid) {
    $newCid = saveClipping($uid, 0, $clipping->ORIGFILE, $clipping->CONTENT, $clipping->NAME, $clipping->SUBTITLE, $clipping->COLOR);
    setUserNotification($uid, 'Your shared clippings have changed.');
    echo json_encode(shareClipping($newCid, $_GET['cid'], $uid));
  }
} else {
  echo json_encode(array(400 => "Invalid arguments"));
}
