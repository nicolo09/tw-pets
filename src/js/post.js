//Prendo id
const cards = document.querySelectorAll('[id^="post-card-"]');
//ID ha tutti gli id dei post presenti
const id = cards[0].id.split("-")[2];
//Se un post ha il like o no
getPostLiked(id);
getPostSaved(id);
const timestamp = generateDate();
let offset = 0;
const n = 5;
const answerButtonToAttach = [];
const loadAnswersToAttach = [];
const offsetAnswers = {};
loadComment(id);

attachLike(id);
attachSave(id);
attachDelete(id);
attachNewComment(id);

const intersectionObserver = new IntersectionObserver(entries => {
    if (entries[0].intersectionRatio != 0) {
        //Per evitare multipli trigger
        $("#spinner-post-" + id).addClass("d-none");
        loadComment(id);
    }
})

//This observes the spinner
intersectionObserver.observe(document.getElementById("spinner-post-" + id));

//Attacca gli event listener a tutti i bottoni "rispondi" in answerButtonToAttach del post_id dato
function attachAnswerButton(id) {
    answerButtonToAttach.forEach(element => {
        document.getElementById(id + "-comment-" + element).addEventListener('click', () => {
            postFather = -1;
            changeLabel(id, element);
        });

    });
    //Così facendo, quando carico nuovi commenti attacco l'event listener solo a loro
    //e non attacco allo stesso pulsante molti event listener per lo stesso evento
    answerButtonToAttach.length = 0;
}

//Attacca gli event listener a tutti i bottoni "rispondi" in answerButtonToAttach del post_id dato
function attachLoadAnswersButton(id_post) {
    loadAnswersToAttach.forEach(element => {
        document.getElementById(id_post + "-son-comment-" + element).addEventListener('click', () => {
            //Carico n commenti, con offset
            const localOffset = offsetAnswers[element];
            const comments = fetch("tell-js-answer-comments.php?id_post=" + id_post + "&n=" + n + "&offset=" + localOffset + "&timestamp=" + timestamp + "&id_comment=" + element).then((response) => {
                if (!response.ok) {
                    throw new Error("Something went wrong!");
                }
                return response.json();
            }).then((data) => {
                //data è composta da vettore dei commenti
                data.forEach((comment, index) => {
                    addAnswerComment(comment);
                });

                //La prossima volta inizio a leggere i commenti da offset incrementato
                offsetAnswers[element] = offsetAnswers[element] + data.length;
                //Ho caricato n elementi? Se si->potrebbero esserci altri commenti
                if (data.length != n) {
                    //Ho caricato meno elementi, cavo il bottone
                    $("#" + id_post + "-son-comment-" + element).addClass("d-none");
                }
            });
        });

    });
    //Così facendo, quando carico nuovi commenti attacco l'event listener solo a loro
    //e non attacco allo stesso pulsante molti event listener per lo stesso evento
    loadAnswersToAttach.length = 0;
}

//Cambia la label id-label 
function changeLabel(post_id, comment_id) {
    const label = document.getElementById(post_id + "-label");
    const singleLike = fetch("tell-js-user-id-comment.php?id=" + comment_id).then((response) => {
        if (!response.ok) {
            throw new Error("Something went wrong!");
        }
        return response.json();
    }).then((data) => {
        if (data == null) {
            postFather = -1;
            label.innerText = "Aggiungi un commento a questo post:";
        } else {
            postFather = comment_id;
            label.innerText = "Rispondi al commento di " + data + ":";
            $("#" + post_id + "-label").append('<button class="col-1 btn btn btn-outline-danger" id="' + post_id + '-close">X</button>');
            const close = document.getElementById(post_id + "-close").addEventListener('click', () => {
                postFather = -1;
                label.innerText = "Aggiungi un commento a questo post:";
            });
        }
    });

}

function loadComment(id_post) {
    //Query al php per chiedere i commenti
    const comments = fetch("tell-js-comments.php?id_post=" + id_post + "&n=" + n + "&offset=" + offset + "&timestamp=" + timestamp).then((response) => {
        if (!response.ok) {
            throw new Error("Something went wrong!");
        }
        return response.json();
    }).then((data) => {
        //data è composta da vettore commenti e vettore haRisposte
        comm = data[0];
        hasAnswers = data[1];
        comm.forEach((element, index) => {
            //aggiungo il commento alla pagina
            addComment(element, hasAnswers[index][element["id_commento"]]);
            answerButtonToAttach.push(element["id_commento"]);
            if (hasAnswers[index][element["id_commento"]] == true) {
                loadAnswersToAttach.push(element["id_commento"]);
                offsetAnswers[element["id_commento"]] = 0;
            }
        });
        attachAnswerButton(id);
        attachLoadAnswersButton(id);
        //La prossima volta inizio a leggere i commenti da offset incrementato
        offset = offset + comm.length;
        //Ho caricato n elementi? Se si->potrebbero esserci altri commenti
        if (n == comm.length) {
            //Ci potrebbero essere altri commenti da mostrare
            $("#spinner-post-" + id).removeClass("d-none");
        } else {
            //Ho caricato gli ultimi commenti
            $("#spinner-post-" + id).addClass("d-none");

        }
    });

}

function attachDelete(id) {
    if($("#delete-post-card-" + id).length) {
        $("#delete-post-card-" + id).on("click", function () {
            $.ajax({
                method: "GET",
                url: "delete.php",
                data: {
                    "id_post" : id
                },
                dataType: 'json', 
                success: function (data) {
                    if(data["result"] == 1){
                        window.location.href = $("#" + id + "-creator").attr('href')
                    } else {
                        html = getTopPageAlertPopUp("Si è verificato un errore, impossibile rimuovere il post")
                        $(html).insertBefore("#post-card-" + id)
                    }
                },
                error: function () {
                    html = getTopPageAlertPopUp("Si è verificato un errore, impossibile rimuovere il post")
                    $(html).insertBefore("#post-card-" + id)    
                }
            })
        })
    }
}

function getUserProfileHref(username) {
    return "view-user-profile.php?username=" + username + "&type=person";
}

function addComment(comment, hasAnswers) {
    const correctDate = convertDateToHTML(new Date(comment["timestamp"]));
    text = '<p><a href="' + getUserProfileHref(comment["username"]) + '">' + comment["username"] + '</a>' + ': ' + comment["testo"] + '</p>';
    text += '<p class="text-muted">' + correctDate + '</p>';
    text += '<button id="' + id + '-comment-' + comment["id_commento"] + '" class="comment-answer rounded btn btn-outline-primary">Rispondi</button>';
    if (hasAnswers == true) {
        text += '<button id="' + id + '-son-comment-' + comment["id_commento"] + '" class="rounded btn btn-outline-primary">Leggi le risposte</button>';
    }
    $(".comment-container").append(text);
}

/*Prende in input una Date e la formatta nel formato corretto per metterlo in html*/
function convertDateToHTML(date) {
    const h = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
    const m = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
    const day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    const month = date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
    const correctDate = day + "/" + month + "/" + date.getFullYear() + " " + h + ":" + m;
    return correctDate;
}

function addAnswerComment(comment) {
    const id_padre = comment["id_padre"];
    const id_post = comment["id_post"];
    const correctDate = convertDateToHTML(new Date(comment["timestamp"]));
    text = '<p class="a-indent"><a href="' + getUserProfileHref(comment["username"]) + '">' + comment["username"] + '</a>' + ': ' + comment["testo"] + '</p>';
    text += '<p class="a-indent text-muted">' + correctDate + '</p>';

    $(text).insertBefore("#" + id_post + "-son-comment-" + id_padre);
}
