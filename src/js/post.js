const IMGDIR = "img/";

//Prendo id
const cards = document.querySelectorAll('[id^="post-card-"]');
//ID ha tutti gli id dei post presenti
const id = [];
//Se un post ha il like o no
const likedID = {};
const savedID = {};
for (i = 0; i < cards.length; i++) {
    tmp = cards[i].id.split("-")[2];
    id.push(tmp);
    likedID[tmp] = false;
    savedID[tmp] = false;
}

//Chiedo al db se i post hanno like o no
id.forEach(element => getPostLiked(element));
id.forEach(element => getPostSaved(element));

//Metto la grafica dei bottoni
id.forEach(element => {
    styleButtonLike(element);
    styleButtonSave(element);
    attachLike(element);
    attachSave(element)
});

function styleButtonLike(id) {
    //TODO: Add n likes
    const buttonL = document.getElementById("like-post-card-" + id);
    if (buttonL != null) {
        if (likedID[id] == true) {
            //Post ha like
            buttonL.innerHTML = '<img src="' + IMGDIR + 'thumb_up_filled.svg" alt="" />Mi Piace ';
        } else {
            //Post non ha like
            buttonL.innerHTML = '<img src="' + IMGDIR + 'thumb_up.svg" alt="" />Mi Piace ';
        }
    }
}

function styleButtonSave(id) {
    const buttonS = document.getElementById("save-post-card-" + id);
    if (buttonS != null) {
        if (likedID[id] == true) {
            //Post ha like
            buttonS.innerHTML = '<img src="' + IMGDIR + 'star_filled.svg" alt="" />Salvato ';
        } else {
            //Post non ha like
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
        likedID[id] = data;
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
                styleButtonLike(id);
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
                styleButtonSave(id);
            }
        });
    });
}