const filePicker = document.getElementById('imgprofile');
const image = document.getElementById('imgPreview');

image.addEventListener('click', function(){
    filePicker.click();
})


filePicker.addEventListener('change', function(){
    imagePreview(filePicker);
})

/* When selecting an image this shows its preview on 
 * an img tag with id=#imgPreview */
function imagePreview(input) {
    if (input.files && input.files[0] && input.files[0].name.match(/\.(jpg|jpeg|png|gif)$/i)) {
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#imgPreview')
                .attr('src', e.target.result)
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$(function() {
    $('#multiSelector').select2({
        placeholder: "Seleziona altri padroni del tuo animale (opzionale)",
        closeOnSelect: false,
        minimumResultsForSearch: Infinity,
        templateResult: function(data) {
            if (!data.element) {
                return data.text;
            }
            let $result = $('<span class="select2-results__option">');
            let $img = $('<img class="miniature">').attr('src', data.element.getAttribute("data-img"));
            let $text = $('<span class="ms-1 miniature-text">').text(data.text);

            $result.append($img).append($text);
            return $result;
        },
        width: "100%"
    });
});
