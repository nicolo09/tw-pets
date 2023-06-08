let offset = 0;
const numberToFetch = 10;

const queryString = window.location.search
const urlParams = new URLSearchParams(queryString)

const type = urlParams.has('animals') ? "animal" : (urlParams.has('persons') ? "person" : null)
const search = urlParams.has('animals') ? urlParams.get('animals') : (urlParams.has('persons') ? urlParams.get('persons') : null)

const intersectionObserver = new IntersectionObserver(entries => {
    if(entries[0].intersectionRatio != 0 && type != null && search != null) {
        $.ajax({
            url: 'search-results.php',
            type: 'get',
            data: {
                'type': type,
                'search': search,
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
                let error = $("<p></p>").addClass("text-danger text-center text-decoration-underline").attr('id', "error").text("Impossibile caricare altri risultati, riprovare pù tardi")
                $("#container").append(error)
            }
        })
    }
})

intersectionObserver.observe(document.getElementById("spinner"))

