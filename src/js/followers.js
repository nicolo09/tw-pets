let offset = 1
const numberToFetch = 10

const queryString = window.location.search
const urlParams = new URLSearchParams(queryString)

const type = urlParams.has('animal') ? "animal" : "person"
const user = urlParams.has('animal') ? urlParams.get('animal') : urlParams.get('person')

const intersectionObserver = new IntersectionObserver(entries => {
    if(entries[0].intersectionRatio != 0 && type != null && user != null) {
        $.ajax({
            url: 'profile-followers.php',
            type: 'get',
            data: {
                'type': type,
                'user': user,
                'offset': (offset * numberToFetch),
                'quantity': numberToFetch
            },
            dataType: 'json',
            success: function (data) {
                $("#container").append(data)
                offset++
                if(data == "" || $('<div></div>').html(data).children().length < numberToFetch) {
                    $("#spinner").remove()
                }
            },
            error: function() {
                $("#spinner").remove()
                let error = $("<p></p>").addClass("text-danger text-center text-decoration-underline").attr('id', "error").text("Impossibile caricare altri followers, riprovare pù tardi")
                $("#container").append(error)
            }
        })
    }
})

intersectionObserver.observe(document.getElementById("spinner"))
