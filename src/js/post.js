params = window.location.search.split("&");
id=-1;
if(params.length>0&&params[0]!=""){
    //C'è effettivamente un parametro
    for(i=0; i<params.length; i++){
        if(params[i].includes("id")){
            id=processString(params[i]);
        }
    }
}

document.getElementById("likeB").addEventListener('click',()=>{
    window.location.href = 'like.php?id='+id;
});

document.getElementById("saveB").addEventListener('click',()=>{
    window.location.href = 'save.php?id='+id;
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