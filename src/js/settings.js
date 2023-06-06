//Retrieve settings as json object

$.ajax({
    url: 'profile-settings.php',
    type: 'get',
    data: {
        'json': true
    },
    dataType: 'json',
    success: function (response) {
        for (var key in response) {
            let item = document.getElementById(key);
            if (response.hasOwnProperty(key) && item != null) {
                item.checked = response[key];
                item.addEventListener('change', event => {
                    //Set toggle switch to a spinner
                    item.hidden = true;
                    $('<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>').prependTo(item.parentElement);
                    $.ajax({
                        url: 'profile-settings.php',
                        type: 'post',
                        data: { 'setting': event.target.id, 'value': event.target.checked },
                        success: function (response) {
                            item.hidden = false;
                            item.parentElement.removeChild(item.parentElement.firstChild);
                        },
                        error: function(){
                            console.error('Error while saving settings');
                            alert('Errore durante il salvataggio delle impostazioni');
                        }
                    });
                });
                // Enable element
                item.disabled = false;
            }
        }
    }
});

$("#change-email").on('click', function () {
    //Redirect to change email page
    window.location.href = "change-email-profile.php";
});

$("#change-password").on('click', function () {
    //Redirect to change password page
    window.location.href = "change-password-profile.php";
});
