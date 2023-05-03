document.querySelector('#add-animal-button').addEventListener('click', () => {
    window.location.href = 'profile-manage-animal.php';
});

let buttons = document.getElementsByClassName("manage-button");
for (var i = 0; i < buttons.length; i++){
    buttons[i].myParam = buttons[i].id.split("_")[1];
    buttons[i].addEventListener('click', goToEditAnimal, false);
};

function goToEditAnimal(evt) {
    window.location.href = 'profile-manage-animal.php?animal=' + evt.currentTarget.myParam;
};

function goToAnimal(animal_username){
    //location.href = ""; //TODO go to animal page
    console.log(animal_username);
};