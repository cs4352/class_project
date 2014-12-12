<?php

/**
 * Fetches all comments for the specified clipping.
 *
 * @param int $cid
 *  The clipping's id.
 *
 * @return array
 *  All comments for the specified clipping.
 */
function getCommentsByClippingId($cid) {
  // If this was a shared clipping, we need to get comments from the original
  // clipping.
  require_once(dirname(__FILE__) . '/shared_clipping.php');
  $origcid = getOriginalCid($cid);
  if (isset($origcid)) {
    $cid = $origcid;
  }

  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');
  $sql = sqlSetup();
  $query = "SELECT * FROM COMMENTS WHERE CID=$cid";
  $result = mysqli_query($sql, $query);

  $comments = array();
  while ($obj = mysqli_fetch_object($result)) {
    $comments[] = $obj;
  }

  return $comments;
}

/**
 * Creates a comment.
 *
 * @param int $cid
 *  The clipping's id. If this is a shared clipping, this will be reassigned to
 *  the original.
 * @param int $uid
 *  The user's id.
 * @param string $content
 *  The content of the clipping.
 */
function createComment($cid, $uid, $content) {
  // If this was a shared clipping, we need to get comments from the original
  // clipping.
  require_once(dirname(__FILE__) . '/shared_clipping.php');
  $origcid = getOriginalCid($cid);
  if (isset($origcid)) {
    $cid = $origcid;
  }

  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');
  $sql = sqlSetup();
  $query = "INSERT INTO COMMENTS (CID, UID, CONTENT)
            VALUES
            ($cid, $uid, \"$content\")";
  mysqli_query($sql, $query) or die("A MySQL error has occurred.<br />Error: (" . mysqli_errno($sql) . ") " . mysqli_error($sql));
}

function commentDeleteComment($id) {
  require_once(dirname(__FILE__) . '/../helpers/database_helper.php');

  $sql = sqlSetup();
  $query = "DELETE FROM COMMENTS
        WHERE ID=$id";
  mysqli_query($sql, $query);
}
