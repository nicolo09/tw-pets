/* IMPORTANT Needs post-utils.js to work */
const timestamp = generateDate()
let offset = 1
let finished = 0

document.querySelectorAll('[id^="post-card-"]').forEach(post => {
    let id = post.id.split("-")[2]
    attachStyleAndEventListeners(id)
})

const intersectionObserver = new IntersectionObserver(entries => {
    if(entries[0].intersectionRatio != 0) {
        $.ajax({
            url: 'home.php',
            type: 'get',
            data: {
                'offset': offset * 10,
                'finished': finished
            },
            dataType: 'json',
            success: function(data) {
                if(data["html"] != "") {
                    $('#post-list').append(data["html"])
                    data["postIDs"].forEach(id => attachStyleAndEventListeners(id))
                    offset++
                    if(finished != data["finished"]) {
                        finished = data["finished"]
                        offset = 1
                    }
                } else {
                    $("#spinner").addClass("d-none")
                }
            },
            error: function () {
                // TODO
            }
        })
    }
})

intersectionObserver.observe(document.getElementById("spinner"))
