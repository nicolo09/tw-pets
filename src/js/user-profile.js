params = window.location.search.split("&");

//Possibili costanti del parametro type
const ANIMAL = "animal";
const PERSON = "person";

let type = PERSON;
let username = "";

//Se non passo parametri->lunghezza comunque 1 ma primo parametro ""
if (params.length > 0 && params[0] != "") {
    //Hai parametri
    if (params.length == 1) {
        if (params[0].includes("username")) {
            //E' un username
            username = processString(params[0]);
            type = PERSON;
        } else if (params[0].includes("type")) {
            //E' un tipo
            type = processString(params[0]);
            username = "";
        } else {
            //Nessuno dei due, redirect a tua pagina
            username = "";
            type = PERSON;
        }
    } else if (params.length == 2) {
        const tmp = switchUsernamePerson(params[0], params[1]);
        username = processString(tmp[0]);
        type = processString(tmp[1]);
    } else {
        //3+ Parametri?
        tmpUs = "";
        tmpTy = PERSON;
        for (i = 0; i < params.length; i++) {
            if (params[i].includes("username")) {
                //E' un username
                tmpUs = processString(params[i]);
            } else if (params[i].includes("type")) {
                //E' un tipo
                tmpTy = processString(params[i]);
            }
        }
        username = tmpUs;
        type = tmpTy;

    }
} else {
    //Non hai parametri, se stesso quindi
    type = PERSON;
    username = "";
}

if (type == PERSON) {
    //Attacco bottone animale
    //Redirect a pagina con get nome utente della persona che li gestisce, username
    if(username==""){
        document.getElementById("animals").addEventListener('click', ()=>{
            window.location.href = 'show-animals.php';
        });
    }else{
        document.getElementById("animals").addEventListener('click', ()=>{
            window.location.href = 'show-animals.php?username='+username;
        });
    }
    
}
if(username!=""){
    //Puoi seguire un utente solo se non è il tuo account
    //Se non segui->inizi a seguire
    //Se lo segui->smetti di seguire
    document.getElementById("follow").addEventListener('click', ()=>{
        window.location.href = 'follow.php?username='+username;
    });
}

if(username==""){
    document.getElementById("followers").addEventListener('click', ()=>{
        window.location.href = 'followers.php';
    });
}else{
    document.getElementById("followers").addEventListener('click', ()=>{
        window.location.href = 'followers.php?username='+username;
    });
}

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

//Questa funzione ritorna un array composto da username e type
function switchUsernamePerson(s1, s2) {
    if (s1.includes("username")) {
        //s1 è primo
        return Array(s1, s2);
    } else {
        //s2 è primo
        return Array(s2, s1);
    }
}