$(document).ready(function() {
    loadPage();
    fileUploadFormHandler();
    addCommentSubmitHandler();
    addNotebookSubmitHandler();
    searchSubmitHandler();
})

var origSelectedShareRecipients = [];
var origUnselectedShareRecipients = [];
var selectedShareRecipients = [];
var unselectedShareRecipients = [];

function loadPage() {
    displayNotifications();
    loadClippings();
}

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

    // Remove previous options for the notebook form.
    $('#clipping-notebook')
        .find('option')
        .remove();

    $.ajax({
        url: window.location.origin + JSI_IWP_DIR + '/api/rest/notebook/get_user_notebooks.php?uid=' + JSIuid
    }).done(function(response) {
        var responseObject = JSON.parse(response);
        for (var i in responseObject) {
            var markup = JSInotebookRowTemplate.replace('%id', 'notebook-' + responseObject[i].ID).replace('%name', responseObject[i].NAME);
            $('#sidebar-list').append(markup);

            // Populate the notebooks list for the clipping creation form.
            $('#clipping-notebook')
                .append($("<option></option>")
                    .attr("value", responseObject[i].ID)
                    .text(responseObject[i].NAME));
        }


        $.ajax({
            url: window.location.origin + JSI_IWP_DIR  + '/api/rest/clipping.php?uid=' + JSIuid
        }).done(function(response) {
            var responseObject = JSON.parse(response);
            var numResponses = 0;
            var promises = [];
            for (var i in responseObject) {
                numResponses ++;
                var prependMarkup = function(data, index) {
                    return $.ajax({
                        url: window.location.origin + JSI_IWP_DIR  + '/api/markup/markup-clipping_sidebar_row.php?id=' + data[index].ID + '&uid=' + JSIuid + '&name=' + data[index].NAME + '&subtitle=' + data[index].SUBTITLE
                    }).done(function(markup) {
                        $('#notebook-' + data[index].NOTEBOOK_ID).append(markup);
                    });
                };
                var promise = prependMarkup(responseObject, i);
                promises.push(promise);
            }
            // If clippings were loaded...
            if (promises.length > 0) {
                clippingsPageLoad();
                $.when.apply($, promises).done(function() {
                    clickClipping('clipping-' + responseObject[numResponses - 1].ID);
                });
            }
            else {
                noClippingsPageLoad();
            }
        });

    });
}

function clippingsPageLoad() {
    $('#new-user-area').hide();
    $('#content-header').show();
    $('#clipping-content').show();
    $('#comments-area').show();
}

function noClippingsPageLoad() {
    $('#content-header').hide();
    $('#clipping-content').hide();
    $('#comments-area').hide();
    $('#new-user-area').show();
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
    document.getElementById('clipping-content').innerHTML = nl2br(contents.CONTENT);

    document.getElementById('clipping-title').innerHTML = contents.NAME;

    // Get the clippings comments.
    loadClippingComments(id);

    if(contents.NAME.length > 0)
    {
        // document.getElementById('info-button').innerHTML = 'Info';
        // document.getElementById('comment-button').innerHTML = 'Comment';
        // document.getElementById('organize-button').innerHTML = 'Organize';
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

    // Get the link to the original file.
    $.ajax({
       url:  window.location.origin + JSI_IWP_DIR  + "/api/rest/file/get_file_by_file_id.php?fid=" + contents.ORIGFILE
    }).done(function(response) {
        var responseObject = JSON.parse(response);
        $('#orig-file-link').attr('href', window.location.origin + JSI_IWP_DIR + '/uploads/' + responseObject.NAME);
    });
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
    markupObj = {};
    promiseArray = [];
    $.ajax({
        url: window.location.origin + JSI_IWP_DIR  + '/api/rest/comments/get_comments_by_clipping_id.php?cid=' + cid + '&name=true'
    }).done(function(response) {
        response = JSON.parse(response);
        getMarkup = function(i, response, markupObj) {
            return $.ajax({
                url: window.location.origin + JSI_IWP_DIR  + '/api/markup/markup-comment_row.php?fname=' + response[i].FNAME + '&lname=' + response[i].LNAME + '&content=' + response[i].CONTENT
            }).done(function(markup) {
                var key = response[i].ID;
                markupObj[key] = markup;
            });
        };
        for (var i in response) {
            promiseArray.push(getMarkup(i, response, markupObj));
        }
        $.when.apply($, promiseArray).done(function(response) {
            var keys = Object.keys(markupObj);
            keys = keys.sort(function(a, b) {
                return a - b;
            })
            for(var i in keys) {
                $('#comments-content').prepend(markupObj[keys[i]]);
            }
        });
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

function addNotebookSubmitHandler() {
    $('#notebook-form').submit(function(event) {
        event.preventDefault();

        // Get the notebook's name.
        var name = $('#notebook-name').val();

        // Create the notebook.
        $.ajax({
            url: window.location.origin + JSI_IWP_DIR  + "/api/rest/notebook/create_notebook.php?name=" + name + "&uid=" + JSIuid
        }).done(function(response) {
            hideNotebookOverlay();

            var paras = document.getElementsByClassName('sidebar-list-link');

            while(paras[0]) {
                paras[0].parentNode.removeChild(paras[0]);
            }

            loadClippings();

            $('#notebook-name').val('');
        });
    });
}

function searchSubmitHandler() {
    $('#sidebar-search-form').submit(function(event) {
        event.preventDefault();
        performSearch();
    });
}

function sidebarSearch() {
    var searchbox = $('.sidebar-search-input');

    if(searchbox.val() == '') {
        $('.sidebar-list-link').remove();
        loadClippings();
    } else {
        performSearch();
    }
}

function performSearch() {
    // Remove current notes.
    $('.sidebar-list-link').remove();

    // Get the search term.
    var searchTerm = $('#sidebar-search-input').val();

    $.ajax({
        url: window.location.origin + JSI_IWP_DIR  + "/api/rest/clipping/get_clipping_search.php?uid=" + JSIuid + "&term=" + searchTerm
    }).done(function(response){
        var responseObject = JSON.parse(response);
        var numResponses = 0;
        var promises = [];
        for (var i in responseObject) {
            numResponses ++;
            var prependMarkup = function(data, index) {
                return $.ajax({
                    url: window.location.origin + JSI_IWP_DIR  + '/api/markup/markup-clipping_sidebar_row.php?id=' + data[index].ID + '&uid=' + JSIuid + '&name=' + data[index].NAME + '&subtitle=' + data[index].SUBTITLE
                }).done(function(markup) {
                    $('#sidebar-list').append(markup);
                });
            };
            var promise = prependMarkup(responseObject, i);
            promises.push(promise);
        }
        // If clippings were loaded...
        if (promises.length > 0) {
            clippingsPageLoad();
            $.when.apply($, promises).done(function() {
                clickClipping('clipping-' + responseObject[numResponses - 1].ID);
            });
        }
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

function uploadIsReady() {
    var fileSelect = document.getElementById('file-select');
    var files = fileSelect.files;
    var file = files[0];

    if(file == null || file == undefined)
    {
        // Change button to being greyed out
        alert("Don't change button color");
        return false;
    }

    document.getElementById('upload-button').style.backgroundColor = '#EF4D68';

    return true;
}

function addClippingIsReady() {
    var clipping = document.getElementById('clipping-text');
    var clippingName = document.getElementById('clipping-name');
    var clippingDescription = document.getElementById('clipping-subtitle');

    if(clipping.value == '' || clippingName.value == '' || clippingDescription.value == '')
    {
        document.getElementById('save-clipping').style.backgroundColor = '#6B6B6B';
    } else 
    {
        document.getElementById('save-clipping').style.backgroundColor = '#EF4D68';
    }
}

function addCommentIsReady() {
    var comment = document.getElementById('comment-content');

    if(comment.value == '')
    {
        document.getElementById('comment-submit').style.backgroundColor = '#6B6B6B';
    } else 
    {
        document.getElementById('comment-submit').style.backgroundColor = '#EF4D68';
    }
}

function addNotebookIsReady() {
    var notebookName = document.getElementById('notebook-name');

    if(notebookName.value == '')
    {
        document.getElementById('save-notebook').style.backgroundColor = '#6B6B6B';
    } else
    {
        document.getElementById('save-notebook').style.backgroundColor = '#EF4D68';
    }
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

        if(file == null || file == undefined)
        {
            alert("No file has been selected");
        }

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

        // Change the modal header.
        $('#add-clipping-overlay h2').text('Create A Clipping');
    }

    // Handle clipping submit.
    clippingForm.onsubmit = function(event) {
        event.preventDefault();

        // Get the content.
        var name = document.getElementById('clipping-name').value;
        var subtitle = document.getElementById('clipping-subtitle').value;
        var content = document.getElementById('clipping-text').value;
        var file = document.getElementById('fid').value;
        var notebookId = document.getElementById('clipping-notebook').value;

        // Set up the request to get the contents of the file.
        var xhr = new XMLHttpRequest();

        // Open the connection.
        xhr.open('GET', window.location.origin + JSI_IWP_DIR  + "/api/rest/clipping.php?userId=" + JSIuid + "&file=" + file + "&content=" + encodeURIComponent(content) + "&name=" + name + "&subtitle=" + subtitle + "&notebook_id=" + notebookId, false);
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
        // Change the modal header.
        $('#add-clipping-overlay h2').text('Upload A Document');
    }
}

function nl2br(str, is_xhtml) {
    //  discuss at: http://phpjs.org/functions/nl2br/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Philip Peterson
    // improved by: Onno Marsman
    // improved by: Atli Þór
    // improved by: Brett Zamir (http://brett-zamir.me)
    // improved by: Maximusya
    // bugfixed by: Onno Marsman
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //    input by: Brett Zamir (http://brett-zamir.me)
    //   example 1: nl2br('Kevin\nvan\nZonneveld');
    //   returns 1: 'Kevin<br />\nvan<br />\nZonneveld'
    //   example 2: nl2br("\nOne\nTwo\n\nThree\n", false);
    //   returns 2: '<br>\nOne<br>\nTwo<br>\n<br>\nThree<br>\n'
    //   example 3: nl2br("\nOne\nTwo\n\nThree\n", true);
    //   returns 3: '<br />\nOne<br />\nTwo<br />\n<br />\nThree<br />\n'

    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display

    return (str + '')
        .replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
