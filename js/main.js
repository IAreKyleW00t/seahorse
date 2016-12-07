$(document).ready(function() {
    $('.button-collapse').sideNav(); // Mobile navigation
    $('.modal-trigger').leanModal(); // Modals
    $('select').material_select(); // Select
    
    /* Automatically close the side-nav when
        a link is clicked. */
    $('.side-nav').click(function() {
        $('.button-collapse').sideNav('hide');
    });
});

/**
 * Load data from a JSON file asynchronously.
 */
function loadJSON(file, callback) {
    var x = new XMLHttpRequest();
    x.overrideMimeType('application/json');
    x.open('GET', file, true); // Asynchronous
    x.onreadystatechange = function() {
        if (x.readyState == 4 && x.status == '200') {
            callback(x.responseText);
        }
    };

    x.send(null);
}