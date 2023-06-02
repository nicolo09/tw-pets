var offset = 0;

const queryString = window.location.search
const urlParams = new URLSearchParams(queryString)

const type = urlParams.has('animals') ? "animal" : (urlParams.has('persons') ? "person" : null)
const search = urlParams.has('animals') ? urlParams.get('animals') : (urlParams.has('persons') ? urlParams.get('persons') : null)

const intersectionObserver = new IntersectionObserver(entries => {
    if(entries[0].intersectionRatio != 0 && type != null && search != null) {
        $.ajax({
            url: 'utils/getResults.php',
            type: 'get',
            data: {
                'type': type,
                'search': search,
                'offset': (offset * 10)
            },
            dataType: 'json',
            success: function (data) {
                if($("#error").length > 0) {
                    $("#error").remove()
                }
                $("#spinner").removeClass("d-none")
                if(data != "") {
                    $("#container").append(data)
                    offset++
                } else {
                    $("#spinner").addClass("d-none")
                }
            },
            error: function() {
                $("#spinner").addClass("d-none")
                let error = $("<p></p>").addClass("text-danger text-center text-decoration-underline").attr('id', "error").text("Impossibile caricare altri risultati, riprovare pù tardi")
                $("#container").append(error)
            }
        })
    }
})

intersectionObserver.observe(document.getElementById("spinner"))

