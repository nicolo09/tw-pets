//Gets all present cards
const cards = document.querySelectorAll('[id^="post-card-"]');
//Gets the post id
const id = cards[0].id.split("-")[2];
//Styling buttons
getPostLiked(id);
getPostSaved(id);
const timestamp = generateDate();
let offset = 0;
const n = 5;
const answerButtonToAttach = [];
const loadAnswersToAttach = [];
const offsetAnswers = {};
loadComment(id);
//Attaching eventlisteners to post buttons
attachLike(id);
attachSave(id);
attachDelete(id);
attachNewComment(id);

const intersectionObserver = new IntersectionObserver(entries => {
    if (entries[0].intersectionRatio != 0) {
        //Avoids multiple triggers
        $("#spinner-post-" + id).addClass("d-none");
        loadComment(id);
    }
})

//This observes the spinner
intersectionObserver.observe(document.getElementById("spinner-post-" + id));

//Attaches eventListeners to all "answer" buttons of the post with the given id
function attachAnswerButton(id) {
    answerButtonToAttach.forEach(element => {
        document.getElementById(id + "-comment-" + element).addEventListener('click', () => {
            postFather = -1;
            changeLabel(id, element);
        });

    });
    //By doing this, when new comments get loaded, the eventlistener gets attached only to the new ones
    answerButtonToAttach.length = 0;
}

//Attaches eventListeners to all "load more answers" buttons of the post with the given id
function attachLoadAnswersButton(id_post) {
    loadAnswersToAttach.forEach(element => {
        document.getElementById(id_post + "-son-comment-" + element).addEventListener('click', () => {
            //Loading n comments with an offset
            const localOffset = offsetAnswers[element];
            const comments = fetch("tell-js-answer-comments.php?id_post=" + id_post + "&n=" + n + "&offset=" + localOffset + "&timestamp=" + timestamp + "&id_comment=" + element).then((response) => {
                if (!response.ok) {
                    throw new Error("Something went wrong!");
                }
                return response.json();
            }).then((data) => {
                //data contains the comments array
                data.forEach((comment, index) => {
                    addAnswerComment(comment);
                });

                //Next time comments will be read with an increased offset
                offsetAnswers[element] = offsetAnswers[element] + data.length;
                //If n comments were loaded there might be more
                if (data.length != n) {
                    //Less than n comments were loaded, there aren't any more comments to be loaded
                    $("#" + id_post + "-son-comment-" + element).addClass("d-none");
                }
            });
        });

    });
    //By doing this, when new comments get loaded, the eventlistener gets attached only to the new ones
    loadAnswersToAttach.length = 0;
}

//Changes the content of a id-label 
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
    //Loading comments from php
    const comments = fetch("tell-js-comments.php?id_post=" + id_post + "&n=" + n + "&offset=" + offset + "&timestamp=" + timestamp).then((response) => {
        if (!response.ok) {
            throw new Error("Something went wrong!");
        }
        return response.json();
    }).then((data) => {
        //data contains comments array and "hasAnswers" array
        const comm = data[0];
        const hasAnswers = data[1];
        comm.forEach((element, index) => {
            //adding comment to the page
            addComment(element, hasAnswers[index][element["id_commento"]]);
            answerButtonToAttach.push(element["id_commento"]);
            if (hasAnswers[index][element["id_commento"]] == true) {
                loadAnswersToAttach.push(element["id_commento"]);
                offsetAnswers[element["id_commento"]] = 0;
            }
        });
        attachAnswerButton(id);
        attachLoadAnswersButton(id);
        //Next time comments will be read with an increased offset
        offset = offset + comm.length;
        //If n comments were loaded there might be more
        if (n == comm.length) {
            //There might be more comments to be shown
            $("#spinner-post-" + id).removeClass("d-none");
        } else {
            //All the comments were loaded
            $("#spinner-post-" + id).addClass("d-none");

        }
    });

}

//Attaches delete eventListener to the delete button, if it's present
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
                        const html = getTopPageAlertPopUp("Si è verificato un errore, impossibile rimuovere il post")
                        $(html).insertBefore("#post-card-" + id)
                    }
                },
                error: function () {
                    const html = getTopPageAlertPopUp("Si è verificato un errore, impossibile rimuovere il post")
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
    let text = '<p><a href="' + getUserProfileHref(comment["username"]) + '">' + comment["username"] + '</a>' + ': ' + comment["testo"] + '</p>';
    text += '<p class="text-muted">' + correctDate + '</p>';
    text += '<button id="' + id + '-comment-' + comment["id_commento"] + '" class="comment-answer rounded btn btn-outline-primary">Rispondi</button>';
    if (hasAnswers == true) {
        text += '<button id="' + id + '-son-comment-' + comment["id_commento"] + '" class="rounded btn btn-outline-primary">Leggi le risposte</button>';
    }
    $(".comment-container").append(text);
}

// Takes a Date and formats it so that it can be shown in the page
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
    let text = '<p class="ms-5"><a href="' + getUserProfileHref(comment["username"]) + '">' + comment["username"] + '</a>' + ': ' + comment["testo"] + '</p>';
    text += '<p class="ms-5 text-muted">' + correctDate + '</p>';

    $(text).insertBefore("#" + id_post + "-son-comment-" + id_padre);
}
