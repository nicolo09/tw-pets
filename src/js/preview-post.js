const postimg = document.getElementById('post-img--1');
const img=sessionStorage.getItem("img-loaded");
if(img){
    postimg.src = img;
}
