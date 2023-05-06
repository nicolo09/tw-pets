console.log("prova")
console.log(document.querySelectorAll('#immagine'));

const imgs=document.querySelectorAll('#immagine').forEach((item=>{
    item.addEventListener('click', () => {
        console.log("Ciao, hai cliccato una immagine! ");
    });
}));