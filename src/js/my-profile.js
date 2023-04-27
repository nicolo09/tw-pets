document.querySelector('#settings-button').addEventListener('click', () => {
    window.location.href = 'profile-settings.php';
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

