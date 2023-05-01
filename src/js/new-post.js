const img = document.getElementById("imgPreview");
const file = document.getElementById("imgpostinput");
const animals = document.getElementById("selectAnimals");
img.style.display = "none";

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

function createAnimalDisplay(selectedAnimals) {
    const container = document.querySelector(".animal-display");
    let html = "";
    if (selectedAnimals.length != 0) {
        const numRows = Math.ceil(selectedAnimals.length / 2);
        let counter = 0;
        for (let i = 0; i < numRows; i++) {
            html += `
        <div class="row mt-5">`;
            html += `<div class="text-center col">
                <img id="animalPreview" src="img/facebook-default-profile-pic.jpg" alt="Immagine profilo di ppepe" class="rounded-circle proPic">
                <p>ppepe${counter}</p>
                </div>`;
            counter++;
            if (counter + 1 <= selectedAnimals.length) {
                //Ci stanno almeno due elementi
                html += `<div class="text-center col">
                <img id="animalPreview" src="img/facebook-default-profile-pic.jpg" alt="Immagine profilo di ppepe" class="rounded-circle proPic">
                <p>ppepe${counter}</p>
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