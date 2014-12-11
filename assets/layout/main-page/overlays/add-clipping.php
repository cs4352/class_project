<div>
    <div id="overlay-background"></div>
    <div id="add-clipping-overlay" class="overlay">
      <div id="overlay-box">
        <div id="overlay-close-button" onclick="hideClippingOverlay()">X</div>
        <h2 id="overlay-title">Upload A Document</h2>
        <div id="overlay-content">
          <form id="file-form" action="" enctype="multipart/form-data" method="POST">
            <div class="row">
              <input type="file" id="file-select" name="file" accept="text/plain" required onchange="uploadIsReady()"/>
            </div>
            <div class="row" style="margin-top: 20px;">
              At this time you may only upload .txt files
            </div>
            <div class="row">
              <button type="submit" id="upload-button">Upload</button>
            </div>
          </form>
          <form id="clipping-form" action="" enctype="multipart/form-data" method="POST" style="display: none">
            <input type="hidden" id="fid" value=""/>
            <p class="field-header"><strong>Highlight</strong>, then <strong>Drag</strong> the text you want to keep into the box on the right.</p><br />
            <div id="clipping-drag-container">
              <textarea id="uploaded-file-text" name="uploaded-file-text" ondragstart="drag(event)" draggable="true" resizable spellcheck="false"></textarea>
              <textarea id="clipping-text" name="clipping-text" ondrop="drop(event)" ondragover="allowDrop(event)" onchange="addClippingIsReady()" onkeyup="addClippingIsReady()" required resizeable title="This is an error message" placeholder="Drag selected text here"></textarea><br />
            </div>
            <input type="text" id="clipping-name" name="clipping-name" placeholder="Clipping Name" onchange="addClippingIsReady()" onkeyup="addClippingIsReady()" required /><br />
            <textarea type="text" id="clipping-subtitle" name="clipping-subtitle" placeholder="Clipping Description" onchange="addClippingIsReady()" onkeyup="addClippingIsReady()" required></textarea>
            <label id="clipping-notebook-label" for="clipping-notebook">Select a notebook to save your clipping in</label>
            <select id="clipping-notebook">
              <!-- Options populated in js -->
            </select><br />
            <br />
            <label id="clipping-color-label" for="clipping-color">Select a color for your clipping</label><br />
            <input id="clipping-color" type="color" value="#9AC5FF" /><br />
            <button type="submit" id="save-clipping">Save Clipping</button>
          </form>
        </div>
      </div>
    </div>
</div>
