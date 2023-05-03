const img = document.getElementById("imgPreview");
const file = document.getElementById("imgpostinput");
const animals = document.getElementById("selectAnimals");
img.style.display = "none";


const finalAnimals = [];
const animalList = fetch("tell-js-animals.php").then((response) => {
    if (!response.ok) {
        throw new Error("Something went wrong!");
    }
    return response.json();
}).then((data) => {
    data.forEach(element => {
        finalAnimals.push(element);
    })
});

//First call to hide the image element
file.addEventListener('change', () => {
    imagePreviewShow(file);
});

/* When selecting an image this shows its preview on 
 * an img tag with id=#imgPreview */
function imagePreviewShow(input) {
    if (input.files && input.files[0] && input.files[0].name.match(/\.(jpg|jpeg|png|gif)$/i)) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imgPreview')
                .attr('src', e.target.result)
        };
        img.style.display = "";
        reader.readAsDataURL(input.files[0]);
    } else {
        img.style.display = "none";
    }
}


function getAnimalIndex(username, fullList) {
    let tmp=-1;
    fullList.forEach(animal=>{
        if(animal["username"]==username){
            tmp=fullList.indexOf(animal);
        }
    })
    return tmp;
}


function createAnimalDisplay(selectedAnimals) {
    const container = document.querySelector(".animal-display");
    let html = "";
    if (selectedAnimals.length != 0) {
        const numRows = Math.ceil(selectedAnimals.length / 2);
        let counter = 0;
        for (let i = 0; i < numRows; i++) {
            const index=getAnimalIndex(selectedAnimals[counter],finalAnimals);
            const anim = finalAnimals[index];

            html += `
        <div class="row mt-5">`;
            html += `<div class="text-center col">
                <img id="animalPreview" src="${IMG_DIR}${anim["immagine"]}" alt="Immagine profilo di ${anim["username"]}" class="rounded-circle proPic">
                <p>${anim["username"]}</p>
                </div>`;
            counter++;
            if (counter + 1 <= selectedAnimals.length) {
                //Ci stanno almeno due elementi
                const index=getAnimalIndex(selectedAnimals[counter],finalAnimals);
                const anim = finalAnimals[index];
                html += `<div class="text-center col">
                <img id="animalPreview" src="${IMG_DIR}${anim["immagine"]}" alt="Immagine profilo di ${anim["username"]}" class="rounded-circle proPic">
                <p>${anim["username"]}</p>
                </div>`;
                counter++;
            }
            html += `
    </div>`;

        }
    }
    container.innerHTML = html;
}

animals.addEventListener('change', () => {
    var selected = [...animals.options]
        .filter(option => option.selected)
        .map(option => option.value);
    createAnimalDisplay(selected);
});