var offset = 1;

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
            success: function (response) {
                if($("#error").length > 0) {
                    $("#error").remove()
                }
                $("#spinner").removeClass("d-none")
                if(response["results"].length > 0){
                    response["results"].forEach(key => {
                        let container = $("<a></a>").attr('href',"view-user-profile.php?username=" + key["username"] + "&type=" + type).append( 
                        $("<div></div>").addClass("card result-bar").append(
                            $("<div></div>").addClass("card-body p-2").append(
                                $("<div></div>").addClass("result-element").append(
                                    $("<img></img>").addClass("miniature").attr('src', IMG_DIR + key["immagine"]).attr('alt', "Immagine profilo di " + key["username"])
                                ).append(
                                    $("<label></label>").addClass("fs-4 fw-bold miniatureLabel").text(key["username"])
                                ))))
            
                        $("#container").append(container)
                    })
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

