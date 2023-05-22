const IMGDIR = "img/";
const N = 5;

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
const shownComments = [];
let postFather = -1;

findComments(id);

attachLike(id);
attachSave(id);
attachNewComment(id);
attachAnswerButton(id);
attachShowAllButton(id);


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
                $(".comments").prepend($('<p class="text-danger">Errore nel mettere like</p>'));
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
                $(".comments").prepend($('<p class="text-danger">Errore nel salvare il post</p>'));
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
                $("#" + id + "-commentTextArea").val('');
                postFather = -1;
                document.getElementById(id + "-label").innerText = "Aggiungi un commento a questo post:";
                //TODO: Cambia il display dei commenti o popup per mostrare che è stato aggiunto

            },
            error: function (request, status, error) {
                $(".comments").prepend($('<p class="text-danger">Errore nel salvare il commento</p>'));
            }
        });
    });
}

//Riempie il vettore show comments di array id-post, id-commenti presenti
function findComments(id) {
    const comments = document.querySelectorAll('[id^="' + id + '-comment-"]');
    for (i = 0; i < comments.length; i++) {
        tmp = comments[i].id.split("-")[2];
        shownComments.push(tmp);
    }
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

//Attacca gli event listener a tutti i bottoni "rispondi" in shownComments del post_id dato
function attachAnswerButton(id) {
    shownComments.forEach(element => {
        document.getElementById(id + "-comment-" + element).addEventListener('click', () => {
            postFather = -1;
            changeLabel(id, element);
        });

    })
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

function loadMorePosts(id, n) {
    console.log("TODO");
}
