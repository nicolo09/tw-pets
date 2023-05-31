const numberToFetch = 5;

function attachSaveButtonsEventListeners() {
    $("[id^=save-post-card-]").on("click", function () {
        // Remove the post from the page
        $("#post-card-" + this.id.split("-")[3]).remove();
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
                } else {
                    $('#post-list').append(data);
                    attachSaveButtonsEventListeners();
                }
            }
        })
    }
});

spinnerPostsLazyLoadObserver.observe($("#posts-spinner").get(0));
attachSaveButtonsEventListeners();