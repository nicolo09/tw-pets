const postimg = document.getElementById('post-img--1');
const img = sessionStorage.getItem("img-loaded");

const buttonL = document.getElementById("like-post-card--1");
const buttonS = document.getElementById("save-post-card--1");

buttonL.innerHTML = '<img src="img/thumb_up.svg" alt="" /> Mi Piace'
buttonS.innerHTML = '<img src="img/star.svg" alt="" /> Salva'

if(img) {
    postimg.src = img;
}
