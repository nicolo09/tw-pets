//Possibili costanti del parametro type
const ANIMAL = "animal";
const PERSON = "person";

const queryString = window.location.search
const urlParams = new URLSearchParams(queryString)

let type = urlParams.has('type') ? urlParams.get('type') : PERSON;
let username = urlParams.has('username') ? urlParams.get('username') : null;

if (type == PERSON) {
    //Attacco bottone animale
    //Redirect a pagina con get nome utente della persona che li gestisce, username
    if(username == null){
        document.getElementById("animals").addEventListener('click', ()=>{
            window.location.href = 'profile-animals.php';
        });
    }else{
        document.getElementById("animals").addEventListener('click', ()=>{
            window.location.href = 'profile-animals.php?user='+username;
        });
    }
    
}

if(username != null){
    //Puoi seguire un utente solo se non è il tuo account
    //Se non segui->inizi a seguire
    //Se lo segui->smetti di seguire
    document.getElementById("follow").addEventListener('click', ()=>{
        window.location.href = 'follow.php?username='+username+"&type="+type;
    });
}

if(username==null){
    document.getElementById("followers").addEventListener('click', ()=>{
        window.location.href = 'followers.php';
    });
}else{
    document.getElementById("followers").addEventListener('click', ()=>{
        window.location.href = 'followers.php?'+type+'='+username;
    });
}
