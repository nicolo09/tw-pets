document.querySelector('#add-animal-button').addEventListener('click', () => {
    window.location.href = 'profile-manage-animal.php';
});

let modifyButtons = document.getElementsByClassName("manage-button");
let profileButtons = document.getElementsByClassName("proPicBtn");

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
    //location.href = "user-profile.php?animal=" + evt.currentTarget.myParam; //TODO go to animal page
    console.log(evt.currentTarget.myParam);
};