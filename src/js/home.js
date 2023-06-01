/* IMPORTANT Needs post-utils.js to work */
const startTime = generateDate()
let offset = 0
let finished = 0
let seed = Math.random()

const intersectionObserver = new IntersectionObserver(entries => {
    if(entries[0].intersectionRatio != 0) {
        $.ajax({
            url: 'home.php',
            type: 'get',
            data: {
                'offset': offset * 10,
                'finished': finished,
                'startTime': startTime,
                'seed': seed
            },
            dataType: 'json',
            success: function(data) {
                if($("#error").length > 0) {
                    $("#error").remove()
                }
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
                    let message = $("<p></p>").addClass("text-center text-decoration-underline").text("Hai visto TUTTI i post! Complimenti!")
                    $("#post-list").append(message)
                }
            },
            error: function () {
                $("#spinner").addClass("d-none")
                let error = $("<p></p>").addClass("text-danger text-center text-decoration-underline").attr('id', "error").text("Impossibile caricare altri risultati, riprovare pù tardi")
                $("#post-list").append(error)
            }
        })
    }
})

intersectionObserver.observe(document.getElementById("spinner"))
