const numberToFetch = 10;

const intersectionObserver = new IntersectionObserver(entries => {
    if (entries[0].intersectionRatio != 0) {
        $.ajax({
            url: "followed.php",
            type: "GET",
            data: {
                offset: $("#profile-list").children().length,
                number: numberToFetch,
            },
            success: function (data) {
                $("#profile-list").append(data);
                if (data == "" || $('<div></div>').html(data).children().length < numberToFetch) {
                    $("#spinner").remove();
                    if ($("#profile-list").children().length == 0) {
                        $("#profile-list").append($("<h2>").addClass("text-center").text("Nessun profilo seguito"));
                    }
                }
            }
        });
    }
});

intersectionObserver.observe(document.getElementById("spinner"));