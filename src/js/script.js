/* When selecting an image this shows its preview on 
 * an img tag with id=#imgPreview */
function imagePreview(input){
    if (input.files && input.files[0] && input.files[0].name.match(/\.(jpg|jpeg|png|gif)$/i)) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imgPreview')
                .attr('src', e.target.result)
        };

        reader.readAsDataURL(input.files[0]);
    }
}
