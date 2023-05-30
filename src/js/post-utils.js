const IMGDIR = "img/";
let postFather = -1; //Shown comments' ID

function styleButtonLike(id, isLiked, n) {
    const buttonL = document.getElementById("like-post-card-" + id);
    if (buttonL != null) {
        if (isLiked) {
            //Post ha like
            buttonL.innerHTML = '<img src="' + IMGDIR + 'thumb_up_filled.svg" alt="" />' + n + ' Mi Piace ';
        } else {
            //Post non ha like
            buttonL.innerHTML = '<img src="' + IMGDIR + 'thumb_up.svg" alt="" />' + n + ' Mi Piace ';
        }
    }
}

function styleButtonSave(id, isSaved) {
    const buttonS = document.getElementById("save-post-card-" + id);
    if (buttonS != null) {
        if (isSaved) {
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
        styleButtonLike(id, data[0], data[1]);
    });
}

function getPostSaved(id) {
    const singleSave = fetch("tell-js-save.php?id=" + id).then((response) => {
        if (!response.ok) {
            throw new Error("Something went wrong!");
        }
        return response.json();
    }).then((data) => {
        styleButtonSave(id, data);
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
                popUp("Errore nel mettere il like", "#comments-" + id);
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
                popUp("Errore nel salvare il post", "#comments-" + id);
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
                    successPopUp("Commento pubblicato con successo", "#comments-" + id);
                }
            },
            error: function (request, status, error) {
                popUp("Errore nel salvare il commento", "#comments-" + id);
            }
        });
    });
}

function popUp(text, commentID) {
    $(commentID).prepend($('<div class="alert alert-danger alert-dismissible fade show" role="alert"> <label class="top-page-popup">' + text + '</label> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'));
}

function successPopUp(text, commentID) {
    $(commentID).prepend($('<div class="alert alert-success alert-dismissible fade show" role="alert"> <label class="top-page-popup">' + text + '</label> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'));
}

function generateDate() {
    const d = new Date()
    return d.toISOString().split('T')[0] + ' ' + d.toTimeString().split(' ')[0]
}