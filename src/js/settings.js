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
            item = document.getElementById(key);
            if (response.hasOwnProperty(key) && item != null) {
                item.checked = response[key];
                item.addEventListener('change', event => {
                    $.ajax({
                        url: 'profile-settings.php',
                        type: 'post',
                        data: { 'setting': event.target.id, 'value': event.target.checked },
                        success: function (response) { 
                            //TODO: Add success message and handle error
                         }
                    });
                });
                // Enable element
                item.disabled = false;
            }
        }
    }
    //TODO: Handle error
});

$("#change-email").on('click', function () {
    //Redirect to change email page
    window.location.href = "change-email.php";
});

$("#change-password").on('click', function () {
    //Redirect to change password page
    window.location.href = "change-password.php";
});
