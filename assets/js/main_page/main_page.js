$(document).ready(function() {
    displayNotifications();
    loadClippings();
    fileUploadFormHandler();
    addCommentSubmitHandler();
})

var origSelectedShareRecipients = [];
var origUnselectedShareRecipients = [];
var selectedShareRecipients = [];
var unselectedShareRecipients = [];

function displayNotifications() {
    $.ajax({
        url: window.location.origin + JSI_IWP_DIR  + '/api/rest/get_user_notification.php?uid=' + JSIuid
    }).done(function(response) {
        response = JSON.parse(response)[0];
       if (response != '') {
           swal(response);
       }
    });
}

// Loads clipping links into the sidebar.
function loadClippings() {
    $.ajax({
        url: window.location.origin + JSI_IWP_DIR  + '/api/rest/clipping.php?uid=' + JSIuid
    }).done(function(response) {
        var responseObject = JSON.parse(response);
        var numResponses = 0;
        var promises = [];
        for (var i in responseObject) {
            numResponses ++;
            var promise = $.ajax({
                url: window.location.origin + JSI_IWP_DIR  + '/api/markup/markup-clipping_sidebar_row.php?id=' + responseObject[i].ID + '&uid=' + JSIuid + '&name=' + responseObject[i].NAME + '&subtitle=' + responseObject[i].SUBTITLE
            }).done(function(markup) {
                $('#sidebar-list').prepend(markup);

                // Click the last clipping.
                // TODO: Find a better way to do this.
            });
            promises.push(promise);
        }
        $.when.apply($, promises).done(function() {
            clickClipping('clipping-' + responseObject[numResponses - 1].ID);
        });
    });
}

// Loads users for the share modal.
function loadPrevSharedUsers(cid) {
    $.ajax({
        url: window.location.origin + JSI_IWP_DIR  + '/api/rest/get_previously_shared_users.php?cid=' + cid + '&uid=' + JSIuid
    }).done(function(response) {
        var responseObject = JSON.parse(response);
        for (var i in responseObject) {
            origSelectedShareRecipients.push(responseObject[i].ID);
            selectedShareRecipients.push(responseObject[i].ID);
            $.ajax({
                url: window.location.origin + JSI_IWP_DIR  + '/api/markup/markup-share_user_row.php?id=' + responseObject[i].ID + '&fname=' + responseObject[i].FNAME + '&lname=' + responseObject[i].LNAME + '&shared=true'
            }).done(function(markup) {
                $('#user-share-list').prepend(markup);
            });
        }
    });
}

function loadShareUsers(cid) {
    $.ajax({
        url: window.location.origin + JSI_IWP_DIR  + '/api/rest/get_share_users.php?cid=' + cid + '&uid=' + JSIuid
    }).done(function(response) {
        var responseObject = JSON.parse(response);
        for (var i in responseObject) {
            origUnselectedShareRecipients.push(responseObject[i].ID);
            unselectedShareRecipients.push(responseObject[i].ID);
            $.ajax({
                url: window.location.origin + JSI_IWP_DIR  + '/api/markup/markup-share_user_row.php?id=' + responseObject[i].ID + '&fname=' + responseObject[i].FNAME + '&lname=' + responseObject[i].LNAME + '&shared=false'
            }).done(function(markup) {
                $('#user-share-list').prepend(markup);
            });
        }
    });
}

// Add Clipping Modal controls. ///////////////////////////////////////////////////
function showClippingOverlay() {
    el = document.getElementById("add-clipping-overlay");
    el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";

    showOverlayBackground();
}

function hideClippingOverlay() {
    el = document.getElementById("add-clipping-overlay");
    el.style.visibility = "hidden";

    hideOverlayBackground();
}

// Add Notebook Modal controls. ///////////////////////////////////////////////////
function showNotebookOverlay() {
    el = document.getElementById("add-notebook-overlay");
    el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";

    showOverlayBackground();
}

function hideNotebookOverlay() {
    el = document.getElementById("add-notebook-overlay");
    el.style.visibility = "hidden";

    hideOverlayBackground();
}

// Share Modal controls. ///////////////////////////////////////////////////////////
function showShareOverlay() {

    // Load the users that can be shared with.
    var selectedClippingId = document.getElementsByClassName('selected')[0].id;
    id = selectedClippingId.substring(selectedClippingId.indexOf('-') + 1);
    loadShareUsers(id);
    loadPrevSharedUsers(id);

    el = document.getElementById("share-overlay");
    el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";

    showOverlayBackground();
}

function hideShareOverlay() {
    el = document.getElementById("share-overlay");
    el.style.visibility = "hidden";

    // Remove all of the users who could be shared to.
    var paras = document.getElementsByClassName('user-share-list-link');

    while(paras[0]) {
        paras[0].parentNode.removeChild(paras[0]);
    }

    var paras = document.getElementsByClassName('user-previously-shared-list-link');

    while(paras[0]) {
        paras[0].parentNode.removeChild(paras[0]);
    }

    // Clear out the share array.
    selectedShareRecipients = [];
    unselectedShareRecipients = [];

    hideOverlayBackground();
}

function showOverlayBackground() {
    bg = document.getElementById("overlay-background");
    bg.style.display = (bg.style.display == "block") ? "none" : "block";
}

function hideOverlayBackground() {
    bg = document.getElementById("overlay-background");
    bg.style.display = "none";
}

// Onmouseup handler to copy text from clipping source to clipping result.
function drag(ev) {
    if (window.getSelection) {
        text = window.getSelection().toString();
        ev.dataTransfer.setData("text", text);
    }
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    document.getElementById("clipping-text").value += data;
}

function clickClipping(id) {
    // Determine if the clipping is shared with the user or not.
    var shared = ($('#' + id + '> .shared').length == 0) ? false : true;

    // Deselect any previously selected clipping.
    var selectedClippings = document.getElementsByClassName('selected');
    for (var i = 0; i < selectedClippings.length; i++) {
        selectedClippings[i].classList.remove('selected');
    }

    // Mark the clipping as selected.
    var element = document.getElementById(id);
    element.classList.add('selected');

    // Get the id of the clipping.
    id = id.substring(id.indexOf('-') + 1);

    // Get the clippings content from the API.
    var xhr = new XMLHttpRequest();
    xhr.open('GET', window.location.origin + JSI_IWP_DIR  + "/api/rest/clipping.php?id=" + id, false);
    xhr.send();
    var contents = JSON.parse(xhr.responseText);

    // Populate the content area with the clipping contents.
    document.getElementById('clipping-content').value = contents.CONTENT;

    document.getElementById('clipping-title').innerHTML = contents.NAME;

    // Get the clippings comments.
    loadClippingComments(id);

    if(contents.NAME.length > 0)
    {
        document.getElementById('info-button').innerHTML = 'Info';
        document.getElementById('comment-button').innerHTML = 'Comment';
        document.getElementById('organize-button').innerHTML = 'Organize';
        if (!shared) {
            document.getElementById('share-button').innerHTML = 'Share';
            document.getElementById('share-button').onclick = showShareOverlay;
        } else {
            document.getElementById('share-button').innerHTML = '';
            document.getElementById('share-button').onclick = function() {
                swal('Sorry, but you cannot share a clipping that was shared with you.');
            }
        }
    }
}

/**
 * Load's a clipping's comments into the comment area.
 *
 * @param int cid
 *  The clipping's id.
 */
function loadClippingComments(cid) {

    // Clear out old comments.
    var paras = document.getElementsByClassName('comment-row');

    while(paras[0]) {
        paras[0].parentNode.removeChild(paras[0]);
    }

    // Load fresh comments.
    $.ajax({
        url: window.location.origin + JSI_IWP_DIR  + '/api/rest/comments/get_comments_by_clipping_id.php?cid=' + cid + '&name=true'
    }).done(function(response) {
        response = JSON.parse(response);
        for (var i in response) {
            $.ajax({
                url: window.location.origin + JSI_IWP_DIR  + '/api/markup/markup-comment_row.php?fname=' + response[i].FNAME + '&lname=' + response[i].LNAME + '&content=' + response[i].CONTENT
            }).done(function(markup) {
                $('#comments-content').prepend(markup);
            });
        }
    });
}

function addCommentSubmitHandler() {
    $('#comment-form').submit(function(event) {
        event.preventDefault();

        // Get the info for the clipping.
        var selectedClippingId = document.getElementsByClassName('selected')[0].id;
        var id = selectedClippingId.substring(selectedClippingId.indexOf('-') + 1);

        // Get the content of the comment.
        var content = $('#comment-content').val();

        // Create the comment.
        $.ajax({
            url: window.location.origin + JSI_IWP_DIR  + "/api/rest/comments/create_comment.php?cid=" + id + "&uid=" + JSIuid + "&content=" + content
        }).done(function(response) {
            loadClippingComments(id);
            $('#comment-content').val("");
        });
    });
}

function clickUser(uid) {
    var $clickedRow = $('#' + uid);
    var uid = uid.substring(uid.indexOf('-') + 1);


    if ($clickedRow.hasClass('user-previously-shared-list-link')) {
        // Unselect.
        $clickedRow.removeClass('user-previously-shared-list-link');
        $clickedRow.addClass('user-share-list-link');
        var $child = $clickedRow.children('.user-previously-shared-list-cell');
        $child.removeClass('user-previously-shared-list-cell');
        $child.addClass('user-share-list-cell')
        selectedShareRecipients = $.grep(selectedShareRecipients, function(value) {
            return value != uid;
        });
        unselectedShareRecipients.push(uid);
    }
    else {
        // Select.
        $clickedRow.addClass('user-previously-shared-list-link');
        $clickedRow.removeClass('user-share-list-link');
        var $child = $clickedRow.children('.user-share-list-cell');
        $child.addClass('user-previously-shared-list-cell');
        $child.removeClass('user-share-list-cell')
        unselectedShareRecipients = $.grep(unselectedShareRecipients, function(value) {
            return value != uid;
        });
        selectedShareRecipients.push(uid);
    }
}

// Share the clipping with the user.
function shareSubmit() {

    // Get the info for the clipping.
    var selectedClippingId = document.getElementsByClassName('selected')[0].id;
    id = selectedClippingId.substring(selectedClippingId.indexOf('-') + 1);

    // Share the clipping with the users who have been selected.
    // Convert the uids array to JSON.
    // Filter out those who have already been shared with.
    selectedShareRecipients = selectedShareRecipients.filter(function(val) {
        return origSelectedShareRecipients.indexOf(val) == -1;
    });
    var uidsJson = JSON.stringify(selectedShareRecipients);

    // Share the clipping with the user.
    var xhr = new XMLHttpRequest();
    xhr.open('GET', window.location.origin + JSI_IWP_DIR  + "/api/rest/batch_share_clipping.php?cid=" + id + "&uids=" + uidsJson + "&current_uid=" + JSIuid, true);
    xhr.send();

    // Unshare the clipping with the users who have been selected.
    // Convert the uids array to JSON.
    // Filter out those who have already been shared with.
    unselectedShareRecipients = unselectedShareRecipients.filter(function(val) {
        return origUnselectedShareRecipients.indexOf(val) == -1;
    });
    var uidsJson = JSON.stringify(unselectedShareRecipients);

    // Share the clipping with the user.
    var xhr = new XMLHttpRequest();
    xhr.open('GET', window.location.origin + JSI_IWP_DIR  + "/api/rest/batch_unshare_clipping.php?cid=" + id + "&uids=" + uidsJson + "&current_uid=" + JSIuid, true);
    xhr.send();

    // Clear out the share arrays.
    selectedShareRecipients = [];
    unselectedShareRecipients = [];

    hideShareOverlay();

    // TODO: Fix bug where you can't un/re share without refreshing the page.
    location.reload();
    //swal("Clipping shared!");
}

function fileUploadFormHandler() {
    // Handle file uploads.
    var fileUploadForm = document.getElementById('file-form');
    var clippingForm = document.getElementById('clipping-form');
    var fileSelect = document.getElementById('file-select');
    var uploadButton = document.getElementById('upload-button');

    fileUploadForm.onsubmit = function(event) {

        // Cancel the form submit from going through.
        event.preventDefault();

        // Update button text.
        uploadButton.innerHTML = 'Uploading...';

        // Get the selected files from the input.
        var files = fileSelect.files;
        var file = files[0];

        // Create a new FormData object.
        var formData = new FormData();

        // Add the file to the request.
        formData.append('file[]', file, file.name);

        // Set up the request.
        var xhr = new XMLHttpRequest();

        // Open the connection.
        xhr.open('POST', window.location.origin + JSI_IWP_DIR  + "/helpers/file_upload.php", false);

        // Send the Data.
        xhr.send(formData);

        // Get the name of the file.
        var response = xhr.responseText;
        response = JSON.parse(response);
        var fname = response.fname;
        var fid = response.fid;
        document.getElementById('fid').value = fid;

        // Set up the request to get the contents of the file.
        var xhr = new XMLHttpRequest();

        // Open the connection.
        xhr.open('GET', window.location.origin + JSI_IWP_DIR  + "/uploads/" + fname, false);

        // Send the request.
        xhr.send();

        var fileContents = xhr.responseText;

        document.getElementById("uploaded-file-text").value = fileContents;

        // Change which form is showing.
        fileUploadForm.style.display = 'none';
        clippingForm.style.display = 'block';

        // Reset the upload button.
        uploadButton.innerHTML = 'Upload';
    }

    // Handle clipping submit.
    clippingForm.onsubmit = function(event) {
        event.preventDefault();

        // Get the content.
        var name = document.getElementById('clipping-name').value;
        var subtitle = document.getElementById('clipping-subtitle').value;
        var content = document.getElementById('clipping-text').value;
        var file = document.getElementById('fid').value;

        // Set up the request to get the contents of the file.
        var xhr = new XMLHttpRequest();

        // Open the connection.
        xhr.open('GET', window.location.origin + JSI_IWP_DIR  + "/api/rest/clipping.php?userId=" + JSIuid + "&file=" + file + "&content=" + content + "&name=" + name + "&subtitle=" + subtitle, false);
        xhr.send();
        hideClippingOverlay();

        var paras = document.getElementsByClassName('sidebar-list-link');

        while(paras[0]) {
            paras[0].parentNode.removeChild(paras[0]);
        }

        loadClippings();

        // Reset the form
        clippingForm.style.display = 'none';
        fileUploadForm.style.display = 'block';

        clippingForm.reset();
        fileUploadForm.reset();
    }
}
