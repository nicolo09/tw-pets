const IMGDIR = "img/";

//Prendo id
const cards = document.querySelectorAll('[id^="post-card-"]');
//ID ha tutti gli id dei post presenti
const id = [];
//Se un post ha il like o no
const likedID = {};
const savedID = {};
const nlikesID = {};
const shownComments = [];
let postFather=-1;
for (i = 0; i < cards.length; i++) {
    tmp = cards[i].id.split("-")[2];
    id.push(tmp);
    likedID[tmp] = false;
    savedID[tmp] = false;
    nlikesID[tmp] = 0;
    findComments(tmp);
}

//Chiedo al db se i post hanno like o no
id.forEach(element => {
    getPostLiked(element);
    getPostSaved(element);
    attachLike(element);
    attachSave(element);
    attachNewComment(element);
    attachAnswerButton(element);
});

function styleButtonLike(id) {
    const buttonL = document.getElementById("like-post-card-" + id);
    const n = nlikesID[id];
    if (buttonL != null) {
        if (likedID[id] == true) {
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
        if (savedID[id] == true) {
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
        likedID[id] = data[0];
        nlikesID[id] = data[1];
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
        savedID[id] = data;
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
        const text=document.getElementById(id+"-commentTextArea").value;
        $.ajax({
            method: "GET",
            url: "comment.php",
            data: {
                "id_post": id,
                "id_padre":postFather,
                "text": text
            },
            success: function (response) {
                //TODO: Mostra che hai salvato il commento all'utente
                
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
        shownComments.push([id, tmp]);
    }
}

//Attacca gli event listener a tutti i bottoni "rispondi" in shownComments del post_id dato
function attachAnswerButton(id) {
    shownComments.forEach(element => {
        if (element[0] == id) {
            document.getElementById(id + "-comment-" + element[1]).addEventListener('click', () => {
                postFather=-1;
                changeLabel(id, element[1]);
            });
        }
    })
}

//Cambia la label id-label 
function changeLabel(post_id, comment_id) {
    const label=document.getElementById(post_id + "-label");
    const singleLike = fetch("tell-js-user-id-comment.php?id=" + comment_id).then((response) => {
        if (!response.ok) {
            throw new Error("Something went wrong!");
        }
        return response.json();
    }).then((data) => {
        if(data==null){
            postFather=post_id;
            label.innerText="Aggiungi un commento a questo post:";
        }else{
            postFather=-1;
            label.innerText="Rispondi al commento di "+data+":";
        }
    });
    
}
