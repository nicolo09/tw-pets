//Possible values for type variable
const ANIMAL = "animal";
const PERSON = "person";

const queryString = window.location.search
const urlParams = new URLSearchParams(queryString)

let type = urlParams.has('type') ? urlParams.get('type') : PERSON;
let username = urlParams.has('username') ? urlParams.get('username') : null;

if (type == PERSON) {
    //Attaching eventListener to animal button
    //Opens the animal list of the user
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
    //Own account can't be followed
    //If the account is not being followed the user can follow it
    //If the account is already being followed the user can unfollow it
    let followButton = document.getElementById("follow");
    if(followButton != null){
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
