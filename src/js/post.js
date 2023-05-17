const IMGDIR = "img/";

//Prendo id
const cards = document.querySelectorAll('[id^="post-card-"]');
//ID ha tutti gli id dei post presenti
const id = [];
//Se un post ha il like o no
const likedID = {};
for (i = 0; i < cards.length; i++) {
    tmp = cards[i].id.split("-")[2];
    id.push(tmp);
    likedID[tmp] = false;
}

//Chiedo al db se i post hanno like o no
id.forEach(element => getPostLiked(element));
//Metto la grafica dei bottoni
id.forEach(element => styleButton(element));


function styleButton(id){
    //TODO: Add n likes
    const buttonL = document.getElementById("like-post-card-" + id);
    //TODO: Implement save button functionality
    const buttonS = document.getElementById("save-post-card-" + id);
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



document.getElementById("likeB").addEventListener('click', () => {
    $.ajax({
        method: "GET",
        url: "like.php",
        data: {
            "id": id
        },
        success: function (response) {
            //cambio grafica
            //$("#likeB").
        }
    });
});

document.getElementById("saveB").addEventListener('click', () => {
    window.location.href = 'save.php?id=' + id;
});

//Questa funzione ritorna la stringa del parametro get
//es stringa input: ?username=Mario
//Ritorna Mario
//es stringa type=animal
//Ritorna animal
function processString(totString) {
    if (totString.length > 0) {
        tmp = totString.split('?');
        //Se ha un ? iniziale viene rimosso
        index = -1;
        if (tmp.length == 1) {
            //Non c'era ? iniziale
            index = 0;
        } else {
            //C'era ? e quindi il testo è nella seconda parte
            index = 1;
        }
        substring = tmp[index].split('=');
        //La prima parte di substring è la parte del parametro prima dell =
        return substring[1];
    }
}