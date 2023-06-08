const numberToFetch = 5;

function attachSaveButtonsEventListeners() {
    $("[id^=save-post-card-]").on("click", function () {
        // Remove the post from the page
        $("#post-card-" + this.id.split("-")[3]).remove();
    });
}

function attachPostButtons() {
    const cards = document.querySelectorAll('[id^="post-card-"]');
    cards.forEach(card => {
        //Gets the post id
        const id = card.id.split("-")[2];
        attachStyleAndEventListeners(id);
    });
}

const spinnerPostsLazyLoadObserver = new IntersectionObserver(entries => {
    if (entries[0].intersectionRatio != 0) {
        $.ajax({
            url: 'profile-saved.php',
            type: 'get',
            data: {
                'offset': $("[id^=post-card-]").length,
                'number': numberToFetch
            },
            success: function (data) {
                if (data == "") {
                    spinnerPostsLazyLoadObserver.unobserve($("#posts-spinner").get(0));
                    //Hide spinner
                    $("#posts-spinner").remove();
                    if ($("#post-list").children().length == 0) {
                        $("#post-list").append("<h2 class='text-center'>Nessun post salvato</h2>");
                    }
                } else {
                    $('#post-list').append(data);
                    attachSaveButtonsEventListeners();
                    attachPostButtons();
                }
            }
        })
    }
});

spinnerPostsLazyLoadObserver.observe($("#posts-spinner").get(0));
attachSaveButtonsEventListeners();
attachPostButtons();
