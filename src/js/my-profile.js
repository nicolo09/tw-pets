$('#settings-button').on("click", function () {
    window.location.href = 'profile-settings.php';
});

$("#animals-button").on("click", function () {
    window.location.href = 'profile-animals.php';
});

$("#add-post-button").on("click", function () {
    window.location.href = 'create-new-post-profile.php';
});

$("#view-profile-button").on("click", function () {
    window.location.href = 'view-user-profile.php';
});

$("#notifications-button").on("click", function () {
    window.location.href = 'profile-notifications.php';
});

$("#view-followed-button").on("click", function () {
    window.location.href = 'view-followed-profile.php';
});

$("#saved-posts-button").on('click', function() {
    window.location.href = 'profile-saved.php';
});

document.querySelector('#logout-button').addEventListener('click', () => {
    // Ask for confirmation
    $('#buttons-div').empty();
    $('#buttons-div').append(`
        <h1 class="text-center">Sei sicuro di voler uscire?</h1>
        <button id="yes-button" class="btn btn-primary">Si</button>
        <button id="no-button" class="btn btn-primary">No</button>
    `);
    $('#yes-button').on("click", function () {
        window.location.href = 'logout.php';
    });
    $('#no-button').on("click", function () {
        window.location.href = 'tab-profile.php';
    });
});
