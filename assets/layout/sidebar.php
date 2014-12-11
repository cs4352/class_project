<div id="sidebar">
  <div id="sidebar-buttons">
    <div id="sidebar-search-button" class="sidebar-button">
      <img src="assets/images/search.png" alt="">
    </div>
    <div id="sidebar-add-button" class="sidebar-button">
      <img src="assets/images/add.png" alt=""> 
    </div>
  </div>
  <div id="sidebar-add-buttons" class="revealable">
    <div class="sidebar-add-button margin-right-10" onclick="showClippingOverlay()">
      Add Clipping
    </div>
    <div class="sidebar-add-button" onclick="showNotebookOverlay()">
      Add Notebook
    </div>
  </div>
  <div id="sidebar-search" class="revealable">
    <form id="sidebar-search-form" method="get" action="">
      <input type="text" class="sidebar-search-input" id="sidebar-search-input" name="q" size="21" maxlength="120" onchange="sidebarSearch()" onkeyup="sidebarSearch()" placeholder="Search...">
      <button type="submit">Search</button>
    </form>
  </div>
  <div id="sidebar-title">
    My Clippings
  </div>
  <div id="sidebar-list">
    <!-- load_clippings.js populates this div -->
  </div>
</div>

<script>

  document.querySelector('#sidebar-add-button').onclick = function(){
    document.getElementById('sidebar-add-buttons').classList.toggle('animation-open');
    document.getElementById('sidebar-add-button').classList.toggle('pink-background');
  };

  document.querySelector('#sidebar-search-button').onclick = function(){
    document.getElementById('sidebar-search').classList.toggle('animation-open');
    document.getElementById('sidebar-search-button').classList.toggle('pink-background');
  };

</script>