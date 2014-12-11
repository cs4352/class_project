<?php
// Save the user's UID.
$uid = $_SESSION['current_user'];
$notebookRowTemplate = '<a id="%id" onclick="" class="sidebar-list-link"><div class="sidebar-list-cell-notebook"><div class="sidebar-list-cell-interior-padding"><div class="title">%name</div></div></div></a>';
?>
<script>
  var JSIuid = <?php print($uid) ?>;
  var JSInotebookRowTemplate = <?php print('\'' . $notebookRowTemplate . '\'') ?>;
  var JSI_IWP_DIR = <?php print('"' . ($_IWP_DIR_ ? $_IWP_DIR_ : 0) . '"') ?>;
  if (JSI_IWP_DIR == 0) {
    JSI_IWP_DIR = "";
  }
</script>