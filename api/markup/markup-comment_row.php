<?php

if (isset($_GET['fname']) && isset($_GET['lname']) && isset($_GET['content']) && isset($_GET['id'])) {
  $fname = $_GET['fname'];
  $lname = $_GET['lname'];
  $content = $_GET['content'];
  $id = $_GET['id'];

  $markup = '<div class="comment-row">
  <a id="deletecomment-' . $id . '" class="clipping-delete" onclick="deleteComment(this.id)" href="#">&#10006</a>
  <h4 class="comment-header">' . $fname . ' ' . $lname . ' Commented</h4>
        <p class="comment-body">' . $content . '</p>
      </div>';
  print $markup;
}
else {
  echo json_encode(array(400 => "Invalid arguments"));
}
