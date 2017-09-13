function show_log() {
    $.ajax({
        url: "history.php",
        cache: false,
        complete: function Start(){setTimeout(show_log, 2000); },
        success: function (html) {
            $("#show_log").html(html);
        }
    });
}
$(document).ready(function () {
    show_log();
    return false;
});

//document.getElementById('show_log').innerHTML = "";