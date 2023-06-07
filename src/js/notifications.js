const numberToFetch = 10;

function addEventListeners() {
    $(".btn-delete-notification").on("click", function () {
        let id = $(this).attr("id");
        id = id.replace("btn-delete-", "");
        $.ajax({
            url: "delete-notification.php",
            type: "GET",
            data: {
                "id": id
            },
            success: function (data) {
                if (data["success"] == true) {
                    $("#notification-" + id).remove();
                }
                else {
                    alert(data["error"]);
                }
                updateTitleAndButton();
            }
        })
    });
}

function updateTitleAndButton() {
    let count = $(".notification").length;
    if (count == 0) {
        $("#title").text("Non ci sono notifiche");
        $("#btn-delete-all-notifications").prop("disabled", true);
    }
    else {
        $("#title").text("Notifiche");
        $("#btn-delete-all-notifications").prop("disabled", false);
    }
    updateNotificationBadge();
}

$("#btn-delete-all-notifications").on("click", function () {
    $.ajax({
        url: "delete-notification.php",
        type: "GET",
        data: {
            "id": "all"
        },
        success: function (data) {
            if (data["success"] == true) {
                $(".notification").remove();
            }
            else {
                alert(data["error"]);
            }
            updateTitleAndButton();
        }
    })
});

const intersectionObserver = new IntersectionObserver(entries => {
    if (entries[0].intersectionRatio != 0) {
        $.ajax({
            url: 'fetch-notifications.php',
            type: 'get',
            data: {
                'offset': $(".notification").length,
                'number': numberToFetch
            },
            success: function (data) {
                $('#notifications-list').append(data);
                addEventListeners();
                if (data == "") {
                    intersectionObserver.unobserve($("#spinner").get(0));
                    //Hide spinner
                    $("#spinner").remove();
                }
            }
        })
    }
});

intersectionObserver.observe($("#spinner").get(0));