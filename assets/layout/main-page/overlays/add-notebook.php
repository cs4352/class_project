<div>
    <div id="overlay-background"></div>
    <div id="add-notebook-overlay" class="overlay">
      <div id="overlay-box">
        <div id="overlay-close-button" onclick="hideNotebookOverlay()">X</div>
        <h2 id="overlay-title">Add Notebook</h2>
        <div id="overlay-content">
          <form id="notebook-form" action="" enctype="multipart/form-data" method="POST">
            <input type="text" id="notebook-name" name="notebook-name" placeholder="Notebook Name" onchange="addNotebookIsReady()" onkeyup="addNotebookIsReady()" required /><br />
            <button type="submit" id="save-notebook">Save Notebook</button>
          </form>
        </div>
      </div>
    </div>
</div>