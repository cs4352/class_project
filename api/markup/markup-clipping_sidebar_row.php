<?php

require_once(dirname(__FILE__) . '/../shared_clipping.php');
require_once(dirname(__FILE__) . '/../../config.php');
if (isset($_GET['id']) && isset($_GET['uid']) && isset($_GET['name']) && isset($_GET['subtitle']) && isset($_GET['color'])) {

  $id = 'clipping-' . $_GET['id'];
  $uid = $_GET['uid'];
  $name = $_GET['name'];
  $subtitle = $_GET['subtitle'];
  $shared = isClippingSharedWIthUser($_GET['id'], $uid);
  $cell_class = $shared ? 'shared sidebar-list-cell' : 'sidebar-list-cell';
  $color = '#' . $_GET['color'];

  $markup = '
<a id="' . $id . '" onclick="clickClipping(this.id)" class="sidebar-list-link">
  <div class="' . $cell_class . '">
      <div id="delete' . $id . '" class="clipping-delete" onclick="deleteClipping(this.id)" href="#">&#10006</div>
      <img class="clipping-shared-icon" src="http://' . $_SERVER['HTTP_HOST'] . $_IWP_DIR_ . '/assets/images/shared-note.png" />
    <div class="sidebar-list-cell-top-color" style="background-color: ' . $color . ';"></div>
    <div class="sidebar-list-cell-interior-padding">
      <div class="title">
        ' . $name . '
      </div>
      <div class="subtitle">
        ' . $subtitle . '
      </div>
    </div>
  </div>
</a>';
  print $markup;
}
