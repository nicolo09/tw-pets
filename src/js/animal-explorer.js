const addbutton = document.querySelector('#add-animal-button')

if(addbutton) { 
    document.querySelector('#add-animal-button').addEventListener('click', () => {
        window.location.href = 'profile-manage-animal.php';
    });
}

let modifyButtons = document.getElementsByClassName("manage-button");
let profileButtons = document.getElementsByClassName("pro-pic-btn");

for (var i = 0; i < modifyButtons.length; i++){
    modifyButtons[i].myParam = modifyButtons[i].id.split("_")[1];
    modifyButtons[i].addEventListener('click', goToEditAnimal, false);
};

for (var i = 0; i < profileButtons.length; i++){
    profileButtons[i].myParam = profileButtons[i].id.split("_")[0];
    profileButtons[i].addEventListener('click', goToAnimal, false);
};


function goToEditAnimal(evt) {
    window.location.href = 'profile-manage-animal.php?animal=' + evt.currentTarget.myParam;
};

function goToAnimal(evt){
    location.href = "view-user-profile.php?username=" + evt.currentTarget.myParam+"&type=animal";
};
