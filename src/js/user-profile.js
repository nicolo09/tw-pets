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
    let followButton = document.getElementById("follow");
    if(followButton){
        followButton.addEventListener('click', ()=>{
            window.location.href = 'follow.php?username='+username+"&type="+type;
        });
    } else {
        let page = type == PERSON ? "edit-profile.php" : "profile-manage-animal.php?animal=" + username;
        document.getElementById("modify").addEventListener('click', () => {
            window.location.href = page;
        })
    }
}

if(username==null){
    document.getElementById("followers").addEventListener('click', ()=>{
        window.location.href = 'profile-followers.php';
    });
}else{
    document.getElementById("followers").addEventListener('click', ()=>{
        window.location.href = 'profile-followers.php?'+type+'='+username;
    });
}
