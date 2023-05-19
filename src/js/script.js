const IMG_DIR = "img/";
const UPDATE_INTERVAL = 15000; //15 seconds

//Select 2 default theme
$.fn.select2.defaults.set("theme", "bootstrap-5");

//Notification badge on profile nav element
function updateNotificationBadge() {
    $.ajax({
        url: "profile-notifications.php",
        type: "GET",
        data: {
            "number": "true"
        },
        dataType: "json",
        success: function (data) {
            if (data["count"] > 0) {
                let count = data["count"] + (data["hasMore"] ? "+" : "");
                if ($('#profile-nav-element').find('.notification-badge').length > 0) {
                    //Update badge
                    $('#profile-nav-element').find('.notification-badge').text(count);
                    $('#profile-nav-element').find('.visually-hidden').text(count + 'Nuove notifiche');
                }
                else {
                    //Create badge
                    $('#profile-nav-element').append(' <span class="badge bg-info notification-badge">' + count + '</span><span class="visually-hidden notification-badge">' + count + 'Nuove notifiche</span>');
                }
            } else {
                $('#profile-nav-element').find('.notification-badge').remove();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error: " + jqXHR.responseText);
        }
    });
}

const refreshBadgeInterval = setInterval(function () {
    updateNotificationBadge();
}, UPDATE_INTERVAL);

//Update notification badge on page load
jQuery(function () {
    updateNotificationBadge();
});