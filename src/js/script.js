/* When selecting an image this shows its preview on 
 * an img tag with id=#imgPreview */
function imagePreview(input){
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        console.log("ciao")
        reader.onload = function (e) {
            $('#imgPreview')
                .attr('src', e.target.result)
                .width(150)
                .height(200);
        };

        reader.readAsDataURL(input.files[0]);
    }
}
