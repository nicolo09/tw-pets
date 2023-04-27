const img = document.getElementById("imgPreview");
const file = document.getElementById("imgpostinput");
img.style.display="none";

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
    }else{
        img.style.display = "none";
    }
}