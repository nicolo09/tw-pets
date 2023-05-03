$.fn.select2.defaults.set( "theme", "bootstrap-5" );

$(document).ready(function() {
    $('#multiSelector').select2({
        placeholder: "Seleziona altri padroni del tuo animale (opzionale)",
        closeOnSelect: false,
        minimumResultsForSearch: Infinity,
        templateResult: function(data) {
            if (!data.element) {
                return data.text;
            }
            var $result = $('<span class="select2-results__option">');
            var $img = $('<img class="miniature">').attr('src', data.element.getAttribute("data-img"));
            var $text = $('<span>').text(data.text);

            $result.append($img).append($text);
            return $result;
        },
        width: "100%"
    });
});
