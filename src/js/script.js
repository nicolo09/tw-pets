const IMG_DIR = "img/";

//Select 2 default theme
$.fn.select2.defaults.set("theme", "bootstrap-5");

//Notification badge on profile nav element
$.ajax({
    url: "profile-notifications.php",
    type: "GET",
    data: {
        "number": "true"
    },
    dataType: "json",
    success: function (data) {
        if (data["count"] > 0) {
            $('#profile-nav-element').append(' <span class="badge bg-info">' + data["count"] + (data["hasMore"] ? "+" : "") + '</span><span class="visually-hidden">Nuove notifiche</span>');
        }
    }
});
