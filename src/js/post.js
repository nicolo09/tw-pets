const IMGDIR = "img/";

//Prendo id
const cards = document.querySelectorAll('[id^="post-card-"]');
//ID ha tutti gli id dei post presenti
const id = cards[0].id.split("-")[2];
//Se un post ha il like o no
let likedID = false;
let nlikesID = 0;
getPostLiked(id);
let savedID = false;
getPostSaved(id);
//ID dei commenti mostrati
const answerButtonToAttach = [];
let postFather = -1;
const timestamp = generateDate();
let offset = 0;
const n = 5;
let maxComments = 0;
getNComments(id);

attachLike(id);
attachSave(id);
attachNewComment(id);
attachShowAllButton(id);

const intersectionObserver = new IntersectionObserver(entries => {
    if (entries[0].intersectionRatio != 0) {
        //Per evitare multipli trigger
        $("#spinner-post-" + id).addClass("d-none");
        loadComment(id);
    }
})

//This observes the spinner
intersectionObserver.observe(document.getElementById("spinner-post-" + id));

function styleButtonLike(id) {
    const buttonL = document.getElementById("like-post-card-" + id);
    const n = nlikesID;
    if (buttonL != null) {
        if (likedID == true) {
            //Post ha like
            buttonL.innerHTML = '<img src="' + IMGDIR + 'thumb_up_filled.svg" alt="" />' + n + ' Mi Piace ';
        } else {
            //Post non ha like
            buttonL.innerHTML = '<img src="' + IMGDIR + 'thumb_up.svg" alt="" />' + n + ' Mi Piace ';
        }
    }
}

function styleButtonSave(id) {
    const buttonS = document.getElementById("save-post-card-" + id);
    if (buttonS != null) {
        if (savedID == true) {
            //Post è stato salvato
            buttonS.innerHTML = '<img src="' + IMGDIR + 'star_filled.svg" alt="" />Salvato ';
        } else {
            //Post non è stato salvato
            buttonS.innerHTML = '<img src="' + IMGDIR + 'star.svg" alt="" />Salva ';
        }
    }
}

function getPostLiked(id) {
    const singleLike = fetch("tell-js-like.php?id=" + id).then((response) => {
        if (!response.ok) {
            throw new Error("Something went wrong!");
        }
        return response.json();
    }).then((data) => {
        //I dati vengono inviati come {bool, num}
        likedID = data[0];
        nlikesID = data[1];
        styleButtonLike(id);
    });
}

function getPostSaved(id) {
    const singleSave = fetch("tell-js-save.php?id=" + id).then((response) => {
        if (!response.ok) {
            throw new Error("Something went wrong!");
        }
        return response.json();
    }).then((data) => {
        savedID = data;
        styleButtonSave(id);
    });
}

function attachLike(id) {
    document.getElementById("like-post-card-" + id).addEventListener('click', () => {
        $.ajax({
            method: "GET",
            url: "like.php",
            data: {
                "id": id
            },
            success: function (response) {
                getPostLiked(id);
            },
            error: function (request, status, error) {
                popUp("Errore nel mettere il like");
            }
        });
    });
}

function attachSave(id) {
    document.getElementById("save-post-card-" + id).addEventListener('click', () => {
        $.ajax({
            method: "GET",
            url: "save.php",
            data: {
                "id": id
            },
            success: function (response) {
                getPostSaved(id);
            },
            error: function (request, status, error) {
                popUp("Errore nel salvare il post");
            }
        });
    });
}

function attachNewComment(id) {
    document.getElementById(id + "-new-comment").addEventListener('click', () => {
        const text = document.getElementById(id + "-commentTextArea").value;
        $.ajax({
            method: "GET",
            url: "comment.php",
            data: {
                "id_post": id,
                "id_padre": postFather,
                "text": text
            },
            success: function (response) {
                if (text != "") {
                    $("#" + id + "-commentTextArea").val('');
                    postFather = -1;
                    document.getElementById(id + "-label").innerText = "Aggiungi un commento a questo post:";
                    successPopUp("Commento pubblicato con successo");
                }
            },
            error: function (request, status, error) {
                popUp("Errore nel salvare il commento");
            }
        });
    });
}

//Se c'è il pulsante Mostra altri commenti gli aggiunge la funzionalità
function attachShowAllButton(id) {
    const button = document.getElementById(id + "-load-comment");
    if (button != null) {
        //Il pulsante esiste, ci sono più commenti da caricare
        button.addEventListener('click', () => {
            console.log("TODO");
            //loadMorePosts(id, N);
        });
    }
}

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

function popUp(text) {
    $(".comments").prepend($('<div class="alert alert-danger alert-dismissible fade show" role="alert"> <label class="top-page-popup">' + text + '</label> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'));
}

function successPopUp(text) {
    $(".comments").prepend($('<div class="alert alert-success alert-dismissible fade show" role="alert"> <label class="top-page-popup">' + text + '</label> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'));
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

        //La prossima volta inizio a leggere i commenti da offset incrementato
        offset = offset + comm.length;
        comm.forEach((element, index) => {
            //aggiungo il commento alla pagina
            addComment(element, hasAnswers[index][element["id_commento"]]);
            answerButtonToAttach.push(element["id_commento"]);
        });
        attachAnswerButton(id);
        if (offset == maxComments) {
            //Ho caricato gli ultimi commenti
            $("#spinner-post-" + id).addClass("d-none");
        } else {
            //Ci sono altri commenti da mostrare
            $("#spinner-post-" + id).addClass("d-block");

        }
    });

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

function getNComments(id_post) {
    const comments = fetch("tell-js-n-comments.php?id_post=" + id_post + "&timestamp=" + timestamp).then((response) => {
        if (!response.ok) {
            throw new Error("Something went wrong!");
        }
        return response.json();
    }).then((data) => {
        maxComments = data[0];
        loadComment(id_post);
    });
}


function generateDate() {
    const d = new Date();
    return convertDateToSQL(d);
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

/*Prende in input una Date e la formatta nel formato corretto per metterlo in sql*/
function convertDateToSQL(d) {
    const h = d.getHours() < 10 ? '0' + d.getHours() : d.getHours();
    const m = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
    const s = d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds();
    const day = d.getDate() < 10 ? '0' + d.getDate() : d.getDate();
    const month = d.getMonth() + 1 < 10 ? '0' + (d.getMonth() + 1) : (d.getMonth() + 1);
    const correctDate = d.getFullYear() + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
    return correctDate;
}